/**
 * Bloque Encabezado de sección — editor (JS plano, sin build).
 * Título y contenido (InnerBlocks: párrafos / listas) se editan en el lienzo.
 * Variante, tamaño del título e imágenes en la barra lateral.
 */
( function ( wp ) {
    var el        = wp.element.createElement;
    var __        = wp.i18n.__;
    var useSelect = wp.data.useSelect;
    var be        = wp.blockEditor;
    var c         = wp.components;

    var ALLOWED  = [ 'core/paragraph', 'core/list', 'core/list-item' ];
    var TEMPLATE = [ [ 'core/paragraph', { placeholder: __( 'Descripción (opcional)…', 'ago' ) } ] ];

    function useImageUrl( id ) {
        return useSelect( function ( select ) {
            if ( ! id ) { return ''; }
            var media = select( 'core' ).getMedia( id );
            return media ? media.source_url : '';
        }, [ id ] );
    }

    function ImagePicker( props ) {
        return el( be.MediaUploadCheck, null,
            el( be.MediaUpload, {
                allowedTypes: [ 'image' ],
                value: props.value,
                onSelect: function ( media ) { props.onChange( media.id ); },
                render: function ( obj ) {
                    return el( 'div', { style: { marginBottom: '12px' } },
                        el( 'p', { style: { margin: '0 0 6px', fontWeight: 600 } }, props.label ),
                        el( c.Button, { variant: 'secondary', onClick: obj.open },
                            props.value ? __( 'Cambiar', 'ago' ) : __( 'Seleccionar', 'ago' ) ),
                        props.value ? el( c.Button, {
                            variant: 'link', isDestructive: true, style: { marginLeft: '8px' },
                            onClick: function () { props.onChange( 0 ); }
                        }, __( 'Quitar', 'ago' ) ) : null
                    );
                }
            } )
        );
    }

    wp.blocks.registerBlockType( 'ago/section-header', {
        edit: function ( props ) {
            var a        = props.attributes;
            var set      = function ( key ) {
                return function ( value ) { var o = {}; o[ key ] = value; props.setAttributes( o ); };
            };
            var isCenter = a.variant === 'center';
            var imgL     = useImageUrl( a.imageLeftId );
            var imgR     = useImageUrl( a.imageRightId );

            var titleClass = 'ago-title ago-title--' + ( a.titleSize === 'lg' ? 'lg' : 'xl' ) +
                ( a.titleStacked ? ' ago-title--stacked' : '' ) + ' section-header__title';

            return el( wp.element.Fragment, null,
                el( be.InspectorControls, null,
                    el( c.PanelBody, { title: __( 'Diseño', 'ago' ), initialOpen: true },
                        el( c.SelectControl, {
                            label: __( 'Variante', 'ago' ), value: a.variant,
                            options: [
                                { label: __( 'Texto izquierda + imagen derecha', 'ago' ), value: 'side' },
                                { label: __( 'Texto centrado + imágenes a los lados', 'ago' ), value: 'center' }
                            ],
                            onChange: set( 'variant' )
                        } ),
                        el( c.SelectControl, {
                            label: __( 'Tamaño del título', 'ago' ), value: a.titleSize,
                            options: [
                                { label: __( 'Grande (80px)', 'ago' ),  value: 'xl' },
                                { label: __( 'Mediano (60px)', 'ago' ), value: 'lg' }
                            ],
                            onChange: set( 'titleSize' )
                        } ),
                        el( c.ToggleControl, {
                            label: __( 'Título en dos líneas (fino arriba, negrita abajo)', 'ago' ),
                            checked: a.titleStacked, onChange: set( 'titleStacked' )
                        } )
                    ),
                    el( c.PanelBody, { title: __( 'Imágenes decorativas', 'ago' ), initialOpen: true },
                        isCenter ? el( ImagePicker, { label: __( 'Imagen izquierda', 'ago' ), value: a.imageLeftId, onChange: set( 'imageLeftId' ) } ) : null,
                        el( ImagePicker, { label: __( 'Imagen derecha', 'ago' ), value: a.imageRightId, onChange: set( 'imageRightId' ) } )
                    )
                ),
                el( 'section', be.useBlockProps( { className: 'section-header section-header--' + ( isCenter ? 'center' : 'side' ) } ),
                    isCenter && imgL ? el( 'img', { className: 'section-header__img section-header__img--left', src: imgL, alt: '' } ) : null,
                    imgR ? el( 'img', { className: 'section-header__img section-header__img--right', src: imgR, alt: '' } ) : null,
                    el( 'div', { className: 'section-header__inner' },
                        el( 'div', { className: 'section-header__body' },
                            el( 'h2', { className: titleClass },
                                el( be.RichText, {
                                    tagName: 'span', className: 'ago-title__light', allowedFormats: [],
                                    value: a.titleLight, onChange: set( 'titleLight' ), placeholder: __( 'Título (fino)', 'ago' )
                                } ),
                                el( be.RichText, {
                                    tagName: 'span', className: 'ago-title__bold', allowedFormats: [],
                                    value: a.titleBold, onChange: set( 'titleBold' ), placeholder: __( 'Título (negrita)', 'ago' )
                                } )
                            ),
                            el( 'div', { className: 'section-header__content' },
                                el( be.InnerBlocks, { allowedBlocks: ALLOWED, template: TEMPLATE } )
                            )
                        )
                    )
                )
            );
        },
        save: function () {
            return el( be.InnerBlocks.Content );
        }
    } );
} )( window.wp );
