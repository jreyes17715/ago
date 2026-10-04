/**
 * Bloque Línea de tiempo — editor (JS plano, sin build).
 * Todo se edita en el lienzo: título, contenido (negrita/enlaces) y año de cada tarjeta.
 * Botones para subir/bajar, quitar y agregar tarjetas. La alternancia claro/oscuro es automática.
 */
( function ( wp ) {
    var el = wp.element.createElement;
    var __ = wp.i18n.__;
    var be = wp.blockEditor;
    var c  = wp.components;

    wp.blocks.registerBlockType( 'ago/timeline', {
        edit: function ( props ) {
            var items = props.attributes.items || [];

            function update( i, key, value ) {
                var next = items.map( function ( it, j ) {
                    if ( j !== i ) { return it; }
                    var copy = { title: it.title || '', content: it.content || '', year: it.year || '' };
                    copy[ key ] = value;
                    return copy;
                } );
                props.setAttributes( { items: next } );
            }
            function move( i, dir ) {
                var j = i + dir;
                if ( j < 0 || j >= items.length ) { return; }
                var next = items.slice(); var tmp = next[ i ]; next[ i ] = next[ j ]; next[ j ] = tmp;
                props.setAttributes( { items: next } );
            }
            function remove( i ) {
                props.setAttributes( { items: items.filter( function ( x, j ) { return j !== i; } ) } );
            }
            function add() {
                props.setAttributes( { items: items.concat( [ { title: '', content: '', year: '' } ] ) } );
            }

            return el( 'section', be.useBlockProps( { className: 'timeline' } ),
                el( 'ol', { className: 'timeline__list' },
                    items.map( function ( it, i ) {
                        return el( 'li', { key: i, className: 'timeline__item' },
                            el( 'div', { className: 'timeline__card' },
                            el( 'div', { className: 'timeline__body' },
                                el( be.RichText, {
                                    tagName: 'h3', className: 'timeline__title', allowedFormats: [],
                                    value: it.title, onChange: function ( v ) { update( i, 'title', v ); },
                                    placeholder: __( 'Título', 'ago' )
                                } ),
                                el( be.RichText, {
                                    tagName: 'p', className: 'timeline__text',
                                    allowedFormats: [ 'core/bold', 'core/italic', 'core/link' ],
                                    value: it.content, onChange: function ( v ) { update( i, 'content', v ); },
                                    placeholder: __( 'Contenido…', 'ago' )
                                } )
                            ),
                            el( be.RichText, {
                                tagName: 'p', className: 'timeline__year', allowedFormats: [],
                                value: it.year, onChange: function ( v ) { update( i, 'year', v ); },
                                placeholder: __( 'Año', 'ago' )
                            } )
                            ),
                            el( 'div', { className: 'timeline__tools' },
                                el( c.Button, { icon: 'arrow-up-alt2', label: __( 'Subir', 'ago' ), size: 'small', disabled: i === 0, onClick: function () { move( i, -1 ); } } ),
                                el( c.Button, { icon: 'arrow-down-alt2', label: __( 'Bajar', 'ago' ), size: 'small', disabled: i === items.length - 1, onClick: function () { move( i, 1 ); } } ),
                                el( c.Button, { icon: 'no-alt', label: __( 'Quitar', 'ago' ), size: 'small', isDestructive: true, onClick: function () { remove( i ); } } )
                            )
                        );
                    } )
                ),
                el( c.Button, { variant: 'secondary', icon: 'plus', onClick: add, style: { marginTop: '24px' } }, __( 'Agregar tarjeta', 'ago' ) )
            );
        },
        save: function () {
            return null;
        }
    } );
} )( window.wp );
