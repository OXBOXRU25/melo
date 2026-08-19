<?php
/**
 * Хлебные крошки.
 *
 * Файл темы, заменённый пакетом MELO. Отличие одно: на шаблонах MELO
 * видимый блок здесь не выводится — крошки стоят внутри первого экрана,
 * над заголовком, по решению заказчика. Разметку для поисковиков
 * (JSON-LD) при этом оставляем на месте: она невидима и дублировать её
 * ниже незачем.
 *
 * На всех прочих страницах сайта вывод прежний.
 *
 * @package oxboxwise
 */

global $post;

if ( ! function_exists( 'bcn_display' ) ) {
	return;
}

$melo_own = function_exists( 'melo_is_melo_template' ) && melo_is_melo_template();

if ( ! $melo_own ) : ?>
	<div class="breadcrumbs ">
		<ul class="breadcrumbs__list container">
			<?php bcn_display_list(); ?>
		</ul>
	</div>
<?php endif; ?>
<script type="application/ld+json"><?php bcn_display_json_ld(); ?></script>
