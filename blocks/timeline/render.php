<?php
/**
 * Bloque Línea de tiempo — AGO
 * Cada ítem: título, contenido y año. La alternancia claro/oscuro la hace el
 * CSS (:nth-child), así que agregar o quitar ítems mantiene el patrón solo.
 *
 * @var array $attributes
 * @package AGO
 */

defined( 'ABSPATH' ) || exit;

$ago_tl_items = array_values( array_filter(
    is_array( $attributes['items'] ?? null ) ? $attributes['items'] : [],
    static fn( $item ) => is_array( $item ) && ( ( $item['title'] ?? '' ) || ( $item['content'] ?? '' ) || ( $item['year'] ?? '' ) )
) );

if ( ! $ago_tl_items ) {
    return;
}
?>
<section <?php echo get_block_wrapper_attributes( [ 'class' => 'timeline' ] ); ?>>
    <ol class="timeline__list">
        <?php foreach ( $ago_tl_items as $ago_tl_item ) : ?>
            <li class="timeline__item">
                <div class="timeline__card">
                    <div class="timeline__body">
                        <?php if ( ! empty( $ago_tl_item['title'] ) ) : ?>
                            <h3 class="timeline__title"><?php echo esc_html( $ago_tl_item['title'] ); ?></h3>
                        <?php endif; ?>
                        <?php if ( ! empty( $ago_tl_item['content'] ) ) : ?>
                            <p class="timeline__text"><?php echo wp_kses_post( $ago_tl_item['content'] ); ?></p>
                        <?php endif; ?>
                    </div>
                    <?php if ( ! empty( $ago_tl_item['year'] ) ) : ?>
                        <p class="timeline__year"><?php echo esc_html( $ago_tl_item['year'] ); ?></p>
                    <?php endif; ?>
                </div>
            </li>
        <?php endforeach; ?>
    </ol>
</section>
