<?php
/**
 * Bloque Hero — AGO
 * Solo markup: fondo + pre-título, título, contenido, fecha y botón.
 * El header (logo + menú) NO es parte del bloque: vive en header.php y flota encima.
 *
 * @var array $attributes
 * @package AGO
 */

defined( 'ABSPATH' ) || exit;

$ago_hero = wp_parse_args( $attributes, [
    'bgId'     => 0,
    'pretitle' => '',
    'title'    => '',
    'content'  => '',
    'date'     => '',
    'btnText'  => '',
    'btnUrl'   => '',
] );
?>
<section <?php echo get_block_wrapper_attributes( [ 'class' => 'hero' ] ); ?>>

    <?php if ( $ago_hero['bgId'] ) : ?>
        <?php echo wp_get_attachment_image( (int) $ago_hero['bgId'], 'full', false, [
            'class'         => 'hero__bg',
            'alt'           => '',
            'loading'       => 'eager',
            'fetchpriority' => 'high',
        ] ); ?>
    <?php endif; ?>

    <div class="hero__inner">
        <div class="hero__content">

            <?php if ( $ago_hero['pretitle'] ) : ?>
                <p class="hero__pretitle"><?php echo esc_html( $ago_hero['pretitle'] ); ?></p>
            <?php endif; ?>

            <?php if ( $ago_hero['title'] ) : ?>
                <h1 class="hero__title"><?php echo esc_html( $ago_hero['title'] ); ?></h1>
            <?php endif; ?>

            <?php if ( $ago_hero['content'] ) : ?>
                <p class="hero__text"><?php echo nl2br( esc_html( $ago_hero['content'] ) ); ?></p>
            <?php endif; ?>

            <?php if ( $ago_hero['date'] ) : ?>
                <p class="hero__date"><?php echo esc_html( $ago_hero['date'] ); ?></p>
            <?php endif; ?>

            <?php
            get_template_part( 'template-parts/atoms/button', null, [
                'text'    => $ago_hero['btnText'],
                'url'     => $ago_hero['btnUrl'],
                'variant' => 'light',
                'class'   => 'hero__cta',
            ] );
            ?>

        </div>
    </div>

</section>
