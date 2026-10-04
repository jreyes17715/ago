/**
 * Bloque Equipo (junta directiva) — editor (JS plano, sin build).
 * Clic en la foto para elegirla. Título, puesto y nombre se escriben en el lienzo.
 * Mover / quitar en cada tarjeta; "Agregar persona" al final. Columnas en la barra lateral.
 */
( function ( wp ) {
    var el        = wp.element.createElement;
    var __        = wp.i18n.__;
    var useSelect = wp.data.useSelect;
    var be        = wp.blockEditor;
    var c         = wp.components;

    wp.blocks.registerBlockType( 'ago/team-grid', {
        edit: function ( props ) {
            var a     = props.attributes;
            var items = a.items || [];
            var ids   = items.map( function ( it ) { return it.imageId || 0; } );

            var urls = useSelect( function ( select ) {
                return ids.map( function ( id ) {
                    if ( ! id ) { return ''; }
                    var m = select( 'core' ).getMedia( id );
                    if ( ! m ) { return ''; }
                    var s = m.media_details && m.media_details.sizes;
                    return ( s && s.medium_large && s.medium_large.source_url ) || m.source_url;
                } );
            }, [ ids.join( ',' ) ] );

            function update( i, key, value ) {
                props.setAttributes( { items: items.map( function ( it, j ) {
                    if ( j !== i ) { return it; }
                    var copy = { imageId: it.imageId || 0, role: it.role || '', name: it.name || '', quote: it.quote || '' };
                    copy[ key ] = value;
                    return copy;
                } ) } );
            }
            function move( i, dir ) {
                var j = i + dir;
                if ( j < 0 || j >= items.length ) { return; }
                var next = items.slice(); var t = next[ i ]; next[ i ] = next[ j ]; next[ j ] = t;
                props.setAttributes( { items: next } );
            }
            function remove( i ) {
                props.setAttributes( { items: items.filter( function ( x, j ) { return j !== i; } ) } );
            }
            function add() {
                props.setAttributes( { items: items.concat( [ { imageId: 0, role: '', name: '', quote: '' } ] ) } );
            }

            return el( wp.element.Fragment, null,
                el( be.InspectorControls, null,
                    el( c.PanelBody, { title: __( 'Diseño', 'ago' ), initialOpen: true },
                        el( c.RangeControl, {
                            label: __( 'Personas por fila', 'ago' ), value: a.columns, min: 1, max: 4,
                            onChange: function ( v ) { props.setAttributes( { columns: v } ); }
                        } ),
                        el( c.SelectControl, {
                            label: __( 'Estilo del nombre', 'ago' ), value: a.nameStyle,
                            options: [
                                { label: __( 'Normal (con puesto)', 'ago' ),   value: 'default' },
                                { label: __( 'Centrado en negrita', 'ago' ),   value: 'centered' }
                            ],
                            help: __( 'Centrado: ideal sin puesto (ej. Nuestros Maestros).', 'ago' ),
                            onChange: function ( v ) { props.setAttributes( { nameStyle: v } ); }
                        } )
                    )
                ),
                el( 'section', be.useBlockProps( { className: 'team-grid' + ( a.nameStyle === 'centered' ? ' team-grid--name-centered' : '' ), style: { '--tg-columns': a.columns } } ),
                    el( be.RichText, {
                        tagName: 'h2', className: 'team-grid__title', allowedFormats: [],
                        value: a.title, onChange: function ( v ) { props.setAttributes( { title: v } ); },
                        placeholder: __( 'Título (ej. Periodo 2025-2027)', 'ago' )
                    } ),
                    el( 'ul', { className: 'team-grid__list' },
                        items.map( function ( it, i ) {
                            return el( 'li', { key: i, className: 'team-grid__item' },
                                el( be.MediaUploadCheck, null,
                                    el( be.MediaUpload, {
                                        allowedTypes: [ 'image' ], value: it.imageId,
                                        onSelect: function ( m ) { update( i, 'imageId', m.id ); },
                                        render: function ( obj ) {
                                            return el( 'button', { type: 'button', className: 'team-grid__photo team-grid__pick', onClick: obj.open, title: __( 'Elegir foto', 'ago' ) },
                                                urls[ i ] ? el( 'img', { className: 'team-grid__img', src: urls[ i ], alt: '' } ) : __( '+ Foto', 'ago' )
                                            );
                                        }
                                    } )
                                ),
                                el( 'div', { className: 'team-grid__panel' },
                                    el( be.RichText, {
                                        tagName: 'span', className: 'team-grid__role', allowedFormats: [],
                                        value: it.role, onChange: function ( v ) { update( i, 'role', v ); },
                                        placeholder: __( 'Puesto', 'ago' )
                                    } ),
                                    el( be.RichText, {
                                        tagName: 'p', className: 'team-grid__name', allowedFormats: [],
                                        value: it.name, onChange: function ( v ) { update( i, 'name', v ); },
                                        placeholder: __( 'Nombre', 'ago' )
                                    } )
                                ),
                                el( 'div', { className: 'team-grid__quote-edit' },
                                    el( 'span', { className: 'team-grid__quote-edit-label' }, __( 'Cita al voltear (opcional)', 'ago' ) ),
                                    el( be.RichText, {
                                        tagName: 'p', allowedFormats: [ 'core/bold', 'core/italic' ],
                                        value: it.quote, onChange: function ( v ) { update( i, 'quote', v ); },
                                        placeholder: __( '“Escribe la cita… (Ctrl+B para negrita). Vacío = la tarjeta no gira.”', 'ago' )
                                    } )
                                ),
                                el( 'div', { className: 'team-grid__tools' },
                                    el( c.Button, { icon: 'arrow-left-alt2', label: __( 'Mover antes', 'ago' ), size: 'small', disabled: i === 0, onClick: function () { move( i, -1 ); } } ),
                                    el( c.Button, { icon: 'arrow-right-alt2', label: __( 'Mover después', 'ago' ), size: 'small', disabled: i === items.length - 1, onClick: function () { move( i, 1 ); } } ),
                                    el( c.Button, { icon: 'no-alt', label: __( 'Quitar', 'ago' ), size: 'small', isDestructive: true, onClick: function () { remove( i ); } } )
                                )
                            );
                        } )
                    ),
                    el( c.Button, { variant: 'secondary', icon: 'plus', onClick: add, style: { marginTop: '24px' } }, __( 'Agregar persona', 'ago' ) )
                )
            );
        },
        save: function () {
            return null;
        }
    } );
} )( window.wp );
