<?php
/**
 * Single: Servicios
 * Template para el detalle de cada servicio del CPT.
 *
 * @package Blue Vision Tech
 */

get_header();
?>

<?php while ( have_posts() ) : the_post(); ?>

    <!-- Hero interior — fuera del <main> para evitar padding lateral -->
    <?php $thumb_url = get_the_post_thumbnail_url( null, 'full' ); ?>
    <section class="page-hero"<?php echo $thumb_url ? ' style="background-image:url(\'' . esc_url( $thumb_url ) . '\')"' : ''; ?>>
        <div class="page-hero__overlay"></div>
        <div class="page-hero__content bvt-container">
            <h1 class="page-hero__title"><?php the_title(); ?></h1>
            <?php //bvt_breadcrumb(); ?>
        </div>
    </section>

    <main id="content" class="site-content">
    <!-- Contenido del servicio -->
    <article class="servicio bvt-section">
        <div class="servicio__inner bvt-container">

            <!-- Extracto + CTA -->
            <?php if ( has_excerpt() ) : ?>
            <div class="servicio__intro">
                <p class="servicio__excerpt"><?php echo esc_html( get_the_excerpt() ); ?></p>
                <a href="<?php echo esc_url( home_url( '/contacto/' ) ); ?>" class="bvt-btn bvt-btn--md">
                    <?php esc_html_e( 'Solicitar cotización', 'blue-vision-tech' ); ?>
                </a>
            </div>
            <?php endif; ?>

            <!-- Contenido libre del editor -->
            <div class="servicio__content bvt-prose">
                <?php the_content(); ?>
            </div>

        </div>
    </article>

<?php endwhile; ?>

    </main><!-- #content .site-content -->

<?php get_footer(); ?>
