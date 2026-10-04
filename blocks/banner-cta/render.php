<?php
/**
 * Bloque Banner CTA — AGO
 * Fondo de lado a lado (sin max-width, sin degradado: la imagen ya trae su color).
 * Título fino + negrita, contenido con negritas/enlaces y botón.
 *
 * @var array $attributes
 * @package AGO
 */

defined( 'ABSPATH' ) || exit;

$ago_bc = wp_parse_args( $attributes, [
    'bgId'       => 0,
    'titleLight' => '',
    'titleBold'  => '',
    'content'    => '',
    'btnText'    => '',
    'btnUrl'     => '',
    'btnVariant' => 'light',
] );
?>
<section <?php echo get_block_wrapper_attributes( [ 'class' => 'banner-cta' ] ); ?>>

    <?php if ( $ago_bc['bgId'] ) : ?>
        <?php echo wp_get_attachment_image( (int) $ago_bc['bgId'], 'full', false, [
            'class'   => 'banner-cta__bg',
            'alt'     => '',
            'loading' => 'lazy',
        ] ); ?>
    <?php endif; ?>

    <div class="banner-cta__inner">

        <?php if ( $ago_bc['titleLight'] || $ago_bc['titleBold'] ) : ?>
            <h2 class="banner-cta__title">
                <?php if ( $ago_bc['titleLight'] ) : ?>
                    <span class="banner-cta__title-light"><?php echo esc_html( $ago_bc['titleLight'] ); ?></span>
                <?php endif; ?>
                <?php if ( $ago_bc['titleBold'] ) : ?>
                    <span class="banner-cta__title-bold"><?php echo esc_html( $ago_bc['titleBold'] ); ?></span>
                <?php endif; ?>
            </h2>
        <?php endif; ?>

        <?php if ( $ago_bc['content'] ) : ?>
            <p class="banner-cta__text"><?php echo wp_kses_post( $ago_bc['content'] ); ?></p>
        <?php endif; ?>

        <?php
        get_template_part( 'template-parts/atoms/button', null, [
            'text'    => $ago_bc['btnText'],
            'url'     => $ago_bc['btnUrl'],
            'variant' => $ago_bc['btnVariant'],
            'class'   => 'banner-cta__cta',
        ] );
        ?>

    </div>
</section>
