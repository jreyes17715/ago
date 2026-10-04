/**
 * Bloque Lista con puntos — editor (JS plano, sin build).
 * Título y cada punto se escriben en el lienzo (los puntos admiten negrita y enlaces).
 * "Agregar punto" al final; la ✕ de cada punto lo quita. Columnas en la barra lateral.
 */
( function ( wp ) {
    var el = wp.element.createElement;
    var __ = wp.i18n.__;
    var be = wp.blockEditor;
    var c  = wp.components;

    wp.blocks.registerBlockType( 'ago/point-list', {
        edit: function ( props ) {
            var a     = props.attributes;
            var items = a.items || [];
            var cols  = a.columns === 1 ? 1 : 2;
            var per   = Math.max( 1, Math.ceil( items.length / cols ) );

            function setItem( i, v ) {
                var next = items.slice(); next[ i ] = v; props.setAttributes( { items: next } );
            }
            function removeItem( i ) {
                props.setAttributes( { items: items.filter( function ( x, j ) { return j !== i; } ) } );
            }
            function addItem() {
                props.setAttributes( { items: items.concat( [ '' ] ) } );
            }

            var groups = [];
            for ( var g = 0; g < cols; g++ ) {
                groups.push( items.map( function ( v, i ) { return i; } ).slice( g * per, ( g + 1 ) * per ) );
            }

            return el( wp.element.Fragment, null,
                el( be.InspectorControls, null,
                    el( c.PanelBody, { title: __( 'Diseño', 'ago' ), initialOpen: true },
                        el( c.SelectControl, {
                            label: __( 'Columnas', 'ago' ), value: String( cols ),
                            options: [ { label: '1', value: '1' }, { label: '2', value: '2' } ],
                            onChange: function ( v ) { props.setAttributes( { columns: parseInt( v, 10 ) } ); }
                        } ),
                        el( 'p', { style: { color: '#757575', fontSize: '12px' } },
                            __( 'Los puntos se reparten en orden: la primera columna recibe la mitad (redondeada hacia arriba).', 'ago' ) )
                    )
                ),
                el( 'section', be.useBlockProps( { className: 'point-list point-list--cols-' + cols } ),
                    el( be.RichText, {
                        tagName: 'h2', className: 'point-list__title', allowedFormats: [],
                        value: a.title, onChange: function ( v ) { props.setAttributes( { title: v } ); },
                        placeholder: __( 'Título (opcional)', 'ago' )
                    } ),
                    el( 'div', { className: 'point-list__columns' },
                        groups.map( function ( group, gi ) {
                            return el( 'ul', { key: gi, className: 'point-list__list' },
                                group.map( function ( i ) {
                                    return el( 'li', { key: i, className: 'point-list__item' },
                                        el( 'span', { className: 'point-list__dot' } ),
                                        el( be.RichText, {
                                            tagName: 'p', className: 'point-list__text',
                                            allowedFormats: [ 'core/bold', 'core/italic', 'core/link' ],
                                            value: items[ i ], onChange: function ( v ) { setItem( i, v ); },
                                            placeholder: __( 'Escribe el punto…', 'ago' )
                                        } ),
                                        el( c.Button, {
                                            icon: 'no-alt', label: __( 'Quitar punto', 'ago' ), isDestructive: true, size: 'small',
                                            className: 'point-list__remove', onClick: function () { removeItem( i ); }
                                        } )
                                    );
                                } )
                            );
                        } )
                    ),
                    el( c.Button, { variant: 'secondary', icon: 'plus', onClick: addItem, style: { marginTop: '24px' } },
                        __( 'Agregar punto', 'ago' ) )
                )
            );
        },
        save: function () {
            return null;
        }
    } );
} )( window.wp );
