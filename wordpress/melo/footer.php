<?php
/**
 * Подвал сайта и закрывающая часть документа.
 *
 * @package melo
 */

?>
</main>

<!-- ============ Подвал ============ -->
<footer class="site-footer" id="contacts">
	<div class="container">
		<div class="site-footer__grid">
			<p class="site-footer__claim"><?php echo esc_html( melo_contact( 'claim' ) ); ?></p>

			<div class="footer-contacts">
				<a class="footer-contacts__major" href="tel:<?php echo esc_attr( melo_contact( 'free_raw' ) ); ?>">
					<?php echo esc_html( melo_contact( 'phone_free' ) ); ?>
				</a>
				<a class="footer-contacts__major" href="mailto:<?php echo esc_attr( melo_contact( 'email' ) ); ?>">
					<?php echo esc_html( melo_contact( 'email' ) ); ?>
				</a>

				<span class="footer-contacts__gap"></span>

				<a class="footer-contacts__major" href="<?php echo esc_url( melo_contact( 'telegram' ) ); ?>" rel="noopener">Telegram</a>
				<a class="footer-contacts__major" href="<?php echo esc_url( melo_contact( 'max' ) ); ?>" rel="noopener">MAX</a>

				<p class="footer-contacts__address"><?php echo esc_html( melo_contact( 'address' ) ); ?></p>
			</div>

			<nav class="footer-nav" aria-label="<?php esc_attr_e( 'Меню в подвале', 'melo' ); ?>">
				<?php
				wp_nav_menu(
					array(
						'theme_location' => 'footer',
						'container'      => false,
						'items_wrap'     => '%3$s',
						'depth'          => 1,
						'walker'         => new Melo_Nav_Walker(),
						'link_class'     => '',
						'fallback_cb'    => 'melo_fallback_footer_nav',
					)
				);
				?>
			</nav>

			<a class="footer-top-link" href="#top"><?php esc_html_e( 'Наверх', 'melo' ); ?></a>
		</div>

		<div class="site-footer__bottom">
			<span><?php echo esc_html( gmdate( 'Y' ) . ' ' . get_bloginfo( 'name' ) ); ?></span>

			<?php
			$privacy = get_privacy_policy_url();
			if ( $privacy ) :
				?>
				<a href="<?php echo esc_url( $privacy ); ?>"><?php esc_html_e( 'Политика конфиденциальности', 'melo' ); ?></a>
			<?php else : ?>
				<span><?php esc_html_e( 'Политика конфиденциальности', 'melo' ); ?></span>
			<?php endif; ?>

			<span><?php esc_html_e( 'Разработано в OXBOX', 'melo' ); ?></span>
		</div>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
