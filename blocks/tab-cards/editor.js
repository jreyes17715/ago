/**
 * Bloque Tarjetas con encabezado (contenedor) — editor (JS plano, sin build).
 * Solo acepta tarjetas ago/tab-card. Plantilla inicial: Visión, Misión y Valores (ancho completo).
 */
( function ( wp ) {
    var el = wp.element.createElement;
    var __ = wp.i18n.__;
    var be = wp.blockEditor;

    var TEMPLATE = [
        [ 'ago/tab-card', { title: __( 'Visión', 'ago' ) } ],
        [ 'ago/tab-card', { title: __( 'Misión', 'ago' ) } ],
        [ 'ago/tab-card', { title: __( 'Valores', 'ago' ), wide: true } ]
    ];

    wp.blocks.registerBlockType( 'ago/tab-cards', {
        edit: function () {
            var blockProps = be.useBlockProps( { className: 'tab-cards' } );
            var gridProps  = be.useInnerBlocksProps( { className: 'tab-cards__grid' }, {
                allowedBlocks: [ 'ago/tab-card' ],
                template: TEMPLATE,
                orientation: 'horizontal'
            } );
            return el( 'section', blockProps, el( 'div', gridProps ) );
        },
        save: function () {
            return el( be.InnerBlocks.Content );
        }
    } );
} )( window.wp );
