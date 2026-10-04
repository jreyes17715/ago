<?php
/**
 * Bloque Equipo (junta directiva) — AGO
 * Título + tarjetas: foto con corte diagonal inferior izquierdo y, encima,
 * panel blanco (también con corte) con puesto (píldora) y nombre.
 *
 * @var array $attributes
 * @package AGO
 */

defined( 'ABSPATH' ) || exit;

$ago_tg_title   = $attributes['title'] ?? '';
$ago_tg_columns = max( 1, min( 4, (int) ( $attributes['columns'] ?? 3 ) ) );
$ago_tg_center  = 'centered' === ( $attributes['nameStyle'] ?? 'default' );
$ago_tg_items   = array_values( array_filter(
    is_array( $attributes['items'] ?? null ) ? $attributes['items'] : [],
    static fn( $item ) => is_array( $item ) && ( ! empty( $item['imageId'] ) || ! empty( $item['name'] ) || ! empty( $item['role'] ) )
) );

if ( ! $ago_tg_title && ! $ago_tg_items ) {
    return;
}
?>
<section <?php echo get_block_wrapper_attributes( [
    'class' => 'team-grid' . ( $ago_tg_center ? ' team-grid--name-centered' : '' ),
    'style' => '--tg-columns:' . $ago_tg_columns . ';',
] ); ?>>

    <?php if ( $ago_tg_title ) : ?>
        <h2 class="team-grid__title"><?php echo esc_html( $ago_tg_title ); ?></h2>
    <?php endif; ?>

    <?php if ( $ago_tg_items ) : ?>
        <ul class="team-grid__list">
            <?php foreach ( $ago_tg_items as $ago_tg_item ) :
                $ago_tg_quote = trim( (string) ( $ago_tg_item['quote'] ?? '' ) );
                $ago_tg_flip  = '' !== trim( wp_strip_all_tags( $ago_tg_quote ) );
                ?>
                <li class="team-grid__item<?php echo $ago_tg_flip ? ' team-grid__item--flip' : ''; ?>"<?php echo $ago_tg_flip ? ' tabindex="0"' : ''; ?>>
                    <div class="team-grid__flipper">

                        <!-- Frente: foto + panel -->
                        <div class="team-grid__face team-grid__face--front">
                            <div class="team-grid__photo">
                                <?php if ( ! empty( $ago_tg_item['imageId'] ) ) : ?>
                                    <?php echo wp_get_attachment_image( (int) $ago_tg_item['imageId'], 'medium_large', false, [
                                        'class' => 'team-grid__img',
                                        'alt'   => esc_attr( $ago_tg_item['name'] ?? '' ),
                                    ] ); ?>
                                <?php endif; ?>
                            </div>
                            <div class="team-grid__panel">
                                <?php if ( ! empty( $ago_tg_item['role'] ) ) : ?>
                                    <span class="team-grid__role"><?php echo esc_html( $ago_tg_item['role'] ); ?></span>
                                <?php endif; ?>
                                <?php if ( ! empty( $ago_tg_item['name'] ) ) : ?>
                                    <p class="team-grid__name"><?php echo esc_html( $ago_tg_item['name'] ); ?></p>
                                <?php endif; ?>
                            </div>
                        </div>

                        <?php if ( $ago_tg_flip ) : ?>
                            <!-- Reverso: cita (solo si se llenó) -->
                            <div class="team-grid__face team-grid__face--back">
                                <blockquote class="team-grid__quote"><?php echo wp_kses_post( $ago_tg_quote ); ?></blockquote>
                            </div>
                        <?php endif; ?>

                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

</section>
