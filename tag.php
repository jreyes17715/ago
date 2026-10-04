<?php
/**
 * Tag Archive
 * Muestra posts filtrados por la tag actual.
 * Mismo layout que page-blog.php.
 *
 * @package Blue Vision Tech
 */

defined( 'ABSPATH' ) || exit;

get_header();

$tag   = get_queried_object();
$paged = get_query_var( 'paged' ) ?: 1;

// Imagen destacada de la página de blog (configurada en Apariencia → Personalizar → Blog)
$hero_thumb = bvt_blog_hero_thumb();

?>

<!-- Hero con imagen de la página de blog -->
<?php if ( $hero_thumb ) : ?>
<section class="page-hero" style="background-image: url('<?php echo esc_url( $hero_thumb ); ?>');">
    <div class="page-hero__overlay"></div>
    <div class="page-hero__content bvt-container">
        <h1 class="page-hero__title"><?php echo esc_html( $tag->name ); ?></h1>
        <?php if ( $tag->description ) : ?>
        <p class="page-hero__subtitle"><?php echo esc_html( $tag->description ); ?></p>
        <?php endif; ?>
    </div>
</section>
<?php else : ?>
<section class="page-hero page-hero--no-image">
    <div class="page-hero__overlay"></div>
    <div class="page-hero__content bvt-container">
        <h1 class="page-hero__title"><?php echo esc_html( $tag->name ); ?></h1>
    </div>
</section>
<?php endif; ?>

<main id="content" class="site-content">
<section class="blog-listing bvt-section">
    <div class="blog-listing__inner bvt-container">

        <!-- Posts filtrados por tag -->
        <h2 class="screen-reader-text">
            <?php printf( esc_html__( 'Artículos con la etiqueta: %s', 'blue-vision-tech' ), esc_html( $tag->name ) ); ?>
        </h2>

        <div class="blog-listing__posts">
            <?php if ( have_posts() ) : ?>

                <?php while ( have_posts() ) : the_post();

                    $author_name = get_the_author();

                    $tags_list = get_the_tags();
                    if ( ! $tags_list ) { $tags_list = get_the_category(); }

                    $seo_desc = get_post_meta( get_the_ID(), '_bvt_seo_description', true );
                    $excerpt  = $seo_desc ?: get_the_excerpt();

                ?>
                <article class="blog-listing__card">

                    <?php if ( has_post_thumbnail() ) : ?>
                    <a href="<?php the_permalink(); ?>" class="blog-listing__card-img-link">
                        <?php the_post_thumbnail( 'medium', [ 'class' => 'blog-listing__card-img' ] ); ?>
                    </a>
                    <?php endif; ?>

                    <div class="blog-listing__card-body">

                        <h3 class="blog-listing__card-title">
                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                        </h3>

                        <div class="blog-listing__card-meta">
                            <span class="blog-listing__card-author"><?php echo esc_html( $author_name ); ?></span>
                            <time class="blog-listing__card-date" datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>">
                                <?php echo esc_html( get_the_date() ); ?>
                            </time>
                        </div>

                        <?php if ( $tags_list ) : ?>
                        <div class="blog-listing__card-tags">
                            <?php foreach ( $tags_list as $index => $t ) :
                                $t_url  = get_tag_link( $t->term_id );
                                $t_name = $t->name;
                            ?>
                                <a href="<?php echo esc_url( $t_url ); ?>" class="blog-listing__card-tag"><?php echo esc_html( $t_name ); ?></a><?php if ( $index < count( $tags_list ) - 1 ) echo '<span class="blog-listing__card-tag-sep">, </span>'; ?>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>

                        <?php if ( $excerpt ) : ?>
                        <p class="blog-listing__card-excerpt"><?php echo esc_html( $excerpt ); ?></p>
                        <?php endif; ?>

                        <a href="<?php the_permalink(); ?>" class="blog-listing__card-link">
                            <?php esc_html_e( 'Leer más', 'blue-vision-tech' ); ?>
                            <span class="blog-listing__card-arrow" aria-hidden="true">
                                <i class="fa-solid fa-arrow-up-right" aria-hidden="true"></i>
                            </span>
                        </a>

                    </div>

                </article>
                <?php endwhile; ?>

                <!-- Paginación -->
                <?php
                $big = 999999999;
                $pagination = paginate_links( [
                    'base'      => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
                    'format'    => '?paged=%#%',
                    'current'   => max( 1, $paged ),
                    'total'     => $GLOBALS['wp_query']->max_num_pages,
                    'prev_text' => '← Anterior',
                    'next_text' => 'Siguiente →',
                ] );
                if ( $pagination ) : ?>
                <nav class="blog-listing__pagination">
                    <?php echo $pagination; ?>
                </nav>
                <?php endif; ?>

            <?php else : ?>
                <p class="blog-listing__empty">
                    <?php esc_html_e( 'No hay artículos con esta etiqueta.', 'blue-vision-tech' ); ?>
                </p>
            <?php endif; ?>

        </div>

        <!-- Sidebar: quick links a servicios -->
        <aside class="blog-listing__sidebar">
            <?php get_template_part( 'template-parts/sidebar-servicios' ); ?>
        </aside>

    </div>
</section>
</main><!-- #content .site-content -->

<?php get_footer(); ?>
