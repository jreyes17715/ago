/**
 * Bloque Banner CTA — editor (JS plano, sin build).
 * Título y contenido se editan en el lienzo (el contenido admite negrita, cursiva y enlaces).
 * Fondo y botón en la barra lateral.
 */
( function ( wp ) {
    var el        = wp.element.createElement;
    var __        = wp.i18n.__;
    var useSelect = wp.data.useSelect;
    var be        = wp.blockEditor;
    var c         = wp.components;

    function useImageUrl( id ) {
        return useSelect( function ( select ) {
            if ( ! id ) { return ''; }
            var media = select( 'core' ).getMedia( id );
            return media ? media.source_url : '';
        }, [ id ] );
    }

    wp.blocks.registerBlockType( 'ago/banner-cta', {
        edit: function ( props ) {
            var a   = props.attributes;
            var set = function ( key ) {
                return function ( value ) { var o = {}; o[ key ] = value; props.setAttributes( o ); };
            };
            var bg = useImageUrl( a.bgId );

            return el( wp.element.Fragment, null,
                el( be.InspectorControls, null,
                    el( c.PanelBody, { title: __( 'Fondo', 'ago' ), initialOpen: true },
                        el( be.MediaUploadCheck, null,
                            el( be.MediaUpload, {
                                allowedTypes: [ 'image' ],
                                value: a.bgId,
                                onSelect: function ( media ) { props.setAttributes( { bgId: media.id } ); },
                                render: function ( obj ) {
                                    return el( c.Button, { variant: 'secondary', onClick: obj.open },
                                        a.bgId ? __( 'Cambiar imagen', 'ago' ) : __( 'Seleccionar imagen', 'ago' ) );
                                }
                            } )
                        ),
                        a.bgId ? el( c.Button, {
                            variant: 'link', isDestructive: true,
                            onClick: function () { props.setAttributes( { bgId: 0 } ); }
                        }, __( 'Quitar imagen', 'ago' ) ) : null
                    ),
                    el( c.PanelBody, { title: __( 'Botón', 'ago' ), initialOpen: true },
                        el( c.TextControl, { label: __( 'Texto', 'ago' ),  value: a.btnText, onChange: set( 'btnText' ) } ),
                        el( c.TextControl, { label: __( 'Enlace', 'ago' ), value: a.btnUrl,  onChange: set( 'btnUrl' ), type: 'url' } ),
                        el( c.SelectControl, {
                            label: __( 'Estilo', 'ago' ), value: a.btnVariant,
                            options: [ { label: __( 'Claro', 'ago' ), value: 'light' }, { label: __( 'Oscuro', 'ago' ), value: 'dark' }, { label: __( 'Contorno', 'ago' ), value: 'outline' } ],
                            onChange: set( 'btnVariant' )
                        } )
                    )
                ),
                el( 'section', be.useBlockProps( { className: 'banner-cta' } ),
                    bg ? el( 'img', { className: 'banner-cta__bg', src: bg, alt: '' } ) : null,
                    el( 'div', { className: 'banner-cta__inner' },
                        el( 'h2', { className: 'banner-cta__title' },
                            el( be.RichText, {
                                tagName: 'span', className: 'banner-cta__title-light', allowedFormats: [],
                                value: a.titleLight, onChange: set( 'titleLight' ), placeholder: __( 'Título (fino)', 'ago' )
                            } ),
                            el( be.RichText, {
                                tagName: 'span', className: 'banner-cta__title-bold', allowedFormats: [],
                                value: a.titleBold, onChange: set( 'titleBold' ), placeholder: __( 'Título (negrita)', 'ago' )
                            } )
                        ),
                        el( be.RichText, {
                            tagName: 'p', className: 'banner-cta__text',
                            allowedFormats: [ 'core/bold', 'core/italic', 'core/link' ],
                            value: a.content, onChange: set( 'content' ),
                            placeholder: __( 'Contenido… (selecciona palabras y usa Ctrl+B para negrita)', 'ago' )
                        } ),
                        a.btnText ? el( 'span', { className: 'ago-btn ago-btn--' + a.btnVariant + ' banner-cta__cta' },
                            el( 'span', { className: 'ago-btn__icon' }, '→' ),
                            el( 'span', { className: 'ago-btn__label' }, a.btnText )
                        ) : null
                    )
                )
            );
        },
        save: function () {
            return null;
        }
    } );
} )( window.wp );
