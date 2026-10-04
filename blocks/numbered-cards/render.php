<?php
/**
 * Bloque Tarjetas numeradas — AGO
 * Número automático (01, 02…), título y contenido. La alternancia blanco/azul
 * y la esquina recortada las hace el CSS.
 *
 * @var array $attributes
 * @package AGO
 */

defined( 'ABSPATH' ) || exit;

$ago_nc_columns = max( 1, min( 4, (int) ( $attributes['columns'] ?? 3 ) ) );
$ago_nc_items   = array_values( array_filter(
    is_array( $attributes['items'] ?? null ) ? $attributes['items'] : [],
    static fn( $item ) => is_array( $item ) && ( ! empty( $item['title'] ) || ! empty( $item['content'] ) )
) );

if ( ! $ago_nc_items ) {
    return;
}
?>
<section <?php echo get_block_wrapper_attributes( [
    'class' => 'numbered-cards',
    'style' => '--nc-columns:' . $ago_nc_columns . ';',
] ); ?>>
    <ol class="numbered-cards__list">
        <?php foreach ( $ago_nc_items as $ago_nc_i => $ago_nc_item ) : ?>
            <li class="numbered-cards__item">
                <div class="numbered-cards__card">
                    <div class="numbered-cards__inner">
                        <span class="numbered-cards__number" aria-hidden="true"><?php echo esc_html( str_pad( (string) ( $ago_nc_i + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span>
                        <?php if ( ! empty( $ago_nc_item['title'] ) ) : ?>
                            <h3 class="numbered-cards__title"><?php echo esc_html( $ago_nc_item['title'] ); ?></h3>
                        <?php endif; ?>
                        <?php if ( ! empty( $ago_nc_item['content'] ) ) : ?>
                            <p class="numbered-cards__text"><?php echo wp_kses_post( $ago_nc_item['content'] ); ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            </li>
        <?php endforeach; ?>
    </ol>
</section>
