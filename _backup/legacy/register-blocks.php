<?php
/**
 * Block registration — Blue Vision Tech
 * Registra todos los bloques Gutenberg personalizados del tema.
 *
 * @package Blue Vision Tech
 */

defined( 'ABSPATH' ) || exit;

/* ------------------------------------------------------------------ */
/*  Register blocks (block.json maneja render.php directamente)       */
/* ------------------------------------------------------------------ */
add_action( 'init', function () {
    $blocks = [
        'hero',
        'trayectoria',
        'logos-carousel',
        'aliados',
        'about',
        'values',
        'certifications',
        'services',
        'services-all',
        'blog',
        'page-hero',
        'sobre-nosotros',
        'contribuimos',
        'marquee',
        'equipo',
        'testimonios',
        'proceso',
        'tarifas',
        'header-blog',
        'blog-listing',
        'contacto',
        'faq',
    ];

    foreach ( $blocks as $block ) {
        register_block_type(
            get_template_directory() . '/blocks/' . $block
        );
    }
} );

/* ------------------------------------------------------------------ */
/*  Font Awesome Free — frontend y editor                              */
/* ------------------------------------------------------------------ */
define( 'BVT_FA_VERSION', '6.5.1' );
define( 'BVT_FA_CDN', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/' . BVT_FA_VERSION . '/css/all.min.css' );

add_action( 'wp_enqueue_scripts', function () {
    wp_enqueue_style( 'font-awesome', BVT_FA_CDN, [], BVT_FA_VERSION );
} );

add_action( 'enqueue_block_editor_assets', function () {
    wp_enqueue_style( 'font-awesome-editor', BVT_FA_CDN, [], BVT_FA_VERSION );
} );

/* ------------------------------------------------------------------ */
/*  Swiper — carrusel del bloque "Aliados"                             */
/* ------------------------------------------------------------------ */
define( 'DD_SWIPER_VERSION', '11.1.14' );

add_action( 'wp_enqueue_scripts', function () {
    wp_enqueue_style(
        'swiper',
        'https://cdnjs.cloudflare.com/ajax/libs/Swiper/' . DD_SWIPER_VERSION . '/swiper-bundle.min.css',
        [],
        DD_SWIPER_VERSION
    );
    wp_enqueue_script(
        'swiper',
        'https://cdnjs.cloudflare.com/ajax/libs/Swiper/' . DD_SWIPER_VERSION . '/swiper-bundle.min.js',
        [],
        DD_SWIPER_VERSION,
        true
    );
} );

/* ------------------------------------------------------------------ */
/*  Enqueue editor scripts                                             */
/* ------------------------------------------------------------------ */
add_action( 'enqueue_block_editor_assets', function () {
    $base_deps = [ 'wp-blocks', 'wp-element', 'wp-block-editor', 'wp-components', 'wp-i18n', 'wp-rich-text', 'wp-data', 'wp-server-side-render' ];

    // Formato "acento" compartido — debe cargar ANTES que cualquier editor.js que lo use.
    $shared_path = 'blocks/shared/format-accent.js';
    $shared_full = get_template_directory() . '/' . $shared_path;
    $shared_handle = 'ago-format-accent';

    if ( file_exists( $shared_full ) ) {
        wp_register_script(
            $shared_handle,
            get_template_directory_uri() . '/' . $shared_path,
            $base_deps,
            filemtime( $shared_full ),
            true
        );
        wp_enqueue_script( $shared_handle );
    }

    $scripts = [
        'ago-hero-editor'        => 'blocks/hero/editor.js',
        'ago-trayectoria-editor' => 'blocks/trayectoria/editor.js',
        'ago-logos-carousel-editor' => 'blocks/logos-carousel/editor.js',
        'ago-aliados-editor'        => 'blocks/aliados/editor.js',
        'bvt-about-editor'           => 'blocks/about/editor.js',
        'bvt-values-editor'          => 'blocks/values/editor.js',
        'bvt-certifications-editor'  => 'blocks/certifications/editor.js',
        'ago-services-editor' => 'blocks/services/editor.js',
        'bvt-services-all-editor'    => 'blocks/services-all/editor.js',
        'ago-blog-editor' => 'blocks/blog/editor.js',
        'bvt-page-hero-editor'       => 'blocks/page-hero/editor.js',
        'ago-sobre-nosotros-editor' => 'blocks/sobre-nosotros/editor.js',
        'ago-contribuimos-editor' => 'blocks/contribuimos/editor.js',
        'ago-marquee-editor' => 'blocks/marquee/editor.js',
        'ago-equipo-editor' => 'blocks/equipo/editor.js',
        'ago-testimonios-editor' => 'blocks/testimonios/editor.js',
        'ago-proceso-editor' => 'blocks/proceso/editor.js',
        'ago-tarifas-editor' => 'blocks/tarifas/editor.js',
        'ago-header-blog-editor' => 'blocks/header-blog/editor.js',
        'ago-blog-listing-editor' => 'blocks/blog-listing/editor.js',
        'bvt-contacto-editor'        => 'blocks/contacto/editor.js',
        'bvt-faq-editor'             => 'blocks/faq/editor.js',
    ];

    foreach ( $scripts as $handle => $path ) {
        $full_path = get_template_directory() . '/' . $path;

        if ( ! file_exists( $full_path ) ) {
            continue;
        }

        wp_register_script(
            $handle,
            get_template_directory_uri() . '/' . $path,
            array_merge( $base_deps, [ $shared_handle ] ),
            filemtime( $full_path ),
            true
        );

        wp_enqueue_script( $handle );
    }
} );
