<?php
/**
 * Спрайт иконок страниц MELO.
 *
 * Один набор на все шаблоны. До этого каждый шаблон нёс свой спрайт, и
 * они успели разойтись: на странице услуги было пять «архитектурных»
 * значков, а под ландшафт или стройку рисовать было нечем.
 *
 * Подключается один раз в начале контента:
 *   get_template_part( 'template-parts/melo-icons' );
 *
 * Рисунки — только контур: цвет наследуется от текста, поэтому иконка
 * сама подстраивается под секцию, и отдельных файлов не нужно вовсе.
 * Все значки нарисованы в одной сетке 24×24 с толщиной 1.3, иначе в
 * одном списке они читались бы разного веса.
 *
 * Добавляя значок, впиши его и в список выбора в inc/melo-fields.php —
 * иначе заказчик его в админке не увидит.
 *
 * @package oxboxwise
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<svg width="0" height="0" style="position:absolute" aria-hidden="true" focusable="false">

	<?php /* Стрелки и общее */ ?>
	<symbol id="melo-i-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
		<path d="M4 12h15M13 6l6 6-6 6"/>
	</symbol>

	<?php /* Контакты */ ?>
	<symbol id="melo-i-phone" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round">
		<path d="M6.5 3.5h3l1.5 4-2 1.5a12 12 0 0 0 6 6l1.5-2 4 1.5v3a1.5 1.5 0 0 1-1.6 1.5C11.6 19.5 4.5 12.4 4 5.1A1.5 1.5 0 0 1 5.5 3.5z"/>
	</symbol>
	<symbol id="melo-i-mail" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round">
		<rect x="3" y="5" width="18" height="14" rx="1.5"/><path d="M3.5 6.5 12 13l8.5-6.5"/>
	</symbol>
	<symbol id="melo-i-pin" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round">
		<path d="M12 21s7-6.1 7-11a7 7 0 1 0-14 0c0 4.9 7 11 7 11z"/><circle cx="12" cy="10" r="2.6"/>
	</symbol>
	<symbol id="melo-i-clock" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round">
		<circle cx="12" cy="12" r="8.5"/><path d="M12 7v5.2l3.4 2"/>
	</symbol>

	<?php /* Проектирование */ ?>
	<symbol id="melo-i-plot-search" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round">
		<path d="M3 4h13v9"/><path d="M3 4v16h9"/><path d="M3 12h6"/>
		<circle cx="17" cy="17" r="4"/><path d="M20 20l1.6 1.6"/>
	</symbol>
	<symbol id="melo-i-house-plan" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round">
		<path d="M2.5 10.5 12 3l9.5 7.5"/><path d="M5 12.5V21h14v-8.5"/><path d="M10 21v-5h4v5"/>
	</symbol>
	<symbol id="melo-i-site-plan" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round">
		<path d="M3 3.5h18v17H3z"/><path d="M3 13h8v7.5"/><path d="M11 13V8h10"/>
		<circle cx="16.5" cy="17" r="2.2"/>
	</symbol>
	<symbol id="melo-i-structure" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round">
		<path d="M3 20.5h18"/><path d="M5 20.5V9l7-4.5L19 9v11.5"/>
		<path d="M5 12.5h14"/><path d="M5 16.5h14"/><path d="M12 12.5v8"/>
	</symbol>
	<symbol id="melo-i-permit" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round">
		<path d="M14 2.5H6.5a1 1 0 0 0-1 1v17a1 1 0 0 0 1 1h11a1 1 0 0 0 1-1V7z"/>
		<path d="M14 2.5V7h4.5"/><path d="M8.5 15.5l2 2 4.5-4.5"/>
	</symbol>
	<symbol id="melo-i-ruler" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round">
		<rect x="2.5" y="8" width="19" height="8" rx="1.2"/>
		<path d="M7 8v3"/><path d="M11 8v4"/><path d="M15 8v3"/><path d="M19 8v4"/>
	</symbol>

	<?php /* Дизайн */ ?>
	<?php /* Четыре кружка в рамке читаются как выкраска. Предыдущий вариант
	         мешал в одной коробке кружки и квадрат — при 24px это была
	         просто каша из фигур. */ ?>
	<symbol id="melo-i-palette" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round">
		<rect x="3.5" y="3.5" width="17" height="17" rx="2.5"/>
		<circle cx="9" cy="9" r="1.7"/><circle cx="15" cy="9" r="1.7"/>
		<circle cx="9" cy="15" r="1.7"/><circle cx="15" cy="15" r="1.7"/>
	</symbol>
	<symbol id="melo-i-view" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round">
		<path d="M2.5 12S6 5.5 12 5.5 21.5 12 21.5 12 18 18.5 12 18.5 2.5 12 2.5 12z"/>
		<circle cx="12" cy="12" r="3"/>
	</symbol>
	<symbol id="melo-i-facade" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round">
		<path d="M4 20.5V6.5l8-3 8 3v14"/><path d="M2.5 20.5h19"/>
		<rect x="7.5" y="9" width="3.5" height="3.5"/><rect x="13" y="9" width="3.5" height="3.5"/>
		<path d="M10 20.5v-4.5h4v4.5"/>
	</symbol>
	<symbol id="melo-i-sofa" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round">
		<path d="M4.5 11V8a2 2 0 0 1 2-2h11a2 2 0 0 1 2 2v3"/>
		<rect x="2.5" y="11" width="19" height="6" rx="1.5"/>
		<path d="M6 17v2.5"/><path d="M18 17v2.5"/><path d="M8 11V8.5h8V11"/>
	</symbol>

	<?php /* Ландшафт */ ?>
	<symbol id="melo-i-tree" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round">
		<path d="M12 21v-5.5"/><path d="M9.5 21h5"/>
		<path d="M12 15.5a5.5 5.5 0 0 1-1.8-10.7A4 4 0 0 1 17.6 6a4.5 4.5 0 0 1-1.9 8.6z"/>
	</symbol>
	<symbol id="melo-i-gazebo" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round">
		<path d="M2.5 10 12 4l9.5 6"/><path d="M5.5 10v10.5"/><path d="M18.5 10v10.5"/>
		<path d="M2.5 20.5h19"/><path d="M5.5 13.5h13"/>
	</symbol>
	<symbol id="melo-i-lamp" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round">
		<path d="M8.5 15.5a5.5 5.5 0 1 1 7 0c-.9 1-1.3 1.7-1.4 2.5h-4.2c-.1-.8-.5-1.5-1.4-2.5z"/>
		<path d="M10 20.5h4"/>
	</symbol>

	<?php /* Стройка и сопровождение */ ?>
	<?php /* Валик, а не строительная каска: каска в круге при 24px читалась
	         как звонок на ресепшене — купол с кнопкой сверху и подставкой. */ ?>
	<symbol id="melo-i-roller" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round">
		<rect x="2.5" y="4" width="12" height="5" rx="1.2"/>
		<path d="M14.5 6.5h4a1.5 1.5 0 0 1 1.5 1.5v2a1.5 1.5 0 0 1-1.5 1.5H12"/>
		<rect x="9.8" y="11.5" width="4.4" height="3.2" rx="1"/>
		<path d="M12 14.7V21"/>
	</symbol>
	<symbol id="melo-i-wallet" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round">
		<rect x="2.5" y="5.5" width="19" height="13" rx="2"/>
		<path d="M2.5 9.5h19"/><circle cx="17" cy="14" r="1.4"/>
	</symbol>
	<symbol id="melo-i-calendar" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round">
		<rect x="3" y="5" width="18" height="16" rx="1.8"/>
		<path d="M3 9.5h18"/><path d="M8 3v4"/><path d="M16 3v4"/><path d="M7.5 13.5h4"/>
	</symbol>
	<symbol id="melo-i-shield-check" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round">
		<path d="M12 2.8 19.3 5.6v5.9c0 4.4-3.1 8.2-7.3 9.7-4.2-1.5-7.3-5.3-7.3-9.7V5.6z"/>
		<path d="M8.8 12.2 11 14.4l4.2-4.3"/>
	</symbol>
	<symbol id="melo-i-key" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round">
		<circle cx="8" cy="12" r="4"/><path d="M12 12h9.5"/>
		<path d="M17.5 12v3"/><path d="M20.5 12v2.2"/>
	</symbol>

</svg>
