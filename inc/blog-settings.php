<?php
/**
 * Blog Settings
 * Registra un ajuste en el Customizer para elegir la página de listado de blog
 * y expone helpers reutilizables en todo el tema.
 *
 * @package Blue Vision Tech
 */

defined( 'ABSPATH' ) || exit;

/* ------------------------------------------------------------------ */
/*  Customizer: selector de página de blog                             */
/* ------------------------------------------------------------------ */
add_action( 'customize_register', function ( WP_Customize_Manager $wp_customize ) : void {

    $wp_customize->add_section( 'bvt_blog', [
        'title'    => 'Blog',
        'priority' => 105,
    ] );

    $wp_customize->add_setting( 'bvt_blog_listing_page', [
        'default'           => 0,
        'sanitize_callback' => 'absint',
        'transport'         => 'refresh',
    ] );

    $wp_customize->add_control( 'bvt_blog_listing_page', [
        'label'       => 'Página de listado de blog',
        'description' => 'Su imagen destacada se usará como fondo del hero en posts y páginas de tags.',
        'section'     => 'bvt_blog',
        'type'        => 'dropdown-pages',
    ] );
} );

/* ------------------------------------------------------------------ */
/*  Helpers                                                            */
/* ------------------------------------------------------------------ */

/**
 * ID de la página configurada como listado de blog.
 */
function bvt_blog_listing_page_id() : int {
    return (int) get_theme_mod( 'bvt_blog_listing_page', 0 );
}

/**
 * URL de la imagen destacada de la página de listado de blog.
 * Devuelve string vacío si no hay página o no tiene imagen.
 */
function bvt_blog_hero_thumb() : string {
    $id = bvt_blog_listing_page_id();
    if ( ! $id ) {
        return '';
    }
    return get_the_post_thumbnail_url( $id, 'full' ) ?: '';
}
