<?php
/**
 * Bloque Tarjeta CTA — AGO
 * Tarjeta redondeada DENTRO del contenedor (respeta max-width). Fondo = imagen
 * subida (sin degradado: el color y la figura ya vienen en la imagen).
 * Título (átomo title), contenido opcional y hasta 2 botones (átomo button).
 *
 * @var array $attributes
 * @package AGO
 */

defined( 'ABSPATH' ) || exit;

$ago_cc = wp_parse_args( $attributes, [
    'bgId'         => 0,
    'titleLight'   => '',
    'titleBold'    => '',
    'titleStacked' => true,
    'titleSize'    => 'lg',
    'content'      => '',
    'btn1Text'     => '',
    'btn1Url'      => '',
    'btn1Variant'  => 'light',
    'btn1Icon'     => 'arrow',
    'btn2Text'     => '',
    'btn2Url'      => '',
    'btn2Variant'  => 'light',
    'btn2Icon'     => 'arrow',
] );
?>
<section <?php echo get_block_wrapper_attributes( [ 'class' => 'cta-card' ] ); ?>>
    <div class="cta-card__box">

        <?php if ( $ago_cc['bgId'] ) : ?>
            <?php echo wp_get_attachment_image( (int) $ago_cc['bgId'], 'full', false, [
                'class' => 'cta-card__bg', 'alt' => '', 'loading' => 'lazy',
            ] ); ?>
        <?php endif; ?>

        <div class="cta-card__body">
            <?php
            get_template_part( 'template-parts/atoms/title', null, [
                'light'   => $ago_cc['titleLight'],
                'bold'    => $ago_cc['titleBold'],
                'stacked' => (bool) $ago_cc['titleStacked'],
                'size'    => $ago_cc['titleSize'],
                'class'   => 'cta-card__title',
            ] );
            ?>

            <?php if ( $ago_cc['content'] ) : ?>
                <p class="cta-card__text"><?php echo wp_kses_post( $ago_cc['content'] ); ?></p>
            <?php endif; ?>

            <?php if ( $ago_cc['btn1Text'] || $ago_cc['btn2Text'] ) : ?>
                <div class="cta-card__actions">
                    <?php
                    foreach ( [ 1, 2 ] as $ago_cc_n ) {
                        get_template_part( 'template-parts/atoms/button', null, [
                            'text'    => $ago_cc[ 'btn' . $ago_cc_n . 'Text' ],
                            'url'     => $ago_cc[ 'btn' . $ago_cc_n . 'Url' ],
                            'variant' => $ago_cc[ 'btn' . $ago_cc_n . 'Variant' ],
                            'icon'    => $ago_cc[ 'btn' . $ago_cc_n . 'Icon' ],
                            'class'   => 'cta-card__btn',
                        ] );
                    }
                    ?>
                </div>
            <?php endif; ?>
        </div>

    </div>
</section>
