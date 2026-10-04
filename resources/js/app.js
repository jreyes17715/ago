/* ==========================================================================
   AGO — frontend JS
   ========================================================================== */

document.addEventListener( 'DOMContentLoaded', function () {

	/* ---------------------------------------------------------------- */
	/*  Header — toggle menu mobile                                      */
	/* ---------------------------------------------------------------- */
	var header = document.getElementById( 'masthead' );
	var toggle = header ? header.querySelector( '.header__toggle' ) : null;

	if ( header && toggle ) {
		toggle.addEventListener( 'click', function () {
			var isOpen = header.classList.toggle( 'is-open' );
			toggle.setAttribute( 'aria-expanded', isOpen ? 'true' : 'false' );
			document.body.classList.toggle( 'is-menu-open', isOpen );
		} );
	}

	/* ---------------------------------------------------------------- */
	/*  Header — fondo solido + pegado al top despues de 30px de scroll  */
	/*  (transparente arriba del todo, para que se vea el hero atras).   */
	/* ---------------------------------------------------------------- */
	if ( header ) {
		var SCROLL_THRESHOLD = 30;
		var scrollTicking = false;

		function updateHeaderScrollState() {
			header.classList.toggle( 'site-header--scrolled', window.scrollY > SCROLL_THRESHOLD );
			scrollTicking = false;
		}

		function onScroll() {
			if ( ! scrollTicking ) {
				window.requestAnimationFrame( updateHeaderScrollState );
				scrollTicking = true;
			}
		}

		window.addEventListener( 'scroll', onScroll, { passive: true } );
		updateHeaderScrollState(); // por si la pagina carga ya scrolleada
	}

	/* ---------------------------------------------------------------- */
	/*  Parallax flotante al mover el mouse — cualquier bloque puede      */
	/*  usarlo agregando la clase ".dd-parallax" + data-depth/data-rotate */
	/*  al elemento (ver blocks/hero, blocks/servicios).                  */
	/* ---------------------------------------------------------------- */
	var floaters = document.querySelectorAll( '.dd-parallax' );

	if ( floaters.length && window.matchMedia( '(hover: hover) and (pointer: fine)' ).matches ) {
		var ticking = false;
		var mouseX  = 0;
		var mouseY  = 0;

		window.addEventListener( 'mousemove', function ( e ) {
			mouseX = e.clientX - window.innerWidth / 2;
			mouseY = e.clientY - window.innerHeight / 2;

			if ( ! ticking ) {
				window.requestAnimationFrame( updateParallax );
				ticking = true;
			}
		} );

		function updateParallax() {
			floaters.forEach( function ( el ) {
				var depth  = parseFloat( el.getAttribute( 'data-depth' ) ) || 0.02;
				var rotate = parseFloat( el.getAttribute( 'data-rotate' ) ) || 0;
				var x = mouseX * depth;
				var y = mouseY * depth;
				el.style.transform = 'translate3d(' + x + 'px, ' + y + 'px, 0) rotate(' + rotate + 'deg)';
			} );
			ticking = false;
		}
	}

	/* ---------------------------------------------------------------- */
	/*  Carrusel Swiper — bloque "Aliados" (logos, 3 a la vez)            */
	/*  Swiper carga desde CDN (ver inc/blocks/register-blocks.php), asi  */
	/*  que se inicializa de forma defensiva esperando a que exista.      */
	/* ---------------------------------------------------------------- */
	function initAliadosSwiper() {
		var nodes = document.querySelectorAll( '.aliados__swiper' );

		if ( ! nodes.length || typeof window.Swiper === 'undefined' ) {
			return false;
		}

		nodes.forEach( function ( node ) {
			if ( node.dataset.swiperInitialized ) {
				return;
			}

			node.dataset.swiperInitialized = 'true';

			// Elementos de paginacion propios, para no depender del markup PHP.
			var pagination = document.createElement( 'div' );
			pagination.className = 'swiper-pagination';
			node.appendChild( pagination );

			new window.Swiper( node, {
				slidesPerView: 1,
				spaceBetween: 16,
				pagination: { el: pagination, clickable: true },
				breakpoints: {
					640: { slidesPerView: 2 },
					900: { slidesPerView: 3 },
				},
			} );
		} );

		return true;
	}

	/* ---------------------------------------------------------------- */
	/*  Carrusel Swiper — bloque "Blog" (entradas nativas, 1 fila)        */
	/* ---------------------------------------------------------------- */
	function initBlogSwiper() {
		var nodes = document.querySelectorAll( '.blog__swiper' );

		if ( ! nodes.length || typeof window.Swiper === 'undefined' ) {
			return false;
		}

		nodes.forEach( function ( node ) {
			if ( node.dataset.swiperInitialized ) {
				return;
			}

			node.dataset.swiperInitialized = 'true';

			var pagination = document.createElement( 'div' );
			pagination.className = 'swiper-pagination';
			node.appendChild( pagination );

			new window.Swiper( node, {
				slidesPerView: 1,
				centeredSlides: false,
				spaceBetween: 24,
				pagination: { el: pagination, clickable: true },
				breakpoints: {
					// Desde tablet: 3 visibles, centrado — el slide del medio
					// queda mas chico y los laterales a tamaño completo
					// (ver .swiper-slide-active en blocks/blog.css).
					900: { slidesPerView: 3, centeredSlides: true, spaceBetween: 32 },
				},
			} );
		} );

		return true;
	}

	/* ---------------------------------------------------------------- */
	/*  Carrusel Swiper — bloque "Nuestro Equipo" (fotos grandes,         */
	/*  auto-scroll continuo sin pausas, tipo cinta). freeMode + autoplay */
	/*  con delay:0 hace que Swiper anime de un frame al siguiente sin    */
	/*  "parar" en cada slide, a diferencia de los otros carruseles.      */
	/* ---------------------------------------------------------------- */
	function initEquipoSwiper() {
		var nodes = document.querySelectorAll( '.equipo__swiper' );

		if ( ! nodes.length || typeof window.Swiper === 'undefined' ) {
			return false;
		}

		nodes.forEach( function ( node ) {
			if ( node.dataset.swiperInitialized ) {
				return;
			}

			node.dataset.swiperInitialized = 'true';

			new window.Swiper( node, {
				slidesPerView: 'auto',
				spaceBetween: 0,
				loop: true,
				speed: 6000,
				allowTouchMove: true,
				freeMode: { enabled: true, momentum: false },
				autoplay: {
					delay: 0,
					disableOnInteraction: false,
					pauseOnMouseEnter: false,
				},
			} );
		} );

		return true;
	}

	/* ---------------------------------------------------------------- */
	/*  Carrusel Swiper — bloque "Testimonios" (1 foto a la vez,          */
	/*  paginacion tipo "barra" en vez de dots + flecha "siguiente"       */
	/*  circular propia, ver testimonios.css).                            */
	/* ---------------------------------------------------------------- */
	function initTestimoniosSwiper() {
		var nodes = document.querySelectorAll( '.testimonios__swiper' );

		if ( ! nodes.length || typeof window.Swiper === 'undefined' ) {
			return false;
		}

		nodes.forEach( function ( node ) {
			if ( node.dataset.swiperInitialized ) {
				return;
			}

			node.dataset.swiperInitialized = 'true';

			// El boton "siguiente" vive FUERA de .testimonios__swiper (Swiper
			// le pone overflow:hidden a ".swiper" y recortaba el boton), asi
			// que se busca en el wrapper de arriba, no dentro de "node".
			var wrap       = node.closest( '.testimonios__col-carousel' ) || node.parentElement;
			var pagination = node.querySelector( '.testimonios__pagination' );
			var nextEl     = wrap ? wrap.querySelector( '.testimonios__next' ) : null;

			new window.Swiper( node, {
				slidesPerView: 1,
				loop: true,
				pagination: pagination ? { el: pagination, clickable: true } : false,
				navigation: nextEl ? { nextEl: nextEl } : false,
			} );
		} );

		return true;
	}

	/* ---------------------------------------------------------------- */
	/*  Carrusel Swiper — "Blogs Relacionados" (single.php, 3 a la vez,   */
	/*  con flechas prev/next visibles a los dos lados a la vez, a         */
	/*  diferencia de Testimonios que solo tiene "siguiente").            */
	/* ---------------------------------------------------------------- */
	function initRelacionadosSwiper() {
		var nodes = document.querySelectorAll( '.relacionados__swiper' );

		if ( ! nodes.length || typeof window.Swiper === 'undefined' ) {
			return false;
		}

		nodes.forEach( function ( node ) {
			if ( node.dataset.swiperInitialized ) {
				return;
			}

			node.dataset.swiperInitialized = 'true';

			// Los botones viven FUERA de .relacionados__swiper (mismo motivo
			// que en Testimonios: Swiper le pone overflow:hidden a ".swiper").
			var wrap    = node.closest( '.relacionados__carousel-wrap' ) || node.parentElement;
			var prevEl  = wrap ? wrap.querySelector( '.relacionados__prev' ) : null;
			var nextEl  = wrap ? wrap.querySelector( '.relacionados__next' ) : null;

			new window.Swiper( node, {
				slidesPerView: 1,
				spaceBetween: 24,
				loop: true,
				navigation: ( prevEl || nextEl ) ? { prevEl: prevEl, nextEl: nextEl } : false,
				breakpoints: {
					640: { slidesPerView: 2 },
					960: { slidesPerView: 3 },
				},
			} );
		} );

		return true;
	}

	// Swiper carga desde CDN (ver inc/blocks/register-blocks.php); si todavia
	// no esta disponible en DOMContentLoaded, reintentamos una vez en "load".
	var aliadosReady     = initAliadosSwiper();
	var blogReady        = initBlogSwiper();
	var equipoReady      = initEquipoSwiper();
	var testimoniosReady = initTestimoniosSwiper();
	var relacionadosReady = initRelacionadosSwiper();

	if ( ! aliadosReady || ! blogReady || ! equipoReady || ! testimoniosReady || ! relacionadosReady ) {
		window.addEventListener( 'load', function () {
			if ( ! aliadosReady ) {
				initAliadosSwiper();
			}
			if ( ! blogReady ) {
				initBlogSwiper();
			}
			if ( ! equipoReady ) {
				initEquipoSwiper();
			}
			if ( ! testimoniosReady ) {
				initTestimoniosSwiper();
			}
			if ( ! relacionadosReady ) {
				initRelacionadosSwiper();
			}
		} );
	}
} );
