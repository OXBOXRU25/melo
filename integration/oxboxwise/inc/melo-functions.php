<?php
/**
 * MELO — вспомогательные функции внутренних страниц
 *
 * Подключается из functions.php темы одной строкой:
 *   require get_template_directory() . '/inc/melo-functions.php';
 *
 * Ничего не переопределяет и ни на что не вешается глобально — только
 * объявляет две функции, которыми пользуются шаблоны MELO.
 *
 * @package oxboxwise
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Контакты для шапки и подвала страниц MELO.
 *
 * Значения по умолчанию — рыба из макета. Переопределяются фильтром,
 * без правки шаблонов:
 *
 *   add_filter( 'melo_contacts', function ( $data ) {
 *       $data['phone']     = '+7 843 000 00 00';
 *       $data['phone_raw'] = '+78430000000';
 *       return $data;
 *   } );
 *
 * @param string $key Ключ значения.
 * @return string
 */
function melo_contact( $key ) {
	static $data = null;

	if ( null === $data ) {
		$data = apply_filters(
			'melo_contacts',
			array(
				'phone'     => '+7 900 000 00 00',
				'phone_raw' => '+79000000000',
				'phone2'    => '8 800 000 00 00',
				'phone2_raw'=> '88000000000',
				'email'     => 'info@name.ru',
				'address'   => 'г. Казань, ул. Ленина 00',
				'claim'     => 'Всегда открыт для новых проектов и сотрудничества.',
				'telegram'  => '#',
				'max'       => '#',
				'policy'    => '#',
			)
		);
	}

	return isset( $data[ $key ] ) ? $data[ $key ] : '';
}

/**
 * Меню в разметке MELO.
 *
 * Выводит плоский список ссылок без обёрток — так же, как в макете.
 * Walker не используется намеренно: разметка нужна предсказуемая, а
 * стандартный вывод тянет за собой ul/li и свои классы.
 *
 * @param string $location   Область меню: menu_main или menu_footer.
 * @param string $link_class Класс ссылки.
 * @param array  $fallback   Запасной набор «подпись => адрес», пока меню не назначено.
 */
function melo_nav( $location, $link_class = '', $fallback = array() ) {
	$items = array();

	if ( has_nav_menu( $location ) ) {
		$locations = get_nav_menu_locations();
		$menu      = wp_get_nav_menu_object( $locations[ $location ] );
		if ( $menu ) {
			$items = wp_get_nav_menu_items( $menu->term_id );
		}
	}

	if ( empty( $items ) ) {
		foreach ( $fallback as $label => $href ) {
			printf(
				'<a class="%s" href="%s">%s</a>',
				esc_attr( $link_class ),
				esc_url( $href ),
				esc_html( $label )
			);
		}
		return;
	}

	$current = get_the_ID();

	foreach ( $items as $item ) {
		if ( (int) $item->menu_item_parent !== 0 ) {
			continue;   // подпункты в этой разметке не предусмотрены
		}

		$is_current = ( 'page' === $item->object && (int) $item->object_id === (int) $current );

		printf(
			'<a class="%1$s" href="%2$s"%3$s>%4$s</a>',
			esc_attr( $link_class ),
			esc_url( $item->url ),
			$is_current ? ' aria-current="page"' : '',
			esc_html( $item->title )
		);
	}
}
