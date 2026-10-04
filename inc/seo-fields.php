<?php
/**
 * SEO Fields — Meta box con campos SEO Title y SEO Description.
 * Se muestra en el editor de páginas, posts y servicios.
 * No activo si hay Rank Math o Yoast SEO instalados.
 *
 * @package Blue Vision Tech
 */

defined( 'ABSPATH' ) || exit;

if ( defined( 'RANK_MATH_VERSION' ) || defined( 'WPSEO_VERSION' ) ) {
    return;
}

/* ------------------------------------------------------------------ */
/*  Registrar metas                                                    */
/* ------------------------------------------------------------------ */
add_action( 'init', function () {
    $post_types = [ 'page', 'post', 'servicios' ];
    $shared = [
        'show_in_rest'      => true,
        'single'            => true,
        'type'              => 'string',
        'auth_callback'     => fn() => current_user_can( 'edit_posts' ),
    ];

    foreach ( $post_types as $pt ) {
        register_post_meta( $pt, '_bvt_seo_title', array_merge( $shared, [
            'sanitize_callback' => 'sanitize_text_field',
            'description'       => 'SEO Title personalizado',
        ] ) );
        register_post_meta( $pt, '_bvt_seo_description', array_merge( $shared, [
            'sanitize_callback' => 'sanitize_textarea_field',
            'description'       => 'Meta description personalizada',
        ] ) );
    }
} );

/* ------------------------------------------------------------------ */
/*  Meta box                                                           */
/* ------------------------------------------------------------------ */
add_action( 'add_meta_boxes', function () {
    add_meta_box(
        'bvt-seo-fields',
        'SEO',
        'bvt_seo_fields_render',
        [ 'page', 'post', 'servicios' ],
        'normal',
        'high'
    );
} );

function bvt_seo_fields_render( WP_Post $post ) : void {
    wp_nonce_field( 'bvt_seo_fields_save', 'bvt_seo_fields_nonce' );

    $seo_title       = get_post_meta( $post->ID, '_bvt_seo_title',       true );
    $seo_description = get_post_meta( $post->ID, '_bvt_seo_description', true );

    $title_len = mb_strlen( $seo_title );
    $desc_len  = mb_strlen( $seo_description );
    ?>
    <style>
        .bvt-seo-field { margin-bottom: 16px; }
        .bvt-seo-field label { display: block; font-weight: 600; margin-bottom: 4px; font-size: 13px; }
        .bvt-seo-field input,
        .bvt-seo-field textarea { width: 100%; box-sizing: border-box; }
        .bvt-seo-field textarea { height: 80px; resize: vertical; }
        .bvt-seo-counter { font-size: 11px; color: #888; margin-top: 3px; }
        .bvt-seo-counter.warn { color: #d63638; }
        .bvt-seo-hint { font-size: 11px; color: #888; margin-top: 6px; }
    </style>

    <div class="bvt-seo-field">
        <label for="bvt_seo_title">SEO Title</label>
        <input
            type="text"
            id="bvt_seo_title"
            name="bvt_seo_title"
            value="<?php echo esc_attr( $seo_title ); ?>"
            placeholder="Déjalo vacío para usar el título por defecto"
            maxlength="120"
        >
        <div class="bvt-seo-counter <?php echo $title_len > 70 ? 'warn' : ''; ?>">
            <?php echo esc_html( $title_len ); ?> caracteres
            <?php if ( $title_len > 0 && $title_len <= 70 ) echo '✓ buen largo'; ?>
            <?php if ( $title_len > 70 ) echo '⚠ recomendado: máx 70'; ?>
        </div>
    </div>

    <div class="bvt-seo-field">
        <label for="bvt_seo_description">Meta Description</label>
        <textarea
            id="bvt_seo_description"
            name="bvt_seo_description"
            placeholder="Déjalo vacío para usar el extracto o el inicio del contenido"
            maxlength="320"
        ><?php echo esc_textarea( $seo_description ); ?></textarea>
        <div class="bvt-seo-counter <?php echo $desc_len > 160 ? 'warn' : ''; ?>">
            <?php echo esc_html( $desc_len ); ?> caracteres
            <?php if ( $desc_len > 0 && $desc_len <= 160 ) echo '✓ buen largo'; ?>
            <?php if ( $desc_len > 160 ) echo '⚠ recomendado: máx 160'; ?>
        </div>
    </div>

    <p class="bvt-seo-hint">
        Estos campos sobreescriben el título y descripción generados automáticamente
        en la etiqueta &lt;title&gt;, Open Graph y Twitter Card.
    </p>
    <?php
}

/* ------------------------------------------------------------------ */
/*  Guardar metas                                                      */
/* ------------------------------------------------------------------ */
add_action( 'save_post', function ( int $post_id ) : void {
    // Verificaciones de seguridad
    if ( ! isset( $_POST['bvt_seo_fields_nonce'] ) ) return;
    if ( ! wp_verify_nonce( $_POST['bvt_seo_fields_nonce'], 'bvt_seo_fields_save' ) ) return;
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
    if ( ! current_user_can( 'edit_post', $post_id ) ) return;

    // SEO Title
    if ( isset( $_POST['bvt_seo_title'] ) ) {
        $value = sanitize_text_field( wp_unslash( $_POST['bvt_seo_title'] ) );
        if ( $value ) {
            update_post_meta( $post_id, '_bvt_seo_title', $value );
        } else {
            delete_post_meta( $post_id, '_bvt_seo_title' );
        }
    }

    // SEO Description
    if ( isset( $_POST['bvt_seo_description'] ) ) {
        $value = sanitize_textarea_field( wp_unslash( $_POST['bvt_seo_description'] ) );
        if ( $value ) {
            update_post_meta( $post_id, '_bvt_seo_description', $value );
        } else {
            delete_post_meta( $post_id, '_bvt_seo_description' );
        }
    }
} );

// El campo SEO Title solo afecta og:title y twitter:title (ver seo.php).
// El <title> del documento lo genera WordPress con el título normal de la página.
