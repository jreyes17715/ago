<?php
/**
 * Bloque Encabezado de sección — AGO
 *
 * variant = side   → título + contenido a la izquierda (alineados al contenedor),
 *                    imagen decorativa a la derecha pegada al borde de la pantalla.
 * variant = center → contenido centrado, imagen a la izquierda y a la derecha
 *                    pegadas a los bordes de la pantalla.
 * El contenido viene de InnerBlocks ($content): párrafos, listas, etc.
 *
 * @var array  $attributes
 * @var string $content
 * @package AGO
 */

defined( 'ABSPATH' ) || exit;

$ago_sh = wp_parse_args( $attributes, [
    'variant'      => 'side',
    'titleLight'   => '',
    'titleBold'    => '',
    'titleStacked' => false,
    'titleSize'    => 'xl',
    'imageLeftId'  => 0,
    'imageRightId' => 0,
] );

$ago_sh_variant = 'center' === $ago_sh['variant'] ? 'center' : 'side';
?>
<section <?php echo get_block_wrapper_attributes( [ 'class' => 'section-header section-header--' . $ago_sh_variant ] ); ?>>

    <?php if ( 'center' === $ago_sh_variant && $ago_sh['imageLeftId'] ) : ?>
        <?php echo wp_get_attachment_image( (int) $ago_sh['imageLeftId'], 'large', false, [
            'class' => 'section-header__img section-header__img--left', 'alt' => '', 'aria-hidden' => 'true',
        ] ); ?>
    <?php endif; ?>

    <?php if ( $ago_sh['imageRightId'] ) : ?>
        <?php echo wp_get_attachment_image( (int) $ago_sh['imageRightId'], 'large', false, [
            'class' => 'section-header__img section-header__img--right', 'alt' => '', 'aria-hidden' => 'true',
        ] ); ?>
    <?php endif; ?>

    <div class="section-header__inner">
        <div class="section-header__body">
            <?php
            get_template_part( 'template-parts/atoms/title', null, [
                'light'   => $ago_sh['titleLight'],
                'bold'    => $ago_sh['titleBold'],
                'stacked' => (bool) $ago_sh['titleStacked'],
                'size'    => $ago_sh['titleSize'],
                'class'   => 'section-header__title',
            ] );
            ?>

            <?php if ( trim( wp_strip_all_tags( $content ) ) ) : ?>
                <div class="section-header__content">
                    <?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput -- HTML de bloques core ya filtrado por WP. ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

</section>
