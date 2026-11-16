/**
 * Sidehovedet er "sticky" og bliver mindre, når man scroller ned.
 *
 * Når det bliver mindre, lægges en margen under det, der svarer præcis til forskellen.
 * Indholdet nedenfor flytter sig derfor ikke, scroll-positionen ændrer sig ikke,
 * og sidehovedet kan ikke hoppe frem og tilbage. Ved indlæsning ændres layoutet slet ikke
 * (ingen layout-skift / CLS).
 */
( function () {
	const ON = 80;
	const OFF = 20;
	const header = document.querySelector( '.wp-site-blocks > header' );
	if ( ! header ) {
		return;
	}
	const body = document.body;
	let scrolled = false;
	let delta = 0; // hvor meget mindre sidehovedet er i lille udgave

	// Mål forskellen mellem stor og lille udgave uden overgange og uden at noget når at blive tegnet.
	function measureDelta() {
		const was = body.classList.contains( 'lov-scrolled' );
		body.classList.add( 'lov-header-measuring' );
		body.classList.remove( 'lov-scrolled' );
		const big = header.getBoundingClientRect().height;
		body.classList.add( 'lov-scrolled' );
		const small = header.getBoundingClientRect().height;
		if ( ! was ) {
			body.classList.remove( 'lov-scrolled' );
		}
		body.classList.remove( 'lov-header-measuring' );
		delta = Math.max( 0, Math.round( big - small ) );
		header.style.setProperty( '--lov-header-delta', delta + 'px' );
	}

	function update() {
		const y = window.scrollY || window.pageYOffset;
		if ( ! scrolled && y > ON ) {
			if ( ! delta ) {
				measureDelta();
			}
			scrolled = true;
			body.classList.add( 'lov-scrolled' );
		} else if ( scrolled && y < OFF ) {
			scrolled = false;
			body.classList.remove( 'lov-scrolled' );
		}
	}

	let ticking = false;
	window.addEventListener( 'scroll', () => {
		if ( ticking ) {
			return;
		}
		ticking = true;
		// Næste frame – med en timer som reserve, hvis browseren ikke tegner (fx skjult fane).
		let done = false;
		const go = () => {
			if ( ! done ) {
				done = true;
				ticking = false;
				update();
			}
		};
		if ( window.requestAnimationFrame ) {
			window.requestAnimationFrame( go );
		}
		setTimeout( go, 50 );
	}, { passive: true } );

	let t;
	window.addEventListener( 'resize', () => {
		clearTimeout( t );
		t = setTimeout( () => {
			delta = 0;
			if ( scrolled ) {
				measureDelta();
			}
		}, 150 );
	} );
	update();
} )();
