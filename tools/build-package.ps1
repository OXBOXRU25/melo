# ============================================================
#  MELO - сборка пакета для передачи
#
#  Делает два архива в папке dist:
#    melo.zip           - тема WordPress, ставится через админку
#    melo-peredacha.zip - всё вместе: страница, ассеты, тема, инструкция
#
#  Внутри общего архива index.html - это САМА страница, а не переадресация:
#  двойной клик открывает вёрстку сразу, без промежуточного шага.
#
#  Запуск:  powershell -File tools\build-package.ps1
# ============================================================

$ErrorActionPreference = 'Stop'

Add-Type -AssemblyName System.IO.Compression
Add-Type -AssemblyName System.IO.Compression.FileSystem

$root = Split-Path -Parent $PSScriptRoot
$dist = Join-Path $root 'dist'
$stage = Join-Path $env:TEMP ('melo-build-' + [System.Guid]::NewGuid().ToString('N').Substring(0, 8))

# --- Compress-Archive пишет пути с обратными слешами, это нарушение формата
#     ZIP: Windows распакует, macOS развернёт кашу из файлов вида "assets\a.css".
#     Поэтому собираем сами и подставляем прямые слеши вручную.
function New-Zip {
    param(
        [string] $SourceDir,   # что упаковываем
        [string] $ZipPath,     # куда
        [string] $Prefix       # префикс пути внутри архива, может быть пустым
    )

    if (Test-Path $ZipPath) { Remove-Item $ZipPath -Force }

    $slash = [char]47
    $back = [char]92
    $zip = [System.IO.Compression.ZipFile]::Open($ZipPath, [System.IO.Compression.ZipArchiveMode]::Create)
    try {
        $files = Get-ChildItem -Path $SourceDir -Recurse -File -Force
        foreach ($f in $files) {
            $rel = $f.FullName.Substring($SourceDir.Length).TrimStart($back)
            $rel = $rel.Replace($back, $slash)
            if ($Prefix) { $rel = $Prefix + $slash + $rel }
            [void][System.IO.Compression.ZipFileExtensions]::CreateEntryFromFile(
                $zip, $f.FullName, $rel, [System.IO.Compression.CompressionLevel]::Optimal)
        }
    }
    finally {
        $zip.Dispose()
    }

    $size = [Math]::Round((Get-Item $ZipPath).Length / 1MB, 2)
    $count = (Get-ChildItem -Path $SourceDir -Recurse -File -Force).Count
    Write-Host ("  " + (Split-Path $ZipPath -Leaf) + " - " + $count + " файлов, " + $size + " МБ")
}

if (-not (Test-Path $dist)) { New-Item -ItemType Directory -Path $dist | Out-Null }

# ---------- 1. Тема WordPress ----------
Write-Host 'Собираю тему:'
$themeSrc = Join-Path $root 'wordpress\melo'
New-Zip -SourceDir $themeSrc -ZipPath (Join-Path $dist 'melo.zip') -Prefix 'melo'

# ---------- 2. Пакет передачи ----------
Write-Host 'Собираю пакет передачи:'
New-Item -ItemType Directory -Path $stage | Out-Null
try {
    # Все страницы из корня. Пропускаем index.html (собирается ниже),
    # служебные файлы с подчёркиванием и standalone-сборки в один файл.
    $pages = Get-ChildItem -Path $root -Filter '*.html' -File |
        Where-Object { $_.Name -ne 'index.html' -and $_.Name -notlike '_*' -and $_.Name -notlike '*.standalone.html' }

    foreach ($p in $pages) {
        Copy-Item $p.FullName (Join-Path $stage $p.Name)
    }
    Write-Host ('  страниц: ' + $pages.Count)

    # index.html - копия главной страницы, чтобы архив открывался двойным кликом
    $main = 'dizayn-interera-i-eksterera.html'
    Copy-Item (Join-Path $root $main) (Join-Path $stage 'index.html')

    Copy-Item (Join-Path $root 'assets') (Join-Path $stage 'assets') -Recurse
    Copy-Item (Join-Path $root 'PROJECT.md') (Join-Path $stage 'PROJECT.md')

    $wp = Join-Path $stage 'wordpress'
    New-Item -ItemType Directory -Path $wp | Out-Null
    Copy-Item $themeSrc (Join-Path $wp 'melo') -Recurse
    Copy-Item (Join-Path $dist 'melo.zip') (Join-Path $wp 'melo.zip')

    Copy-Item (Join-Path $root 'tools\README-peredacha.txt') (Join-Path $stage 'README.txt')

    New-Zip -SourceDir $stage -ZipPath (Join-Path $dist 'melo-peredacha.zip') -Prefix ''
}
finally {
    if (Test-Path $stage) { Remove-Item $stage -Recurse -Force }
}

Write-Host ''
Write-Host ('Готово: ' + $dist)
