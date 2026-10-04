<?php
/**
 * Template para páginas estáticas de WordPress.
 *
 * - bvt/page-hero se renderiza ANTES del <main> (sin padding lateral).
 * - Bloques BVT restantes se renderizan directamente (tienen su propio contenedor).
 * - Bloques core consecutivos se agrupan en un wrapper bvt-section + bvt-prose
 *   para que la tipografía y el contenedor apliquen automáticamente.
 *
 * @package Blue Vision Tech
 */

defined('ABSPATH') || exit;

get_header();

while ( have_posts() ) :
    the_post();
    the_content();
endwhile;

get_footer();
