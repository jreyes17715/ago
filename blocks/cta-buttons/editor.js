/**
 * Bloque Frase + botones — editor (JS plano, sin build).
 * La frase se escribe en el lienzo; los dos botones se configuran en la barra lateral.
 */
( function ( wp ) {
    var el = wp.element.createElement;
    var __ = wp.i18n.__;
    var be = wp.blockEditor;
    var c  = wp.components;

    var VARIANTS = [
        { label: __( 'Oscuro', 'ago' ),   value: 'dark' },
        { label: __( 'Claro', 'ago' ),    value: 'light' },
        { label: __( 'Contorno', 'ago' ), value: 'outline' }
    ];

    function preview( text, variant ) {
        return el( 'span', { className: 'ago-btn ago-btn--' + variant + ' cta-buttons__btn' },
            el( 'span', { className: 'ago-btn__icon' }, '→' ),
            el( 'span', { className: 'ago-btn__label' }, text )
        );
    }

    wp.blocks.registerBlockType( 'ago/cta-buttons', {
        edit: function ( props ) {
            var a   = props.attributes;
            var set = function ( key ) {
                return function ( value ) { var o = {}; o[ key ] = value; props.setAttributes( o ); };
            };

            function panel( n, title ) {
                return el( c.PanelBody, { title: title, initialOpen: true },
                    el( c.TextControl, { label: __( 'Texto (vacío = oculto)', 'ago' ), value: a[ 'btn' + n + 'Text' ], onChange: set( 'btn' + n + 'Text' ) } ),
                    el( c.TextControl, { label: __( 'Enlace', 'ago' ), value: a[ 'btn' + n + 'Url' ], onChange: set( 'btn' + n + 'Url' ), type: 'url' } ),
                    el( c.SelectControl, { label: __( 'Estilo', 'ago' ), value: a[ 'btn' + n + 'Variant' ], options: VARIANTS, onChange: set( 'btn' + n + 'Variant' ) } )
                );
            }

            return el( wp.element.Fragment, null,
                el( be.InspectorControls, null,
                    panel( 1, __( 'Botón 1', 'ago' ) ),
                    panel( 2, __( 'Botón 2', 'ago' ) )
                ),
                el( 'section', be.useBlockProps( { className: 'cta-buttons' } ),
                    el( 'div', { className: 'cta-buttons__inner' },
                        el( be.RichText, {
                            tagName: 'h2', className: 'cta-buttons__title', allowedFormats: [],
                            value: a.title, onChange: set( 'title' ),
                            placeholder: __( '“Escribe la frase…”', 'ago' )
                        } ),
                        ( a.btn1Text || a.btn2Text ) ? el( 'div', { className: 'cta-buttons__actions' },
                            a.btn1Text ? preview( a.btn1Text, a.btn1Variant ) : null,
                            a.btn2Text ? preview( a.btn2Text, a.btn2Variant ) : null
                        ) : el( 'p', { style: { textAlign: 'center', color: '#757575' } }, __( 'Configura los botones en la barra lateral →', 'ago' ) )
                    )
                )
            );
        },
        save: function () {
            return null;
        }
    } );
} )( window.wp );
