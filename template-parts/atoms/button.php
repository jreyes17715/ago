<?php
/**
 * Átomo — Botón AGO
 *
 * Uso:
 *   get_template_part( 'template-parts/atoms/button', null, [
 *       'text'    => 'Conoce como ser parte',
 *       'url'     => '/unete',
 *       'variant' => 'dark',          // 'dark' | 'light' | 'outline'
 *       'icon'    => 'arrow',         // 'arrow' | 'mail' | 'phone'
 *       'class'   => 'hero__cta',     // opcional: clase BEM del bloque padre (tamaño)
 *   ] );
 *
 * Con icon = mail / phone, si la URL no trae esquema se completa sola:
 *   'info@ago.org'   → mailto:info@ago.org
 *   '+502 4299-1613' → tel:+50242991613
 *
 * @package AGO
 */

defined( 'ABSPATH' ) || exit;

$ago_btn = wp_parse_args( $args ?? [], [
    'text'    => '',
    'url'     => '#',
    'variant' => 'dark',
    'icon'    => 'arrow',
    'class'   => '',
] );

if ( '' === $ago_btn['text'] ) {
    return;
}

$ago_btn_variant = in_array( $ago_btn['variant'], [ 'dark', 'light', 'outline' ], true ) ? $ago_btn['variant'] : 'dark';
$ago_btn_icon    = in_array( $ago_btn['icon'], [ 'arrow', 'mail', 'phone' ], true ) ? $ago_btn['icon'] : 'arrow';
$ago_btn_classes = trim( 'ago-btn ago-btn--' . $ago_btn_variant . ' ' . $ago_btn['class'] );

$ago_btn_url = trim( (string) $ago_btn['url'] );
if ( 'mail' === $ago_btn_icon && $ago_btn_url && false === strpos( $ago_btn_url, ':' ) ) {
    $ago_btn_url = 'mailto:' . $ago_btn_url;
} elseif ( 'phone' === $ago_btn_icon && $ago_btn_url && false === strpos( $ago_btn_url, ':' ) ) {
    $ago_btn_url = 'tel:' . preg_replace( '/[^0-9+]/', '', $ago_btn_url );
}

// Markup estático (no viene del usuario).
$ago_btn_svgs = [
    'arrow' => '<path d="M4 12h15M13 6l6 6-6 6" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>',
    'mail'  => '<rect x="3" y="5" width="18" height="14" rx="2" stroke="currentColor" stroke-width="2"/><path d="M3.5 6.5 12 13l8.5-6.5" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>',
    'phone' => '<path d="M6.6 3.5h2.6l1.5 4.2-2 1.4a12 12 0 0 0 6.2 6.2l1.4-2 4.2 1.5v2.6a2 2 0 0 1-2.2 2A16.5 16.5 0 0 1 4.6 5.7a2 2 0 0 1 2-2.2Z" fill="currentColor"/>',
];
?>
<a class="<?php echo esc_attr( $ago_btn_classes ); ?>" href="<?php echo esc_url( $ago_btn_url ); ?>">
    <span class="ago-btn__icon" aria-hidden="true">
        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><?php echo $ago_btn_svgs[ $ago_btn_icon ]; // phpcs:ignore WordPress.Security.EscapeOutput -- SVG estático. ?></svg>
    </span>
    <span class="ago-btn__label"><?php echo esc_html( $ago_btn['text'] ); ?></span>
</a>
