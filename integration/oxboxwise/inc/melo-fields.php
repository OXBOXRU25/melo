<?php
/**
 * Поля админки для страниц MELO.
 *
 * Подключается из functions.php темы, следом за melo-functions.php:
 *
 *   require get_template_directory() . '/inc/melo-fields.php';
 *
 * Зачем файл. Весь текст наших страниц лежал PHP-массивами внутри
 * шаблонов: заказчик его не видел и править не мог. Здесь эти же данные
 * объявлены полями ACF, а шаблоны читают их через melo_field() и
 * melo_rows() — с прежними массивами в качестве запасного значения.
 * Пустое поле поэтому не обнуляет страницу, а показывает то, что было.
 *
 * Группы регистрируются КОДОМ, а не рисуются в админке. Тогда они едут
 * вместе с пакетом, лежат в git и поднимаются сами на любом сайте, где
 * стоит эта тема, — не нужно повторять полсотни кликов на боевом.
 *
 * Привязка — по шаблону страницы, поэтому поля видны только там, где
 * действительно нужны.
 *
 * @package oxboxwise
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* =====================================================================
 * 1. Чтение значений
 * ================================================================== */

/**
 * Значение поля ACF или запасное.
 *
 * Пустое поле НЕ должно обнулять страницу: заказчик, случайно стерев
 * текст, увидит прежний, а не дыру в вёрстке.
 *
 * @param string $name     Имя поля.
 * @param mixed  $fallback Что вернуть, если поле пустое.
 * @param mixed  $post_id  Запись; по умолчанию текущая.
 * @return mixed
 */
function melo_field( $name, $fallback = '', $post_id = false ) {
	if ( ! function_exists( 'get_field' ) ) {
		return $fallback;
	}
	$value = get_field( $name, $post_id );
	if ( null === $value || false === $value || '' === $value || array() === $value ) {
		return $fallback;
	}
	return $value;
}

/**
 * Строки повторителя или запасной массив.
 *
 * @param string $name     Имя повторителя.
 * @param array  $fallback Что вернуть, если строк нет.
 * @param mixed  $post_id  Запись; по умолчанию текущая.
 * @return array
 */
function melo_rows( $name, $fallback = array(), $post_id = false ) {
	$rows = melo_field( $name, array(), $post_id );
	if ( ! is_array( $rows ) || ! $rows ) {
		return $fallback;
	}
	return $rows;
}

/**
 * Дописать в строки недостающие ключи.
 *
 * У запасного массива ключ есть всегда, а у строки повторителя может не
 * быть вовсе — например, поле добавили позже, чем заполнили страницу.
 * Приводим к одному виду здесь, чтобы разметка не проверяла каждый ключ
 * и не сыпала предупреждениями на боевом.
 *
 * @param array $rows Строки.
 * @param array $keys Ожидаемые ключи.
 * @return array
 */
function melo_fill( $rows, $keys ) {
	if ( ! is_array( $rows ) ) {
		return array();
	}
	foreach ( $rows as $i => $row ) {
		if ( ! is_array( $row ) ) {
			continue;
		}
		foreach ( $keys as $key ) {
			if ( ! isset( $rows[ $i ][ $key ] ) ) {
				$rows[ $i ][ $key ] = '';
			}
		}
	}
	return $rows;
}

/**
 * Строки списка из многострочного поля.
 *
 * Вложенный повторитель в админке — это лишний уровень кнопок ради
 * простого перечня. Здесь пункт = строка, и заказчику понятнее.
 *
 * Разделители собираются через chr(): обратный слеш в этом файле не
 * используется намеренно, он слишком легко теряется при генерации.
 *
 * @param mixed $value    Текст из поля либо готовый массив.
 * @param array $fallback Запасной список.
 * @return array
 */
function melo_lines( $value, $fallback = array() ) {
	if ( is_array( $value ) ) {
		return $value ? $value : $fallback;
	}
	$value = trim( (string) $value );
	if ( '' === $value ) {
		return $fallback;
	}
	$parts = explode( chr( 10 ), str_replace( chr( 13 ), chr( 10 ), $value ) );
	$parts = array_filter( array_map( 'trim', $parts ), 'strlen' );
	return $parts ? array_values( $parts ) : $fallback;
}

/**
 * Адрес картинки.
 *
 * Значение приходит либо из ACF (массив медиафайла или готовый адрес),
 * либо из шаблона именем файла в папке темы. Разбираем оба случая, иначе
 * при переходе на поля пришлось бы править каждую разметку.
 *
 * @param mixed  $value    Поле ACF или имя файла.
 * @param string $fallback Имя файла в img/melo/ на случай пустого поля.
 * @return string
 */
function melo_img_src( $value, $fallback = '' ) {
	if ( is_array( $value ) ) {
		$value = isset( $value['url'] ) ? $value['url'] : '';
	}
	$value = trim( (string) $value );
	if ( '' === $value ) {
		$value = $fallback;
	}
	if ( '' === $value ) {
		return '';
	}
	/* Полный адрес или путь от корня сайта — отдаём как есть; иначе это
	   имя файла из папки темы. */
	if ( 0 === strpos( $value, 'http' ) || 0 === strpos( $value, '/' ) ) {
		return $value;
	}
	return get_template_directory_uri() . '/img/melo/' . $value;
}

/**
 * Описание картинки.
 *
 * Своё поле важнее: заказчик заполняет его рядом с картинкой. Пусто —
 * берём alt из медиатеки, и только потом запасной текст из шаблона.
 *
 * @param mixed  $image    Поле картинки ACF.
 * @param string $own      Значение своего поля alt.
 * @param string $fallback Запасной текст.
 * @return string
 */
function melo_img_alt( $image, $own = '', $fallback = '' ) {
	$own = trim( (string) $own );
	if ( '' !== $own ) {
		return $own;
	}
	if ( is_array( $image ) && ! empty( $image['alt'] ) ) {
		return $image['alt'];
	}
	return $fallback;
}

/* =====================================================================
 * 2. Значения по умолчанию
 *
 * Один источник на всё: шаблоны берут отсюда запасные значения, а
 * скрипт первичного заполнения — то, что кладёт в поля. Держать эти
 * тексты в двух местах нельзя: они разойдутся, и разойдутся незаметно —
 * после заполнения полей запасное значение уже не показывается.
 * ================================================================== */

/**
 * Тексты по умолчанию для группы полей.
 *
 * @param string $group contacts | service | direction | home.
 * @return array
 */
function melo_defaults( $group ) {
	/* Этапы и карточки повторяются на нескольких шаблонах. */
	$steps = array(
		array( 'title' => 'Замер и планировка', 'text' => 'Выезжаем на объект, снимаем размеры и готовим варианты планировочных решений.' ),
		array( 'title' => 'Концепция', 'text' => 'Собираем стилистику, палитру и материалы — по каждому помещению и по фасаду.' ),
		array( 'title' => 'Визуализация', 'text' => 'Показываем фотореалистичный результат до начала работ и правим, пока это бесплатно.' ),
		array( 'title' => 'Чертежи и надзор', 'text' => 'Комплект документации для бригады, комплектация объекта и контроль до сдачи.' ),
	);

	$cards = array(
		array(
			'img'   => 'dir-1.jpg',
			'alt'   => 'Архитектурный макет и рабочие чертежи здания',
			'title' => 'Архитектурное проектирование<br>и планирование',
			'lead'  => 'Сюда входят услуги по:',
			'items' => array(
				'Консультация дизайнера по подбору недвижимости (помощь в выборе правильного «исходника» участка).',
				'Проектирование домов (архитектурный раздел).',
				'Планирование участка и ситуационный план.',
			),
		),
		array(
			'img'   => 'dir-3.jpg',
			'alt'   => 'Фасад современного загородного дома',
			'title' => 'Дизайн интерьера и экстерьера',
			'lead'  => 'Всё, что касается визуального облика и стиля самого здания.',
			'items' => array(
				'Дизайн-проект интерьера дома.',
				'Дизайн экстерьера (фасады, заборы, входные группы).',
			),
		),
		array(
			'img'   => 'dir-2.jpg',
			'alt'   => 'Благоустроенная территория вокруг загородного дома',
			'title' => 'Ландшафт и малые архитектурные<br>формы (МАФ)',
			'lead'  => 'Благоустройство территории вокруг дома.',
			'items' => array(
				'Ландшафтная архитектура (генплан, зонирование).',
				'Проектирование малых сооружений (бани, беседки, навесы, бассейны, зоны отдыха).',
			),
		),
		array(
			'img'   => 'project-6.jpg',
			'alt'   => 'Готовая кухня-гостиная после реализации проекта',
			'title' => 'Реализация и строительство',
			'lead'  => 'Переход от чертежей к физическому воплощению.',
			'items' => array(
				'Строительство и ремонт (реализация объекта под ключ).',
				'Комплектация объектов (подбор мебели, материалов, оборудования).',
			),
		),
		array(
			'img'   => 'project-2.jpg',
			'alt'   => 'Интерьер гостиной, сданной под ключ',
			'title' => 'Сопровождение и управление',
			'lead'  => 'Сервис, который снимает головную боль с заказчика.',
			'items' => array(
				'Управление объектом (комплексное сопровождение всех процессов).',
				'Авторский надзор (обычно идёт в связке с управлением).',
			),
		),
	);

	$all = array(

		'contacts' => array(
			'melo_lead'            => 'Приезжайте в офис, звоните или напишите — обсудим задачу, сориентируем по срокам и бюджету. На письма отвечаем в течение рабочего дня.',
			'melo_hero_btn'        => 'Написать нам',
			'melo_ways_eyebrow'    => 'Связь',
			'melo_ways_title'      => 'Как с нами связаться',
			'melo_messengers_text' => 'Пишите в мессенджеры, если так удобнее:',
			/* Телефон, почта и адрес намеренно пустые: они живут в общих
			   настройках сайта, и шаблон подставляет их сам. Продублируй
			   их здесь — у заказчика появятся две копии одного контакта,
			   и вторую он однажды забудет поправить. */
			'melo_ways'            => array(
				array( 'icon' => 'melo-i-phone', 'role' => 'Телефон', 'value' => '', 'href' => '', 'note' => 'Пн–Пт, 10:00–19:00' ),
				array( 'icon' => 'melo-i-mail', 'role' => 'Почта', 'value' => '', 'href' => '', 'note' => 'Отвечаем в течение рабочего дня' ),
				array( 'icon' => 'melo-i-pin', 'role' => 'Офис', 'value' => '', 'href' => '', 'note' => '2 этаж, вход со стороны сквера' ),
				array( 'icon' => 'melo-i-clock', 'role' => 'Часы работы', 'value' => 'Пн–Пт 10:00–19:00', 'href' => '', 'note' => 'Сб и Вс — по договорённости' ),
			),
		),

		'service' => array(
			'melo_lead'              => 'Проектируем дом от посадки на участке до рабочих чертежей. Помогаем выбрать правильный «исходник» — участок, на котором проект получится.',
			'melo_service_title'     => 'Что входит в услугу',
			'melo_service_image'     => 'dir-1.jpg',
			'melo_service_image_alt' => 'Архитектурный макет дома и рабочие чертежи',
			'melo_service_items'     => array(
				array(
					'icon'  => 'melo-i-plot-search',
					'title' => 'Консультация по подбору недвижимости',
					'text'  => 'Помогаем выбрать правильный «исходник» — участок, на котором проект вообще получится: рельеф, подъезды, ориентация по сторонам света и ограничения застройки.',
				),
				array(
					'icon'  => 'melo-i-house-plan',
					'title' => 'Проектирование домов',
					'text'  => 'Архитектурный раздел целиком: планировки этажей, фасады, разрезы, узлы и посадка здания на участке.',
				),
				array(
					'icon'  => 'melo-i-site-plan',
					'title' => 'Планирование участка и ситуационный план',
					'text'  => 'Генплан участка: расположение дома и построек, подъезды, дорожки, зоны и трассировка инженерных сетей.',
				),
				array(
					'icon'  => 'melo-i-structure',
					'title' => 'Конструктивные решения',
					'text'  => 'Фундамент, несущие стены, перекрытия и кровля с расчётом нагрузок — чтобы дом простоял столько, сколько нарисован.',
				),
				array(
					'icon'  => 'melo-i-permit',
					'title' => 'Разрешительная документация',
					'text'  => 'Уведомление о планируемом строительстве, ГПЗУ и согласования — собираем комплект и ведём его до положительного ответа.',
				),
			),
			'melo_steps_eyebrow'     => 'Процесс',
			'melo_steps_title'       => 'Как мы работаем',
			'melo_steps'             => $steps,
			'melo_works_eyebrow'     => 'Проекты',
			'melo_works_title'       => 'Примеры реализации',
		),

		'direction' => array(
			'melo_lead'          => 'Создаём цельный образ дома — от планировки комнат до фасада и входной группы. Считаем каждый метр, каждый материал и каждый рубль бюджета.',
			'melo_cards_title'   => 'Направления деятельности',
			'melo_cards'         => $cards,
			'melo_steps_eyebrow' => 'Процесс',
			'melo_steps_title'   => 'Как мы работаем',
			'melo_steps'         => $steps,
			'melo_works_eyebrow' => 'Проекты',
			'melo_works_title'   => 'Примеры реализации',
		),

		'home' => array(
			'melo_cards_title' => 'Направления деятельности',
			'melo_cards'       => $cards,
		),
	);

	return isset( $all[ $group ] ) ? $all[ $group ] : array();
}

/**
 * Какая группа полей отвечает за какой шаблон страницы.
 *
 * @return array Шаблон => array( ключ melo_defaults(), ключ группы ACF ).
 */
function melo_template_groups() {
	return array(
		'templates/template-melo-contacts.php'     => array( 'contacts', 'group_melo_contacts' ),
		'templates/template-melo-service.php'      => array( 'service', 'group_melo_service' ),
		'templates/template-melo-direction.php'    => array( 'direction', 'group_melo_direction' ),
		'templates/template-mainpage-updated.php'  => array( 'home', 'group_melo_home' ),
	);
}

/**
 * Заполнить пустые поля страницы значениями по умолчанию.
 *
 * Нужно в двух местах: разовым скриптом при переносе и хуком при создании
 * страницы из админки. Поэтому логика здесь, а не в скрипте: разойтись
 * этим двум дорогам нельзя.
 *
 * Пишем по КЛЮЧУ поля, а не по имени: имя melo_lead есть в трёх группах
 * с разными ключами, и запись по имени легла бы не туда — молча.
 *
 * @param int      $post_id    Страница.
 * @param string   $group      Ключ melo_defaults().
 * @param string   $group_key  Ключ группы ACF.
 * @param bool     $force      Перезаписывать заполненные поля.
 * @param callable $image_cb   Как превратить имя файла в id вложения.
 *                             Не задан — поля с картинками пропускаются,
 *                             и шаблон подставит файл из папки темы.
 * @return array Строки отчёта.
 */
function melo_seed_page( $post_id, $group, $group_key, $force = false, $image_cb = null ) {
	$log = array();

	if ( ! function_exists( 'acf_get_field_group' ) ) {
		return array( 'ACF не найден' );
	}

	$acf_group = acf_get_field_group( $group_key );
	if ( ! $acf_group ) {
		return array( 'группа ' . $group_key . ' не зарегистрирована' );
	}

	$keys = array();
	foreach ( acf_get_fields( $acf_group ) as $f ) {
		if ( ! empty( $f['name'] ) ) {
			$keys[ $f['name'] ] = $f['key'];
		}
	}

	/* Поля, чьё значение — вложение медиатеки, а не текст. */
	$images = array( 'melo_service_image', 'img' );

	foreach ( melo_defaults( $group ) as $name => $value ) {
		if ( ! isset( $keys[ $name ] ) ) {
			continue;
		}

		$current = get_field( $keys[ $name ], $post_id );
		$filled  = ! ( null === $current || false === $current || '' === $current || array() === $current );
		if ( $filled && ! $force ) {
			continue;
		}

		if ( is_array( $value ) && isset( $value[0] ) && is_array( $value[0] ) ) {
			$rows = array();
			foreach ( $value as $row ) {
				$out = array();
				$alt = isset( $row['alt'] ) ? $row['alt'] : '';
				foreach ( $row as $sub => $sub_value ) {
					if ( in_array( $sub, $images, true ) ) {
						if ( ! is_callable( $image_cb ) ) {
							continue;
						}
						$out[ $sub ] = call_user_func( $image_cb, $sub_value, $alt );
						continue;
					}
					$out[ $sub ] = ( 'items' === $sub && is_array( $sub_value ) )
						? implode( chr( 10 ), $sub_value )
						: $sub_value;
				}
				$rows[] = $out;
			}
			$prepared = $rows;
			$note     = count( $rows ) . ' строк';
		} elseif ( in_array( $name, $images, true ) ) {
			if ( ! is_callable( $image_cb ) ) {
				continue;
			}
			$prepared = call_user_func( $image_cb, $value, '' );
			$note     = $prepared;
		} else {
			$prepared = $value;
			$note     = is_string( $value ) ? '«' . mb_substr( $value, 0, 48 ) . '»' : $value;
		}

		if ( '' === $prepared || array() === $prepared || 0 === $prepared ) {
			continue;
		}

		update_field( $keys[ $name ], $prepared, $post_id );
		$log[] = $name . ' <- ' . $note;
	}

	return $log;
}

/**
 * Новая страница на шаблоне MELO приходит с готовым содержимым.
 *
 * Иначе заказчик, создав страницу и выбрав шаблон, получает пустые формы
 * и не понимает, что именно туда класть. Заполняем ОДИН раз, отмечая
 * страницу метой: дальше это его текст, и переписывать его мы не вправе.
 *
 * Поля с картинками не трогаем — шаблон подставит файл из папки темы, а
 * настоящую фотографию заказчик выберет сам.
 */
/* Приоритет 20 — строго после ACF: она пишет присланные значения на
   save_post с приоритетом 10, и заполни мы поля раньше, она бы тут же
   затёрла их пустыми. Не acf/save_post: тот хук срабатывает только когда
   в запросе есть поля, а страницу можно создать и не трогая их. */
add_action( 'save_post', 'melo_prefill_new_page', 20 );

function melo_prefill_new_page( $post_id ) {
	if ( ! is_numeric( $post_id ) || 'page' !== get_post_type( $post_id ) ) {
		return;
	}
	if ( wp_is_post_autosave( $post_id ) || wp_is_post_revision( $post_id ) ) {
		return;
	}
	/* Пустая заготовка, которую WordPress заводит при нажатии «Добавить»:
	   шаблон там ещё не выбран, заполнять нечего. */
	if ( 'auto-draft' === get_post_status( $post_id ) ) {
		return;
	}
	if ( get_post_meta( $post_id, '_melo_prefilled', true ) ) {
		return;
	}

	$groups = melo_template_groups();
	$tpl    = get_post_meta( $post_id, '_wp_page_template', true );
	if ( ! isset( $groups[ $tpl ] ) ) {
		return;
	}

	melo_seed_page( $post_id, $groups[ $tpl ][0], $groups[ $tpl ][1] );
	update_post_meta( $post_id, '_melo_prefilled', 1 );
}

/* =====================================================================
 * 3. Заготовки групп полей
 *
 * Блоки повторяются на нескольких шаблонах, поэтому объявлены функциями:
 * одно описание — одна правка. Ключи разводятся префиксом, они обязаны
 * быть уникальными на весь сайт.
 * ================================================================== */

/** Вкладка. */
function melo_f_tab( $p, $slug, $label ) {
	return array(
		'key'       => 'field_' . $p . '_tab_' . $slug,
		'label'     => $label,
		'name'      => '',
		'type'      => 'tab',
		'placement' => 'top',
		'endpoint'  => 0,
	);
}

/** Лид первого экрана. */
function melo_f_lead( $p, $placeholder ) {
	return array(
		'key'          => 'field_' . $p . '_lead',
		'label'        => 'Текст под заголовком',
		'name'         => 'melo_lead',
		'type'         => 'textarea',
		'rows'         => 3,
		'new_lines'    => '',
		'placeholder'  => $placeholder,
		'instructions' => 'Абзац на первом экране. Пусто — останется текст, заданный в шаблоне.',
	);
}

/** Пара «надпись + заголовок» для раздела. */
function melo_f_head( $p, $slug, $eyebrow, $title ) {
	return array(
		array(
			'key'         => 'field_' . $p . '_' . $slug . '_eyebrow',
			'label'       => 'Надпись над заголовком',
			'name'        => 'melo_' . $slug . '_eyebrow',
			'type'        => 'text',
			'placeholder' => $eyebrow,
			'wrapper'     => array( 'width' => '30' ),
		),
		array(
			'key'         => 'field_' . $p . '_' . $slug . '_title',
			'label'       => 'Заголовок раздела',
			'name'        => 'melo_' . $slug . '_title',
			'type'        => 'text',
			'placeholder' => $title,
			'wrapper'     => array( 'width' => '70' ),
		),
	);
}

/** Повторитель «Как мы работаем». */
function melo_f_steps( $p ) {
	return array(
		'key'          => 'field_' . $p . '_steps',
		'label'        => 'Этапы',
		'name'         => 'melo_steps',
		'type'         => 'repeater',
		'layout'       => 'block',
		'button_label' => 'Добавить этап',
		'instructions' => 'Нумерация проставляется сама, писать её в заголовке не нужно.',
		'sub_fields'   => array(
			array(
				'key'     => 'field_' . $p . '_step_title',
				'label'   => 'Название этапа',
				'name'    => 'title',
				'type'    => 'text',
				'wrapper' => array( 'width' => '35' ),
			),
			array(
				'key'     => 'field_' . $p . '_step_text',
				'label'   => 'Описание',
				'name'    => 'text',
				'type'    => 'textarea',
				'rows'    => 2,
				'wrapper' => array( 'width' => '65' ),
			),
		),
	);
}

/** Повторитель карточек направлений. */
function melo_f_cards( $p, $with_url = false ) {
	$sub = array(
		array(
			'key'           => 'field_' . $p . '_card_img',
			'label'         => 'Фотография',
			'name'          => 'img',
			'type'          => 'image',
			'return_format' => 'array',
			'preview_size'  => 'medium',
			'library'       => 'all',
			'wrapper'       => array( 'width' => '30' ),
		),
		array(
			'key'          => 'field_' . $p . '_card_alt',
			'label'        => 'Описание фотографии',
			'name'         => 'alt',
			'type'         => 'text',
			'instructions' => 'Для поиска и озвучивания. Пусто — возьмётся из медиатеки.',
			'wrapper'      => array( 'width' => '70' ),
		),
		array(
			'key'          => 'field_' . $p . '_card_title',
			'label'        => 'Заголовок карточки',
			'name'         => 'title',
			'type'         => 'text',
			'instructions' => 'Разрешён только тег переноса строки.',
		),
		array(
			'key'   => 'field_' . $p . '_card_lead',
			'label' => 'Вводная строка',
			'name'  => 'lead',
			'type'  => 'text',
		),
		array(
			'key'          => 'field_' . $p . '_card_items',
			'label'        => 'Пункты списка',
			'name'         => 'items',
			'type'         => 'textarea',
			'rows'         => 4,
			'new_lines'    => '',
			'instructions' => 'По одному пункту в строке.',
		),
	);

	if ( $with_url ) {
		$sub[] = array(
			'key'          => 'field_' . $p . '_card_url',
			'label'        => 'Ссылка',
			'name'         => 'url',
			'type'         => 'text',
			'instructions' => 'Адрес страницы направления. Пусто — карточка ведёт на страницу по умолчанию.',
		);
	}

	return array(
		'key'          => 'field_' . $p . '_cards',
		'label'        => 'Карточки',
		'name'         => 'melo_cards',
		'type'         => 'repeater',
		'layout'       => 'block',
		'button_label' => 'Добавить карточку',
		'sub_fields'   => $sub,
	);
}

/**
 * Значки, доступные пунктам услуги.
 *
 * Ключ — id символа из template-parts/melo-icons.php, значение — то, что
 * видит заказчик. Добавил значок в спрайт — впиши сюда, иначе выбрать его
 * будет нельзя.
 *
 * @return array
 */
function melo_icon_choices() {
	return array(
		'melo-i-plot-search'  => 'Поиск участка',
		'melo-i-house-plan'   => 'План дома',
		'melo-i-site-plan'    => 'План участка',
		'melo-i-structure'    => 'Конструкции',
		'melo-i-permit'       => 'Документы',
		'melo-i-ruler'        => 'Замер',
		'melo-i-palette'      => 'Палитра и материалы',
		'melo-i-view'         => 'Визуализация',
		'melo-i-facade'       => 'Фасад',
		'melo-i-sofa'         => 'Мебель и комплектация',
		'melo-i-tree'         => 'Озеленение',
		'melo-i-gazebo'       => 'Беседка и малые формы',
		'melo-i-lamp'         => 'Освещение',
		'melo-i-roller'       => 'Отделка',
		'melo-i-wallet'       => 'Смета и бюджет',
		'melo-i-calendar'     => 'Сроки и график',
		'melo-i-shield-check' => 'Контроль и приёмка',
		'melo-i-key'          => 'Сдача под ключ',
	);
}

/** Выбор иконки из спрайта. */
function melo_f_icon( $key, $choices ) {
	return array(
		'key'           => $key,
		'label'         => 'Иконка',
		'name'          => 'icon',
		'type'          => 'select',
		'choices'       => $choices,
		'allow_null'    => 0,
		'ui'            => 0,
		'return_format' => 'value',
		'instructions'  => 'Рисунки заданы в коде — свой файл сюда не подставить.',
		'wrapper'       => array( 'width' => '25' ),
	);
}

/* =====================================================================
 * 4. Регистрация
 * ================================================================== */

add_action( 'acf/init', 'melo_register_fields' );

function melo_register_fields() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	/* Общие настройки группы: у всех одинаковые, поэтому одним массивом. */
	$base = array(
		'menu_order'            => 0,
		'position'              => 'normal',
		'style'                 => 'default',
		'label_placement'       => 'top',
		'instruction_placement' => 'label',
		'active'                => true,
	);

	/**
	 * Правило привязки к шаблону.
	 *
	 * WordPress хранит путь от корня темы, то есть с папкой templates/.
	 * Второе правило — на случай темы, где шаблоны лежат в корне: лишним
	 * не будет, а переносить пакет проще.
	 */
	$where = function ( $file ) {
		return array(
			array(
				array(
					'param'    => 'page_template',
					'operator' => '==',
					'value'    => 'templates/' . $file,
				),
			),
			array(
				array(
					'param'    => 'page_template',
					'operator' => '==',
					'value'    => $file,
				),
			),
		);
	};

	$ways_head = melo_f_head( 'mc', 'ways', 'Связь', 'Как с нами связаться' );

	/* ---------------- Контакты ---------------- */
	acf_add_local_field_group( array_merge( $base, array(
		'key'         => 'group_melo_contacts',
		'title'       => 'Страница контактов',
		'description' => 'Содержимое страницы. Текст из редактора на этом шаблоне не выводится.',
		'location'    => $where( 'template-melo-contacts.php' ),
		'fields'      => array(
			melo_f_tab( 'mc', 'hero', 'Первый экран' ),
			melo_f_lead( 'mc', 'Приезжайте в офис, звоните или напишите...' ),
			array(
				'key'         => 'field_mc_btn',
				'label'       => 'Надпись на кнопке',
				'name'        => 'melo_hero_btn',
				'type'        => 'text',
				'placeholder' => 'Написать нам',
			),

			melo_f_tab( 'mc', 'ways', 'Способы связи' ),
			$ways_head[0],
			$ways_head[1],
			array(
				'key'          => 'field_mc_ways',
				'label'        => 'Способы связи',
				'name'         => 'melo_ways',
				'type'         => 'repeater',
				'layout'       => 'block',
				'button_label' => 'Добавить способ',
				'sub_fields'   => array(
					melo_f_icon( 'field_mc_way_icon', array(
						'melo-i-phone' => 'Телефон',
						'melo-i-mail'  => 'Конверт',
						'melo-i-pin'   => 'Метка на карте',
						'melo-i-clock' => 'Часы',
					) ),
					array(
						'key'     => 'field_mc_way_role',
						'label'   => 'Подпись',
						'name'    => 'role',
						'type'    => 'text',
						'wrapper' => array( 'width' => '35' ),
					),
					array(
						'key'          => 'field_mc_way_value',
						'label'        => 'Значение',
						'name'         => 'value',
						'type'         => 'text',
						'instructions' => 'Пусто — подставится из общих настроек сайта.',
						'wrapper'      => array( 'width' => '40' ),
					),
					array(
						'key'          => 'field_mc_way_href',
						'label'        => 'Ссылка',
						'name'         => 'href',
						'type'         => 'text',
						'instructions' => 'Например tel:+74951234567 или mailto:mail@site.ru. Пусто — значение будет просто текстом.',
						'wrapper'      => array( 'width' => '50' ),
					),
					array(
						'key'     => 'field_mc_way_note',
						'label'   => 'Приписка',
						'name'    => 'note',
						'type'    => 'text',
						'wrapper' => array( 'width' => '50' ),
					),
				),
			),
			array(
				'key'          => 'field_mc_msg_text',
				'label'        => 'Строка про мессенджеры',
				'name'         => 'melo_messengers_text',
				'type'         => 'text',
				'placeholder'  => 'Пишите в мессенджеры, если так удобнее:',
				'instructions' => 'Сами адреса Telegram и MAX задаются в «Общих настройках».',
			),
		),
	) ) );

	/* ---------------- Страница услуги ---------------- */
	acf_add_local_field_group( array_merge( $base, array(
		'key'      => 'group_melo_service',
		'title'    => 'Страница услуги',
		'location' => $where( 'template-melo-service.php' ),
		'fields'   => array_merge(
			array(
				melo_f_tab( 'ms', 'hero', 'Первый экран' ),
				melo_f_lead( 'ms', 'Проектируем дом от посадки на участке до рабочих чертежей...' ),

				melo_f_tab( 'ms', 'what', 'Что входит в услугу' ),
				array(
					'key'         => 'field_ms_title',
					'label'       => 'Заголовок раздела',
					'name'        => 'melo_service_title',
					'type'        => 'text',
					'placeholder' => 'Что входит в услугу',
				),
				array(
					'key'           => 'field_ms_image',
					'label'         => 'Фотография раздела',
					'name'          => 'melo_service_image',
					'type'          => 'image',
					'return_format' => 'array',
					'preview_size'  => 'medium',
					'library'       => 'all',
					'wrapper'       => array( 'width' => '30' ),
				),
				array(
					'key'          => 'field_ms_image_alt',
					'label'        => 'Описание фотографии',
					'name'         => 'melo_service_image_alt',
					'type'         => 'text',
					'instructions' => 'Пусто — возьмётся из медиатеки.',
					'wrapper'      => array( 'width' => '70' ),
				),
				array(
					'key'          => 'field_ms_items',
					'label'        => 'Пункты',
					'name'         => 'melo_service_items',
					'type'         => 'repeater',
					'layout'       => 'block',
					'button_label' => 'Добавить пункт',
					'sub_fields'   => array(
						melo_f_icon( 'field_ms_item_icon', melo_icon_choices() ),
						array(
							'key'     => 'field_ms_item_title',
							'label'   => 'Заголовок',
							'name'    => 'title',
							'type'    => 'text',
							'wrapper' => array( 'width' => '75' ),
						),
						array(
							'key'   => 'field_ms_item_text',
							'label' => 'Описание',
							'name'  => 'text',
							'type'  => 'textarea',
							'rows'  => 2,
						),
					),
				),

				melo_f_tab( 'ms', 'steps', 'Как мы работаем' ),
			),
			melo_f_head( 'ms', 'steps', 'Процесс', 'Как мы работаем' ),
			array( melo_f_steps( 'ms' ) ),
			array( melo_f_tab( 'ms', 'works', 'Примеры реализации' ) ),
			melo_f_head( 'ms', 'works', 'Проекты', 'Примеры реализации' )
		),
	) ) );

	/* ---------------- Страница направления ---------------- */
	acf_add_local_field_group( array_merge( $base, array(
		'key'      => 'group_melo_direction',
		'title'    => 'Страница направления',
		'location' => $where( 'template-melo-direction.php' ),
		'fields'   => array_merge(
			array(
				melo_f_tab( 'md', 'hero', 'Первый экран' ),
				melo_f_lead( 'md', 'Создаём цельный образ дома — от планировки комнат до фасада...' ),

				melo_f_tab( 'md', 'cards', 'Направления' ),
				array(
					'key'         => 'field_md_cards_title',
					'label'       => 'Заголовок раздела',
					'name'        => 'melo_cards_title',
					'type'        => 'text',
					'placeholder' => 'Направления деятельности',
				),
				melo_f_cards( 'md' ),

				melo_f_tab( 'md', 'steps', 'Как мы работаем' ),
			),
			melo_f_head( 'md', 'steps', 'Процесс', 'Как мы работаем' ),
			array( melo_f_steps( 'md' ) ),
			array( melo_f_tab( 'md', 'works', 'Примеры реализации' ) ),
			melo_f_head( 'md', 'works', 'Проекты', 'Примеры реализации' )
		),
	) ) );

	/* ---------------- Главная ---------------- */
	acf_add_local_field_group( array_merge( $base, array(
		'key'         => 'group_melo_home',
		'title'       => 'Направления деятельности (слайдер)',
		'description' => 'Блок под первым экраном главной страницы.',
		'location'    => $where( 'template-mainpage-updated.php' ),
		'fields'      => array(
			array(
				'key'         => 'field_mh_cards_title',
				'label'       => 'Заголовок раздела',
				'name'        => 'melo_cards_title',
				'type'        => 'text',
				'placeholder' => 'Направления деятельности',
			),
			melo_f_cards( 'mh', true ),
		),
	) ) );
}
