<?php
/**
 * AEO — JSON-LD Structured Data
 * Organization, WebSite, BreadcrumbList, BlogPosting, Service, FAQPage.
 *
 * @package Blue Vision Tech
 */

defined( 'ABSPATH' ) || exit;

add_action( 'wp_head', 'bvt_schema_output', 2 );

/* ══════════════════════════════════════════════════════════════════
   HELPERS
══════════════════════════════════════════════════════════════════ */

/**
 * Construye el nodo Organization reutilizable en todo el schema.
 */
function bvt_schema_organization(): array {
    $name     = get_bloginfo( 'name' );
    $url      = home_url( '/' );
    $logo_id  = get_theme_mod( 'custom_logo' );
    $logo_url = '';

    if ( $logo_id ) {
        $logo_src = wp_get_attachment_image_src( $logo_id, 'full' );
        if ( $logo_src ) {
            $logo_url = $logo_src[0];
        }
    }

    // Redes sociales
    $same_as = [];
    foreach ( [ 'bvt_social_facebook', 'bvt_social_linkedin', 'bvt_social_instagram' ] as $key ) {
        $val = get_theme_mod( $key, '' );
        if ( $val && $val !== '#' ) {
            $same_as[] = esc_url( $val );
        }
    }

    // Sedes como contactPoints + PostalAddress
    $locations      = [];
    $contact_points = [];

    for ( $i = 1; $i <= 4; $i++ ) {
        $sede_name    = get_theme_mod( "bvt_sede_{$i}_name",    '' );
        $sede_address = get_theme_mod( "bvt_sede_{$i}_address", '' );
        $sede_phone   = get_theme_mod( "bvt_sede_{$i}_phone",   '' );

        if ( ! $sede_name ) {
            continue;
        }

        $location = [
            '@type' => 'Place',
            'name'  => $sede_name,
        ];
        if ( $sede_address ) {
            $location['address'] = [
                '@type'           => 'PostalAddress',
                'streetAddress'   => $sede_address,
                'addressLocality' => $sede_name,
            ];
        }
        $locations[] = $location;

        if ( $sede_phone ) {
            $contact_points[] = [
                '@type'             => 'ContactPoint',
                'telephone'         => $sede_phone,
                'contactType'       => 'customer service',
                'areaServed'        => $sede_name,
                'availableLanguage' => [ 'Spanish', 'English' ],
            ];
        }
    }

    // Países como areaServed
    $area_served = [];
    $countries_raw = get_theme_mod( 'bvt_countries_list', '' );
    if ( $countries_raw ) {
        $lines = array_filter( array_map( 'trim', explode( "\n", $countries_raw ) ) );
        foreach ( $lines as $line ) {
            $parts = explode( ':', $line, 2 );
            $area_served[] = trim( $parts[0] );
        }
        $area_served = array_unique( $area_served );
    }

    $org = [
        '@type'  => 'Organization',
        '@id'    => $url . '#organization',
        'name'   => $name,
        'url'    => $url,
    ];

    if ( $logo_url ) {
        $org['logo'] = [
            '@type'            => 'ImageObject',
            'url'              => $logo_url,
            'contentUrl'       => $logo_url,
        ];
    }
    if ( $same_as )        $org['sameAs']        = array_values( $same_as );
    if ( $contact_points ) $org['contactPoint']  = $contact_points;
    if ( $locations )      $org['location']      = $locations;
    if ( $area_served )    $org['areaServed']     = array_values( $area_served );

    return $org;
}

/* ══════════════════════════════════════════════════════════════════
   OUTPUT PRINCIPAL
══════════════════════════════════════════════════════════════════ */

function bvt_schema_output() {
    $graphs = [];

    /* ── WebSite ──────────────────────────────────────────────── */
    $graphs[] = [
        '@type'            => 'WebSite',
        '@id'              => home_url( '/' ) . '#website',
        'url'              => home_url( '/' ),
        'name'             => get_bloginfo( 'name' ),
        'description'      => get_bloginfo( 'description' ),
        'publisher'        => [ '@id' => home_url( '/' ) . '#organization' ],
        'potentialAction'  => [
            '@type'       => 'SearchAction',
            'target'      => [
                '@type'       => 'EntryPoint',
                'urlTemplate' => home_url( '/?s={search_term_string}' ),
            ],
            'query-input' => 'required name=search_term_string',
        ],
        'inLanguage'       => get_locale(),
    ];

    /* ── Organization ─────────────────────────────────────────── */
    $graphs[] = bvt_schema_organization();

    /* ── BreadcrumbList ───────────────────────────────────────── */
    $breadcrumb = bvt_schema_breadcrumb();
    if ( $breadcrumb ) {
        $graphs[] = $breadcrumb;
    }

    /* ── BlogPosting ──────────────────────────────────────────── */
    if ( is_singular( 'post' ) ) {
        $graphs[] = bvt_schema_blog_posting();
    }

    /* ── Service ──────────────────────────────────────────────── */
    if ( is_singular( 'servicios' ) ) {
        $graphs[] = bvt_schema_service();
    }

    /* ── Output ───────────────────────────────────────────────── */
    $schema = [
        '@context' => 'https://schema.org',
        '@graph'   => $graphs,
    ];

    echo "\n<script type=\"application/ld+json\">\n";
    echo wp_json_encode( $schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT );
    echo "\n</script>\n\n";
}

/* ══════════════════════════════════════════════════════════════════
   BREADCRUMB LIST
══════════════════════════════════════════════════════════════════ */

function bvt_schema_breadcrumb(): ?array {
    if ( is_front_page() || is_home() ) {
        return null;
    }

    $items = [];
    $pos   = 1;

    // Inicio
    $items[] = [
        '@type'    => 'ListItem',
        'position' => $pos++,
        'name'     => __( 'Inicio', 'blue-vision-tech' ),
        'item'     => home_url( '/' ),
    ];

    // Sección intermedia
    if ( is_singular( 'post' ) ) {
        $blog_page_id = get_option( 'page_for_posts' );
        $blog_name    = $blog_page_id ? get_the_title( $blog_page_id ) : __( 'Blog', 'blue-vision-tech' );
        $blog_url     = $blog_page_id ? get_permalink( $blog_page_id ) : home_url( '/blog/' );

        $items[] = [
            '@type'    => 'ListItem',
            'position' => $pos++,
            'name'     => $blog_name,
            'item'     => $blog_url,
        ];
    } elseif ( is_singular( 'servicios' ) ) {
        $items[] = [
            '@type'    => 'ListItem',
            'position' => $pos++,
            'name'     => __( 'Servicios', 'blue-vision-tech' ),
            'item'     => get_post_type_archive_link( 'servicios' ),
        ];
    } elseif ( is_page() ) {
        // Páginas con parent
        $ancestors = get_post_ancestors( get_queried_object_id() );
        foreach ( array_reverse( $ancestors ) as $ancestor_id ) {
            $items[] = [
                '@type'    => 'ListItem',
                'position' => $pos++,
                'name'     => get_the_title( $ancestor_id ),
                'item'     => get_permalink( $ancestor_id ),
            ];
        }
    }

    // Página actual
    $items[] = [
        '@type'    => 'ListItem',
        'position' => $pos,
        'name'     => get_the_title(),
        'item'     => get_permalink(),
    ];

    return [
        '@type'           => 'BreadcrumbList',
        'itemListElement' => $items,
    ];
}

/* ══════════════════════════════════════════════════════════════════
   BLOG POSTING
══════════════════════════════════════════════════════════════════ */

function bvt_schema_blog_posting(): array {
    $post_id     = get_queried_object_id();
    $post        = get_post( $post_id );
    $author_id   = $post->post_author;
    $author_name = get_the_author_meta( 'display_name', $author_id );
    $author_url  = get_author_posts_url( $author_id );

    $image_url = '';
    if ( has_post_thumbnail( $post_id ) ) {
        $thumb = wp_get_attachment_image_src( get_post_thumbnail_id( $post_id ), 'large' );
        if ( $thumb ) {
            $image_url = $thumb[0];
        }
    }

    $description = '';
    if ( has_excerpt( $post_id ) ) {
        $description = wp_strip_all_tags( get_the_excerpt( $post_id ) );
    } else {
        $description = wp_trim_words( wp_strip_all_tags( $post->post_content ), 30, '…' );
    }

    $schema = [
        '@type'            => 'BlogPosting',
        '@id'              => get_permalink( $post_id ) . '#blogposting',
        'headline'         => get_the_title( $post_id ),
        'description'      => $description,
        'url'              => get_permalink( $post_id ),
        'datePublished'    => get_the_date( 'c', $post_id ),
        'dateModified'     => get_the_modified_date( 'c', $post_id ),
        'inLanguage'       => get_locale(),
        'author'           => [
            '@type' => 'Person',
            'name'  => $author_name,
            'url'   => $author_url,
        ],
        'publisher'        => [ '@id' => home_url( '/' ) . '#organization' ],
        'isPartOf'         => [ '@id' => home_url( '/' ) . '#website' ],
        'mainEntityOfPage' => [ '@id' => get_permalink( $post_id ) ],
    ];

    if ( $image_url ) {
        $schema['image'] = [
            '@type' => 'ImageObject',
            'url'   => $image_url,
        ];
    }

    // Categorías como keywords
    $cats = get_the_category( $post_id );
    if ( $cats ) {
        $schema['keywords'] = implode( ', ', wp_list_pluck( $cats, 'name' ) );
        $schema['articleSection'] = $cats[0]->name;
    }

    return $schema;
}

/* ══════════════════════════════════════════════════════════════════
   SERVICE
══════════════════════════════════════════════════════════════════ */

function bvt_schema_service(): array {
    $post_id = get_queried_object_id();

    $description = '';
    if ( has_excerpt( $post_id ) ) {
        $description = wp_strip_all_tags( get_the_excerpt( $post_id ) );
    } else {
        $resumen = get_post_meta( $post_id, '_bvt_resumen', true );
        if ( $resumen ) {
            $description = wp_strip_all_tags( $resumen );
        }
    }

    $image_url = '';
    if ( has_post_thumbnail( $post_id ) ) {
        $thumb = wp_get_attachment_image_src( get_post_thumbnail_id( $post_id ), 'large' );
        if ( $thumb ) {
            $image_url = $thumb[0];
        }
    }

    // Países disponibles como areaServed
    $area_served   = [];
    $countries_raw = get_theme_mod( 'bvt_countries_list', '' );
    if ( $countries_raw ) {
        $lines = array_filter( array_map( 'trim', explode( "\n", $countries_raw ) ) );
        foreach ( $lines as $line ) {
            $parts = explode( ':', $line, 2 );
            $area_served[] = trim( $parts[0] );
        }
        $area_served = array_unique( $area_served );
    }

    $schema = [
        '@type'       => 'Service',
        '@id'         => get_permalink( $post_id ) . '#service',
        'name'        => get_the_title( $post_id ),
        'url'         => get_permalink( $post_id ),
        'provider'    => [ '@id' => home_url( '/' ) . '#organization' ],
        'serviceType' => get_the_title( $post_id ),
    ];

    if ( $description )  $schema['description'] = $description;
    if ( $area_served )  $schema['areaServed']  = array_values( $area_served );
    if ( $image_url )    $schema['image']        = $image_url;

    return $schema;
}
