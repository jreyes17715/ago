/**
 * Bloque Hero — editor (sin build: JS plano con wp.element).
 * Campos en la barra lateral; la vista previa es el mismo render.php.
 */
( function ( wp ) {
    var el                 = wp.element.createElement;
    var __                 = wp.i18n.__;
    var useBlockProps      = wp.blockEditor.useBlockProps;
    var InspectorControls  = wp.blockEditor.InspectorControls;
    var MediaUpload        = wp.blockEditor.MediaUpload;
    var MediaUploadCheck   = wp.blockEditor.MediaUploadCheck;
    var PanelBody          = wp.components.PanelBody;
    var TextControl        = wp.components.TextControl;
    var TextareaControl    = wp.components.TextareaControl;
    var Button             = wp.components.Button;
    var ServerSideRender   = wp.serverSideRender;

    wp.blocks.registerBlockType( 'ago/hero', {
        edit: function ( props ) {
            var a   = props.attributes;
            var set = function ( key ) {
                return function ( value ) {
                    var obj = {};
                    obj[ key ] = value;
                    props.setAttributes( obj );
                };
            };

            return el( 'div', useBlockProps(),
                el( InspectorControls, null,
                    el( PanelBody, { title: __( 'Fondo', 'ago' ), initialOpen: true },
                        el( MediaUploadCheck, null,
                            el( MediaUpload, {
                                allowedTypes: [ 'image' ],
                                value: a.bgId,
                                onSelect: function ( media ) { props.setAttributes( { bgId: media.id } ); },
                                render: function ( obj ) {
                                    return el( Button, { variant: 'secondary', onClick: obj.open },
                                        a.bgId ? __( 'Cambiar imagen', 'ago' ) : __( 'Seleccionar imagen', 'ago' )
                                    );
                                }
                            } )
                        ),
                        a.bgId ? el( Button, {
                            variant: 'link',
                            isDestructive: true,
                            onClick: function () { props.setAttributes( { bgId: 0 } ); }
                        }, __( 'Quitar imagen', 'ago' ) ) : null
                    ),
                    el( PanelBody, { title: __( 'Contenido', 'ago' ), initialOpen: true },
                        el( TextControl,     { label: __( 'Pre-título', 'ago' ), value: a.pretitle, onChange: set( 'pretitle' ) } ),
                        el( TextControl,     { label: __( 'Título', 'ago' ),     value: a.title,    onChange: set( 'title' ) } ),
                        el( TextareaControl, { label: __( 'Contenido', 'ago' ),  value: a.content,  onChange: set( 'content' ) } ),
                        el( TextControl,     { label: __( 'Fecha', 'ago' ),      value: a.date,     onChange: set( 'date' ) } )
                    ),
                    el( PanelBody, { title: __( 'Botón', 'ago' ), initialOpen: true },
                        el( TextControl, { label: __( 'Texto', 'ago' ),  value: a.btnText, onChange: set( 'btnText' ) } ),
                        el( TextControl, { label: __( 'Enlace', 'ago' ), value: a.btnUrl,  onChange: set( 'btnUrl' ), type: 'url' } )
                    )
                ),
                el( ServerSideRender, { block: 'ago/hero', attributes: a } )
            );
        },
        save: function () {
            return null;
        }
    } );
} )( window.wp );
