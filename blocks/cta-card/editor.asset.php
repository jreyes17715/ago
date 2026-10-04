<?php
defined( 'ABSPATH' ) || exit;

return [
    'dependencies' => [ 'wp-blocks', 'wp-element', 'wp-block-editor', 'wp-components', 'wp-data', 'wp-i18n' ],
    'version'      => filemtime( __DIR__ . '/editor.js' ),
];
