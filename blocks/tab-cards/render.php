<?php
/**
 * Bloque Tarjetas con encabezado (contenedor) — AGO
 * Solo la cuadrícula; cada tarjeta es un bloque hijo ago/tab-card ($content).
 *
 * @var string $content
 * @package AGO
 */

defined( 'ABSPATH' ) || exit;

if ( ! trim( $content ) ) {
    return;
}
?>
<section <?php echo get_block_wrapper_attributes( [ 'class' => 'tab-cards' ] ); ?>>
    <div class="tab-cards__grid">
        <?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput -- HTML de bloques hijos ya renderizado por WP. ?>
    </div>
</section>
