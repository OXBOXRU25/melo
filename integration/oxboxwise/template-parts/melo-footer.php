<?php
/**
 * MELO — подвал внутренних страниц
 *
 * Закрывает каркас, открытый в melo-header.php.
 *
 * Подключается из шаблонов: get_template_part( 'template-parts/melo', 'footer' );
 *
 * Контакты берутся из melo_contact() — правятся фильтром melo_contacts,
 * без вмешательства в разметку. См. inc/melo-functions.php.
 *
 * @package oxboxwise
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
	</main>

	<footer class="melo-site-footer" id="contacts">
		<div class="melo-container">
			<div class="melo-site-footer__grid">
				<p class="melo-site-footer__claim"><?php echo esc_html( melo_contact( 'claim' ) ); ?></p>

				<div class="melo-footer-contacts">
					<a class="melo-footer-contacts__major" href="tel:<?php echo esc_attr( melo_contact( 'phone2_raw' ) ); ?>"><?php echo esc_html( melo_contact( 'phone2' ) ); ?></a>
					<a class="melo-footer-contacts__major" href="mailto:<?php echo esc_attr( melo_contact( 'email' ) ); ?>"><?php echo esc_html( melo_contact( 'email' ) ); ?></a>
					<span class="melo-footer-contacts__gap"></span>
					<a class="melo-footer-contacts__major" href="<?php echo esc_url( melo_contact( 'telegram' ) ); ?>" rel="noopener">Telegram</a>
					<a class="melo-footer-contacts__major" href="<?php echo esc_url( melo_contact( 'max' ) ); ?>" rel="noopener">MAX</a>
					<p class="melo-footer-contacts__address"><?php echo esc_html( melo_contact( 'address' ) ); ?></p>
				</div>

				<nav class="melo-footer-nav" aria-label="Меню в подвале">
					<?php
					melo_nav(
						'menu_footer',
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
				<span>Разработано в OXBOX</span>
			</div>
		</div>
	</footer>

</div><!-- /.melo-page -->

<?php wp_footer(); ?>
</body>
</html>
