/**
 * Kategoriknapperne som sidescroller på mobil:
 *  - den aktive kategori rulles ind i syne
 *  - fade i højre side skjules, når man er nået til enden
 *  - en lille animation ved hver indlæsning viser, at rækken kan scrolles
 */
( function () {
	const nav = document.querySelector( '.lov-cat-pills' );
	if ( ! nav ) {
		return;
	}
	const row = nav.querySelector( '.wp-block-navigation__container' );
	if ( ! row ) {
		return;
	}
	const mq = window.matchMedia( '(max-width: 860px)' );

	function overflowing() {
		return row.scrollWidth > row.clientWidth + 4;
	}
	function updateEnd() {
		const end = ! overflowing() || row.scrollLeft + row.clientWidth >= row.scrollWidth - 4;
		nav.classList.toggle( 'is-at-end', end );
	}

	function init() {
		if ( ! mq.matches ) {
			return;
		}
		// Vis den aktive kategori (fx "Æg" helt ude til højre)
		const active = row.querySelector( '.current-menu-item' );
		if ( active && overflowing() ) {
			const r = active.getBoundingClientRect();
			const rr = row.getBoundingClientRect();
			if ( r.right > rr.right - 24 ) {
				row.scrollLeft += r.right - rr.right + 40;
			}
		}
		updateEnd();

		// Animation: ved hver indlæsning – kun hvis der er mere at se, og først når rækken kan ses
		if ( ! overflowing() || row.scrollLeft > 0 ) {
			return;
		}
		// Én blød bevægelse helt til enden (så den sidste knap ses i sin helhed) og tilbage – uden stop.
		// Farten følger en sinuskurve: langsom start, hurtigst på midten, blød vending og blød landing.
		const DURATION = 1800;
		const play = () => {
			if ( window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches ) {
				return;
			}
			const end = row.scrollWidth - row.clientWidth;
			if ( end <= 0 ) {
				return;
			}
			let stopped = false;
			const stop = () => {
				stopped = true;
			};
			row.addEventListener( 'pointerdown', stop, { once: true } );
			row.addEventListener( 'touchstart', stop, { once: true, passive: true } );
			row.addEventListener( 'wheel', stop, { once: true, passive: true } );
			nav.classList.add( 'lov-hint' );
			const start = performance.now();
			const step = ( now ) => {
				if ( stopped ) {
					nav.classList.remove( 'lov-hint' );
					return;
				}
				const t = Math.min( 1, ( now - start ) / DURATION );
				row.scrollLeft = end * ( 1 - Math.cos( 2 * Math.PI * t ) ) / 2;
				if ( t < 1 ) {
					next( step );
				} else {
					row.scrollLeft = 0;
					nav.classList.remove( 'lov-hint' );
				}
			};
			next( step );
		};
		// Næste frame – med en timer som reserve, hvis browseren ikke tegner (fx skjult fane).
		const next = ( f ) => {
			let done = false;
			const go = () => {
				if ( ! done ) {
					done = true;
					f( performance.now() );
				}
			};
			if ( window.requestAnimationFrame ) {
				window.requestAnimationFrame( go );
			}
			setTimeout( go, 34 );
		};
		// Står bestillingsguiden (pop-up) åben, så vent til den er lukket – ellers kører animationen bag den.
		const run = () => {
			const guide = document.querySelector( 'dialog.lov-guide[open]' );
			if ( guide ) {
				// Lyt efter at "open" forsvinder (mere pålideligt end close-hændelsen).
				const mo = new MutationObserver( () => {
					if ( ! guide.open ) {
						mo.disconnect();
						setTimeout( play, 250 );
					}
				} );
				mo.observe( guide, { attributes: true, attributeFilter: [ 'open' ] } );
			} else {
				play();
			}
		};
		// Er rækken allerede på skærmen, så kør med det samme.
		const r = nav.getBoundingClientRect();
		if ( r.top >= 0 && r.bottom <= window.innerHeight ) {
			// Guiden oprettes af et andet script, der kan køre lige efter – vent et øjeblik.
			setTimeout( run, 50 );
		} else if ( 'IntersectionObserver' in window ) {
			const io = new IntersectionObserver( ( entries ) => {
				if ( entries[ 0 ].isIntersecting ) {
					io.disconnect();
					run();
				}
			}, { threshold: 0.9 } );
			io.observe( nav );
		} else {
			run();
		}
	}

	row.addEventListener( 'scroll', updateEnd, { passive: true } );
	window.addEventListener( 'resize', updateEnd );
	init();
} )();
