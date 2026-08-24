<?php
/**
 * Пять страниц направлений: создать и наполнить.
 *
 * Была одна страница услуги — «Архитектурное проектирование», хотя
 * направлений у бюро пять и на главной они все показаны карточками.
 * Скрипт доводит серию до пяти, наполняет каждую своим текстом и
 * привязывает карточки на главной к соответствующим страницам.
 *
 * Что делает, по шагам:
 *   1. Находит страницу по ярлыку. Нет — создаёт, есть — не трогает
 *      заголовок и дату, только доводит недостающее.
 *   2. Ставит шаблон «MELO — страница услуги».
 *   3. Заполняет ПУСТЫЕ поля своим текстом. Заполненные не трогает:
 *      правки заказчика важнее наших заготовок.
 *   4. Прописывает заголовок и описание для поиска, если их нет.
 *   5. Проставляет карточкам на главной ссылки на эти страницы.
 *
 * Запуск (файл кладётся в корень сайта и удаляется сразу после):
 *   https://сайт/melo-make-pages.php?k=КЛЮЧ           — сделать
 *   https://сайт/melo-make-pages.php?k=КЛЮЧ&dry=1     — только показать
 *   https://сайт/melo-make-pages.php?k=КЛЮЧ&force=1   — перезаписать поля
 *
 * Без ключа отдаёт 404.
 *
 * Имена переменных с приставкой melo_ — не педантизм: $melo_page и $melo_pages
 * заняты самим WordPress, и первый же вызов, который трогает глобальные
 * данные записи, подменяет их у нас под ногами. Ловится это только
 * падением где-то в конце скрипта, когда работа уже наполовину сделана.
 */

define( 'MELO_MAKE_KEY', 'melo-2026-08-24' );

require __DIR__ . '/wp-load.php';

if ( ! isset( $_GET['k'] ) || MELO_MAKE_KEY !== $_GET['k'] ) {
	status_header( 404 );
	nocache_headers();
	exit;
}

header( 'Content-Type: text/plain; charset=utf-8' );

$dry   = isset( $_GET['dry'] );
$force = isset( $_GET['force'] );

if ( ! function_exists( 'melo_seed_page' ) ) {
	echo 'Не найдена melo_seed_page() — сначала залейте inc/melo-fields.php.' . PHP_EOL;
	exit;
}

require_once ABSPATH . 'wp-admin/includes/image.php';

const MELO_TPL = 'templates/template-melo-service.php';

/* =====================================================================
 * Содержимое страниц
 *
 * Здесь оно лежит только для первичного наполнения. Дальше это обычные
 * поля в админке, и правится всё оттуда.
 * ================================================================== */

$melo_pages = array(

	array(
		'slug'  => 'melo-arhitekturnoe-proektirovanie',
		'title' => 'Архитектурное проектирование',
		/* Эта страница уже сделана и наполнена — оставляем как есть,
		   перечисляем только ради ссылки с главной и порядка в серии. */
		'keep'  => true,
	),

	array(
		'slug'      => 'melo-dizayn-interera',
		'title'     => 'Дизайн интерьера и экстерьера',
		/* Страница стояла на шаблоне направления, и поля ей достались от
		   него: имена полей в базе общие, ключ группы лежит рядом. Тексты
		   там наши заготовки, а не правки заказчика, поэтому переписываем. */
		'reset'     => true,
		'lead'      => 'Собираем цельный образ дома внутри и снаружи: планировки, материалы, свет и фасады. Показываем результат в визуализации до того, как начнутся работы.',
		'image'     => 'project-2.jpg',
		'image_alt' => 'Интерьер гостиной с каменной стеной и мягким светом',
		'items'     => array(
			array(
				'icon'  => 'melo-i-ruler',
				'title' => 'Обмер и планировочное решение',
				'text'  => 'Снимаем размеры на объекте и предлагаем несколько вариантов планировки: где стены, где свет, как по дому ходят люди.',
			),
			array(
				'icon'  => 'melo-i-palette',
				'title' => 'Концепция, стиль и материалы',
				'text'  => 'Собираем стилистику и палитру по каждому помещению, подбираем отделку, плитку, текстиль и фурнитуру.',
			),
			array(
				'icon'  => 'melo-i-view',
				'title' => 'Фотореалистичная визуализация',
				'text'  => 'Показываем, как всё будет выглядеть, до начала работ — правки на этом этапе ничего не стоят.',
			),
			array(
				'icon'  => 'melo-i-facade',
				'title' => 'Дизайн экстерьера',
				'text'  => 'Фасады, заборы, входные группы и террасы: снаружи дом должен читаться так же собранно, как внутри.',
			),
			array(
				'icon'  => 'melo-i-sofa',
				'title' => 'Рабочие чертежи и комплектация',
				'text'  => 'Комплект документации для бригады плюс подбор мебели, света и оборудования с ценами и сроками поставки.',
			),
		),
		'seo_title' => 'Дизайн интерьера и экстерьера в Казани — студия MELO',
		'seo_desc'  => 'Дизайн-проект интерьера и экстерьера дома: обмер и планировки, подбор материалов, визуализация, фасады и комплектация объекта.',
	),

	array(
		'slug'      => 'melo-landshaft-i-maf',
		'title'     => 'Ландшафт и малые архитектурные формы',
		'lead'      => 'Продолжаем дом за его стенами: зонируем участок, ведём дорожки, ставим баню и беседку. Освещение, полив и дренаж считаем на этапе проекта, а не после посадок.',
		'image'     => 'dir-2.jpg',
		'image_alt' => 'Благоустроенная территория вокруг загородного дома',
		'items'     => array(
			array(
				'icon'  => 'melo-i-site-plan',
				'title' => 'Генплан участка и зонирование',
				'text'  => 'Раскладываем территорию: подъезд, парковка, зона отдыха, сад, детская. Проверяем, что всё это уживается на вашей площади.',
			),
			array(
				'icon'  => 'melo-i-tree',
				'title' => 'Дендроплан и озеленение',
				'text'  => 'Подбираем растения под ваш грунт и освещённость, готовим посадочный план — чтобы сад выглядел собранным и через пять лет.',
			),
			array(
				'icon'  => 'melo-i-gazebo',
				'title' => 'Малые архитектурные формы',
				'text'  => 'Баня, беседка, навес, бассейн, зона барбекю: проектируем их в одном языке с домом, а не докупаем готовыми.',
			),
			array(
				'icon'  => 'melo-i-lamp',
				'title' => 'Освещение, полив и дренаж',
				'text'  => 'Инженерия участка: сценарии света, автополив, ливнёвка и отвод воды. Прокладывается до благоустройства, а не вскрытием готового.',
			),
			array(
				'icon'  => 'melo-i-permit',
				'title' => 'Рабочие чертежи по благоустройству',
				'text'  => 'Развёртки, узлы, ведомости материалов и покрытий — комплект, по которому подрядчик работает без домыслов.',
			),
		),
		'steps'     => array(
			array( 'title' => 'Выезд на участок', 'text' => 'Смотрим рельеф, грунт, освещённость и то, что уже растёт и стоит.' ),
			array( 'title' => 'Генплан и зонирование', 'text' => 'Раскладываем участок по зонам и согласуем схему движения и посадки построек.' ),
			array( 'title' => 'Дендроплан и визуализация', 'text' => 'Подбираем растения и показываем участок таким, каким он станет.' ),
			array( 'title' => 'Чертежи и надзор', 'text' => 'Рабочая документация для подрядчика и контроль работ до сдачи.' ),
		),
		'seo_title' => 'Ландшафтный дизайн и малые формы в Казани — MELO',
		'seo_desc'  => 'Проект благоустройства участка: генплан и зонирование, озеленение, беседки и бани, освещение и полив, рабочие чертежи для подрядчика.',
	),

	array(
		'slug'      => 'melo-realizatsiya-i-stroitelstvo',
		'title'     => 'Реализация и строительство',
		'lead'      => 'Доводим проект до готового объекта: считаем смету, ведём стройку и отделку, закупаем материалы и мебель. Один ответственный на весь процесс вместо пяти подрядчиков.',
		'image'     => 'project-6.jpg',
		'image_alt' => 'Готовая кухня-гостиная после реализации проекта',
		'items'     => array(
			array(
				'icon'  => 'melo-i-wallet',
				'title' => 'Смета и график работ',
				'text'  => 'Разбираем проект на объёмы и сроки. Из чего складывается цена, видно ещё до первого платежа.',
			),
			array(
				'icon'  => 'melo-i-structure',
				'title' => 'Черновые и конструктивные работы',
				'text'  => 'Фундамент, коробка, кровля, перегородки и стяжки — по чертежам, а не «как привыкли на объекте».',
			),
			array(
				'icon'  => 'melo-i-roller',
				'title' => 'Отделка и инженерия',
				'text'  => 'Электрика, вода, вентиляция и вся чистовая отделка по дизайн-проекту, с проверкой на каждом этапе.',
			),
			array(
				'icon'  => 'melo-i-sofa',
				'title' => 'Комплектация объекта',
				'text'  => 'Закупаем мебель, свет, технику и сантехнику, ведём сроки поставки и принимаем всё на объекте.',
			),
			array(
				'icon'  => 'melo-i-key',
				'title' => 'Приёмка и сдача под ключ',
				'text'  => 'Проверяем работы по списку, устраняем замечания и передаём объект с документацией и гарантией.',
			),
		),
		'steps'     => array(
			array( 'title' => 'Смета и график', 'text' => 'Считаем объёмы по проекту и раскладываем работы по срокам.' ),
			array( 'title' => 'Договор и старт', 'text' => 'Фиксируем цену, этапы и порядок оплаты, выводим бригаду на объект.' ),
			array( 'title' => 'Стройка по этапам', 'text' => 'Ведём работы с еженедельным отчётом и приёмкой скрытых работ.' ),
			array( 'title' => 'Приёмка и сдача', 'text' => 'Проверяем по списку, устраняем замечания, передаём объект с гарантией.' ),
		),
		'seo_title' => 'Строительство и ремонт под ключ в Казани — MELO',
		'seo_desc'  => 'Реализация проекта под ключ: смета и график, черновые работы, отделка и инженерия, комплектация объекта, приёмка и гарантия.',
	),

	array(
		'slug'      => 'melo-soprovozhdenie-i-upravlenie',
		'title'     => 'Сопровождение и управление',
		'lead'      => 'Берём на себя переговоры с подрядчиками, сроки и бюджет, пока вы занимаетесь своими делами. Проект доходит до конца таким, каким его нарисовали.',
		'image'     => 'dir-3.jpg',
		'image_alt' => 'Фасад современного загородного дома',
		'items'     => array(
			array(
				'icon'  => 'melo-i-view',
				'title' => 'Авторский надзор',
				'text'  => 'Регулярно бываем на объекте и проверяем, что делают именно то, что в чертежах, — пока ошибку ещё можно исправить дёшево.',
			),
			array(
				'icon'  => 'melo-i-calendar',
				'title' => 'Управление сроками и подрядчиками',
				'text'  => 'Сводим график работ, согласуем очерёдность бригад и держим их в этом графике.',
			),
			array(
				'icon'  => 'melo-i-wallet',
				'title' => 'Контроль бюджета и закупок',
				'text'  => 'Проверяем сметы и счета, следим за ценами материалов и не даём смете расползаться незаметно.',
			),
			array(
				'icon'  => 'melo-i-shield-check',
				'title' => 'Приёмка работ по этапам',
				'text'  => 'Каждый этап принимаем по списку и с фотоотчётом. Скрытые работы не закрываются, пока их не проверили.',
			),
			array(
				'icon'  => 'melo-i-key',
				'title' => 'Сопровождение после сдачи',
				'text'  => 'Остаёмся на связи: гарантийные вопросы, доукомплектация и мелкие доработки.',
			),
		),
		'steps'     => array(
			array( 'title' => 'Знакомство с проектом', 'text' => 'Изучаем документацию и смету, находим слабые места до старта работ.' ),
			array( 'title' => 'График и подрядчики', 'text' => 'Собираем график, согласуем очерёдность бригад и зоны ответственности.' ),
			array( 'title' => 'Надзор и приёмка', 'text' => 'Выезжаем на объект, принимаем этапы и ведём отчётность по бюджету.' ),
			array( 'title' => 'Сдача и гарантия', 'text' => 'Передаём объект, собираем документацию и остаёмся на связи после сдачи.' ),
		),
		'seo_title' => 'Авторский надзор и управление проектом — студия MELO',
		'seo_desc'  => 'Сопровождение объекта: авторский надзор, управление подрядчиками и сроками, контроль бюджета, поэтапная приёмка и поддержка после сдачи.',
	),
);

/* =====================================================================
 * Вспомогательное
 * ================================================================== */

/**
 * Вложение медиатеки для файла из папки темы.
 *
 * Помечаем своей метой, чтобы повторный запуск не наплодил копий.
 */
function melo_make_attachment( $file, $alt = '' ) {
	global $dry;

	$found = get_posts( array(
		'post_type'   => 'attachment',
		'post_status' => 'inherit',
		'numberposts' => 1,
		'meta_key'    => '_melo_seed_src',
		'meta_value'  => $file,
		'fields'      => 'ids',
	) );
	if ( $found ) {
		return (int) $found[0];
	}

	$path = get_template_directory() . '/img/melo/' . $file;
	if ( ! file_exists( $path ) ) {
		return 0;
	}
	if ( $dry ) {
		return 'завести ' . $file;
	}

	$bits = wp_upload_bits( $file, null, file_get_contents( $path ) );
	if ( ! empty( $bits['error'] ) ) {
		return 0;
	}

	$type = wp_check_filetype( $bits['file'] );
	$id   = wp_insert_attachment( array(
		'post_mime_type' => $type['type'],
		'post_title'     => $alt ? $alt : pathinfo( $file, PATHINFO_FILENAME ),
		'post_status'    => 'inherit',
	), $bits['file'] );

	if ( is_wp_error( $id ) || ! $id ) {
		return 0;
	}

	wp_update_attachment_metadata( $id, wp_generate_attachment_metadata( $id, $bits['file'] ) );
	update_post_meta( $id, '_melo_seed_src', $file );
	if ( $alt ) {
		update_post_meta( $id, '_wp_attachment_image_alt', $alt );
	}
	return (int) $id;
}

/** Карта «имя поля => ключ» для группы. */
function melo_make_keys( $group_key ) {
	$group = acf_get_field_group( $group_key );
	if ( ! $group ) {
		return array();
	}
	$keys = array();
	foreach ( acf_get_fields( $group ) as $f ) {
		if ( ! empty( $f['name'] ) ) {
			$keys[ $f['name'] ] = $f['key'];
		}
	}
	return $keys;
}

/** Поле пустое? */
function melo_make_empty( $key, $post_id ) {
	$v = get_field( $key, $post_id );
	return ( null === $v || false === $v || '' === $v || array() === $v );
}

/* =====================================================================
 * Работа
 * ================================================================== */

$service_keys = melo_make_keys( 'group_melo_service' );
if ( ! $service_keys ) {
	echo 'Группа полей «Страница услуги» не зарегистрирована — залейте inc/melo-fields.php.' . PHP_EOL;
	exit;
}

$made = array();

foreach ( $melo_pages as $melo_page ) {
	$existing = get_page_by_path( $melo_page['slug'] );
	$pid      = $existing ? $existing->ID : 0;

	echo $melo_page['title'] . '  [' . $melo_page['slug'] . ']' . PHP_EOL;

	if ( ! $pid ) {
		if ( $dry ) {
			echo '    ~ страницы нет, создал бы' . PHP_EOL . PHP_EOL;
			continue;
		}
		$pid = wp_insert_post( array(
			'post_type'    => 'page',
			'post_status'  => 'publish',
			'post_title'   => $melo_page['title'],
			'post_name'    => $melo_page['slug'],
			'post_content' => '',
		) );
		if ( is_wp_error( $pid ) || ! $pid ) {
			echo '    !! не удалось создать' . PHP_EOL . PHP_EOL;
			continue;
		}
		echo '    + создана, id ' . $pid . PHP_EOL;
	} else {
		echo '    = уже есть, id ' . $pid . PHP_EOL;
	}

	$made[ $melo_page['slug'] ] = $pid;

	if ( ! empty( $melo_page['keep'] ) ) {
		echo '    · страница уже наполнена, содержимое не трогаю' . PHP_EOL . PHP_EOL;
		continue;
	}

	/* Шаблон. Страница «Дизайн интерьера» стояла на шаблоне направления —
	   она показывала слайдер всех пяти направлений и в серии из пяти
	   страниц читалась бы как дубль. */
	$now = get_post_meta( $pid, '_wp_page_template', true );
	if ( MELO_TPL !== $now ) {
		if ( $dry ) {
			echo '    ~ шаблон сменил бы: ' . ( $now ? $now : 'по умолчанию' ) . ' -> ' . MELO_TPL . PHP_EOL;
		} else {
			update_post_meta( $pid, '_wp_page_template', MELO_TPL );
			echo '    + шаблон: ' . MELO_TPL . PHP_EOL;
		}
	}

	/* Поля. Пишем по ключу, только пустые — правки заказчика важнее. */
	$values = array(
		'melo_lead'              => isset( $melo_page['lead'] ) ? $melo_page['lead'] : '',
		'melo_service_title'     => 'Что входит в услугу',
		'melo_service_image_alt' => isset( $melo_page['image_alt'] ) ? $melo_page['image_alt'] : '',
		'melo_service_items'     => isset( $melo_page['items'] ) ? $melo_page['items'] : array(),
		'melo_steps_eyebrow'     => 'Процесс',
		'melo_steps_title'       => 'Как мы работаем',
		'melo_works_eyebrow'     => 'Проекты',
		'melo_works_title'       => 'Примеры реализации',
	);
	if ( isset( $melo_page['steps'] ) ) {
		$values['melo_steps'] = $melo_page['steps'];
	}
	if ( isset( $melo_page['image'] ) ) {
		$values['melo_service_image'] = melo_make_attachment( $melo_page['image'], isset( $melo_page['image_alt'] ) ? $melo_page['image_alt'] : '' );
	}

	foreach ( $values as $name => $value ) {
		if ( '' === $value || array() === $value || 0 === $value || ! isset( $service_keys[ $name ] ) ) {
			continue;
		}
		$overwrite = $force || ! empty( $melo_page['reset'] );
		if ( ! melo_make_empty( $service_keys[ $name ], $pid ) && ! $overwrite ) {
			echo '    = ' . $name . ' — уже заполнено' . PHP_EOL;
			continue;
		}
		$note = is_array( $value ) ? count( $value ) . ' строк' : ( is_string( $value ) ? '«' . mb_substr( $value, 0, 44 ) . '»' : $value );
		if ( $dry ) {
			echo '    ~ ' . $name . ' <- ' . $note . PHP_EOL;
		} else {
			update_field( $service_keys[ $name ], $value, $pid );
			echo '    + ' . $name . ' <- ' . $note . PHP_EOL;
		}
	}

	/* Отметка, чтобы хук автозаполнения не полез поверх нашего текста
	   своими значениями по умолчанию при первом же сохранении. */
	if ( ! $dry ) {
		update_post_meta( $pid, '_melo_prefilled', 1 );
	}

	/* Заголовок и описание для поиска. */
	foreach ( array( '_yoast_wpseo_title' => 'seo_title', '_yoast_wpseo_metadesc' => 'seo_desc' ) as $meta => $src ) {
		if ( empty( $melo_page[ $src ] ) ) {
			continue;
		}
		if ( get_post_meta( $pid, $meta, true ) && ! $force ) {
			echo '    = ' . $meta . ' — уже задано' . PHP_EOL;
			continue;
		}
		if ( $dry ) {
			echo '    ~ ' . $meta . ' <- «' . mb_substr( $melo_page[ $src ], 0, 44 ) . '»' . PHP_EOL;
		} else {
			update_post_meta( $pid, $meta, $melo_page[ $src ] );
			echo '    + ' . $meta . PHP_EOL;
		}
	}

	echo PHP_EOL;
}

/* ---------------------------------------------------------------------
 * Ссылки у карточек на главной
 *
 * Карточки идут в том же порядке, что и страницы, поэтому связываем по
 * позиции. Пустая ссылка означала «вести на единственную готовую
 * страницу услуги» — теперь готовы все пять.
 * ------------------------------------------------------------------ */
echo 'Ссылки карточек на главной:' . PHP_EOL;

$home = (int) get_option( 'page_on_front' );
if ( ! $home ) {
	$front = get_page_by_path( 'glavnaya' );
	$home  = $front ? $front->ID : 0;
}

$home_keys = melo_make_keys( 'group_melo_home' );

if ( ! $home || ! isset( $home_keys['melo_cards'] ) ) {
	echo '  !! главная или её поле карточек не найдены' . PHP_EOL;
} else {
	$cards = get_field( $home_keys['melo_cards'], $home );

	/* Порядок берём из списка страниц, а не из того, что удалось создать:
	   иначе пропущенная страница сдвинула бы все ссылки на одну. */
	$order = array();
	foreach ( $melo_pages as $melo_page ) {
		$order[] = $melo_page['slug'];
	}

	if ( ! is_array( $cards ) || ! $cards ) {
		echo '  !! карточки на главной пусты' . PHP_EOL;
	} else {
		$changed = false;
		foreach ( $cards as $i => $card ) {
			/* Поле картинки читается массивом медиафайла, а записывается
			   идентификатором. Не привести обратно — и повторитель уедет
			   в базу вместе с сериализованным массивом, то есть картинки
			   на главной пропадут. */
			if ( isset( $card['img']['ID'] ) ) {
				$cards[ $i ]['img'] = (int) $card['img']['ID'];
			}

			if ( ! isset( $order[ $i ] ) || ! isset( $made[ $order[ $i ] ] ) ) {
				continue;
			}
			if ( ! empty( $card['url'] ) && ! $force ) {
				echo '  = ' . ( $i + 1 ) . ' — ссылка уже задана' . PHP_EOL;
				continue;
			}
			$url                = get_permalink( $made[ $order[ $i ] ] );
			$cards[ $i ]['url'] = $url;
			$changed            = true;
			echo ( $dry ? '  ~ ' : '  + ' ) . ( $i + 1 ) . ' -> ' . $url . PHP_EOL;
		}
		if ( $changed && ! $dry ) {
			update_field( $home_keys['melo_cards'], $cards, $home );
		}
	}
}

echo PHP_EOL . ( $dry ? 'Пробный прогон, ничего не записано.' : 'Готово.' ) . PHP_EOL;
