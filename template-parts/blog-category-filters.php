<?php
/**
 * Template Part: Fila de filtros de categoria del blog
 * Compartida entre blocks/blog-listing/render.php (pagina "Blog", pill
 * "Todos" siempre activa) y archive.php (archivo nativo de categoria,
 * la categoria actual queda activa). Los links de categoria van a la URL
 * NATIVA de WordPress (get_category_link) — asi cada categoria tiene su
 * propia URL indexable, servida por archive.php, en vez de un query-string
 * sobre una sola pagina.
 *
 * @param array $args { 'current' => int termID activo, 0 = "Todos" }
 *
 * @package AGO
 */

defined( 'ABSPATH' ) || exit;

/** @var array $args */
$current = isset( $args['current'] ) ? (int) $args['current'] : 0;

$categories = get_categories( [ 'hide_empty' => true ] );

if ( empty( $categories ) ) {
    return;
}

// La pagina "Blog" (bloque Header Blog + Blog Listado) es adonde vuelve el
// pill "Todos". Se busca por slug en vez de hardcodear un ID.
$dd_blog_page = get_page_by_path( 'blog' );
$dd_all_url   = $dd_blog_page ? get_permalink( $dd_blog_page ) : home_url( '/' );
?>
<div class="blog-listing__filters">
    <a class="blog-listing__filter<?php echo 0 === $current ? ' is-active' : ''; ?>" href="<?php echo esc_url( $dd_all_url ); ?>">
        <?php esc_html_e( 'Todos', 'ago' ); ?>
    </a>
    <?php foreach ( $categories as $cat ) : ?>
        <a class="blog-listing__filter<?php echo $current === $cat->term_id ? ' is-active' : ''; ?>" href="<?php echo esc_url( get_category_link( $cat->term_id ) ); ?>">
            <?php echo esc_html( $cat->name ); ?>
        </a>
    <?php endforeach; ?>
</div>
