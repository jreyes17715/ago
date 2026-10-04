<?php
/**
 * Bloque Frase + botones — AGO
 * Frase centrada y hasta dos botones (átomo button). Un botón sin texto no se muestra.
 *
 * @var array $attributes
 * @package AGO
 */

defined( 'ABSPATH' ) || exit;

$ago_cb = wp_parse_args( $attributes, [
    'title'       => '',
    'btn1Text'    => '',
    'btn1Url'     => '',
    'btn1Variant' => 'dark',
    'btn2Text'    => '',
    'btn2Url'     => '',
    'btn2Variant' => 'outline',
] );

if ( ! $ago_cb['title'] && ! $ago_cb['btn1Text'] && ! $ago_cb['btn2Text'] ) {
    return;
}
?>
<section <?php echo get_block_wrapper_attributes( [ 'class' => 'cta-buttons' ] ); ?>>
    <div class="cta-buttons__inner">

        <?php if ( $ago_cb['title'] ) : ?>
            <h2 class="cta-buttons__title"><?php echo esc_html( $ago_cb['title'] ); ?></h2>
        <?php endif; ?>

        <?php if ( $ago_cb['btn1Text'] || $ago_cb['btn2Text'] ) : ?>
            <div class="cta-buttons__actions">
                <?php
                foreach ( [ 1, 2 ] as $ago_cb_n ) {
                    get_template_part( 'template-parts/atoms/button', null, [
                        'text'    => $ago_cb[ 'btn' . $ago_cb_n . 'Text' ],
                        'url'     => $ago_cb[ 'btn' . $ago_cb_n . 'Url' ],
                        'variant' => $ago_cb[ 'btn' . $ago_cb_n . 'Variant' ],
                        'class'   => 'cta-buttons__btn',
                    ] );
                }
                ?>
            </div>
        <?php endif; ?>

    </div>
</section>
