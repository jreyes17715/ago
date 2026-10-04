<?php
/**
 * Footer — AGO
 *
 * @package AGO
 */

defined( 'ABSPATH' ) || exit;
?>

    <footer id="colophon" class="site-footer" role="contentinfo">
        <div class="footer__container">

            <?php if ( has_nav_menu( 'footer' ) ) : ?>
                <nav class="footer__nav" aria-label="<?php esc_attr_e( 'Menú de footer', 'ago' ); ?>">
                    <?php
                    wp_nav_menu( [
                        'theme_location' => 'footer',
                        'container'      => false,
                        'menu_class'     => 'footer__menu',
                        'fallback_cb'    => false,
                        'depth'          => 1,
                    ] );
                    ?>
                </nav>
            <?php endif; ?>

            <p class="footer__copyright">
                &copy; <?php echo esc_html( wp_date( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?>
            </p>

        </div>
    </footer><!-- #colophon -->

</div><!-- #page .site -->

<?php wp_footer(); ?>
</body>
</html>
