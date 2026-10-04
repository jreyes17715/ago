/**
 * Bloque Pilares en zigzag — editor (JS plano, sin build).
 * Ícono: clic en el círculo para elegirlo. Título y contenido en el lienzo.
 * Subir / bajar / quitar en cada ítem; "Agregar ítem" al final. Columnas en la barra lateral.
 */
( function ( wp ) {
    var el        = wp.element.createElement;
    var __        = wp.i18n.__;
    var useSelect = wp.data.useSelect;
    var be        = wp.blockEditor;
    var c         = wp.components;

    function pad( n ) { return ( n < 10 ? '0' : '' ) + n; }

    wp.blocks.registerBlockType( 'ago/zigzag-items', {
        edit: function ( props ) {
            var items = props.attributes.items || [];
            var ids   = items.map( function ( it ) { return it.imageId || 0; } );

            var urls = useSelect( function ( select ) {
                return ids.map( function ( id ) {
                    if ( ! id ) { return ''; }
                    var m = select( 'core' ).getMedia( id );
                    return m ? m.source_url : '';
                } );
            }, [ ids.join( ',' ) ] );

            function update( i, key, value ) {
                props.setAttributes( { items: items.map( function ( it, j ) {
                    if ( j !== i ) { return it; }
                    var copy = { imageId: it.imageId || 0, title: it.title || '', content: it.content || '' };
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
                props.setAttributes( { items: items.concat( [ { imageId: 0, title: '', content: '' } ] ) } );
            }

            return el( wp.element.Fragment, null,
                el( be.InspectorControls, null,
                    el( c.PanelBody, { title: __( 'Diseño', 'ago' ), initialOpen: true },
                        el( c.RangeControl, {
                            label: __( 'Ítems por fila', 'ago' ), value: props.attributes.columns, min: 2, max: 6,
                            onChange: function ( v ) { props.setAttributes( { columns: v } ); }
                        } )
                    )
                ),
                el( 'section', be.useBlockProps( { className: 'zigzag-items', style: { '--zz-columns': props.attributes.columns } } ),
                    el( 'ol', { className: 'zigzag-items__list' },
                        items.map( function ( it, i ) {
                            return el( 'li', { key: i, className: 'zigzag-items__item' },
                                el( 'div', { className: 'zigzag-items__media' },
                                    el( be.MediaUploadCheck, null,
                                        el( be.MediaUpload, {
                                            allowedTypes: [ 'image' ], value: it.imageId,
                                            onSelect: function ( m ) { update( i, 'imageId', m.id ); },
                                            render: function ( obj ) {
                                                return el( 'button', {
                                                    type: 'button', onClick: obj.open, className: 'zigzag-items__pick',
                                                    title: __( 'Elegir ícono', 'ago' )
                                                }, urls[ i ] ? el( 'img', { className: 'zigzag-items__img', src: urls[ i ], alt: '' } ) : '+' );
                                            }
                                        } )
                                    ),
                                    el( 'span', { className: 'zigzag-items__number' }, pad( i + 1 ) )
                                ),
                                el( 'div', { className: 'zigzag-items__body' },
                                    el( be.RichText, {
                                        tagName: 'h3', className: 'zigzag-items__title', allowedFormats: [],
                                        value: it.title, onChange: function ( v ) { update( i, 'title', v ); },
                                        placeholder: __( 'Título', 'ago' )
                                    } ),
                                    el( be.RichText, {
                                        tagName: 'p', className: 'zigzag-items__text',
                                        allowedFormats: [ 'core/bold', 'core/italic', 'core/link' ],
                                        value: it.content, onChange: function ( v ) { update( i, 'content', v ); },
                                        placeholder: __( 'Contenido…', 'ago' )
                                    } )
                                ),
                                el( 'div', { className: 'zigzag-items__tools' },
                                    el( c.Button, { icon: 'arrow-left-alt2', label: __( 'Mover antes', 'ago' ), size: 'small', disabled: i === 0, onClick: function () { move( i, -1 ); } } ),
                                    el( c.Button, { icon: 'arrow-right-alt2', label: __( 'Mover después', 'ago' ), size: 'small', disabled: i === items.length - 1, onClick: function () { move( i, 1 ); } } ),
                                    el( c.Button, { icon: 'no-alt', label: __( 'Quitar', 'ago' ), size: 'small', isDestructive: true, onClick: function () { remove( i ); } } )
                                )
                            );
                        } )
                    ),
                    el( c.Button, { variant: 'secondary', icon: 'plus', onClick: add, style: { marginTop: '24px' } }, __( 'Agregar ítem', 'ago' ) )
                )
            );
        },
        save: function () {
            return null;
        }
    } );
} )( window.wp );
