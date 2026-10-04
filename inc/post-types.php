<?php
/**
 * Custom Post Type — Servicios (AGO)
 * Fuente unica de verdad para los servicios de la empresa: alimenta el
 * bloque "Servicios" (tarjeta cerrada = resumen, tarjeta abierta = detalle
 * completo, ver blocks/services/render.php).
 *
 * Nota: el post type "servicios" ya estaba referenciado (pero nunca
 * registrado) en inc/schema.php, inc/seo.php, inc/seo-fields.php y
 * template-parts/sidebar-servicios.php — herencia del tema BVT anterior.
 * Se usa ese mismo slug aqui para que ese codigo (breadcrumbs, SEO, sidebar
 * de servicios) empiece a funcionar de una vez, en lugar de crear un CPT
 * paralelo con otro nombre.
 *
 * Los campos (icono, texto corto de card, CTA) se guardan con un metabox
 * clasico (add_meta_box + save_post), NO con el panel del sidebar de
 * Gutenberg vía REST — se probaron 2 variantes de ese enfoque y el icono
 * SVG no terminaba de guardarse de forma confiable. El metabox clasico usa
 * el mismo <form> que ya envia "Guardar"/"Publicar", sin pasar por REST/
 * sincronizacion de entidades, y es el mismo patron 100% probado que ya
 * usaba este tema para la imagen de card antes de este cambio.
 *
 * @package AGO
 */

defined( 'ABSPATH' ) || exit;

add_action( 'init', function () {
    register_post_type( 'servicios', [
        'labels' => [
            'name'               => __( 'Servicios', 'ago' ),
            'singular_name'      => __( 'Servicio', 'ago' ),
            'add_new'            => __( 'Agregar servicio', 'ago' ),
            'add_new_item'       => __( 'Agregar servicio', 'ago' ),
            'edit_item'          => __( 'Editar servicio', 'ago' ),
            'new_item'           => __( 'Nuevo servicio', 'ago' ),
            'view_item'          => __( 'Ver servicio', 'ago' ),
            'search_items'       => __( 'Buscar servicios', 'ago' ),
            'not_found'          => __( 'No se encontraron servicios', 'ago' ),
            'all_items'          => __( 'Servicios', 'ago' ),
            'menu_name'          => __( 'Servicios', 'ago' ),
        ],
        'public'       => true,
        'show_in_rest' => true,
        'has_archive'  => true,
        'rewrite'      => [ 'slug' => 'servicios' ],
        'menu_position' => 21,
        'menu_icon'    => 'dashicons-hammer',
        // "page-attributes" habilita el campo "Orden" — controla el orden de
        // las tarjetas y la numeracion (01),(02)... + color alterno al abrir.
        'supports'     => [ 'title', 'editor', 'thumbnail', 'page-attributes' ],
    ] );
} );

/* ------------------------------------------------------------------ */
/*  Metabox "Datos del servicio"                                      */
/* ------------------------------------------------------------------ */
add_action( 'add_meta_boxes', function () {
    add_meta_box(
        'dd-servicio-datos',
        __( 'Datos del servicio', 'ago' ),
        'dd_servicio_datos_render',
        'servicios',
        'side',
        'default'
    );
} );

function dd_servicio_datos_render( WP_Post $post ) {
    wp_nonce_field( 'dd_servicio_datos_save', 'dd_servicio_datos_nonce' );

    $icon_id   = (int) get_post_meta( $post->ID, 'dd_icon_id', true );
    $icon_url  = $icon_id ? wp_get_attachment_image_url( $icon_id, 'medium' ) : '';
    $card_text = get_post_meta( $post->ID, 'dd_card_text', true );
    $cta_text  = get_post_meta( $post->ID, 'dd_cta_text', true );
    $cta_url   = get_post_meta( $post->ID, 'dd_cta_url', true );
    ?>
    <style>
        #dd-icon-preview { max-width: 64px; display:<?php echo $icon_url ? 'block' : 'none'; ?>; margin-bottom:8px; }
        #dd-icon-remove  { display:<?php echo $icon_url ? 'inline-block' : 'none'; ?>; color:#d63638; cursor:pointer; font-size:12px; background:none; border:none; padding:0; margin:6px 0 0; }
        .dd-servicio-field { margin-bottom: 14px; }
        .dd-servicio-field label { display:block; font-weight:600; font-size:12px; margin-bottom:4px; }
        .dd-servicio-field textarea,
        .dd-servicio-field input[type="text"],
        .dd-servicio-field input[type="url"] { width:100%; }
    </style>

    <div class="dd-servicio-field">
        <label><?php esc_html_e( 'Ícono (SVG)', 'ago' ); ?></label>
        <input type="hidden" id="dd_icon_id" name="dd_icon_id" value="<?php echo esc_attr( $icon_id ?: '' ); ?>">
        <img id="dd-icon-preview" src="<?php echo esc_url( $icon_url ); ?>" alt="">
        <button type="button" id="dd-icon-select" class="button button-secondary" style="width:100%;">
            <?php echo $icon_url ? esc_html__( 'Cambiar ícono', 'ago' ) : esc_html__( 'Seleccionar SVG', 'ago' ); ?>
        </button>
        <button type="button" id="dd-icon-remove"><?php esc_html_e( 'Quitar ícono', 'ago' ); ?></button>
    </div>

    <div class="dd-servicio-field">
        <label for="dd_card_text"><?php esc_html_e( 'Texto corto (tarjeta cerrada)', 'ago' ); ?></label>
        <textarea id="dd_card_text" name="dd_card_text" rows="3"><?php echo esc_textarea( $card_text ); ?></textarea>
        <p style="font-size:11px;color:#888;margin:4px 0 0;">
            <?php esc_html_e( 'Se muestra en la tarjeta antes de abrirla. Si lo dejás vacío, se usa el extracto del post.', 'ago' ); ?>
        </p>
    </div>

    <div class="dd-servicio-field">
        <label for="dd_cta_text"><?php esc_html_e( 'Texto del botón (CTA, tarjeta abierta)', 'ago' ); ?></label>
        <input type="text" id="dd_cta_text" name="dd_cta_text" value="<?php echo esc_attr( $cta_text ); ?>" placeholder="<?php esc_attr_e( 'Agendar sesión', 'ago' ); ?>">
    </div>

    <div class="dd-servicio-field">
        <label for="dd_cta_url"><?php esc_html_e( 'URL del botón (CTA)', 'ago' ); ?></label>
        <input type="url" id="dd_cta_url" name="dd_cta_url" value="<?php echo esc_attr( $cta_url ); ?>" placeholder="https://…">
    </div>

    <script>
    ( function ( $ ) {
        var frame;
        var $input   = $( '#dd_icon_id' );
        var $preview = $( '#dd-icon-preview' );
        var $select  = $( '#dd-icon-select' );
        var $remove  = $( '#dd-icon-remove' );

        $select.on( 'click', function () {
            if ( frame ) { frame.open(); return; }
            frame = wp.media( { title: '<?php echo esc_js( __( 'Seleccionar SVG', 'ago' ) ); ?>', button: { text: '<?php echo esc_js( __( 'Usar este ícono', 'ago' ) ); ?>' }, multiple: false } );
            frame.on( 'select', function () {
                var attachment = frame.state().get( 'selection' ).first().toJSON();
                $input.val( attachment.id );
                $preview.attr( 'src', ( attachment.sizes && attachment.sizes.medium ) ? attachment.sizes.medium.url : attachment.url ).show();
                $select.text( '<?php echo esc_js( __( 'Cambiar ícono', 'ago' ) ); ?>' );
                $remove.show();
            } );
            frame.open();
        } );

        $remove.on( 'click', function () {
            $input.val( '' );
            $preview.hide().attr( 'src', '' );
            $select.text( '<?php echo esc_js( __( 'Seleccionar SVG', 'ago' ) ); ?>' );
            $remove.hide();
        } );
    } )( jQuery );
    </script>
    <?php
}

add_action( 'save_post_servicios', function ( int $post_id ) {
    if ( ! isset( $_POST['dd_servicio_datos_nonce'] ) ) {
        return;
    }
    if ( ! wp_verify_nonce( $_POST['dd_servicio_datos_nonce'], 'dd_servicio_datos_save' ) ) {
        return;
    }
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }
    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }

    $icon_id = isset( $_POST['dd_icon_id'] ) ? absint( $_POST['dd_icon_id'] ) : 0;
    if ( $icon_id ) {
        update_post_meta( $post_id, 'dd_icon_id', $icon_id );
    } else {
        delete_post_meta( $post_id, 'dd_icon_id' );
    }

    if ( isset( $_POST['dd_card_text'] ) ) {
        update_post_meta( $post_id, 'dd_card_text', sanitize_textarea_field( wp_unslash( $_POST['dd_card_text'] ) ) );
    }

    if ( isset( $_POST['dd_cta_text'] ) ) {
        update_post_meta( $post_id, 'dd_cta_text', sanitize_text_field( wp_unslash( $_POST['dd_cta_text'] ) ) );
    }

    if ( isset( $_POST['dd_cta_url'] ) ) {
        update_post_meta( $post_id, 'dd_cta_url', esc_url_raw( wp_unslash( $_POST['dd_cta_url'] ) ) );
    }
} );
