<?php
/**
 * Átomo — Título AGO (parte fina + parte negrita)
 *
 * Uso:
 *   get_template_part( 'template-parts/atoms/title', null, [
 *       'light'   => 'Revista',
 *       'bold'    => 'Oftálmica',
 *       'stacked' => false,          // true: fino arriba, negrita abajo
 *       'size'    => 'xl',           // 'xl' (--text-h1, 80px) | 'lg' (--text-h2, 60px)
 *       'tag'     => 'h2',
 *       'class'   => 'section-header__title',   // clase BEM del bloque padre (márgenes)
 *   ] );
 *
 * @package AGO
 */

defined( 'ABSPATH' ) || exit;

$ago_t = wp_parse_args( $args ?? [], [
    'light'   => '',
    'bold'    => '',
    'stacked' => false,
    'size'    => 'xl',
    'tag'     => 'h2',
    'class'   => '',
] );

if ( '' === $ago_t['light'] && '' === $ago_t['bold'] ) {
    return;
}

$ago_t_tag     = in_array( $ago_t['tag'], [ 'h1', 'h2', 'h3' ], true ) ? $ago_t['tag'] : 'h2';
$ago_t_size    = in_array( $ago_t['size'], [ 'xl', 'lg' ], true ) ? $ago_t['size'] : 'xl';
$ago_t_classes = trim( 'ago-title ago-title--' . $ago_t_size . ( $ago_t['stacked'] ? ' ago-title--stacked' : '' ) . ' ' . $ago_t['class'] );
?>
<<?php echo $ago_t_tag; ?> class="<?php echo esc_attr( $ago_t_classes ); ?>">
    <?php if ( $ago_t['light'] ) : ?><span class="ago-title__light"><?php echo esc_html( $ago_t['light'] ); ?></span><?php endif; ?>
    <?php if ( $ago_t['bold'] ) : ?><span class="ago-title__bold"><?php echo esc_html( $ago_t['bold'] ); ?></span><?php endif; ?>
</<?php echo $ago_t_tag; ?>>
