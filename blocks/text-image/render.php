<?php
/**
 * Bloque Texto + Imagen — AGO
 *
 * Variantes:
 *   layout  = side → título + contenido en una columna, imagen en la otra.
 *   layout  = top  → título centrado arriba; debajo imagen + contenido.
 *   reverse = true → invierte los lados (imagen a la derecha).
 * El contenido viene de InnerBlocks ($content): párrafos, listas (bullets), etc.
 *
 * @var array  $attributes
 * @var string $content
 * @package AGO
 */

defined( 'ABSPATH' ) || exit;

$ago_ti = wp_parse_args( $attributes, [
    'layout'       => 'side',
    'reverse'      => false,
    'titleLight'   => '',
    'titleBold'    => '',
    'titleStacked' => true,
    'imageId'      => 0,
    'image2Id'     => 0,
    'btnText'      => '',
    'btnUrl'       => '',
    'btnVariant'   => 'dark',
] );

$ago_ti_layout  = 'top' === $ago_ti['layout'] ? 'top' : 'side';
$ago_ti_classes = 'text-image text-image--' . $ago_ti_layout . ( $ago_ti['reverse'] ? ' text-image--reverse' : '' );
$ago_ti_has_title = $ago_ti['titleLight'] || $ago_ti['titleBold'];

ob_start();
if ( $ago_ti_has_title ) : ?>
    <h2 class="text-image__title<?php echo $ago_ti['titleStacked'] ? ' text-image__title--stacked' : ''; ?>">
        <?php if ( $ago_ti['titleLight'] ) : ?>
            <span class="text-image__title-light"><?php echo esc_html( $ago_ti['titleLight'] ); ?></span>
        <?php endif; ?>
        <?php if ( $ago_ti['titleBold'] ) : ?>
            <span class="text-image__title-bold"><?php echo esc_html( $ago_ti['titleBold'] ); ?></span>
        <?php endif; ?>
    </h2>
<?php endif;
$ago_ti_title_html = ob_get_clean();
?>
<section <?php echo get_block_wrapper_attributes( [ 'class' => $ago_ti_classes ] ); ?>>
    <div class="text-image__inner">

        <?php if ( 'top' === $ago_ti_layout ) {
            echo $ago_ti_title_html; // phpcs:ignore WordPress.Security.EscapeOutput -- escapado arriba.
        } ?>

        <div class="text-image__grid">

            <div class="text-image__media">
                <?php if ( $ago_ti['imageId'] ) : ?>
                    <?php echo wp_get_attachment_image( (int) $ago_ti['imageId'], 'large', false, [ 'class' => 'text-image__img' ] ); ?>
                <?php endif; ?>
                <?php if ( $ago_ti['image2Id'] ) : ?>
                    <?php echo wp_get_attachment_image( (int) $ago_ti['image2Id'], 'medium_large', false, [ 'class' => 'text-image__img-secondary' ] ); ?>
                <?php endif; ?>
            </div>

            <div class="text-image__body">
                <?php if ( 'side' === $ago_ti_layout ) {
                    echo $ago_ti_title_html; // phpcs:ignore WordPress.Security.EscapeOutput -- escapado arriba.
                } ?>

                <div class="text-image__content">
                    <?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput -- HTML de bloques core ya filtrado por WP. ?>
                </div>

                <?php
                get_template_part( 'template-parts/atoms/button', null, [
                    'text'    => $ago_ti['btnText'],
                    'url'     => $ago_ti['btnUrl'],
                    'variant' => $ago_ti['btnVariant'],
                    'class'   => 'text-image__cta',
                ] );
                ?>
            </div>

        </div>
    </div>
</section>
