<?php
/**
 * Cabecera del tema — AGO
 *
 * @package AGO
 */

defined( 'ABSPATH' ) || exit;

// Si la página trae el bloque Hero, el header flota encima de él (transparente).
$ago_header_overlay = is_singular() && has_block( 'ago/hero', get_queried_object_id() );
$ago_header_class   = 'site-header' . ( $ago_header_overlay ? ' site-header--overlay' : '' );
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="profile" href="https://gmpg.org/xfn/11">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<div id="page" class="site">

    <header id="masthead" class="<?php echo esc_attr( $ago_header_class ); ?>" role="banner">
        <div class="header__inner">

            <div class="header__brand">
                <?php if ( has_custom_logo() ) : ?>
                    <?php the_custom_logo(); ?>
                <?php else : ?>
                    <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="header__site-name" rel="home">
                        <?php bloginfo( 'name' ); ?>
                    </a>
                <?php endif; ?>
            </div>

            <?php if ( has_nav_menu( 'primary' ) ) : ?>
                <nav class="header__nav" aria-label="<?php esc_attr_e( 'Menú principal', 'ago' ); ?>">
                    <?php
                    wp_nav_menu( [
                        'theme_location' => 'primary',
                        'container'      => false,
                        'menu_class'     => 'header__menu',
                        'fallback_cb'    => false,
                        'depth'          => 1,
                    ] );
                    ?>
                </nav>

                <button class="header__toggle" type="button"
                        aria-label="<?php esc_attr_e( 'Abrir menú', 'ago' ); ?>"
                        aria-expanded="false" aria-controls="masthead">
                    <span class="header__toggle-bar"></span>
                    <span class="header__toggle-bar"></span>
                    <span class="header__toggle-bar"></span>
                </button>
            <?php endif; ?>

        </div>
    </header><!-- #masthead -->
