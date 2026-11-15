/**
 * Om gården – tidslinje.
 * Tegner en stiplet sti, der snor sig fra boble til boble ned ad siden.
 * Stien "tegnes" frem, mens man scroller, og stregerne bevæger sig langsomt.
 * Boblerne dukker op, når stien når dem.
 */
( function () {
	const timelines = document.querySelectorAll( '.lov-timeline' );
	if ( ! timelines.length ) {
		return;
	}
	const reduceMotion = window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;
	const SVG = 'http://www.w3.org/2000/svg';

	timelines.forEach( ( tl ) => {
		const items = Array.from( tl.querySelectorAll( '.lov-tl-item' ) );
		if ( ! items.length ) {
			return;
		}
		tl.classList.add( 'lov-tl-ready' );

		const svg = document.createElementNS( SVG, 'svg' );
		svg.setAttribute( 'class', 'lov-tl-svg' );
		svg.setAttribute( 'aria-hidden', 'true' );
		const defs = document.createElementNS( SVG, 'defs' );
		const mask = document.createElementNS( SVG, 'mask' );
		const maskId = 'lov-tl-mask-' + Math.random().toString( 36 ).slice( 2 );
		mask.setAttribute( 'id', maskId );
		mask.setAttribute( 'maskUnits', 'userSpaceOnUse' );
		const reveal = document.createElementNS( SVG, 'path' );
		reveal.setAttribute( 'class', 'lov-tl-reveal' );
		mask.appendChild( reveal );
		defs.appendChild( mask );
		svg.appendChild( defs );

		const dashed = document.createElementNS( SVG, 'path' );
		dashed.setAttribute( 'class', 'lov-tl-path' );
		dashed.setAttribute( 'mask', 'url(#' + maskId + ')' );
		svg.appendChild( dashed );
		tl.prepend( svg );

		let length = 0;
		let stops = []; // hvor langt henne på stien hver boble sidder (0–1)

		function bubbleCenter( item ) {
			const bubble = item.querySelector( '.lov-tl-bubble' ) || item;
			const r = bubble.getBoundingClientRect();
			const base = tl.getBoundingClientRect();
			return { x: r.left - base.left + r.width / 2, y: r.top - base.top + r.height / 2, r: Math.min( r.width, r.height ) / 2 };
		}

		function build() {
			const w = tl.offsetWidth;
			const h = tl.offsetHeight;
			svg.setAttribute( 'viewBox', '0 0 ' + w + ' ' + h );
			svg.setAttribute( 'width', w );
			svg.setAttribute( 'height', h );

			const pts = items.map( bubbleCenter );
			const narrow = w < 720;
			// Start lidt over første boble, slut lidt under sidste
			const start = { x: narrow ? pts[ 0 ].x : w / 2, y: 0 };
			const end = { x: narrow ? pts[ pts.length - 1 ].x : w / 2, y: h };
			const all = [ start ].concat( pts, [ end ] );

			let d = 'M ' + start.x + ' ' + start.y;
			for ( let i = 1; i < all.length; i++ ) {
				const a = all[ i - 1 ];
				const b = all[ i ];
				const dy = b.y - a.y;
				// Bløde S-kurver: lodret ud af den ene boble og ind i den næste,
				// med en lille sidelæns bue, så stien "snor sig".
				const swing = narrow ? 36 : Math.min( 160, w * 0.12 );
				const dir = i % 2 ? 1 : -1;
				const c1 = { x: a.x + swing * dir, y: a.y + dy * 0.45 };
				const c2 = { x: b.x - swing * dir, y: b.y - dy * 0.45 };
				d += ' C ' + c1.x + ' ' + c1.y + ', ' + c2.x + ' ' + c2.y + ', ' + b.x + ' ' + b.y;
			}
			dashed.setAttribute( 'd', d );
			reveal.setAttribute( 'd', d );
			length = dashed.getTotalLength();
			reveal.style.strokeDasharray = length;

			// Find stiens længde ved hver boble (grov søgning er rigeligt præcis)
			stops = pts.map( ( p ) => {
				let best = 0;
				let bestDist = Infinity;
				for ( let s = 0; s <= length; s += 6 ) {
					const q = dashed.getPointAtLength( s );
					const dist = Math.hypot( q.x - p.x, q.y - p.y );
					if ( dist < bestDist ) {
						bestDist = dist;
						best = s;
					}
				}
				return best / length;
			} );
			update();
		}

		function update() {
			if ( ! length ) {
				return;
			}
			if ( reduceMotion ) {
				reveal.style.strokeDashoffset = 0;
				items.forEach( ( it ) => it.classList.add( 'is-visible' ) );
				return;
			}
			const r = tl.getBoundingClientRect();
			const vh = window.innerHeight;
			// Stien følger et punkt 75 % nede på skærmen
			const progress = Math.max( 0, Math.min( 1, ( vh * 0.75 - r.top ) / r.height ) );
			reveal.style.strokeDashoffset = length * ( 1 - progress );
			items.forEach( ( it, i ) => {
				if ( progress >= stops[ i ] - 0.02 || it.getBoundingClientRect().top < vh * 0.85 ) {
					it.classList.add( 'is-visible' );
				}
			} );
		}

		// update() er billig (ingen layout-skrivning før læsning), så den kan køre direkte ved scroll.
		window.addEventListener( 'scroll', update, { passive: true } );

		let resizeTimer;
		window.addEventListener( 'resize', () => {
			clearTimeout( resizeTimer );
			resizeTimer = setTimeout( build, 150 );
		} );
		// Billeder kan ændre højden, når de er hentet
		tl.querySelectorAll( 'img' ).forEach( ( img ) => {
			if ( ! img.complete ) {
				img.addEventListener( 'load', build, { once: true } );
			}
		} );
		if ( document.fonts && document.fonts.ready ) {
			document.fonts.ready.then( build );
		}
		build();
	} );
} )();
