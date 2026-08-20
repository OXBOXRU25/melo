<?php
/**
 * Template Name: MELO — контакты
 * Template Post Type: page
 *
 * Страница контактов в стилистике MELO: интро, способы связи, офис,
 * форма и реквизиты.
 *
 * Данные пока тестовые и лежат массивами ниже — правятся здесь либо
 * переносятся в ACF, разметка вывода при этом не меняется. Телефон,
 * почта и адрес берутся из melo_contact(), чтобы совпадать с шапкой и
 * подвалом: одно значение на весь сайт, а не три копии.
 *
 * Содержимое страницы из редактора здесь НЕ выводится намеренно: в базе
 * у /contacts/ лежит служебный текст политики обработки данных, на
 * странице контактов он не к месту. Понадобится редактируемый текст —
 * заводим поле ACF, а не возвращаем the_content().
 *
 * @package oxboxwise
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

melo_enqueue_page();
get_header();

while ( have_posts() ) :
	the_post();

	$melo_dir = get_template_directory_uri() . '/img/melo/';

	$hero_bg = get_the_post_thumbnail_url( get_the_ID(), 'full' );
	if ( ! $hero_bg ) {
		$hero_bg = $melo_dir . 'project-3.jpg';
	}

	$lead = 'Приезжайте в офис, звоните или напишите — обсудим задачу, сориентируем по срокам и бюджету. На письма отвечаем в течение рабочего дня.';

	/* ---------------------------------------------------------------
	 * Способы связи. icon — id символа из спрайта ниже.
	 * ------------------------------------------------------------- */
	$ways = array(
		array(
			'icon'  => 'melo-i-phone',
			'role'  => 'Телефон',
			'value' => melo_contact( 'phone' ),
			'href'  => 'tel:' . melo_contact( 'phone_raw' ),
			'note'  => 'Пн–Пт, 10:00–19:00',
		),
		array(
			'icon'  => 'melo-i-mail',
			'role'  => 'Почта',
			'value' => melo_contact( 'email' ),
			'href'  => 'mailto:' . melo_contact( 'email' ),
			'note'  => 'Отвечаем в течение рабочего дня',
		),
		array(
			'icon'  => 'melo-i-pin',
			'role'  => 'Офис',
			'value' => melo_contact( 'address' ),
			'href'  => '',
			'note'  => '2 этаж, вход со стороны сквера',
		),
		array(
			'icon'  => 'melo-i-clock',
			'role'  => 'Часы работы',
			'value' => 'Пн–Пт 10:00–19:00',
			'href'  => '',
			'note'  => 'Сб и Вс — по договорённости',
		),
	);

	/* Мессенджеры отдельной строкой: у них своя логика — это не «ещё
	   один контакт», а альтернативный канал того же разговора. */
	$messengers = array(
		array( 'label' => 'Telegram', 'href' => melo_contact( 'telegram' ) ),
		array( 'label' => 'MAX',      'href' => melo_contact( 'max' ) ),
	);

	/* ---------------------------------------------------------------
	 * Как добраться
	 * ------------------------------------------------------------- */
	$route_title = 'Как добраться';
	$route_items = array(
		'От метро «Кремлёвская» — 7 минут пешком через сквер.',
		'Парковка для гостей во дворе, места отмечены табличкой.',
		'Вход со стороны сквера, домофон 12, второй этаж направо.',
		'Приезд лучше согласовать заранее — так встретим на входе.',
	);

	/* ---------------------------------------------------------------
	 * Реквизиты. Значения тестовые: нули на месте цифр оставлены
	 * намеренно, чтобы страницу нельзя было выпустить, не заметив.
	 * ------------------------------------------------------------- */
	$specs_title = 'Реквизиты';
	$specs       = array(
		array( 'Полное наименование', 'Общество с ограниченной ответственностью «МЕЛО»' ),
		array( 'ИНН / КПП', '1600000000 / 160001001' ),
		array( 'ОГРН', '1230000000000' ),
		array( 'Юридический адрес', '420000, Республика Татарстан, г. Казань, ул. Ленина, д. 00, офис 000' ),
		array( 'Расчётный счёт', '40702810000000000000' ),
		array( 'Директор', 'Фамилия Имя Отчество' ),
	);
	?>

<div class="melo-page melo-contacts-page">

	<?php /* Спрайт иконок: symbol вместо отдельных файлов — цвет
	         наследуется от текста, запросов ноль. */ ?>
	<svg class="melo-visually-hidden" aria-hidden="true" focusable="false">
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
		<symbol id="melo-i-arrow" viewBox="0 0 16 16" fill="none">
			<path d="M8 0 6.7 1.3 12.48 7.08H0v1.84h12.48L6.7 14.7 8 16l8-8z" fill="currentColor"/>
		</symbol>
	</svg>

	<!-- ============ 1. Интро ============ -->
	<section class="melo-hero">
		<img class="melo-hero__bg" src="<?php echo esc_url( $hero_bg ); ?>" alt="" width="1920" height="1069">
		<div class="melo-hero__veil"></div>

		<div class="melo-container melo-hero__inner">
			<?php if ( function_exists( 'bcn_display_list' ) ) : ?>
			<nav class="melo-hero__crumbs" aria-label="Хлебные крошки">
				<ul class="melo-breadcrumbs">
					<?php bcn_display_list(); ?>
				</ul>
			</nav>
			<?php endif; ?>

			<h1><?php the_title(); ?></h1>

			<p class="melo-hero__lead"><?php echo esc_html( $lead ); ?></p>

			<a class="melo-btn melo-btn--gold" href="#melo-form">Написать нам</a>
		</div>
	</section>

	<!-- ============ 2. Способы связи ============ -->
	<section class="melo-section melo-section--gray" id="melo-ways">
		<div class="melo-container">
			<div class="melo-section__head melo-section__head--center" data-reveal>
				<span class="melo-eyebrow">Связь</span>
				<h2>Как с нами связаться</h2>
			</div>

			<ul class="melo-contact-grid">
				<?php foreach ( $ways as $way ) : ?>
					<li class="melo-contact" data-reveal>
						<span class="melo-contact__icon" aria-hidden="true">
							<svg viewBox="0 0 24 24"><use href="#<?php echo esc_attr( $way['icon'] ); ?>"></use></svg>
						</span>
						<p class="melo-contact__role"><?php echo esc_html( $way['role'] ); ?></p>
						<?php if ( $way['href'] ) : ?>
							<a class="melo-contact__value ox-selectable" href="<?php echo esc_url( $way['href'] ); ?>"><?php echo esc_html( $way['value'] ); ?></a>
						<?php else : ?>
							<p class="melo-contact__value ox-selectable"><?php echo esc_html( $way['value'] ); ?></p>
						<?php endif; ?>
						<p class="melo-contact__note"><?php echo esc_html( $way['note'] ); ?></p>
					</li>
				<?php endforeach; ?>
			</ul>

			<p class="melo-contact-more" data-reveal>
				Пишите в мессенджеры, если так удобнее:
				<?php
				foreach ( $messengers as $i => $m ) :
					$tail = $i < count( $messengers ) - 1 ? ',' : '.';
					/* Пока адрес не заполнен, ссылки быть не должно: голая
					   решётка уводит страницу наверх, и это читается как
					   поломка. Ставим текст — заполнится, станет ссылкой. */
					if ( '' === $m['href'] || '#' === $m['href'] ) :
						?>
						<span><?php echo esc_html( $m['label'] ); ?></span><?php echo esc_html( $tail ); ?>
					<?php else : ?>
						<a href="<?php echo esc_url( $m['href'] ); ?>" target="_blank" rel="noopener"><?php echo esc_html( $m['label'] ); ?></a><?php echo esc_html( $tail ); ?>
					<?php
					endif;
				endforeach;
				?>
			</p>
		</div>
	</section>

	<!-- ============ 3. Офис ============ -->
	<section class="melo-section melo-section--white" id="melo-office">
		<div class="melo-container">
			<div class="melo-section__head melo-section__head--center" data-reveal>
				<span class="melo-eyebrow">Офис</span>
				<h2><?php echo esc_html( $route_title ); ?></h2>
			</div>

			<div class="melo-office">
				<?php
				/* Карта — заглушка. Встраиваемая карта тянет сторонний скрипт
				   и точку на местности, которой пока нет: адрес тестовый.
				   Когда появится настоящий — сюда встаёт iframe Яндекс.Карт,
				   разметка вокруг не меняется. */
				?>
				<div class="melo-map" data-reveal>
					<div class="melo-map__grid" aria-hidden="true"></div>
					<div class="melo-map__pin" aria-hidden="true">
						<svg viewBox="0 0 24 24"><use href="#melo-i-pin"></use></svg>
					</div>
					<div class="melo-map__card">
						<p class="melo-map__role">Адрес</p>
						<p class="melo-map__value ox-selectable"><?php echo esc_html( melo_contact( 'address' ) ); ?></p>
						<a class="melo-map__link" href="https://yandex.ru/maps/" target="_blank" rel="noopener">
							Открыть в Яндекс.Картах
							<svg class="melo-btn__arrow" aria-hidden="true"><use href="#melo-i-arrow"></use></svg>
						</a>
					</div>
				</div>

				<div class="melo-office__side">
					<ul class="melo-route" data-reveal>
						<?php foreach ( $route_items as $item ) : ?>
							<li><?php echo esc_html( $item ); ?></li>
						<?php endforeach; ?>
					</ul>

					<p class="melo-office__hint" data-reveal>
						Не нашли нужного? Позвоните <a class="ox-selectable" href="tel:<?php echo esc_attr( melo_contact( 'phone_raw' ) ); ?>"><?php echo esc_html( melo_contact( 'phone' ) ); ?></a> —
						подскажем, как доехать.
					</p>
				</div>
			</div>
		</div>
	</section>

	<!-- ============ 4. Форма ============ -->
	<section class="melo-section melo-section--gray" id="melo-form">
		<div class="melo-container">
			<div class="melo-section__head melo-section__head--center" data-reveal>
				<span class="melo-eyebrow">Заявка</span>
				<h2>Написать нам</h2>
			</div>

			<div class="melo-form-card" data-reveal>
				<?php
				/* Разметка формы — темы: её же валидация (js/validation.js
				   цепляется к form[data-controller]) и её же отправка
				   (action=sendform). Отличия только в обёртке и в id: он
				   обязан быть уникальным, форма в модалке зовётся os. */
				?>
				<form action="" class="form melo-form" data-controller id="melo-contacts-form">
					<p class="melo-form__note">
						Оставьте телефон — перезвоним в рабочие часы и обсудим задачу.
					</p>

					<fieldset class="form__col form__col_1">
						<legend>Поля формы</legend>

						<div class="input textarea input-wrapper required form__label melo-field melo-field_req">
							<label for="">
								<input type="text" class="focus-input input-field" required name="name" placeholder="Имя">
							</label>
							<span class="melo-req" aria-hidden="true">*</span>
						</div>

						<div class="input textarea input-wrapper required form__label melo-field melo-field_req">
							<label for="">
								<input type="tel" class="focus-input input-field" required name="phone" placeholder="Телефон">
							</label>
							<span class="melo-req" aria-hidden="true">*</span>
						</div>

						<div class="input textarea input-wrapper form__label melo-field">
							<label for="">
								<input type="email" class="focus-input input-field" name="email" placeholder="Email">
							</label>
						</div>

						<div class="input textarea input-wrapper form__label melo-field melo-field_area">
							<label for="">
								<textarea class="focus-input input-field" name="comment" rows="3" placeholder="Комментарий"></textarea>
							</label>
						</div>

						<button class="btn form__btn btn_animate" type="submit">
							<div class="btn_animate-box">
								<span>Отправить</span>
								<span>Отправить</span>
							</div>
							<div class="btn_animate-box-icon">
								<div class="btn_icon">
									<svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
										<path d="M8 -3.49691e-07L6.70082 1.29918L12.4828 7.0812L3.52868e-07 7.0812L2.72544e-07 8.9188L12.4828 8.9188L6.70081 14.7008L8 16L16 8L8 -3.49691e-07Z" fill="white" />
									</svg>
								</div>
								<div class="btn_icon">
									<svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
										<path d="M8 -3.49691e-07L6.70082 1.29918L12.4828 7.0812L3.52868e-07 7.0812L2.72544e-07 8.9188L12.4828 8.9188L6.70081 14.7008L8 16L16 8L8 -3.49691e-07Z" fill="white" />
									</svg>
								</div>
							</div>
						</button>
					</fieldset>

					<label class="form__description required">
						<input type="checkbox" name="agree" value="Да" required>
						<div class="form__description-decor"></div>
						<p>
							Нажимая кнопку «Отправить», Вы соглашаетесь с
							нашей политикой <a href="<?php echo esc_url( home_url( '/privacy-policy/' ) ); ?>">обработки персональных данных</a>
						</p>
					</label>

					<input type="hidden" name="action" value="sendform">
					<input type="hidden" name="THEME" value="Заявка со страницы контактов">
				</form>
			</div>
		</div>
	</section>

	<!-- ============ 5. Реквизиты ============ -->
	<section class="melo-section melo-section--white" id="melo-specs">
		<div class="melo-container">
			<div class="melo-section__head melo-section__head--center" data-reveal>
				<span class="melo-eyebrow">Документы</span>
				<h2><?php echo esc_html( $specs_title ); ?></h2>
			</div>

			<div class="melo-spec-table ox-selectable">
				<?php foreach ( $specs as $row ) : ?>
					<div class="melo-spec-row" data-reveal>
						<p class="melo-spec-row__title"><?php echo esc_html( $row[0] ); ?></p>
						<p class="melo-spec-row__text"><?php echo esc_html( $row[1] ); ?></p>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

</div><!-- /.melo-page -->

	<?php
endwhile;

get_footer();
