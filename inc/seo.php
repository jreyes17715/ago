<?php
/**
 * SEO — Meta description + Open Graph
 * Solo activo cuando no hay Rank Math ni Yoast SEO instalados.
 *
 * @package Blue Vision Tech
 */

defined( 'ABSPATH' ) || exit;

// No interferir con plugins SEO dedicados
if ( defined( 'RANK_MATH_VERSION' ) || defined( 'WPSEO_VERSION' ) ) {
    return;
}

add_action( 'wp_head', 'bvt_seo_meta_tags', 1 );

function bvt_seo_meta_tags() {

    /* ── Datos base ─────────────────────────────────────────────── */
    $site_name  = get_bloginfo( 'name' );
    $tagline    = get_bloginfo( 'description' );
    $locale     = get_locale();

    /* ── Metas personalizados (seo-fields.php) ──────────────────── */
    $post_id         = is_singular() ? get_queried_object_id() : 0;
    $custom_title    = $post_id ? get_post_meta( $post_id, '_bvt_seo_title',       true ) : '';
    $custom_desc     = $post_id ? get_post_meta( $post_id, '_bvt_seo_description', true ) : '';

    /* ── Título ─────────────────────────────────────────────────── */
    if ( $custom_title ) {
        $title = $custom_title;
    } elseif ( is_singular() ) {
        $title = get_the_title() . ' — ' . $site_name;
    } elseif ( is_home() || is_front_page() ) {
        $title = $tagline ? $site_name . ' — ' . $tagline : $site_name;
    } elseif ( is_post_type_archive( 'servicios' ) ) {
        $title = __( 'Servicios', 'blue-vision-tech' ) . ' — ' . $site_name;
    } elseif ( is_archive() ) {
        $title = get_the_archive_title() . ' — ' . $site_name;
    } elseif ( is_search() ) {
        $title = sprintf( __( 'Búsqueda: %s', 'blue-vision-tech' ), get_search_query() ) . ' — ' . $site_name;
    } else {
        $title = $site_name;
    }

    /* ── Descripción ────────────────────────────────────────────── */
    if ( $custom_desc ) {
        $description = $custom_desc;
    } elseif ( is_singular() ) {
        $post = get_queried_object();
        if ( $post && has_excerpt( $post->ID ) ) {
            $description = wp_strip_all_tags( get_the_excerpt( $post->ID ) );
        } elseif ( $post ) {
            $description = wp_trim_words( wp_strip_all_tags( get_the_content( null, false, $post ) ), 30, '…' );
        } else {
            $description = $tagline;
        }
    } else {
        $description = $tagline;
    }
    $description = wp_strip_all_tags( $description );

    /* ── Imagen ─────────────────────────────────────────────────── */
    $image     = '';
    $image_w   = '';
    $image_h   = '';

    if ( is_singular() ) {
        $post_id = get_queried_object_id();
        if ( has_post_thumbnail( $post_id ) ) {
            $thumb = wp_get_attachment_image_src( get_post_thumbnail_id( $post_id ), 'large' );
            if ( $thumb ) {
                $image   = $thumb[0];
                $image_w = $thumb[1];
                $image_h = $thumb[2];
            }
        }
    }

    // Fallback: logo del sitio
    if ( ! $image ) {
        $logo_id = get_theme_mod( 'custom_logo' );
        if ( $logo_id ) {
            $logo = wp_get_attachment_image_src( $logo_id, 'full' );
            if ( $logo ) {
                $image   = $logo[0];
                $image_w = $logo[1];
                $image_h = $logo[2];
            }
        }
    }

    /* ── URL canónica ───────────────────────────────────────────── */
    if ( is_singular() ) {
        $url = get_permalink();
    } elseif ( is_home() || is_front_page() ) {
        $url = home_url( '/' );
    } else {
        global $wp;
        $url = home_url( add_query_arg( [], $wp->request ) );
    }

    /* ── Tipo OG ────────────────────────────────────────────────── */
    $og_type = ( is_singular( 'post' ) ) ? 'article' : 'website';

    /* ── Output ─────────────────────────────────────────────────── */
    echo "\n<!-- BVT SEO -->\n";

    // Meta description
    if ( $description ) {
        echo '<meta name="description" content="' . esc_attr( $description ) . '">' . "\n";
    }

    // Open Graph
    echo '<meta property="og:site_name" content="' . esc_attr( $site_name ) . '">' . "\n";
    echo '<meta property="og:type"      content="' . esc_attr( $og_type ) . '">' . "\n";
    echo '<meta property="og:title"     content="' . esc_attr( $title ) . '">' . "\n";
    echo '<meta property="og:url"       content="' . esc_url( $url ) . '">' . "\n";
    echo '<meta property="og:locale"    content="' . esc_attr( $locale ) . '">' . "\n";

    if ( $description ) {
        echo '<meta property="og:description" content="' . esc_attr( $description ) . '">' . "\n";
    }
    if ( $image ) {
        echo '<meta property="og:image"        content="' . esc_url( $image ) . '">' . "\n";
        if ( $image_w ) echo '<meta property="og:image:width"  content="' . esc_attr( $image_w ) . '">' . "\n";
        if ( $image_h ) echo '<meta property="og:image:height" content="' . esc_attr( $image_h ) . '">' . "\n";
    }

    // Article tags (solo en posts)
    if ( $og_type === 'article' && is_singular( 'post' ) ) {
        $post_id = get_queried_object_id();
        echo '<meta property="article:published_time" content="' . esc_attr( get_the_date( 'c', $post_id ) ) . '">' . "\n";
        echo '<meta property="article:modified_time"  content="' . esc_attr( get_the_modified_date( 'c', $post_id ) ) . '">' . "\n";
        $cats = get_the_category( $post_id );
        if ( $cats ) {
            echo '<meta property="article:section" content="' . esc_attr( $cats[0]->name ) . '">' . "\n";
        }
    }

    // Twitter Card
    echo '<meta name="twitter:card"        content="summary_large_image">' . "\n";
    echo '<meta name="twitter:title"       content="' . esc_attr( $title ) . '">' . "\n";
    if ( $description ) {
        echo '<meta name="twitter:description" content="' . esc_attr( $description ) . '">' . "\n";
    }
    if ( $image ) {
        echo '<meta name="twitter:image" content="' . esc_url( $image ) . '">' . "\n";
    }

    echo "<!-- /BVT SEO -->\n\n";
}
