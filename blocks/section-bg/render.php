<?php
/**
 * Bloque Sección con fondo — AGO
 * Contenedor a ancho completo: solo pinta el fondo (color de la paleta y/o
 * imagen) detrás de los bloques hijos ($content). Opcional: imágenes
 * decorativas pegadas a los bordes izq./der., centradas verticalmente
 * respecto a TODA la sección (ej. grecas de "Cómo ser un asociado"). Los hijos conservan su
 * propio contenedor/max-width; entre hijos se colapsa el relleno duplicado.
 *
 * @var array  $attributes
 * @var string $content
 * @package AGO
 */

defined( 'ABSPATH' ) || exit;

if ( ! trim( $content ) ) {
    return;
}

$ago_sb_bg  = in_array( $attributes['bg'] ?? 'light', [ 'white', 'light' ], true ) ? $attributes['bg'] : 'light';
$ago_sb_img = (int) ( $attributes['bgImageId'] ?? 0 );
$ago_sb_decos = [
    'left'  => (int) ( $attributes['decoLeftId'] ?? 0 ),
    'right' => (int) ( $attributes['decoRightId'] ?? 0 ),
];
?>
<section <?php echo get_block_wrapper_attributes( [ 'class' => 'section-bg section-bg--' . $ago_sb_bg ] ); ?>>
    <?php if ( $ago_sb_img ) : ?>
        <?php echo wp_get_attachment_image( $ago_sb_img, 'full', false, [
            'class' => 'section-bg__img', 'alt' => '', 'aria-hidden' => 'true', 'loading' => 'lazy',
        ] ); ?>
    <?php endif; ?>
    <?php foreach ( $ago_sb_decos as $ago_sb_side => $ago_sb_deco_id ) : ?>
        <?php if ( $ago_sb_deco_id ) : ?>
            <?php echo wp_get_attachment_image( $ago_sb_deco_id, 'large', false, [
                'class' => 'section-bg__deco section-bg__deco--' . $ago_sb_side, 'alt' => '', 'aria-hidden' => 'true', 'loading' => 'lazy',
            ] ); ?>
        <?php endif; ?>
    <?php endforeach; ?>
    <div class="section-bg__inner">
        <?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput -- HTML de bloques hijos ya renderizado por WP. ?>
    </div>
</section>
