/**
 * Bloque Sección con fondo — editor (JS plano, sin build).
 * Acepta cualquier bloque adentro. Fondo (color / imagen) en la barra lateral.
 */
( function ( wp ) {
    var el        = wp.element.createElement;
    var __        = wp.i18n.__;
    var useSelect = wp.data.useSelect;
    var be        = wp.blockEditor;
    var c         = wp.components;

    wp.blocks.registerBlockType( 'ago/section-bg', {
        edit: function ( props ) {
            var a  = props.attributes;
            var bg = a.bg === 'white' ? 'white' : 'light';

            var urls = useSelect( function ( select ) {
                function url( id ) {
                    if ( ! id ) { return ''; }
                    var m = select( 'core' ).getMedia( id );
                    return m ? m.source_url : '';
                }
                return { bg: url( a.bgImageId ), left: url( a.decoLeftId ), right: url( a.decoRightId ) };
            }, [ a.bgImageId, a.decoLeftId, a.decoRightId ] );
            var imgUrl = urls.bg;

            function picker( label, key ) {
                return el( 'div', { style: { marginBottom: '12px' } },
                    el( 'p', { style: { margin: '0 0 6px', fontWeight: 600 } }, label ),
                    el( be.MediaUploadCheck, null,
                        el( be.MediaUpload, {
                            allowedTypes: [ 'image' ], value: a[ key ],
                            onSelect: function ( m ) { var o = {}; o[ key ] = m.id; props.setAttributes( o ); },
                            render: function ( obj ) {
                                return el( c.Button, { variant: 'secondary', onClick: obj.open },
                                    a[ key ] ? __( 'Cambiar', 'ago' ) : __( 'Seleccionar', 'ago' ) );
                            }
                        } )
                    ),
                    a[ key ] ? el( c.Button, { variant: 'link', isDestructive: true, style: { marginLeft: '8px' },
                        onClick: function () { var o = {}; o[ key ] = 0; props.setAttributes( o ); } }, __( 'Quitar', 'ago' ) ) : null
                );
            }

            var blockProps = be.useBlockProps( { className: 'section-bg section-bg--' + bg } );
            var innerProps = be.useInnerBlocksProps( { className: 'section-bg__inner' }, {
                renderAppender: be.InnerBlocks.ButtonBlockAppender
            } );

            return el( wp.element.Fragment, null,
                el( be.InspectorControls, null,
                    el( c.PanelBody, { title: __( 'Fondo', 'ago' ), initialOpen: true },
                        el( c.SelectControl, {
                            label: __( 'Color de fondo', 'ago' ), value: bg,
                            options: [
                                { label: __( 'Gris claro', 'ago' ), value: 'light' },
                                { label: __( 'Blanco', 'ago' ),     value: 'white' }
                            ],
                            onChange: function ( v ) { props.setAttributes( { bg: v } ); }
                        } ),
                        el( be.MediaUploadCheck, null,
                            el( be.MediaUpload, {
                                allowedTypes: [ 'image' ], value: a.bgImageId,
                                onSelect: function ( m ) { props.setAttributes( { bgImageId: m.id } ); },
                                render: function ( obj ) {
                                    return el( c.Button, { variant: 'secondary', onClick: obj.open },
                                        a.bgImageId ? __( 'Cambiar imagen de fondo', 'ago' ) : __( 'Imagen de fondo (opcional)', 'ago' ) );
                                }
                            } )
                        ),
                        a.bgImageId ? el( c.Button, { variant: 'link', isDestructive: true, onClick: function () { props.setAttributes( { bgImageId: 0 } ); } },
                            __( 'Quitar imagen', 'ago' ) ) : null
                    ),
                    el( c.PanelBody, { title: __( 'Imágenes decorativas laterales', 'ago' ), initialOpen: false },
                        el( 'p', { style: { color: '#757575', fontSize: '12px', marginTop: 0 } },
                            __( 'Pegadas a los bordes de la pantalla y centradas verticalmente respecto a toda la sección (ej. grecas).', 'ago' ) ),
                        picker( __( 'Imagen izquierda', 'ago' ), 'decoLeftId' ),
                        picker( __( 'Imagen derecha', 'ago' ), 'decoRightId' )
                    )
                ),
                el( 'section', blockProps,
                    imgUrl ? el( 'img', { className: 'section-bg__img', src: imgUrl, alt: '' } ) : null,
                    urls.left ? el( 'img', { className: 'section-bg__deco section-bg__deco--left', src: urls.left, alt: '' } ) : null,
                    urls.right ? el( 'img', { className: 'section-bg__deco section-bg__deco--right', src: urls.right, alt: '' } ) : null,
                    el( 'div', innerProps )
                )
            );
        },
        save: function () {
            return el( be.InnerBlocks.Content );
        }
    } );
} )( window.wp );
