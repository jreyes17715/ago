<?php
/**
 * Template Part: Tarjeta de entrada del blog
 * Compartida entre blocks/blog-listing/render.php (pagina "Blog") y
 * archive.php (archivo nativo de categoria) para que ambos se vean
 * identicos sin duplicar el markup. Se usa dentro de un loop ya
 * posicionado (the_post() ya llamado antes de incluir esta parte).
 *
 * @package AGO
 */

defined( 'ABSPATH' ) || exit;

$post_categories = get_the_category();
$cat_name         = ! empty( $post_categories ) ? $post_categories[0]->name : '';

$dd_blog_card_arrow = '<svg width="14" height="14" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M4 12L12 4M12 4H5M12 4V11" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>';
?>
<article class="blog-listing__card">
    <a class="blog-listing__card-media" href="<?php the_permalink(); ?>">
        <?php if ( has_post_thumbnail() ) : ?>
            <?php the_post_thumbnail( 'medium_large', [ 'class' => 'blog-listing__card-img' ] ); ?>
        <?php else : ?>
            <div class="blog-listing__card-img blog-listing__card-img--placeholder"></div>
        <?php endif; ?>
    </a>

    <div class="blog-listing__card-body">
        <div class="blog-listing__card-meta">
            <?php if ( $cat_name ) : ?>
                <span class="blog-listing__card-category"><?php echo esc_html( $cat_name ); ?></span>
            <?php endif; ?>
            <time class="blog-listing__card-date" datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>">
                <?php echo esc_html( get_the_date( 'd \d\e F \d\e Y' ) ); ?>
            </time>
        </div>

        <h3 class="blog-listing__card-title">
            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
        </h3>

        <p class="blog-listing__card-excerpt"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 20 ) ); ?></p>

        <a class="blog-listing__card-cta" href="<?php the_permalink(); ?>">
            <span class="dd-btn dd-btn--dark"><?php esc_html_e( 'Leer blog', 'ago' ); ?></span>
            <span class="dd-btn-icon dd-btn-icon--dark" aria-hidden="true">
                <?php echo $dd_blog_card_arrow; // phpcs:ignore WordPress.Security.EscapeOutput -- markup estatico. ?>
            </span>
        </a>
    </div>
</article>
