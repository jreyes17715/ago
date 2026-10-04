/**
 * Bloque Tarjetas numeradas — editor (JS plano, sin build).
 * Título y contenido en el lienzo; número automático. Mover / quitar por tarjeta,
 * "Agregar tarjeta" al final. Tarjetas por fila en la barra lateral.
 */
( function ( wp ) {
    var el = wp.element.createElement;
    var __ = wp.i18n.__;
    var be = wp.blockEditor;
    var c  = wp.components;

    function pad( n ) { return ( n < 10 ? '0' : '' ) + n; }

    wp.blocks.registerBlockType( 'ago/numbered-cards', {
        edit: function ( props ) {
            var a     = props.attributes;
            var items = a.items || [];

            function update( i, key, value ) {
                props.setAttributes( { items: items.map( function ( it, j ) {
                    if ( j !== i ) { return it; }
                    var copy = { title: it.title || '', content: it.content || '' };
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
                props.setAttributes( { items: items.concat( [ { title: '', content: '' } ] ) } );
            }

            return el( wp.element.Fragment, null,
                el( be.InspectorControls, null,
                    el( c.PanelBody, { title: __( 'Diseño', 'ago' ), initialOpen: true },
                        el( c.RangeControl, {
                            label: __( 'Tarjetas por fila', 'ago' ), value: a.columns, min: 1, max: 4,
                            onChange: function ( v ) { props.setAttributes( { columns: v } ); }
                        } )
                    )
                ),
                el( 'section', be.useBlockProps( { className: 'numbered-cards', style: { '--nc-columns': a.columns } } ),
                    el( 'ol', { className: 'numbered-cards__list' },
                        items.map( function ( it, i ) {
                            return el( 'li', { key: i, className: 'numbered-cards__item' },
                                el( 'div', { className: 'numbered-cards__card' },
                                    el( 'div', { className: 'numbered-cards__inner' },
                                        el( 'span', { className: 'numbered-cards__number' }, pad( i + 1 ) ),
                                        el( be.RichText, {
                                            tagName: 'h3', className: 'numbered-cards__title', allowedFormats: [],
                                            value: it.title, onChange: function ( v ) { update( i, 'title', v ); },
                                            placeholder: __( 'Título', 'ago' )
                                        } ),
                                        el( be.RichText, {
                                            tagName: 'p', className: 'numbered-cards__text',
                                            allowedFormats: [ 'core/bold', 'core/italic', 'core/link' ],
                                            value: it.content, onChange: function ( v ) { update( i, 'content', v ); },
                                            placeholder: __( 'Contenido…', 'ago' )
                                        } )
                                    )
                                ),
                                el( 'div', { className: 'numbered-cards__tools' },
                                    el( c.Button, { icon: 'arrow-left-alt2', label: __( 'Mover antes', 'ago' ), size: 'small', disabled: i === 0, onClick: function () { move( i, -1 ); } } ),
                                    el( c.Button, { icon: 'arrow-right-alt2', label: __( 'Mover después', 'ago' ), size: 'small', disabled: i === items.length - 1, onClick: function () { move( i, 1 ); } } ),
                                    el( c.Button, { icon: 'no-alt', label: __( 'Quitar', 'ago' ), size: 'small', isDestructive: true, onClick: function () { remove( i ); } } )
                                )
                            );
                        } )
                    ),
                    el( c.Button, { variant: 'secondary', icon: 'plus', onClick: add, style: { marginTop: '24px' } }, __( 'Agregar tarjeta', 'ago' ) )
                )
            );
        },
        save: function () {
            return null;
        }
    } );
} )( window.wp );
