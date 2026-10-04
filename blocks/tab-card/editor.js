/**
 * Bloque Tarjeta con encabezado (hija) — editor (JS plano, sin build).
 * Título en el encabezado; contenido con bloques Párrafo / Lista (viñetas, negritas).
 * "Ancho completo" en la barra lateral (ej. Valores).
 */
( function ( wp ) {
    var el = wp.element.createElement;
    var __ = wp.i18n.__;
    var be = wp.blockEditor;
    var c  = wp.components;

    var ALLOWED  = [ 'core/paragraph', 'core/list', 'core/list-item' ];
    var TEMPLATE = [ [ 'core/paragraph', { placeholder: __( 'Contenido… (puedes agregar una lista con viñetas)', 'ago' ) } ] ];

    wp.blocks.registerBlockType( 'ago/tab-card', {
        edit: function ( props ) {
            var a = props.attributes;

            return el( wp.element.Fragment, null,
                el( be.InspectorControls, null,
                    el( c.PanelBody, { title: __( 'Tarjeta', 'ago' ), initialOpen: true },
                        el( c.ToggleControl, {
                            label: __( 'Ancho completo', 'ago' ),
                            help: __( 'Ocupa toda la fila (como "Valores").', 'ago' ),
                            checked: a.wide,
                            onChange: function ( v ) { props.setAttributes( { wide: v } ); }
                        } )
                    )
                ),
                el( 'article', be.useBlockProps( { className: 'tab-card' + ( a.wide ? ' tab-card--wide' : '' ) } ),
                    el( be.RichText, {
                        tagName: 'h3', className: 'tab-card__header', allowedFormats: [],
                        value: a.title, onChange: function ( v ) { props.setAttributes( { title: v } ); },
                        placeholder: __( 'Título', 'ago' )
                    } ),
                    el( 'div', { className: 'tab-card__body' },
                        el( be.InnerBlocks, { allowedBlocks: ALLOWED, template: TEMPLATE } )
                    )
                )
            );
        },
        save: function () {
            return el( be.InnerBlocks.Content );
        }
    } );
} )( window.wp );
