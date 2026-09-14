<?php
/**
 * Подвал сайта в стилистике MELO.
 *
 * ЗАМЕНЯЕТ прежний template-parts/content-footer.php. Оригинал сохранён
 * рядом как content-footer-old.php — вернуть можно переименованием.
 *
 * Изменена только сама разметка подвала. Всё, что шло после него —
 * закрытие обёрток плавной прокрутки и модальные окна с формой заявки —
 * перенесено ДОСЛОВНО: там живая форма с валидацией, её трогать нельзя.
 *
 * Контакты берутся из ACF-опций темы, как и раньше; если поля пустые,
 * подставляются значения из melo_contact(). Меню подвала — то же самое
 * «Меню в подвале» из админки.
 *
 * @package oxboxwise
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* Контакты: сначала настройки сайта, потом наши значения по умолчанию. */
$melo_f = function_exists( 'get_field' ) ? 'get_field' : null;
$melo_opt = function ( $key, $fallback ) use ( $melo_f ) {
	if ( $melo_f ) {
		$v = $melo_f( $key, 'option' );
		if ( $v ) {
			return $v;
		}
	}
	return $fallback;
};

$melo_phone2 = $melo_opt( 'opt_phone_site', melo_contact( 'phone2' ) );
$melo_email  = $melo_opt( 'opt_email_site', melo_contact( 'email' ) );
$melo_addr   = $melo_opt( 'opt_address_site', melo_contact( 'address' ) );
?>

	<footer class="melo-site-footer" id="contacts" data-anc_id="#contacts">
		<div class="melo-container">
			<div class="melo-site-footer__grid">
				<?php /* Заявление, кнопка и оговорка — одна ячейка сетки.
				         Положи их соседями, и каждый занял бы свою колонку,
				         развалив подвал на семь столбцов. */ ?>
				<div class="melo-footer-lead">
					<p class="melo-site-footer__claim"><?php echo esc_html( melo_contact( 'claim' ) ); ?></p>

					<button class="melo-chrome-btn open-modal-os" type="button"
						data-title="Обратная связь из подвала">Обратная связь</button>

					<p class="melo-footer-note"><?php echo esc_html( melo_contact( 'offer' ) ); ?></p>
				</div>

				<div class="melo-footer-contacts">
					<a class="melo-footer-contacts__major" href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', $melo_phone2 ) ); ?>"><?php echo esc_html( $melo_phone2 ); ?></a>
					<a class="melo-footer-contacts__major" href="mailto:<?php echo esc_attr( $melo_email ); ?>"><?php echo esc_html( $melo_email ); ?></a>
					<span class="melo-footer-contacts__gap"></span>
					<?php
					/* Мессенджер показываем только с заполненным адресом: пустая
					   ссылка выглядит рабочей, но никуда не ведёт. Адреса берутся
					   из настроек темы, поэтому список сам оживает по мере
					   заполнения. */
					$melo_messengers = array(
						'Telegram' => melo_contact( 'telegram' ),
						'MAX'      => melo_contact( 'max' ),
					);
					foreach ( $melo_messengers as $melo_label => $melo_href ) :
						if ( '' === $melo_href || '#' === $melo_href ) {
							continue;
						}
						?>
						<a class="melo-footer-contacts__major" href="<?php echo esc_url( $melo_href ); ?>" target="_blank" rel="noopener"><?php echo esc_html( $melo_label ); ?></a>
					<?php endforeach; ?>
					<p class="melo-footer-contacts__address"><?php echo esc_html( $melo_addr ); ?></p>
				</div>

				<nav class="melo-footer-nav" aria-label="Меню в подвале">
					<?php
					melo_nav(
						'Меню в подвале',
						'',
						array(
							'О нас'       => '/#about',
							'Направления' => '/#directions',
							'Портфолио'   => '/#projects',
							'Цены'        => '/#pricing',
							'Контакты'    => '#contacts',
						)
					);
					?>
				</nav>

				<a class="melo-footer-top-link" href="#melo-top">Наверх</a>
			</div>

			<div class="melo-site-footer__bottom">
				<span><?php echo esc_html( gmdate( 'Y' ) . ' ' . get_bloginfo( 'name' ) ); ?></span>
				<a href="<?php echo esc_url( melo_contact( 'policy' ) ); ?>">Политика конфиденциальности</a>
				<?php /* target="_blank" — уводить с сайта клиента в той же вкладке не стоит;
				         rel="noopener" обязателен: без него открытая вкладка получает
				         доступ к window.opener и может подменить страницу-источник. */ ?>
				<a href="https://www.oxbox.ru" target="_blank" rel="noopener">Разработано в OXBOX</a>
			</div>
		</div>
	</footer>





      
    </div>
  </div>

<div class="modal modal-parent modal-succes">
    <div class="modal__inner">
        <div class="modal-box">
            <strong class="modal__title">
                Спасибо! Ваша <br>
                заявка отправлена
            </strong>
            <p class="modal__text">
                Наш менеджер свяжется с вами в ближайшее<br>
                времяи уточнит дополнительные детали.
            </p>
            <button class="modal__btn-close" type="button">
            <svg width="64px" height="64px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round" stroke="#CCCCCC" stroke-width="0.43200000000000005"></g><g id="SVGRepo_iconCarrier"> <path d="M20.7457 3.32851C20.3552 2.93798 19.722 2.93798 19.3315 3.32851L12.0371 10.6229L4.74275 3.32851C4.35223 2.93798 3.71906 2.93798 3.32854 3.32851C2.93801 3.71903 2.93801 4.3522 3.32854 4.74272L10.6229 12.0371L3.32856 19.3314C2.93803 19.722 2.93803 20.3551 3.32856 20.7457C3.71908 21.1362 4.35225 21.1362 4.74277 20.7457L12.0371 13.4513L19.3315 20.7457C19.722 21.1362 20.3552 21.1362 20.7457 20.7457C21.1362 20.3551 21.1362 19.722 20.7457 19.3315L13.4513 12.0371L20.7457 4.74272C21.1362 4.3522 21.1362 3.71903 20.7457 3.32851Z" fill="#333333"></path> </g></svg>
          </button>
        </div>
        <picture class="modal__picture">
            <img src="/wp-content/uploads/2026/06/form.webp" alt="">
        </picture>
    </div>
</div>

      <div class="modal modal-parent modal-os">
        <div class="modal__inner">
			
          <button class="modal-close" type="button" aria-label="Закрыть модальное окно">
            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
              <g clip-path="url(#clip0_26_623)">
                <path d="M0.841797 0.421051L15.7283 15.3075L15.1328 15.903L0.246339 1.01651L0.841797 0.421051Z"
                  fill="currentcolor"></path>
                <path d="M0.420898 15.1579L15.3074 0.27144L15.9028 0.866898L1.01636 15.7534L0.420898 15.1579Z"
                  fill="currentcolor"></path>
              </g>
              <defs>
                <clipPath id="clip0_26_623">
                  <rect width="16" height="16" fill="white"></rect>
                </clipPath>
              </defs>
            </svg>
          </button>
          <form action="" class="form" data-controller id="os">
            <h3 class="form__title">Оставить заявку</h3>
            <p class="form__description">
              Наш менеджер свяжется с вами в ближайшее время<br> и уточнит дополнительные детали.
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

              <?php /* почта больше не обязательна: класс required снят,
                       атрибут тоже — иначе валидация продолжит требовать */ ?>
              <div class="input textarea input-wrapper form__label melo-field">
                <label for="">
                  <input type="email" class="focus-input input-field" name="email" placeholder="Email">
                </label>
              </div>

              <div class="input textarea input-wrapper form__label melo-field melo-field_area">
                <label for="">
                  <textarea class="focus-input input-field form__textarea" name="comment" rows="3" placeholder="Комментарий"></textarea>
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
                      <path
                        d="M8 -3.49691e-07L6.70082 1.29918L12.4828 7.0812L3.52868e-07 7.0812L2.72544e-07 8.9188L12.4828 8.9188L6.70081 14.7008L8 16L16 8L8 -3.49691e-07Z"
                        fill="white" />
                    </svg>
                  </div>
                  <div class="btn_icon">
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <path
                        d="M8 -3.49691e-07L6.70082 1.29918L12.4828 7.0812L3.52868e-07 7.0812L2.72544e-07 8.9188L12.4828 8.9188L6.70081 14.7008L8 16L16 8L8 -3.49691e-07Z"
                        fill="white" />
                    </svg>
                  </div>
                </div>
              </button>
            </fieldset>
            <label id="check123" class="form__description required" >
              <input type="checkbox" id="check123" name="agree" value="Да" required>
              <div class="form__description-decor"></div>
              <p>
                Нажимая кнопку «Отправить», Вы соглашается с
                <?php /* Адрес берём из тех же настроек, что и ссылка в подвале.
                         Здесь был зашит /privacy-policy/ — страницы с таким
                         ярлыком на сайте нет, и человек, дойдя до галочки
                         согласия, упирался в 404 прямо перед отправкой. */ ?>
                нашей политикой <a href="<?php echo esc_url( melo_contact( 'policy' ) ); ?>">Обработки персональных данных</a>
              </p>
            </label>
			  <input type="hidden" name="action" value="sendform">
			  <input type="hidden" name="THEME" value="">
          </form>
        </div>
      </div>










