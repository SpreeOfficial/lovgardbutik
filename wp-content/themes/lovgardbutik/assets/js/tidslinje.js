/**
 * Om gården – tidslinje.
 * Tegner en stiplet sti, der snor sig fra boble til boble ned ad siden.
 * Stien "tegnes" frem, mens man scroller, og boblerne dukker op, når stien når dem.
 *
 * Ydelse:
 *  - Stien består af ét kurvestykke pr. boble, så stiens længde frem til hver boble
 *    kan lægges sammen af få målinger (ikke tusindvis af punkter).
 *  - Genberegning sker kun, når tidslinjens størrelse ændrer sig (ResizeObserver),
 *    og højst én gang pr. billede-frame.
 *  - Scroll-håndteringen læser kun én position og kører kun, mens tidslinjen er i syne.
 */
( function () {
	const timelines = document.querySelectorAll( '.lov-timeline' );
	if ( ! timelines.length ) {
		return;
	}
	const reduceMotion = window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;
	const SVG = 'http://www.w3.org/2000/svg';
	// Næste frame – med en timer som reserve, hvis browseren ikke tegner (fx skjult fane).
	const raf = ( f ) => {
		let done = false;
		const go = () => {
			if ( ! done ) {
				done = true;
				f();
			}
		};
		if ( window.requestAnimationFrame ) {
			window.requestAnimationFrame( go );
		}
		setTimeout( go, 50 );
	};

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
		// Hjælpesti til at måle kurvestykkernes længde (vises ikke).
		const probe = document.createElementNS( SVG, 'path' );
		probe.setAttribute( 'fill', 'none' );
		probe.setAttribute( 'stroke', 'none' );
		svg.appendChild( probe );
		tl.prepend( svg );

		let length = 0;
		let stops = []; // andel af stien (0–1) ved hver boble
		let tlTop = 0; // tidslinjens top i forhold til dokumentet
		let tlHeight = 1;
		let itemTops = []; // hver bobles top i forhold til dokumentet

		function build() {
			const w = tl.offsetWidth;
			const h = tl.offsetHeight;
			const base = tl.getBoundingClientRect();
			const scrollY = window.scrollY || window.pageYOffset;
			tlTop = base.top + scrollY;
			tlHeight = h || 1;

			const pts = items.map( ( item ) => {
				const r = ( item.querySelector( '.lov-tl-bubble' ) || item ).getBoundingClientRect();
				return { x: r.left - base.left + r.width / 2, y: r.top - base.top + r.height / 2 };
			} );
			itemTops = items.map( ( item ) => item.getBoundingClientRect().top + scrollY );

			svg.setAttribute( 'viewBox', '0 0 ' + w + ' ' + h );
			svg.setAttribute( 'width', w );
			svg.setAttribute( 'height', h );

			const narrow = w < 720;
			const start = { x: narrow ? pts[ 0 ].x : w / 2, y: 0 };
			const end = { x: narrow ? pts[ pts.length - 1 ].x : w / 2, y: h };
			const all = [ start ].concat( pts, [ end ] );
			const swing = narrow ? 36 : Math.min( 160, w * 0.12 );

			let d = 'M ' + start.x + ' ' + start.y;
			let acc = 0;
			const segEnds = [];
			for ( let i = 1; i < all.length; i++ ) {
				const a = all[ i - 1 ];
				const b = all[ i ];
				const dy = b.y - a.y;
				const dir = i % 2 ? 1 : -1;
				const seg = 'C ' + ( a.x + swing * dir ) + ' ' + ( a.y + dy * 0.45 ) + ', ' + ( b.x - swing * dir ) + ' ' + ( b.y - dy * 0.45 ) + ', ' + b.x + ' ' + b.y;
				d += ' ' + seg;
				// Ét kurvestykke måles for sig – boblen sidder præcis i stykkets slutpunkt.
				probe.setAttribute( 'd', 'M ' + a.x + ' ' + a.y + ' ' + seg );
				acc += probe.getTotalLength();
				segEnds.push( acc );
			}
			dashed.setAttribute( 'd', d );
			reveal.setAttribute( 'd', d );
			length = acc;
			reveal.style.strokeDasharray = length;
			stops = pts.map( ( p, i ) => segEnds[ i ] / length );
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
			const scrollY = window.scrollY || window.pageYOffset;
			const vh = window.innerHeight;
			const progress = Math.max( 0, Math.min( 1, ( scrollY + vh * 0.75 - tlTop ) / tlHeight ) );
			reveal.style.strokeDashoffset = length * ( 1 - progress );
			for ( let i = 0; i < items.length; i++ ) {
				if ( ! items[ i ].classList.contains( 'is-visible' ) && ( progress >= stops[ i ] - 0.02 || itemTops[ i ] - scrollY < vh * 0.85 ) ) {
					items[ i ].classList.add( 'is-visible' );
				}
			}
		}

		// Scroll: kun mens tidslinjen er i nærheden af skærmen, og højst én gang pr. frame.
		let inView = true;
		let ticking = false;
		function onScroll() {
			if ( ! inView || ticking ) {
				return;
			}
			ticking = true;
			raf( () => {
				ticking = false;
				update();
			} );
		}
		window.addEventListener( 'scroll', onScroll, { passive: true } );
		if ( 'IntersectionObserver' in window ) {
			new IntersectionObserver( ( entries ) => {
				inView = entries[ 0 ].isIntersecting;
				if ( inView ) {
					update();
				}
			}, { rootMargin: '200px 0px' } ).observe( tl );
		}

		// Genberegn kun når størrelsen ændrer sig (billeder, skrifttyper, vinduesbredde).
		let pending = false;
		let lastW = 0;
		let lastH = 0;
		function scheduleBuild() {
			if ( pending ) {
				return;
			}
			pending = true;
			raf( () => {
				pending = false;
				if ( tl.offsetWidth === lastW && tl.offsetHeight === lastH && length ) {
					return;
				}
				lastW = tl.offsetWidth;
				lastH = tl.offsetHeight;
				build();
			} );
		}
		if ( 'ResizeObserver' in window ) {
			new ResizeObserver( scheduleBuild ).observe( tl );
		} else {
			window.addEventListener( 'resize', scheduleBuild );
			window.addEventListener( 'load', scheduleBuild );
		}
		scheduleBuild();
	} );
} )();
