<?php
/**
 * Bloque Lista con puntos — AGO
 * Los puntos se reparten en N columnas (1 o 2) en orden: la primera columna
 * recibe la mitad redondeada hacia arriba. Dentro de cada columna los círculos
 * se conectan con una línea vertical.
 *
 * @var array $attributes
 * @package AGO
 */

defined( 'ABSPATH' ) || exit;

$ago_pl_title   = $attributes['title'] ?? '';
$ago_pl_columns = 1 === (int) ( $attributes['columns'] ?? 2 ) ? 1 : 2;
$ago_pl_items   = array_values( array_filter(
    is_array( $attributes['items'] ?? null ) ? $attributes['items'] : [],
    static fn( $item ) => '' !== trim( wp_strip_all_tags( (string) $item ) )
) );

if ( ! $ago_pl_title && ! $ago_pl_items ) {
    return;
}

$ago_pl_groups = $ago_pl_items ? array_chunk( $ago_pl_items, (int) ceil( count( $ago_pl_items ) / $ago_pl_columns ) ) : [];
?>
<section <?php echo get_block_wrapper_attributes( [ 'class' => 'point-list point-list--cols-' . $ago_pl_columns ] ); ?>>

    <?php if ( $ago_pl_title ) : ?>
        <h2 class="point-list__title"><?php echo esc_html( $ago_pl_title ); ?></h2>
    <?php endif; ?>

    <?php if ( $ago_pl_groups ) : ?>
        <div class="point-list__columns">
            <?php foreach ( $ago_pl_groups as $ago_pl_group ) : ?>
                <ul class="point-list__list">
                    <?php foreach ( $ago_pl_group as $ago_pl_item ) : ?>
                        <li class="point-list__item">
                            <span class="point-list__dot" aria-hidden="true"></span>
                            <p class="point-list__text"><?php echo wp_kses_post( $ago_pl_item ); ?></p>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

</section>
