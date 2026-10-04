<?php
/**
 * Bloque Cuadrícula de logos — AGO
 * N logos por fila (atributo columns); los que sobran bajan a la siguiente fila,
 * centrados. Cada logo puede tener enlace (se abre en pestaña nueva).
 *
 * @var array $attributes
 * @package AGO
 */

defined( 'ABSPATH' ) || exit;

$ago_lg_logos   = is_array( $attributes['logos'] ?? null ) ? $attributes['logos'] : [];
$ago_lg_columns = max( 1, min( 6, (int) ( $attributes['columns'] ?? 3 ) ) );

if ( ! $ago_lg_logos ) {
    return;
}
?>
<section <?php echo get_block_wrapper_attributes( [
    'class' => 'logo-grid',
    'style' => '--logo-grid-columns:' . $ago_lg_columns . ';',
] ); ?>>
    <ul class="logo-grid__list">
        <?php foreach ( $ago_lg_logos as $ago_lg_logo ) :
            $ago_lg_id  = (int) ( $ago_lg_logo['id'] ?? 0 );
            $ago_lg_url = $ago_lg_logo['url'] ?? '';
            if ( ! $ago_lg_id ) {
                continue;
            }
            $ago_lg_img = wp_get_attachment_image( $ago_lg_id, 'medium_large', false, [ 'class' => 'logo-grid__img' ] );
            ?>
            <li class="logo-grid__item">
                <?php if ( $ago_lg_url ) : ?>
                    <a class="logo-grid__link" href="<?php echo esc_url( $ago_lg_url ); ?>" target="_blank" rel="noopener noreferrer">
                        <?php echo $ago_lg_img; // phpcs:ignore WordPress.Security.EscapeOutput -- generado por WP. ?>
                    </a>
                <?php else : ?>
                    <?php echo $ago_lg_img; // phpcs:ignore WordPress.Security.EscapeOutput -- generado por WP. ?>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>
    </ul>
</section>
