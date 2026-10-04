<?php
/**
 * Funciones del tema — AGO
 *
 * @package AGO
 */

defined( 'ABSPATH' ) || exit;

use TailPress\Framework\Theme;
use TailPress\Framework\Assets\ViteCompiler;

require_once get_template_directory() . '/vendor/autoload.php';

/* ------------------------------------------------------------------ */
/*  Theme setup                                                        */
/* ------------------------------------------------------------------ */
add_action( 'after_setup_theme', function () {
    add_theme_support( 'title-tag' );
    add_theme_support( 'post-thumbnails' );
    add_theme_support( 'automatic-feed-links' );
    add_theme_support( 'editor-styles' );
    add_theme_support( 'html5', [
        'comment-list', 'comment-form', 'search-form',
        'gallery', 'caption', 'style', 'script',
    ] );
    add_theme_support( 'custom-logo', [
        'height'      => 60,
        'width'       => 200,
        'flex-height' => true,
        'flex-width'  => true,
    ] );

    register_nav_menus( [
        'primary' => __( 'Menu Principal', 'ago' ),
        'footer'  => __( 'Menu Footer',    'ago' ),
    ] );
} );

/* ------------------------------------------------------------------ */
/*  Google Fonts — frontend y editor                                   */
/* ------------------------------------------------------------------ */
define( 'AGO_GOOGLE_FONTS_URL', 'https://fonts.googleapis.com/css2?family=Golos+Text:wght@400;500;600;700&family=Instrument+Serif:ital@0;1&display=swap' );

add_action( 'wp_enqueue_scripts', function () {
    wp_enqueue_style( 'ago-google-fonts', AGO_GOOGLE_FONTS_URL, [], null );
} );

add_action( 'enqueue_block_editor_assets', function () {
    wp_enqueue_style( 'ago-google-fonts-editor', AGO_GOOGLE_FONTS_URL, [], null );
} );

/* ------------------------------------------------------------------ */
/*  Assets via TailPress + Vite                                        */
/* ------------------------------------------------------------------ */
Theme::instance()
    ->assets( fn( $manager ) => $manager
        ->withCompiler( new ViteCompiler, fn( $compiler ) => $compiler
            ->registerAsset( 'resources/css/app.css' )
            ->registerAsset( 'resources/js/app.js' )
            ->editorStyleFile( 'resources/css/editor-style.css' )
        )
        ->enqueueAssets()
    );

/* ------------------------------------------------------------------ */
/*  Bloques                                                            */
/* ------------------------------------------------------------------ */
require_once get_template_directory() . '/inc/blocks.php';
