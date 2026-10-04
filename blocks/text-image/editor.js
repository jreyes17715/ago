/**
 * Bloque Texto + Imagen — editor (JS plano, sin build).
 * Se edita directo en el lienzo: título, contenido (párrafos / listas) e imágenes.
 * Opciones de diseño, imágenes y botón en la barra lateral.
 */
( function ( wp ) {
    var el                = wp.element.createElement;
    var __                = wp.i18n.__;
    var useSelect         = wp.data.useSelect;
    var be                = wp.blockEditor;
    var c                 = wp.components;

    var ALLOWED  = [ 'core/paragraph', 'core/list', 'core/list-item', 'core/heading' ];
    var TEMPLATE = [ [ 'core/paragraph', { placeholder: __( 'Escribe el contenido… (puedes agregar listas con viñetas)', 'ago' ) } ] ];

    function useImageUrl( id ) {
        return useSelect( function ( select ) {
            if ( ! id ) { return ''; }
            var media = select( 'core' ).getMedia( id );
            if ( ! media ) { return ''; }
            var sizes = media.media_details && media.media_details.sizes;
            return ( sizes && sizes.large && sizes.large.source_url ) || media.source_url;
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

    wp.blocks.registerBlockType( 'ago/text-image', {
        edit: function ( props ) {
            var a    = props.attributes;
            var set  = function ( key ) {
                return function ( value ) { var o = {}; o[ key ] = value; props.setAttributes( o ); };
            };
            var img1 = useImageUrl( a.imageId );
            var img2 = useImageUrl( a.image2Id );

            var classes = 'text-image text-image--' + ( a.layout === 'top' ? 'top' : 'side' ) + ( a.reverse ? ' text-image--reverse' : '' );
            var blockProps = be.useBlockProps( { className: classes } );

            var title = el( 'h2', { className: 'text-image__title' + ( a.titleStacked ? ' text-image__title--stacked' : '' ) },
                el( be.RichText, {
                    tagName: 'span', className: 'text-image__title-light', allowedFormats: [],
                    value: a.titleLight, onChange: set( 'titleLight' ), placeholder: __( 'Título (fino)', 'ago' )
                } ),
                el( be.RichText, {
                    tagName: 'span', className: 'text-image__title-bold', allowedFormats: [],
                    value: a.titleBold, onChange: set( 'titleBold' ), placeholder: __( 'Título (negrita)', 'ago' )
                } )
            );

            var media = el( 'div', { className: 'text-image__media' },
                img1 ? el( 'img', { className: 'text-image__img', src: img1, alt: '' } )
                     : el( c.Placeholder, { icon: 'format-image', label: __( 'Imagen principal', 'ago' ), instructions: __( 'Selecciónala en la barra lateral → Imágenes.', 'ago' ) } ),
                img2 ? el( 'img', { className: 'text-image__img-secondary', src: img2, alt: '' } ) : null
            );

            var body = el( 'div', { className: 'text-image__body' },
                a.layout === 'top' ? null : title,
                el( 'div', { className: 'text-image__content' },
                    el( be.InnerBlocks, { allowedBlocks: ALLOWED, template: TEMPLATE } )
                ),
                a.btnText ? el( 'span', { className: 'ago-btn ago-btn--' + a.btnVariant + ' text-image__cta' },
                    el( 'span', { className: 'ago-btn__icon' }, '→' ),
                    el( 'span', { className: 'ago-btn__label' }, a.btnText )
                ) : null
            );

            return el( wp.element.Fragment, null,
                el( be.InspectorControls, null,
                    el( c.PanelBody, { title: __( 'Diseño', 'ago' ), initialOpen: true },
                        el( c.SelectControl, {
                            label: __( 'Posición del título', 'ago' ),
                            value: a.layout,
                            options: [
                                { label: __( 'Al lado de la imagen', 'ago' ), value: 'side' },
                                { label: __( 'Arriba, centrado', 'ago' ),      value: 'top' }
                            ],
                            onChange: set( 'layout' )
                        } ),
                        el( c.ToggleControl, {
                            label: __( 'Imagen a la derecha', 'ago' ),
                            checked: a.reverse, onChange: set( 'reverse' )
                        } ),
                        el( c.ToggleControl, {
                            label: __( 'Título en dos líneas (fino arriba, negrita abajo)', 'ago' ),
                            checked: a.titleStacked, onChange: set( 'titleStacked' )
                        } )
                    ),
                    el( c.PanelBody, { title: __( 'Imágenes', 'ago' ), initialOpen: true },
                        el( ImagePicker, { label: __( 'Imagen principal', 'ago' ), value: a.imageId, onChange: set( 'imageId' ) } ),
                        el( ImagePicker, { label: __( 'Imagen secundaria (opcional, esquina inferior)', 'ago' ), value: a.image2Id, onChange: set( 'image2Id' ) } )
                    ),
                    el( c.PanelBody, { title: __( 'Botón (opcional)', 'ago' ), initialOpen: false },
                        el( c.TextControl, { label: __( 'Texto', 'ago' ),  value: a.btnText, onChange: set( 'btnText' ) } ),
                        el( c.TextControl, { label: __( 'Enlace', 'ago' ), value: a.btnUrl,  onChange: set( 'btnUrl' ), type: 'url' } ),
                        el( c.SelectControl, {
                            label: __( 'Estilo', 'ago' ), value: a.btnVariant,
                            options: [ { label: __( 'Oscuro', 'ago' ), value: 'dark' }, { label: __( 'Claro', 'ago' ), value: 'light' }, { label: __( 'Contorno', 'ago' ), value: 'outline' } ],
                            onChange: set( 'btnVariant' )
                        } )
                    )
                ),
                el( 'section', blockProps,
                    el( 'div', { className: 'text-image__inner' },
                        a.layout === 'top' ? title : null,
                        el( 'div', { className: 'text-image__grid' }, media, body )
                    )
                )
            );
        },
        save: function () {
            return el( be.InnerBlocks.Content );
        }
    } );
} )( window.wp );
