<?php
/**
 * Registro de bloques — AGO
 *
 * Registra automáticamente cada carpeta /blocks/<nombre>/ que tenga block.json.
 * El editor.js de cada bloque se registra aquí con URL explícita del tema
 * (handle: ago-<nombre>-editor) en vez de "file:./editor.js" en block.json,
 * que en algunas instalaciones (Windows / symlinks) resuelve mal la URL.
 * Para un bloque nuevo NO hay que tocar este archivo ni functions.php.
 *
 * @package AGO
 */

defined( 'ABSPATH' ) || exit;

add_action( 'init', function () {
    $ago_dir = get_template_directory();
    $ago_uri = get_template_directory_uri();

    foreach ( glob( $ago_dir . '/blocks/*/block.json' ) as $ago_block_json ) {
        $ago_block_path = dirname( $ago_block_json );
        $ago_block_name = basename( $ago_block_path );
        $ago_args       = [];

        $ago_editor_js = $ago_block_path . '/editor.js';
        if ( file_exists( $ago_editor_js ) ) {
            $ago_asset_file = $ago_block_path . '/editor.asset.php';
            $ago_asset      = file_exists( $ago_asset_file ) ? require $ago_asset_file : [];
            $ago_handle     = 'ago-' . $ago_block_name . '-editor';

            // Si una dependencia no existe en esta versión de WP, WordPress descarta
            // el script EN SILENCIO y el bloque no aparece. Se filtran las que no
            // están registradas y se avisa en el log (WP_DEBUG_LOG).
            $ago_deps = $ago_asset['dependencies'] ?? [ 'wp-blocks', 'wp-element', 'wp-block-editor', 'wp-components' ];
            foreach ( $ago_deps as $ago_i => $ago_dep ) {
                if ( ! wp_script_is( $ago_dep, 'registered' ) ) {
                    unset( $ago_deps[ $ago_i ] );
                    if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
                        error_log( sprintf( '[AGO] Bloque "%s": dependencia JS "%s" no registrada, se omite.', $ago_block_name, $ago_dep ) );
                    }
                }
            }

            wp_register_script(
                $ago_handle,
                $ago_uri . '/blocks/' . $ago_block_name . '/editor.js',
                array_values( $ago_deps ),
                $ago_asset['version'] ?? filemtime( $ago_editor_js ),
                true
            );

            $ago_args['editor_script_handles'] = [ $ago_handle ];
        }

        register_block_type( $ago_block_path, $ago_args );
    }
} );

// Categoría propia en el insertador de bloques.
add_filter( 'block_categories_all', function ( array $categories ) : array {
    array_unshift( $categories, [
        'slug'  => 'ago',
        'title' => __( 'AGO', 'ago' ),
    ] );
    return $categories;
} );
