<?php
/**
 * Single: Blog Post (AGO)
 * Encabezado (categorias + titulo + extracto + autor/fecha + imagen
 * destacada) + contenido del post + categorias de nuevo (abajo) + Marquee
 * de palabras (administrable desde Personalizar) + carrusel de "Blogs
 * Relacionados" (misma categoria, excluye el post actual). Los 2 fondos
 * decorativos (encabezado / relacionados) son opcionales y administrables
 * desde Personalizar → Blog — Detalle del post; no se fabrican graficos.
 *
 * @package AGO
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
    the_post();

    $dd_single_defaults = dd_single_defaults();
    $dd_header_bg_id    = (int) get_theme_mod( 'dd_single_header_background', $dd_single_defaults['dd_single_header_background'] );
    $dd_header_bg_url   = $dd_header_bg_id ? wp_get_attachment_image_url( $dd_header_bg_id, 'full' ) : '';
    $dd_related_bg_id   = (int) get_theme_mod( 'dd_single_related_background', $dd_single_defaults['dd_single_related_background'] );
    $dd_related_bg_url  = $dd_related_bg_id ? wp_get_attachment_image_url( $dd_related_bg_id, 'full' ) : '';
    $dd_marquee_words   = get_theme_mod( 'dd_single_marquee_words', $dd_single_defaults['dd_single_marquee_words'] );

    $dd_categories   = get_the_category();
    $dd_header_style = $dd_header_bg_url ? 'background-image:url(' . esc_url( $dd_header_bg_url ) . ');' : '';
    ?>

    <section class="single-post__header" style="<?php echo esc_attr( $dd_header_style ); ?>">
        <div class="single-post__container">

            <?php if ( ! empty( $dd_categories ) ) : ?>
                <div class="single-post__categories">
                    <?php foreach ( $dd_categories as $dd_cat ) : ?>
                        <a class="single-post__category" href="<?php echo esc_url( get_category_link( $dd_cat->term_id ) ); ?>">
                            <?php echo esc_html( $dd_cat->name ); ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <h1 class="single-post__title"><?php the_title(); ?></h1>

            <?php $dd_excerpt = get_the_excerpt(); ?>
            <?php if ( $dd_excerpt ) : ?>
                <p class="single-post__excerpt"><?php echo esc_html( $dd_excerpt ); ?></p>
            <?php endif; ?>

            <div class="single-post__meta">
                <span class="single-post__author"><?php echo esc_html( get_the_author() ); ?></span>
                <time class="single-post__date" datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>">
                    <?php echo esc_html( get_the_date( 'd \d\e F \d\e Y' ) ); ?>
                </time>
            </div>

        </div>

        <?php if ( has_post_thumbnail() ) : ?>
            <div class="single-post__media">
                <?php the_post_thumbnail( 'large', [ 'class' => 'single-post__img' ] ); ?>
            </div>
        <?php endif; ?>
    </section>

    <section class="single-post__body">
        <div class="single-post__container single-post__container--narrow">

            <div class="bvt-prose single-post__content">
                <?php the_content(); ?>
            </div>

            <?php if ( ! empty( $dd_categories ) ) : ?>
                <div class="single-post__tags">
                    <?php foreach ( $dd_categories as $dd_cat ) : ?>
                        <a class="single-post__category" href="<?php echo esc_url( get_category_link( $dd_cat->term_id ) ); ?>">
                            <?php echo esc_html( $dd_cat->name ); ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

        </div>
    </section>

    <?php
    // Marquee de palabras — mismo bloque de Gutenberg, administrable desde
    // Personalizar → Blog — Detalle del post (no un valor fijo en el codigo).
    if ( $dd_marquee_words ) {
        echo do_blocks( serialize_block( [ // phpcs:ignore WordPress.Security.EscapeOutput -- do_blocks() ya escapa via el propio render.php del bloque.
            'blockName'    => 'ago/marquee',
            'attrs'        => [ 'words' => $dd_marquee_words ],
            'innerBlocks'  => [],
            'innerHTML'    => '',
            'innerContent' => [],
        ] ) );
    }

    // Blogs relacionados: mismas categorias del post actual, excluyendolo.
    $dd_related_cat_ids = wp_list_pluck( $dd_categories, 'term_id' );

    $dd_related_args = [
        'post_type'           => 'post',
        'post_status'         => 'publish',
        'posts_per_page'      => 9,
        'post__not_in'        => [ get_the_ID() ],
        'ignore_sticky_posts' => true,
    ];

    if ( ! empty( $dd_related_cat_ids ) ) {
        $dd_related_args['category__in'] = $dd_related_cat_ids;
    }

    $dd_related_query = new WP_Query( $dd_related_args );

    $dd_related_style = $dd_related_bg_url ? 'background-image:url(' . esc_url( $dd_related_bg_url ) . ');' : '';

    $dd_prev_arrow = '<svg width="16" height="16" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M14 9H4M4 9L8.5 4.5M4 9L8.5 13.5" stroke="#fff" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>';
    $dd_next_arrow = '<svg width="16" height="16" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M4 9H14M14 9L9.5 4.5M14 9L9.5 13.5" stroke="#fff" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>';

    if ( $dd_related_query->have_posts() ) :
        ?>
        <section class="relacionados" style="<?php echo esc_attr( $dd_related_style ); ?>">
            <div class="relacionados__container">
                <h2 class="relacionados__title"><?php esc_html_e( 'Blogs Relacionados', 'ago' ); ?></h2>

                <div class="relacionados__carousel-wrap">
                    <?php if ( $dd_related_query->post_count > 1 ) : ?>
                        <button type="button" class="relacionados__prev" aria-label="<?php esc_attr_e( 'Blog anterior', 'ago' ); ?>">
                            <?php echo $dd_prev_arrow; // phpcs:ignore WordPress.Security.EscapeOutput -- markup estatico. ?>
                        </button>
                    <?php endif; ?>

                    <div class="swiper relacionados__swiper">
                        <div class="swiper-wrapper">
                            <?php while ( $dd_related_query->have_posts() ) : $dd_related_query->the_post(); ?>
                                <div class="swiper-slide relacionados__slide">
                                    <?php get_template_part( 'template-parts/blog-card' ); ?>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    </div>

                    <?php if ( $dd_related_query->post_count > 1 ) : ?>
                        <button type="button" class="relacionados__next" aria-label="<?php esc_attr_e( 'Siguiente blog', 'ago' ); ?>">
                            <?php echo $dd_next_arrow; // phpcs:ignore WordPress.Security.EscapeOutput -- markup estatico. ?>
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        </section>
        <?php
    endif;
    wp_reset_postdata();

endwhile;

get_footer();
