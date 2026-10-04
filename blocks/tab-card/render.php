<?php
/**
 * Bloque Tarjeta con encabezado (hija de ago/tab-cards) — AGO
 * Encabezado navy con flecha hacia abajo + contenido (InnerBlocks: párrafos y listas).
 *
 * @var array  $attributes
 * @var string $content
 * @package AGO
 */

defined( 'ABSPATH' ) || exit;

$ago_tc_title = $attributes['title'] ?? '';
$ago_tc_wide  = ! empty( $attributes['wide'] );
?>
<article <?php echo get_block_wrapper_attributes( [ 'class' => 'tab-card' . ( $ago_tc_wide ? ' tab-card--wide' : '' ) ] ); ?>>
    <?php if ( $ago_tc_title ) : ?>
        <h3 class="tab-card__header"><?php echo esc_html( $ago_tc_title ); ?></h3>
    <?php endif; ?>
    <div class="tab-card__body">
        <?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput -- HTML de bloques core ya filtrado por WP. ?>
    </div>
</article>
