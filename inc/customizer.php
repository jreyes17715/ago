<?php
/**
 * Customizer — AGO
 * Registra las opciones editables del footer global (fijo en todas las
 * páginas — no es un bloque de Gutenberg).
 *
 * @package AGO
 */

defined( 'ABSPATH' ) || exit;

/**
 * Tags/atributos permitidos para los campos de texto "enriquecido" del
 * footer (título CTA, texto de "únete al equipo"). Mismo criterio que el
 * formato "acento" de los bloques: span/em con clase y estilo inline para
 * cursiva + color. El admin puede envolver una palabra así para lograr el
 * mismo efecto que el RichText de los bloques, por ejemplo:
 * <em style="font-style:italic;font-family:'Instrument Serif',serif;color:#6B3DFE;">palabra</em>
 *
 * Se usa tanto al guardar (sanitize_callback) como al imprimir en footer.php.
 */
function dd_footer_allowed_richtext() {
    return [
        'span'   => [ 'class' => true, 'style' => true ],
        'strong' => [],
        'em'     => [ 'class' => true, 'style' => true ],
        'br'     => [],
    ];
}

/**
 * Sanitiza los campos de texto enriquecido del footer preservando
 * span/em/style (a diferencia de wp_kses_post, que por defecto quita
 * el atributo style).
 */
function dd_sanitize_footer_richtext( $value ) {
    return wp_kses( $value, dd_footer_allowed_richtext() );
}

/**
 * Fuente única de verdad para los valores por defecto del footer.
 * La usan TANTO add_setting() (para que el Personalizador arranque con
 * estos valores) COMO footer.php al leer get_theme_mod() — así nunca se
 * desincronizan (antes footer.php tenía su propio '' como fallback y el
 * footer se veía vacío en el sitio real hasta que se guardaba algo en
 * cada campo del Personalizador).
 */
function dd_footer_defaults() {
    $accent = 'font-style:italic;font-family:\'Instrument Serif\',serif;color:#6B3DFE;';

    return [
        'dd_footer_cta_title'        => '¿Estás listo para <em style="' . $accent . '">empoderar</em> tu visión y hacer <em style="' . $accent . '">crecer</em> tu empresa?',
        'dd_footer_cta_button_text'  => 'Cotiza tu servicio',
        'dd_footer_cta_button_url'   => '#',

        'dd_footer_logo'             => 0,
        'dd_footer_background'       => 0,
        'dd_footer_copyright_text'   => '©DimensiónDUAL.',
        'dd_footer_copyright_sub'    => 'Todos los derechos reservados.',

        'dd_footer_phone'            => '',
        'dd_footer_email'            => '',
        'dd_footer_address'          => '2a. Calle 7-79 zona 9, Edificio Nova Reformador, Oficina 209, Segundo Nivel, Ciudad de Guatemala, Guatemala.',
        'dd_footer_whatsapp_url'     => '',
        'dd_footer_linkedin_url'     => '',
        'dd_footer_instagram_url'    => '',

        'dd_footer_join_text'        => '¿Quieres formar parte de <em style="' . $accent . '">nuestro</em> equipo?',
        'dd_footer_join_button_text' => 'Llenar formulario',
        'dd_footer_join_button_url'  => '#',
    ];
}

/**
 * Valores por defecto del CTA del menú mobile (header). El teléfono, correo
 * y redes del panel mobile reutilizan los mismos theme_mods del footer
 * (dd_footer_phone, dd_footer_whatsapp_url, etc.) — no hace falta
 * duplicarlos aquí, es la misma información de contacto del sitio.
 */
/**
 * Sanitiza un ID de imagen para fondos CSS (background-image), rechazando
 * SVG. Un SVG sin viewBox/tamaño intrinseco definido hace que el navegador
 * le invente un "tamaño natural" (habitualmente 300x150) y background-size:
 * cover lo estira/recorta de forma impredecible — a veces con una franja de
 * color solido donde el navegador rellena. Los iconos SVG del sitio siguen
 * funcionando igual en todos lados; esto solo aplica a fondos.
 */
function dd_sanitize_raster_image_id( $value ) {
    $value = absint( $value );

    if ( ! $value ) {
        return 0;
    }

    if ( 'image/svg+xml' === get_post_mime_type( $value ) ) {
        return 0;
    }

    return $value;
}

function dd_header_defaults() {
    return [
        'dd_header_cta_text'   => 'Agendar asesoría',
        'dd_header_cta_url'    => '#',
        'dd_header_logo_dark'  => 0,
    ];
}

/**
 * Valores por defecto de la plantilla single.php (detalle de un post del
 * blog). Los 2 fondos son opcionales/decorativos — sin nada subido, la
 * seccion usa el fondo blanco por defecto. Las palabras del Marquee
 * reusan el mismo bloque de Gutenberg (ago/marquee) via
 * do_blocks(), asi que aceptan el mismo formato separado por coma.
 */
function dd_single_defaults() {
    return [
        'dd_single_header_background'  => 0,
        'dd_single_related_background' => 0,
        'dd_single_marquee_words'      => 'Branding, Estrategia, Diseño, Consultoría creativa, Growth, Innovación',
    ];
}

add_action( 'customize_register', function ( WP_Customize_Manager $wp_customize ) {

    $d = dd_footer_defaults();

    /* ── Panel general del footer ────────────────────────────────────── */
    $wp_customize->add_panel( 'dd_footer_panel', [
        'title'    => __( 'Footer (global)', 'ago' ),
        'priority' => 51,
    ] );

    /* ── Sección 1 — Fila de CTA (título + botón "Cotiza tu servicio") ── */
    $wp_customize->add_section( 'dd_footer_cta', [
        'title'    => __( 'CTA principal', 'ago' ),
        'panel'    => 'dd_footer_panel',
        'priority' => 10,
    ] );

    $wp_customize->add_setting( 'dd_footer_cta_title', [
        'default'           => $d['dd_footer_cta_title'],
        'sanitize_callback' => 'dd_sanitize_footer_richtext',
    ] );
    $wp_customize->add_control( 'dd_footer_cta_title', [
        'label'       => __( 'Título', 'ago' ),
        'description' => __( 'Admite HTML: envolvé una palabra en <em style="color:#6B3DFE;">palabra</em> para ponerla en cursiva/color, igual que el acento de los bloques.', 'ago' ),
        'section'     => 'dd_footer_cta',
        'type'        => 'textarea',
    ] );

    $wp_customize->add_setting( 'dd_footer_cta_button_text', [
        'default'           => $d['dd_footer_cta_button_text'],
        'sanitize_callback' => 'sanitize_text_field',
    ] );
    $wp_customize->add_control( 'dd_footer_cta_button_text', [
        'label'   => __( 'Texto del botón', 'ago' ),
        'section' => 'dd_footer_cta',
        'type'    => 'text',
    ] );

    $wp_customize->add_setting( 'dd_footer_cta_button_url', [
        'default'           => $d['dd_footer_cta_button_url'],
        'sanitize_callback' => 'esc_url_raw',
    ] );
    $wp_customize->add_control( 'dd_footer_cta_button_url', [
        'label'   => __( 'Enlace del botón', 'ago' ),
        'section' => 'dd_footer_cta',
        'type'    => 'url',
    ] );

    /* ── Sección 2 — Marca, contacto y redes ─────────────────────────── */
    $wp_customize->add_section( 'dd_footer_brand', [
        'title'    => __( 'Marca, contacto y redes', 'ago' ),
        'panel'    => 'dd_footer_panel',
        'priority' => 20,
    ] );

    $wp_customize->add_setting( 'dd_footer_logo', [
        'default'           => $d['dd_footer_logo'],
        'sanitize_callback' => 'absint',
    ] );
    $wp_customize->add_control( new WP_Customize_Media_Control( $wp_customize, 'dd_footer_logo', [
        'label'       => __( 'Logo circular del footer', 'ago' ),
        'description' => __( 'Imagen ya armada (isotipo + texto en círculo), tal como se ve en el diseño.', 'ago' ),
        'section'     => 'dd_footer_brand',
        'mime_type'   => 'image',
    ] ) );

    $wp_customize->add_setting( 'dd_footer_background', [
        'default'           => $d['dd_footer_background'],
        'sanitize_callback' => 'dd_sanitize_raster_image_id',
    ] );
    $wp_customize->add_control( new WP_Customize_Media_Control( $wp_customize, 'dd_footer_background', [
        'label'       => __( 'Imagen de fondo (opcional)', 'ago' ),
        'description' => __( 'Usá una foto (JPG/PNG/WEBP), no un SVG — un SVG de fondo puede verse estirado o con una franja de color rara. Si no subís nada, se usa el degradado morado/negro por defecto.', 'ago' ),
        'section'     => 'dd_footer_brand',
        'mime_type'   => 'image',
    ] ) );

    $wp_customize->add_setting( 'dd_footer_copyright_text', [
        'default'           => $d['dd_footer_copyright_text'],
        'sanitize_callback' => 'sanitize_text_field',
    ] );
    $wp_customize->add_control( 'dd_footer_copyright_text', [
        'label'   => __( 'Copyright — línea 1', 'ago' ),
        'section' => 'dd_footer_brand',
        'type'    => 'text',
    ] );

    $wp_customize->add_setting( 'dd_footer_copyright_sub', [
        'default'           => $d['dd_footer_copyright_sub'],
        'sanitize_callback' => 'sanitize_text_field',
    ] );
    $wp_customize->add_control( 'dd_footer_copyright_sub', [
        'label'   => __( 'Copyright — línea 2', 'ago' ),
        'section' => 'dd_footer_brand',
        'type'    => 'text',
    ] );

    $wp_customize->add_setting( 'dd_footer_phone', [
        'default'           => $d['dd_footer_phone'],
        'sanitize_callback' => 'sanitize_text_field',
    ] );
    $wp_customize->add_control( 'dd_footer_phone', [
        'label'   => __( 'Teléfono', 'ago' ),
        'section' => 'dd_footer_brand',
        'type'    => 'text',
    ] );

    $wp_customize->add_setting( 'dd_footer_email', [
        'default'           => $d['dd_footer_email'],
        'sanitize_callback' => 'sanitize_email',
    ] );
    $wp_customize->add_control( 'dd_footer_email', [
        'label'   => __( 'Correo', 'ago' ),
        'section' => 'dd_footer_brand',
        'type'    => 'email',
    ] );

    $wp_customize->add_setting( 'dd_footer_address', [
        'default'           => $d['dd_footer_address'],
        'sanitize_callback' => 'sanitize_textarea_field',
    ] );
    $wp_customize->add_control( 'dd_footer_address', [
        'label'   => __( 'Dirección', 'ago' ),
        'section' => 'dd_footer_brand',
        'type'    => 'textarea',
    ] );

    $wp_customize->add_setting( 'dd_footer_whatsapp_url', [
        'default'           => $d['dd_footer_whatsapp_url'],
        'sanitize_callback' => 'esc_url_raw',
    ] );
    $wp_customize->add_control( 'dd_footer_whatsapp_url', [
        'label'   => __( 'Enlace de WhatsApp Business', 'ago' ),
        'section' => 'dd_footer_brand',
        'type'    => 'url',
    ] );

    $wp_customize->add_setting( 'dd_footer_linkedin_url', [
        'default'           => $d['dd_footer_linkedin_url'],
        'sanitize_callback' => 'esc_url_raw',
    ] );
    $wp_customize->add_control( 'dd_footer_linkedin_url', [
        'label'   => __( 'Enlace de LinkedIn', 'ago' ),
        'section' => 'dd_footer_brand',
        'type'    => 'url',
    ] );

    $wp_customize->add_setting( 'dd_footer_instagram_url', [
        'default'           => $d['dd_footer_instagram_url'],
        'sanitize_callback' => 'esc_url_raw',
    ] );
    $wp_customize->add_control( 'dd_footer_instagram_url', [
        'label'   => __( 'Enlace de Instagram', 'ago' ),
        'section' => 'dd_footer_brand',
        'type'    => 'url',
    ] );

    /* ── Sección 3 — "Únete a nuestro equipo" ────────────────────────── */
    $wp_customize->add_section( 'dd_footer_join', [
        'title'    => __( 'Únete a nuestro equipo', 'ago' ),
        'panel'    => 'dd_footer_panel',
        'priority' => 30,
    ] );

    $wp_customize->add_setting( 'dd_footer_join_text', [
        'default'           => $d['dd_footer_join_text'],
        'sanitize_callback' => 'dd_sanitize_footer_richtext',
    ] );
    $wp_customize->add_control( 'dd_footer_join_text', [
        'label'       => __( 'Texto', 'ago' ),
        'description' => __( 'Admite HTML igual que el título del CTA principal.', 'ago' ),
        'section'     => 'dd_footer_join',
        'type'        => 'textarea',
    ] );

    $wp_customize->add_setting( 'dd_footer_join_button_text', [
        'default'           => $d['dd_footer_join_button_text'],
        'sanitize_callback' => 'sanitize_text_field',
    ] );
    $wp_customize->add_control( 'dd_footer_join_button_text', [
        'label'   => __( 'Texto del botón', 'ago' ),
        'section' => 'dd_footer_join',
        'type'    => 'text',
    ] );

    $wp_customize->add_setting( 'dd_footer_join_button_url', [
        'default'           => $d['dd_footer_join_button_url'],
        'sanitize_callback' => 'esc_url_raw',
    ] );
    $wp_customize->add_control( 'dd_footer_join_button_url', [
        'label'   => __( 'Enlace del botón', 'ago' ),
        'section' => 'dd_footer_join',
        'type'    => 'url',
    ] );

    /* ── Header — Logo variante oscura ───────────────────────────────── */
    $hd = dd_header_defaults();

    $wp_customize->add_section( 'dd_header_logo', [
        'title'    => __( 'Header — Logo', 'ago' ),
        'priority' => 51,
    ] );

    $wp_customize->add_setting( 'dd_header_logo_dark', [
        'default'           => $hd['dd_header_logo_dark'],
        'sanitize_callback' => 'absint',
    ] );
    $wp_customize->add_control( new WP_Customize_Media_Control( $wp_customize, 'dd_header_logo_dark', [
        'label'       => __( 'Logo — versión negra', 'ago' ),
        'description' => __( 'Se usa en páginas con header claro (fondo blanco, ej. Sobre Nosotros). En páginas con el bloque Hero se sigue usando el logo normal (Identidad del sitio → Personalizar → Identidad).', 'ago' ),
        'section'     => 'dd_header_logo',
        'mime_type'   => 'image',
    ] ) );

    /* ── Header — CTA del menú mobile ─────────────────────────────────── */
    $wp_customize->add_section( 'dd_header_mobile', [
        'title'    => __( 'Header — Menú mobile', 'ago' ),
        'priority' => 52,
    ] );

    $wp_customize->add_setting( 'dd_header_cta_text', [
        'default'           => $hd['dd_header_cta_text'],
        'sanitize_callback' => 'sanitize_text_field',
    ] );
    $wp_customize->add_control( 'dd_header_cta_text', [
        'label'       => __( 'Texto del botón', 'ago' ),
        'description' => __( 'El teléfono, correo y redes del panel mobile se toman de la sección "Marca, contacto y redes" del footer — es la misma información de contacto del sitio.', 'ago' ),
        'section'     => 'dd_header_mobile',
        'type'        => 'text',
    ] );

    $wp_customize->add_setting( 'dd_header_cta_url', [
        'default'           => $hd['dd_header_cta_url'],
        'sanitize_callback' => 'esc_url_raw',
    ] );
    $wp_customize->add_control( 'dd_header_cta_url', [
        'label'   => __( 'Enlace del botón', 'ago' ),
        'section' => 'dd_header_mobile',
        'type'    => 'url',
    ] );

    /* ── Blog — Detalle del post (single.php) ────────────────────────── */
    $sd = dd_single_defaults();

    $wp_customize->add_section( 'dd_single_post', [
        'title'    => __( 'Blog — Detalle del post', 'ago' ),
        'priority' => 53,
    ] );

    $wp_customize->add_setting( 'dd_single_header_background', [
        'default'           => $sd['dd_single_header_background'],
        'sanitize_callback' => 'dd_sanitize_raster_image_id',
    ] );
    $wp_customize->add_control( new WP_Customize_Media_Control( $wp_customize, 'dd_single_header_background', [
        'label'       => __( 'Fondo del encabezado (opcional)', 'ago' ),
        'description' => __( 'Grafico decorativo, esquina superior derecha. Sin nada subido, se ve blanco liso.', 'ago' ),
        'section'     => 'dd_single_post',
        'mime_type'   => 'image',
    ] ) );

    $wp_customize->add_setting( 'dd_single_related_background', [
        'default'           => $sd['dd_single_related_background'],
        'sanitize_callback' => 'dd_sanitize_raster_image_id',
    ] );
    $wp_customize->add_control( new WP_Customize_Media_Control( $wp_customize, 'dd_single_related_background', [
        'label'       => __( 'Fondo de "Blogs Relacionados" (opcional)', 'ago' ),
        'description' => __( 'Grafico decorativo, apoyado abajo (igual criterio que el fondo del footer).', 'ago' ),
        'section'     => 'dd_single_post',
        'mime_type'   => 'image',
    ] ) );

    $wp_customize->add_setting( 'dd_single_marquee_words', [
        'default'           => $sd['dd_single_marquee_words'],
        'sanitize_callback' => 'sanitize_text_field',
    ] );
    $wp_customize->add_control( 'dd_single_marquee_words', [
        'label'       => __( 'Marquee — palabras', 'ago' ),
        'description' => __( 'Separadas por coma. Mismo bloque "Marquee de palabras" que se usa en otras páginas.', 'ago' ),
        'section'     => 'dd_single_post',
        'type'        => 'text',
    ] );
} );
