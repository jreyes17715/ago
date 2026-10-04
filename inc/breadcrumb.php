<?php
/**
 * Breadcrumb helper — Blue Vision Tech
 * Genera el breadcrumb contextual para usar dentro del page-hero.
 *
 * @package Blue Vision Tech
 */

defined( 'ABSPATH' ) || exit;

/**
 * Imprime el nav de breadcrumb según el contexto actual.
 * Soporta: páginas estáticas (con jerarquía), CPT servicios y entradas de blog.
 *
 * @param string $class  Clase CSS del <nav>. Default 'page-hero__breadcrumb'.
 */
function bvt_breadcrumb( string $class = 'page-hero__breadcrumb' ): void {
    $items = bvt_breadcrumb_items();

    if ( empty( $items ) ) {
        return;
    }

    $label = esc_attr__( 'Ruta de navegación', 'blue-vision-tech' );
    echo '<nav class="' . esc_attr( $class ) . '" aria-label="' . $label . '">';
    echo '<ol class="' . esc_attr( $class ) . '__list">';

    $total = count( $items );
    foreach ( $items as $i => $item ) {
        $is_last = ( $i === $total - 1 );
        $li_class = esc_attr( $class ) . '__item';
        if ( $is_last ) {
            $li_class .= ' ' . esc_attr( $class ) . '__item--current';
        }

        echo '<li class="' . $li_class . '"' . ( $is_last ? ' aria-current="page"' : '' ) . '>';
        if ( ! $is_last && ! empty( $item['url'] ) ) {
            echo '<a href="' . esc_url( $item['url'] ) . '">' . esc_html( $item['label'] ) . '</a>';
        } else {
            echo esc_html( $item['label'] );
        }
        echo '</li>';
    }

    echo '</ol>';
    echo '</nav>';
}

/**
 * Construye el array de ítems del breadcrumb para el contexto actual.
 *
 * @return array[]  [ ['label' => string, 'url' => string|''] ]
 */
function bvt_breadcrumb_items(): array {
    $items = [];

    // Inicio siempre primero
    $items[] = [
        'label' => __( 'Inicio', 'blue-vision-tech' ),
        'url'   => home_url( '/' ),
    ];

    if ( is_home() ) {
        // Página de listado del blog (Settings > Reading > "Posts page")
        $blog_page_id = get_option( 'page_for_posts' );
        $label = $blog_page_id ? get_the_title( $blog_page_id ) : __( 'Blog', 'blue-vision-tech' );
        $items[] = [
            'label' => $label,
            'url'   => '',
        ];
        return $items;

    } elseif ( is_singular( 'servicios' ) ) {
        // Usa la página estática con slug 'servicios' como padre en el breadcrumb.
        // (El CPT tiene has_archive=false; la página estática controla /servicios/)
        $services_page = get_page_by_path( 'servicios' );
        $services_url  = $services_page ? get_permalink( $services_page->ID ) : home_url( '/servicios/' );
        $items[] = [
            'label' => __( 'Servicios', 'blue-vision-tech' ),
            'url'   => $services_url,
        ];
        $items[] = [
            'label' => get_the_title(),
            'url'   => '',
        ];

    } elseif ( is_singular( 'post' ) ) {
        // Blog → post actual
        $blog_page_id = get_option( 'page_for_posts' );
        if ( $blog_page_id ) {
            $items[] = [
                'label' => get_the_title( $blog_page_id ),
                'url'   => get_permalink( $blog_page_id ),
            ];
        }
        $items[] = [
            'label' => get_the_title(),
            'url'   => '',
        ];

    } elseif ( is_page() ) {
        // Páginas estáticas con jerarquía
        $ancestors = array_reverse( get_post_ancestors( get_the_ID() ) );
        foreach ( $ancestors as $ancestor_id ) {
            $items[] = [
                'label' => get_the_title( $ancestor_id ),
                'url'   => get_permalink( $ancestor_id ),
            ];
        }
        $items[] = [
            'label' => get_the_title(),
            'url'   => '',
        ];

    } else {
        // Sin contexto reconocido: no mostrar breadcrumb
        return [];
    }

    return $items;
}
