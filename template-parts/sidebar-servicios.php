<?php
/**
 * Template Part: Quick links de Servicios (sidebar)
 * Reutilizado en page-blog.php y single.php
 *
 * @package Blue Vision Tech
 */

defined( 'ABSPATH' ) || exit;

$servicios = new WP_Query( [
    'post_type'      => 'servicios',
    'post_status'    => 'publish',
    'posts_per_page' => -1,
    'orderby'        => 'menu_order',
    'order'          => 'ASC',
] );

if ( ! $servicios->have_posts() ) return;
?>

<div class="sidebar-servicios">
    <h4 class="sidebar-servicios__title">
        <?php esc_html_e( 'Nuestros servicios', 'blue-vision-tech' ); ?>
    </h4>
    <ul class="sidebar-servicios__list">
        <?php while ( $servicios->have_posts() ) : $servicios->the_post(); ?>
        <li class="sidebar-servicios__item">
            <a href="<?php the_permalink(); ?>">
                <?php the_title(); ?>
            </a>
        </li>
        <?php endwhile; wp_reset_postdata(); ?>
    </ul>
</div>
