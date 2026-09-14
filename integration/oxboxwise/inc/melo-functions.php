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
				/* Оговорка про оферту в подвале. Лежит здесь, а не в
				   разметке: текст юридический, менять его будут целиком и
				   сразу везде, где он появится. */
				'offer'      => $opt( 'opt_offer_note', 'Все цены, описания и перечни услуг, указанные на данном сайте, носят исключительно информационный характер и не являются публичной офертой, определяемой положениями Статьи 437 Гражданского кодекса РФ' ),
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
 * Кружки мессенджеров.
 *
 * Рисунки вставляются прямо сюда, а не берутся из спрайта
 * template-parts/melo-icons.php: спрайт печатают только шаблоны MELO, а
 * шапка и подвал стоят на всех страницах сайта — на чужой странице
 * ссылка на символ вела бы в пустоту, и кружки оказались бы пустыми.
 *
 * Мессенджер без адреса пропускается: пустой кружок выглядит рабочим и
 * никуда не ведёт, а это читается как поломка.
 *
 * @param string $class Класс обёртки.
 */
function melo_socials( $class = 'melo-socials' ) {
	/* Оба знака — заливкой, а не контуром: фирменные марки так и
	   выглядят, и рядом в одном ряду они должны быть одного веса. */
	$telegram = '<path fill="currentColor" d="M9.42 15.18l-.4 5.58c.57 0 .82-.24 1.11-.54l2.66-2.54 5.52 4.04c1.01.56 1.73.27 2-.93l3.62-16.97c.32-1.5-.54-2.08-1.53-1.71L1.11 10.32c-1.46.56-1.44 1.37-.25 1.74l5.41 1.69L18.81 5.86c.59-.39 1.12-.17.68.21z"/>';

	/* Знак MAX по присланному заказчиком оригиналу: кольцо с вырезанным
	   внутри пузырём, у пузыря хвостик влево-вниз. */
	$max = '<path fill="currentColor" d="M23.91 10.96Q24 12 23.91 13.04Q23.82 14.08 23.56 15.1Q23.29 16.11 22.84 17.06Q22.39 18 21.79 18.86Q21.18 19.71 20.44 20.44Q19.69 21.16 18.84 21.76Q17.98 22.35 17.04 22.79Q16.09 23.23 15.09 23.51Q14.08 23.79 13.04 23.87Q12 23.95 10.97 23.82Q9.94 23.69 8.98 23.31Q8.02 22.92 6.76 23.1Q5.49 23.28 3.83 23.5Q2.16 23.72 1.85 22.25Q1.53 20.78 1.26 19.57Q0.98 18.36 0.72 17.28Q0.45 16.2 0.27 15.15Q0.09 14.1 0.04 13.05Q-0.01 12 0.1 10.96Q0.21 9.92 0.49 8.92Q0.77 7.91 1.2 6.96Q1.63 6.01 2.22 5.15Q2.81 4.29 3.55 3.55Q4.29 2.81 5.15 2.22Q6.01 1.63 6.96 1.2Q7.91 0.77 8.92 0.5Q9.92 0.23 10.96 0.14Q12 0.04 13.04 0.11Q14.08 0.18 15.1 0.44Q16.12 0.69 17.07 1.14Q18.01 1.59 18.87 2.19Q19.73 2.79 20.46 3.54Q21.19 4.29 21.79 5.15Q22.39 6 22.82 6.95Q23.25 7.9 23.54 8.91Q23.82 9.92 23.91 10.96ZM18.11 11.47Q18.06 10.93 17.92 10.42Q17.77 9.9 17.55 9.42Q17.32 8.93 17 8.5Q16.68 8.07 16.31 7.7Q15.93 7.32 15.49 7.03Q15.04 6.74 14.56 6.51Q14.08 6.28 13.57 6.15Q13.06 6.01 12.53 5.96Q12 5.91 11.47 5.95Q10.94 5.99 10.43 6.12Q9.91 6.25 9.44 6.5Q8.96 6.74 8.55 7.07Q8.13 7.39 7.78 7.78Q7.43 8.16 7.15 8.6Q6.86 9.03 6.64 9.5Q6.42 9.97 6.28 10.47Q6.13 10.97 6.05 11.49Q5.96 12 5.94 12.54Q5.92 13.07 5.92 13.65Q5.91 14.22 5.98 14.83Q6.04 15.44 6.19 16.1Q6.33 16.76 6.64 17.39Q6.95 18.01 7.99 17.59Q9.02 17.17 9.49 17.41Q9.95 17.64 10.45 17.79Q10.95 17.94 11.48 18Q12 18.05 12.53 18.01Q13.05 17.96 13.56 17.83Q14.07 17.7 14.55 17.47Q15.03 17.24 15.46 16.94Q15.89 16.64 16.28 16.28Q16.66 15.91 16.97 15.48Q17.28 15.05 17.52 14.57Q17.75 14.09 17.91 13.58Q18.06 13.07 18.11 12.54Q18.15 12 18.11 11.47Z"/>';

	$list = array(
		'Telegram' => array( melo_contact( 'telegram' ), $telegram ),
		'MAX'      => array( melo_contact( 'max' ), $max ),
	);

	$shown = array();
	foreach ( $list as $label => $item ) {
		if ( '' === $item[0] || '#' === $item[0] ) {
			continue;
		}
		$shown[ $label ] = $item;
	}

	if ( ! $shown ) {
		return;
	}

	echo '<div class="' . esc_attr( $class ) . '">';
	foreach ( $shown as $label => $item ) {
		printf(
			/* Никаких stroke на обёртке: знаки рисуются заливкой, и оставшаяся
			   от прежних контурных значков обводка в 1.3 ложилась поверх
			   заливки — знак толстел и мылился. Заказчик увидел это раньше
			   меня, сказал «размытые и жирные». */
			'<a class="melo-social" href="%1$s" target="_blank" rel="noopener" aria-label="%2$s"><svg viewBox="0 0 24 24" aria-hidden="true">%3$s</svg></a>',
			esc_url( $item[0] ),
			esc_attr( $label ),
			$item[1]
		);
	}
	echo '</div>';
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
