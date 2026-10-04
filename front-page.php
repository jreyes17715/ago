<?php
/**
 * Template para la página de inicio (portada estática).
 * WordPress usa este archivo cuando en Ajustes > Lectura
 * se configura una "Página de inicio" estática.
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
