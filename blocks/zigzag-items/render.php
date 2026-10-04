<?php
/**
 * Bloque Pilares en zigzag — AGO
 * Ítems: ícono en círculo con número automático (01, 02…), título y contenido.
 * El zigzag (impares imagen arriba / pares imagen abajo) lo hace el CSS con
 * :nth-child, así que agregar o quitar ítems mantiene el patrón.
 *
 * @var array $attributes
 * @package AGO
 */

defined( 'ABSPATH' ) || exit;

$ago_zz_items = array_values( array_filter(
    is_array( $attributes['items'] ?? null ) ? $attributes['items'] : [],
    static fn( $item ) => is_array( $item ) && ( ! empty( $item['imageId'] ) || ! empty( $item['title'] ) || ! empty( $item['content'] ) )
) );
$ago_zz_columns = max( 1, min( 6, (int) ( $attributes['columns'] ?? 4 ) ) );

if ( ! $ago_zz_items ) {
    return;
}
?>
<section <?php echo get_block_wrapper_attributes( [
    'class' => 'zigzag-items',
    'style' => '--zz-columns:' . $ago_zz_columns . ';',
] ); ?>>
    <ol class="zigzag-items__list">
        <?php foreach ( $ago_zz_items as $ago_zz_i => $ago_zz_item ) : ?>
            <li class="zigzag-items__item">
                <div class="zigzag-items__media">
                    <?php if ( ! empty( $ago_zz_item['imageId'] ) ) : ?>
                        <?php echo wp_get_attachment_image( (int) $ago_zz_item['imageId'], 'medium', false, [
                            'class' => 'zigzag-items__img', 'alt' => '', 'aria-hidden' => 'true',
                        ] ); ?>
                    <?php endif; ?>
                    <span class="zigzag-items__number" aria-hidden="true"><?php echo esc_html( str_pad( (string) ( $ago_zz_i + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span>
                </div>
                <div class="zigzag-items__body">
                    <?php if ( ! empty( $ago_zz_item['title'] ) ) : ?>
                        <h3 class="zigzag-items__title"><?php echo esc_html( $ago_zz_item['title'] ); ?></h3>
                    <?php endif; ?>
                    <?php if ( ! empty( $ago_zz_item['content'] ) ) : ?>
                        <p class="zigzag-items__text"><?php echo wp_kses_post( $ago_zz_item['content'] ); ?></p>
                    <?php endif; ?>
                </div>
            </li>
        <?php endforeach; ?>
    </ol>
</section>
