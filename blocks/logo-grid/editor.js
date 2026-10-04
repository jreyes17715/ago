/**
 * Bloque Cuadrícula de logos — editor (JS plano, sin build).
 * Logos desde la biblioteca (selección múltiple; el orden se cambia arrastrando
 * en la ventana de la galería). Logos por fila y enlaces en la barra lateral.
 */
( function ( wp ) {
    var el        = wp.element.createElement;
    var __        = wp.i18n.__;
    var useSelect = wp.data.useSelect;
    var be        = wp.blockEditor;
    var c         = wp.components;

    wp.blocks.registerBlockType( 'ago/logo-grid', {
        edit: function ( props ) {
            var a     = props.attributes;
            var logos = a.logos || [];
            var ids   = logos.map( function ( l ) { return l.id; } );

            var media = useSelect( function ( select ) {
                return ids.map( function ( id ) { return select( 'core' ).getMedia( id ); } );
            }, [ ids.join( ',' ) ] );

            function onSelect( items ) {
                // Conserva el enlace de los logos que ya estaban.
                var prev = {};
                logos.forEach( function ( l ) { prev[ l.id ] = l.url || ''; } );
                props.setAttributes( {
                    logos: items.map( function ( m ) { return { id: m.id, url: prev[ m.id ] || '' }; } )
                } );
            }

            function setUrl( index, url ) {
                var next = logos.map( function ( l, i ) { return i === index ? { id: l.id, url: url } : l; } );
                props.setAttributes( { logos: next } );
            }

            function remove( index ) {
                props.setAttributes( { logos: logos.filter( function ( l, i ) { return i !== index; } ) } );
            }

            var picker = el( be.MediaUploadCheck, null,
                el( be.MediaUpload, {
                    allowedTypes: [ 'image' ],
                    multiple: true,
                    gallery: true,
                    value: ids,
                    onSelect: onSelect,
                    render: function ( obj ) {
                        return el( c.Button, { variant: logos.length ? 'secondary' : 'primary', onClick: obj.open },
                            logos.length ? __( 'Agregar / quitar / ordenar logos', 'ago' ) : __( 'Seleccionar logos', 'ago' ) );
                    }
                } )
            );

            return el( wp.element.Fragment, null,
                el( be.InspectorControls, null,
                    el( c.PanelBody, { title: __( 'Cuadrícula', 'ago' ), initialOpen: true },
                        el( c.RangeControl, {
                            label: __( 'Logos por fila', 'ago' ),
                            value: a.columns, min: 1, max: 6,
                            onChange: function ( v ) { props.setAttributes( { columns: v } ); }
                        } ),
                        picker
                    ),
                    logos.length ? el( c.PanelBody, { title: __( 'Enlaces (opcional)', 'ago' ), initialOpen: false },
                        logos.map( function ( l, i ) {
                            var m = media[ i ];
                            return el( 'div', { key: l.id, style: { display: 'flex', gap: '8px', alignItems: 'flex-end', marginBottom: '12px' } },
                                m ? el( 'img', { src: m.source_url, alt: '', style: { width: '48px', height: '32px', objectFit: 'contain' } } ) : null,
                                el( 'div', { style: { flex: 1 } },
                                    el( c.TextControl, {
                                        label: ( m && m.title && m.title.rendered ) || ( __( 'Logo', 'ago' ) + ' ' + ( i + 1 ) ),
                                        value: l.url, type: 'url', placeholder: 'https://',
                                        onChange: function ( v ) { setUrl( i, v ); }
                                    } )
                                ),
                                el( c.Button, { icon: 'no-alt', label: __( 'Quitar', 'ago' ), isDestructive: true, onClick: function () { remove( i ); } } )
                            );
                        } )
                    ) : null
                ),
                el( 'section', be.useBlockProps( { className: 'logo-grid', style: { '--logo-grid-columns': a.columns } } ),
                    logos.length
                        ? el( 'ul', { className: 'logo-grid__list' },
                            logos.map( function ( l, i ) {
                                var m = media[ i ];
                                return el( 'li', { key: l.id, className: 'logo-grid__item' },
                                    m ? el( 'img', { className: 'logo-grid__img', src: m.source_url, alt: '' } ) : el( c.Spinner )
                                );
                            } )
                        )
                        : el( c.Placeholder, { icon: 'grid-view', label: __( 'Cuadrícula de logos', 'ago' ) }, picker )
                )
            );
        },
        save: function () {
            return null;
        }
    } );
} )( window.wp );
