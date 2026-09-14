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

/* Поля админки для страниц MELO. Подключаем отсюда, а не из functions.php
   темы: тогда весь наш код цепляется к сайту одной строкой, и при
   переносе пакета нечего забыть. */
require_once __DIR__ . '/melo-fields.php';

/**
 * Стили шапки и подвала — на всех страницах сайта.
 *
 * Шапка общая, поэтому её стили нельзя запирать под .melo-page. Файл
 * melo-chrome.css изолирован только префиксами классов и подключается
 * последним, чтобы перекрывать прежние правила темы там, где имена
 * всё-таки пересеклись бы.
 *
 * Стили самих страниц (melo-page.css) грузит их собственный каркас —
 * на других страницах они не нужны и не подключаются.
 */
function melo_enqueue_chrome() {
	$file = get_template_directory() . '/css/melo-chrome.css';

	wp_enqueue_style(
		'melo-fonts',
		'https://fonts.googleapis.com/css2?family=Golos+Text:wght@400;500;600;700&family=Merriweather:wght@400;700&display=swap',
		array(),
		null
	);

	wp_enqueue_style(
		'melo-chrome',
		get_template_directory_uri() . '/css/melo-chrome.css',
		array( 'melo-fonts' ),
		file_exists( $file ) ? filemtime( $file ) : null
	);

	/* Стили лайтбокса галереи — только на страницах проектов, где он есть.

	   Тема подключает css/libs.min.css в header.php как <script>, то есть
	   таблицу стилей грузит скриптом, и браузер её не применяет. Галерея
	   из-за этого открывалась голой разметкой под подвалом. Целиком тот
	   файл подключать нельзя — он несёт normalize.css и Swiper и сдвинул
	   бы базовые стили всего сайта, поэтому берём из него только Fancybox. */
	if ( is_singular( 'projects' ) ) {
		$fb = get_template_directory() . '/css/melo-fancybox.css';
		wp_enqueue_style(
			'melo-fancybox',
			get_template_directory_uri() . '/css/melo-fancybox.css',
			array( 'melo-chrome' ),
			file_exists( $fb ) ? filemtime( $fb ) : null
		);
	}

	/* Точечные правки существующих секций темы — светлые «Услуги»
	   и ссылки на карточках. Идут после chrome, чтобы перекрывать. */
	$ovr = get_template_directory() . '/css/melo-overrides.css';
	wp_enqueue_style(
		'melo-overrides',
		get_template_directory_uri() . '/css/melo-overrides.css',
		array( wp_style_is( 'melo-fancybox', 'enqueued' ) ? 'melo-fancybox' : 'melo-chrome' ),
		file_exists( $ovr ) ? filemtime( $ovr ) : null
	);

	$js = get_template_directory() . '/js/melo-page.js';
	wp_enqueue_script(
		'melo-page',
		get_template_directory_uri() . '/js/melo-page.js',
		array(),
		file_exists( $js ) ? filemtime( $js ) : null,
		true
	);
}
add_action( 'wp_enqueue_scripts', 'melo_enqueue_chrome', 100 );

/**
 * Страница собрана на шаблоне MELO?
 *
 * Нужно там, где поведение темы отличается на наших страницах: сейчас —
 * чтобы не выводить хлебные крошки над первым экраном, они перенесены
 * внутрь него.
 */
function melo_is_melo_template() {
	$tpl = get_page_template_slug();
	return $tpl && false !== strpos( $tpl, 'template-melo-' );
}

/**
 * Значок сайта.
 *
 * Ставится в wp_head с приоритетом 1, чтобы перекрыть значок из
 * настройщика WordPress, если он там задан.
 */
function melo_favicon() {
	$dir = get_template_directory_uri() . '/img/melo/';
	echo '<link rel="icon" type="image/png" sizes="32x32" href="' . esc_url( $dir . 'favicon-32.png' ) . '">' . "\n";
	echo '<link rel="icon" type="image/png" sizes="512x512" href="' . esc_url( $dir . 'favicon-512.png' ) . '">' . "\n";
	echo '<link rel="apple-touch-icon" href="' . esc_url( $dir . 'favicon-180.png' ) . '">' . "\n";
}
add_action( 'wp_head', 'melo_favicon', 1 );

/* Значок из настройщика WordPress убираем: иначе в head два набора
   иконок и браузер берёт последний, то есть прежний. */
remove_action( 'wp_head', 'wp_site_icon', 99 );

/**
 * Стили и скрипт содержимого страниц MELO.
 *
 * Вызывается из шаблонов ДО get_header(), чтобы попасть в wp_head().
 * На остальных страницах сайта этот файл не грузится вовсе.
 */
function melo_enqueue_page() {
	$file = get_template_directory() . '/css/melo-page.css';
	wp_enqueue_style(
		'melo-page',
		get_template_directory_uri() . '/css/melo-page.css',
		array( 'melo-chrome' ),
		file_exists( $file ) ? filemtime( $file ) : null
	);

	add_action( 'wp_head', 'melo_reveal_bootstrap', 5 );
}

/**
 * Запуск появления блоков.
 *
 * Прятать блоки можно только при живом JS, иначе без него страница
 * останется пустой навсегда. Поэтому класс melo-js ставит крохотный
 * скрипт в самом верху документа — до отрисовки, чтобы не мигало, — и
 * только под ним в CSS работают правила скрытия.
 *
 * Второй таймер — аварийный: если основной скрипт почему-то не поднялся
 * и не проставил melo-reveal-ready, через 2.5 секунды блоки показываются
 * принудительно.
 *
 * Раньше этот код лежал прямо в шаблоне главной, и на остальных
 * страницах MELO появление не отрабатывало вовсе: класса melo-js там не
 * было, блоки просто стояли на месте. Теперь он общий для всех страниц,
 * которые зовут melo_enqueue_page().
 */
function melo_reveal_bootstrap() {
	?>
<script>
(function () {
	var d = document.documentElement;
	d.classList.add('melo-js');
	setTimeout(function () {
		if (!d.classList.contains('melo-reveal-ready')) d.classList.add('melo-reveal-failsafe');
	}, 2500);
})();
</script>
	<?php
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
		/* Значения берём из настроек темы (ACF, страница «Настройки»),
		   чтобы контакты правились из админки, а не из кода. Что не
		   заполнено — подставляется из умолчаний ниже. */
		$opt = function ( $field, $default = '' ) {
			if ( ! function_exists( 'get_field' ) ) {
				return $default;
			}
			$v = get_field( $field, 'option' );
			if ( is_array( $v ) ) {
				$v = isset( $v['url'] ) ? $v['url'] : '';
			}
			$v = is_string( $v ) ? trim( $v ) : '';
			return '' !== $v ? $v : $default;
		};

		$phone  = $opt( 'opt_phone_site', '+7 960 089 28 40' );
		$phone2 = $opt( 'opt_phone2_site' );

		/* Для tel: нужны только цифры. Ведущую 8 приводим к +7 — иначе
		   часть телефонов набирается, а часть нет, в зависимости от того,
		   как их записали в админке. */
		$raw = function ( $s ) {
			$d = preg_replace( '/\D+/', '', (string) $s );
			if ( '' === $d ) {
				return '';
			}
			if ( 11 === strlen( $d ) && '8' === $d[0] ) {
				$d = '7' . substr( $d, 1 );
			}
			return '+' . $d;
		};

		$data = apply_filters(
			'melo_contacts',
			array(
				'phone'      => $phone,
				'phone_raw'  => $raw( $phone ),
				'phone2'     => $phone2,
				'phone2_raw' => $raw( $phone2 ),
				'email'      => $opt( 'opt_email_site', 'info@name.ru' ),
				'address'    => $opt( 'opt_adr_site', 'г. Казань' ),
				'hours'      => $opt( 'opt_time_site', 'с 10:00 до 19:00' ),
				'claim'      => $opt( 'opt_text_site', 'Всегда открыт для новых проектов и сотрудничества.' ),
				'telegram'   => $opt( 'opt_soc_tg', 'https://t.me/ravil_srf' ),
				/* Пусто, а не «#»: пустой адрес шаблоны выводят текстом,
				   а решётка сделала бы вид рабочей ссылки в никуда. */
				'max'        => $opt( 'opt_soc_max' ),
				'policy'     => $opt( 'opt_link_personal', home_url( '/politika-konfidentsialnosti/' ) ),
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
	$menu  = false;

	/* Сначала как область меню (menu_main, menu_footer), затем как имя
	   самого меню — в теме оно зовётся «Главное меню» и выводится по имени,
	   а не через область. */
	if ( has_nav_menu( $location ) ) {
		$locations = get_nav_menu_locations();
		$menu      = wp_get_nav_menu_object( $locations[ $location ] );
	}
	if ( ! $menu ) {
		$menu = wp_get_nav_menu_object( $location );
	}
	if ( $menu ) {
		$items = wp_get_nav_menu_items( $menu->term_id );
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

	/* На главной часть пунктов ведёт к разделам этой же страницы, а не на
	   внутренние: подменяем адрес по последнему сегменту пути,
	   /services/ -> #services и так далее. Названия пунктов не трогаем,
	   поэтому переименование в админке ничего не сломает.

	   «Кейсы» и «Контакты» в подмену не входят по решению заказчика: у них
	   есть собственные страницы, и вести на якорь вместо страницы значит
	   прятать от человека половину содержимого. */
	$to_anchor = array(
		'about'    => '#about',
		'services' => '#services',
		'areas'    => '#areas',
	);
	$on_front = is_front_page() || is_home();

	foreach ( $items as $item ) {
		if ( (int) $item->menu_item_parent !== 0 ) {
			continue;   // подпункты в этой разметке не предусмотрены
		}

		$is_current = ( 'page' === $item->object && (int) $item->object_id === (int) $current );

		$url = $item->url;

		if ( $on_front ) {
			/* на главной — якорь к разделу этой же страницы */
			$slug = trim( wp_parse_url( $url, PHP_URL_PATH ) ? wp_parse_url( $url, PHP_URL_PATH ) : '', '/' );
			$slug = $slug ? substr( strrchr( '/' . $slug, '/' ), 1 ) : '';
			if ( $slug && isset( $to_anchor[ $slug ] ) ) {
				$url = $to_anchor[ $slug ];
			}
		} elseif ( '' !== $url && '#' === $url[0] && '#contacts' !== $url ) {
			/* На внутренних страницах голый якорь вёл бы в никуда: таких
			   секций здесь нет. Разворачиваем на главную. Исключение —
			   #contacts: подвал есть на каждой странице. */
			$url = home_url( '/' ) . $url;
		}

		printf(
			'<a class="%1$s" href="%2$s"%3$s>%4$s</a>',
			esc_attr( $link_class ),
			esc_url( $url ),
			$is_current ? ' aria-current="page"' : '',
			esc_html( $item->title )
		);
	}
}

/**
 * Версия файла темы для адреса — время его правки.
 *
 * В header.php версия считалась как time(), то есть менялась при каждом
 * запросе: браузер не мог закэшировать ни style.min.css, ни custom.js и
 * качал их заново на каждой странице. Время правки меняется только
 * вместе с файлом, поэтому кэш живёт и обновляется ровно тогда, когда
 * файл действительно изменили.
 *
 * @param string $rel Путь от корня темы, со слешем в начале.
 * @return string
 */
function melo_asset_ver( $rel ) {
	$file = get_template_directory() . $rel;
	return file_exists( $file ) ? (string) filemtime( $file ) : '1';
}

/**
 * Обложка проекта для карточки.
 *
 * У проекта два поля с картинкой, и это ловушка для заказчика:
 *
 * • «Главное изображение» (поле ACF main_image) — широкая полоса-фото
 *   внутри самой страницы проекта;
 * • «Изображение записи» — миниатюра WordPress, из которой рисуется
 *   карточка в каталоге и на главной.
 *
 * Человек заполняет первое, потому что оно так и называется, а карточка
 * до сих пор читала второе — и оставалась с демонстрационным снимком.
 * Поэтому порядок теперь такой: сначала «Главное изображение», и только
 * если оно пустое — миниатюра. Поле начинает работать так, как названо.
 *
 * Размеры project_front_* нарезает тема (903×578, 903×827, 903×1004), и
 * ACF отдаёт их в том же массиве. Нужного среза может не оказаться — у
 * мелкого оригинала его не создают, — тогда берём полный файл: лучше
 * некадрированная картинка, чем пустой src.
 *
 * @param int    $post_id Запись проекта.
 * @param string $size    Имя размера.
 * @return string Адрес картинки или пустая строка.
 */
function melo_project_image( $post_id, $size = 'project_front_big' ) {
	if ( function_exists( 'get_field' ) ) {
		$main = get_field( 'main_image', $post_id );
		if ( is_array( $main ) ) {
			if ( ! empty( $main['sizes'][ $size ] ) ) {
				return $main['sizes'][ $size ];
			}
			if ( ! empty( $main['url'] ) ) {
				return $main['url'];
			}
		}
	}

	$url = get_the_post_thumbnail_url( $post_id, $size );
	if ( ! $url ) {
		$url = get_the_post_thumbnail_url( $post_id, 'full' );
	}

	return $url ? $url : '';
}

/**
 * Фотография первого экрана.
 *
 * Это самая тяжёлая картинка страницы и почти всегда тот самый элемент,
 * по которому Google считает LCP. Поэтому здесь три вещи:
 *
 * • srcset — телефону незачем качать 1920px, он покажет 412;
 * • fetchpriority="high" — браузер по умолчанию считает картинку
 *   неважной и берётся за неё после стилей и скриптов;
 * • loading="eager" — ленивая загрузка тут навредила бы: картинка и так
 *   в кадре, а отложенный запрос сдвигает LCP.
 *
 * Если картинка из медиатеки, srcset собирает сам WordPress. Если это
 * файл темы — ищем рядом варианты вида hero-960.jpg, их готовит
 * tools/make-hero-sizes.php.
 *
 * @param int    $attachment_id Идентификатор вложения или 0.
 * @param string $file          Имя файла в img/melo/ на случай, если вложения нет.
 * @param string $alt           Альтернативный текст.
 */
function melo_hero_image( $attachment_id, $file = 'hero.jpg', $alt = '' ) {
	$attrs = array(
		'class'         => 'melo-hero__bg',
		'alt'           => $alt,
		'sizes'         => '100vw',
		'fetchpriority' => 'high',
		'decoding'      => 'async',
		'loading'       => 'eager',
	);

	if ( $attachment_id ) {
		echo wp_get_attachment_image( $attachment_id, 'full', false, $attrs );
		return;
	}

	$dir  = get_template_directory() . '/img/melo/';
	$uri  = get_template_directory_uri() . '/img/melo/';
	$name = pathinfo( $file, PATHINFO_FILENAME );
	$ext  = pathinfo( $file, PATHINFO_EXTENSION );

	if ( ! file_exists( $dir . $file ) ) {
		return;
	}

	$size = getimagesize( $dir . $file );
	$set  = array();

	foreach ( glob( $dir . $name . '-*.' . $ext ) as $variant ) {
		if ( preg_match( '/-(\d+)\.' . preg_quote( $ext, '/' ) . '$/', $variant, $m ) ) {
			$set[] = esc_url( $uri . basename( $variant ) ) . ' ' . (int) $m[1] . 'w';
		}
	}
	$set[] = esc_url( $uri . $file ) . ' ' . (int) $size[0] . 'w';
	sort( $set, SORT_NATURAL );

	printf(
		'<img class="melo-hero__bg" src="%1$s" srcset="%2$s" sizes="100vw" alt="%3$s" width="%4$d" height="%5$d" fetchpriority="high" decoding="async" loading="eager">',
		esc_url( $uri . $file ),
		esc_attr( implode( ', ', $set ) ),
		esc_attr( $alt ),
		(int) $size[0],
		(int) $size[1]
	);
}

/**
 * Направления деятельности у проектов.
 *
 * Раньше «тип объекта» лежал текстовым полем ACF: одно значение на
 * проект и свободный ввод, то есть опечатка молча выкидывала проект из
 * любой выборки. Заводим отдельную сущность:
 *
 * • иерархическая — значит в админке чекбоксы, а не выпадающий список,
 *   и проект можно отнести сразу к нескольким направлениям. Так и
 *   бывает: один объект — и проектирование, и реализация;
 * • список правится в одном месте, «Записи → Направления»;
 * • у каждого направления свой адрес, он индексируется и им можно
 *   поделиться.
 */
function melo_register_directions() {
	register_taxonomy(
		'napravlenie',
		array( 'projects' ),
		array(
			'labels'            => array(
				'name'          => 'Направления',
				'singular_name' => 'Направление',
				'menu_name'     => 'Направления',
				'all_items'     => 'Все направления',
				'edit_item'     => 'Изменить направление',
				'add_new_item'  => 'Добавить направление',
				'search_items'  => 'Найти направление',
				'not_found'     => 'Направлений не найдено',
			),
			/* Иерархическая — ради чекбоксов в админке. Вложенность мы не
			   используем, но именно этот флаг переключает вид поля. */
			'hierarchical'      => true,
			'public'            => true,
			'show_admin_column' => true,
			'show_in_rest'      => true,
			'rewrite'           => array(
				'slug'       => 'projects/napravlenie',
				'with_front' => false,
			),
		)
	);
}
add_action( 'init', 'melo_register_directions', 5 );

/**
 * Направления в том порядке, в каком их называет заказчик.
 *
 * Порядок задан здесь, а не в админке: по умолчанию термины сортируются
 * по алфавиту, и «Архитектурное проектирование» оказалось бы не первым,
 * а «Реализация» — не четвёртой. Список тот же, что в слайдере на
 * главной, и меняться они должны вместе.
 *
 * @return array Слаг => подпись.
 */
function melo_directions() {
	return array(
		'arhitekturnoe-proektirovanie' => 'Архитектурное проектирование и планирование',
		'dizayn-interera-i-eksterera'  => 'Дизайн интерьера и экстерьера',
		'landshaft-i-maf'              => 'Ландшафт и малые архитектурные формы',
		'realizatsiya-i-stroitelstvo'  => 'Реализация и строительство',
		'soprovozhdenie-i-upravlenie'  => 'Сопровождение и управление',
	);
}

/**
 * Направления одного проекта — слагами, для атрибута в разметке.
 *
 * @param int $post_id Идентификатор записи.
 * @return string Слаги через пробел.
 */
function melo_post_directions( $post_id = 0 ) {
	$terms = get_the_terms( $post_id ?: get_the_ID(), 'napravlenie' );
	if ( ! $terms || is_wp_error( $terms ) ) {
		return '';
	}
	return implode( ' ', wp_list_pluck( $terms, 'slug' ) );
}
