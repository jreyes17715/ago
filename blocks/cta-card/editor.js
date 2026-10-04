/**
 * Bloque Tarjeta CTA — editor (JS plano, sin build).
 * Título y contenido en el lienzo; fondo, diseño del título y botones en la barra lateral.
 */
( function ( wp ) {
    var el        = wp.element.createElement;
    var __        = wp.i18n.__;
    var useSelect = wp.data.useSelect;
    var be        = wp.blockEditor;
    var c         = wp.components;

    var VARIANTS = [
        { label: __( 'Claro', 'ago' ),    value: 'light' },
        { label: __( 'Oscuro', 'ago' ),   value: 'dark' },
        { label: __( 'Contorno', 'ago' ), value: 'outline' }
    ];
    var ICONS = [
        { label: __( 'Flecha', 'ago' ),   value: 'arrow' },
        { label: __( 'Correo', 'ago' ),   value: 'mail' },
        { label: __( 'Teléfono', 'ago' ), value: 'phone' }
    ];
    var ICON_CHAR = { arrow: '→', mail: '✉', phone: '☎' };

    wp.blocks.registerBlockType( 'ago/cta-card', {
        edit: function ( props ) {
            var a   = props.attributes;
            var set = function ( key ) {
                return function ( value ) { var o = {}; o[ key ] = value; props.setAttributes( o ); };
            };
            var bg = useSelect( function ( select ) {
                if ( ! a.bgId ) { return ''; }
                var m = select( 'core' ).getMedia( a.bgId );
                return m ? m.source_url : '';
            }, [ a.bgId ] );

            function btnPanel( n ) {
                var k = 'btn' + n;
                return el( c.PanelBody, { title: __( 'Botón', 'ago' ) + ' ' + n, initialOpen: n === 1 },
                    el( c.TextControl, { label: __( 'Texto (vacío = oculto)', 'ago' ), value: a[ k + 'Text' ], onChange: set( k + 'Text' ) } ),
                    el( c.TextControl, {
                        label: __( 'Enlace', 'ago' ), value: a[ k + 'Url' ], onChange: set( k + 'Url' ),
                        help: __( 'Con ícono Correo/Teléfono puedes poner solo el correo o el número.', 'ago' )
                    } ),
                    el( c.SelectControl, { label: __( 'Ícono', 'ago' ), value: a[ k + 'Icon' ], options: ICONS, onChange: set( k + 'Icon' ) } ),
                    el( c.SelectControl, { label: __( 'Estilo', 'ago' ), value: a[ k + 'Variant' ], options: VARIANTS, onChange: set( k + 'Variant' ) } )
                );
            }

            function preview( n ) {
                var k = 'btn' + n;
                if ( ! a[ k + 'Text' ] ) { return null; }
                return el( 'span', { className: 'ago-btn ago-btn--' + a[ k + 'Variant' ] + ' cta-card__btn' },
                    el( 'span', { className: 'ago-btn__icon' }, ICON_CHAR[ a[ k + 'Icon' ] ] || '→' ),
                    el( 'span', { className: 'ago-btn__label' }, a[ k + 'Text' ] )
                );
            }

            var titleClass = 'ago-title ago-title--' + ( a.titleSize === 'xl' ? 'xl' : 'lg' ) +
                ( a.titleStacked ? ' ago-title--stacked' : '' ) + ' cta-card__title';

            return el( wp.element.Fragment, null,
                el( be.InspectorControls, null,
                    el( c.PanelBody, { title: __( 'Fondo y título', 'ago' ), initialOpen: true },
                        el( be.MediaUploadCheck, null,
                            el( be.MediaUpload, {
                                allowedTypes: [ 'image' ], value: a.bgId,
                                onSelect: function ( m ) { props.setAttributes( { bgId: m.id } ); },
                                render: function ( obj ) {
                                    return el( c.Button, { variant: 'secondary', onClick: obj.open },
                                        a.bgId ? __( 'Cambiar imagen de fondo', 'ago' ) : __( 'Seleccionar imagen de fondo', 'ago' ) );
                                }
                            } )
                        ),
                        a.bgId ? el( c.Button, { variant: 'link', isDestructive: true, onClick: function () { props.setAttributes( { bgId: 0 } ); } },
                            __( 'Quitar imagen', 'ago' ) ) : null,
                        el( 'hr' ),
                        el( c.SelectControl, {
                            label: __( 'Tamaño del título', 'ago' ), value: a.titleSize,
                            options: [ { label: __( 'Mediano (60px)', 'ago' ), value: 'lg' }, { label: __( 'Grande (80px)', 'ago' ), value: 'xl' } ],
                            onChange: set( 'titleSize' )
                        } ),
                        el( c.ToggleControl, {
                            label: __( 'Parte fina y negrita en líneas separadas', 'ago' ),
                            checked: a.titleStacked, onChange: set( 'titleStacked' )
                        } )
                    ),
                    btnPanel( 1 ),
                    btnPanel( 2 )
                ),
                el( 'section', be.useBlockProps( { className: 'cta-card' } ),
                    el( 'div', { className: 'cta-card__box' },
                        bg ? el( 'img', { className: 'cta-card__bg', src: bg, alt: '' } ) : null,
                        el( 'div', { className: 'cta-card__body' },
                            el( 'h2', { className: titleClass },
                                el( be.RichText, {
                                    tagName: 'span', className: 'ago-title__light', allowedFormats: [],
                                    value: a.titleLight, onChange: set( 'titleLight' ), placeholder: __( 'Título fino (opcional)', 'ago' )
                                } ),
                                el( be.RichText, {
                                    tagName: 'span', className: 'ago-title__bold', allowedFormats: [],
                                    value: a.titleBold, onChange: set( 'titleBold' ), placeholder: __( 'Título negrita', 'ago' )
                                } )
                            ),
                            el( be.RichText, {
                                tagName: 'p', className: 'cta-card__text',
                                allowedFormats: [ 'core/bold', 'core/italic', 'core/link' ],
                                value: a.content, onChange: set( 'content' ),
                                placeholder: __( 'Contenido (opcional)…', 'ago' )
                            } ),
                            ( a.btn1Text || a.btn2Text ) ? el( 'div', { className: 'cta-card__actions' }, preview( 1 ), preview( 2 ) ) : null
                        )
                    )
                )
            );
        },
        save: function () {
            return null;
        }
    } );
} )( window.wp );
