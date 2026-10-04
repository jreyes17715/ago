<?php
/**
 * Archivo de categoria (AGO)
 * Se usa cuando se hace clic en una categoria desde el bloque "Blog
 * Listado" — cada categoria tiene asi su propia URL real e indexable
 * (mejor para SEO que filtrar por query-string sobre una sola pagina).
 * Misma tarjeta/fila de filtros que la pagina "Blog" (template-parts/),
 * asi se ven identicos aunque uno sea Pagina y el otro sea archivo nativo.
 *
 * @package AGO
 */

defined( 'ABSPATH' ) || exit;

get_header();

$dd_current_term = is_category() ? get_queried_object() : null;
?>

<section class="archive-header">
    <div class="archive-header__container">
        <h1 class="archive-header__title">
            <?php echo $dd_current_term ? esc_html( $dd_current_term->name ) : esc_html__( 'Blog', 'ago' ); ?>
        </h1>
    </div>
</section>

<section class="blog-listing">
    <div class="blog-listing__container">

        <?php
        get_template_part( 'template-parts/blog-category-filters', null, [
            'current' => $dd_current_term ? $dd_current_term->term_id : 0,
        ] );
        ?>

        <?php if ( have_posts() ) : ?>

            <div class="blog-listing__grid">
                <?php while ( have_posts() ) : the_post(); ?>
                    <?php get_template_part( 'template-parts/blog-card' ); ?>
                <?php endwhile; ?>
            </div>

            <?php
            global $wp_query;
            if ( $wp_query->max_num_pages > 1 ) :
                ?>
                <nav class="blog-listing__pagination">
                    <?php
                    echo paginate_links( [ // phpcs:ignore WordPress.Security.EscapeOutput -- paginate_links() ya escapa internamente.
                        'current'   => max( 1, get_query_var( 'paged' ) ),
                        'total'     => $wp_query->max_num_pages,
                        'prev_text' => __( '← Anterior', 'ago' ),
                        'next_text' => __( 'Siguiente →', 'ago' ),
                    ] );
                    ?>
                </nav>
            <?php endif; ?>

        <?php else : ?>
            <p class="blog-listing__empty"><?php esc_html_e( 'No hay entradas en esta categoría todavía.', 'ago' ); ?></p>
        <?php endif; ?>

    </div>
</section>

<?php get_footer(); ?>
