/**
 * Tæller på produktkortene: [−] (antal) [+]
 * Taler med WooCommerce' Store API og holder alle tællere og
 * klik og hent-listen i toppen opdateret.
 */
( function () {
	const cfg = window.lovCc;
	if ( ! cfg ) {
		return;
	}
	let nonce = cfg.nonce;
	let keys = {}; // produkt-id -> varelinjens nøgle på listen
	let counts = {};

	const steppers = () => document.querySelectorAll( '.lov-stepper[data-product-id]' );

	function render( bumpId ) {
		steppers().forEach( ( st ) => {
			const id = Number( st.dataset.productId );
			const n = counts[ id ] || 0;
			const box = st.closest( '.lov-qty-box' );
			const circle = st.querySelector( '.lov-qty' );
			const out = st.querySelector( '.lov-qty-n' );
			if ( out.textContent !== String( n ) ) {
				out.textContent = n;
				if ( id === bumpId ) {
					circle.classList.add( 'bump' );
					setTimeout( () => circle.classList.remove( 'bump' ), 250 );
				}
			}
			circle.classList.toggle( 'has-items', n > 0 );
			if ( box ) {
				const wasIn = box.classList.contains( 'lov-in-list' );
				box.classList.toggle( 'lov-in-list', n > 0 );
				// Bevar fokus for tastaturbrugere, når knappen skifter til tæller og tilbage
				if ( ! wasIn && n > 0 && id === bumpId && box.contains( document.activeElement ) ) {
					st.querySelector( '.lov-plus' ).focus();
				}
				if ( wasIn && n === 0 && id === bumpId ) {
					const add = box.querySelector( '.lov-add, button:not(.lov-step-btn), a.button' );
					if ( add ) {
						add.focus();
					}
				}
			}
		} );
	}

	function readCart( cart ) {
		keys = {};
		counts = {};
		( cart.items || [] ).forEach( ( item ) => {
			counts[ item.id ] = ( counts[ item.id ] || 0 ) + item.quantity;
			keys[ item.id ] = item.key;
		} );
	}

	function api( path, body ) {
		return fetch( cfg.api + path, {
			method: body ? 'POST' : 'GET',
			credentials: 'same-origin',
			headers: Object.assign( { 'Content-Type': 'application/json' }, body ? { Nonce: nonce } : {} ),
			body: body ? JSON.stringify( body ) : undefined,
		} ).then( ( r ) => {
			const fresh = r.headers.get( 'Nonce' );
			if ( fresh ) {
				nonce = fresh;
			}
			return r.json().then( ( data ) => ( r.ok ? data : Promise.reject( data ) ) );
		} );
	}

	function refresh( bumpId ) {
		if ( ! steppers().length ) {
			return Promise.resolve();
		}
		return api( '' ).then( ( cart ) => {
			readCart( cart );
			render( bumpId );
		} ).catch( () => {} );
	}

	// Fortæl WooCommerce' egne blokke (fx listen i toppen), at indholdet er ændret.
	function announce( type ) {
		document.body.dispatchEvent( new CustomEvent( type, { bubbles: true, detail: { preserveCartData: false } } ) );
	}

	let busy = false;
	function change( id, delta, box ) {
		if ( busy ) {
			return;
		}
		busy = true;
		box.classList.add( 'is-busy' );
		const current = counts[ id ] || 0;
		const next = current + delta;
		let req;
		if ( delta > 0 && ! keys[ id ] ) {
			req = api( '/add-item', { id, quantity: 1 } );
		} else if ( next <= 0 ) {
			req = api( '/remove-item', { key: keys[ id ] } );
		} else {
			req = api( '/update-item', { key: keys[ id ], quantity: next } );
		}
		req.then( ( cart ) => {
			readCart( cart );
			render( id );
			announce( delta > 0 ? 'wc-blocks_added_to_cart' : 'wc-blocks_removed_from_cart' );
		} )
			.catch( () => refresh( id ) )
			.finally( () => {
				busy = false;
				box.classList.remove( 'is-busy' );
			} );
	}

	document.addEventListener( 'click', ( e ) => {
		// "Tilføj til liste" på produktsiden (vores egen knap)
		const own = e.target.closest( '.lov-add[data-product-id]' );
		if ( own ) {
			const box = own.closest( '.lov-qty-box' );
			change( Number( own.dataset.productId ), 1, box );
			return;
		}
		const btn = e.target.closest( '.lov-step-btn' );
		if ( btn ) {
			const st = btn.closest( '.lov-stepper' );
			change( Number( st.dataset.productId ), btn.classList.contains( 'lov-plus' ) ? 1 : -1, st.closest( '.lov-qty-box' ) || st );
			return;
		}
		// Klik på "Tilføj til liste" (WooCommerce' egen knap): opdatér når den er færdig.
		const add = e.target.closest( '.lov-qty-box [data-product_id]' );
		if ( add ) {
			lastAdded = Number( add.dataset.product_id );
		}
	} );

	let lastAdded = 0;
	// Ændringer fra WooCommerce selv (knappen, eller fjernet fra listen i toppen)
	[ 'wc-blocks_added_to_cart', 'wc-blocks_removed_from_cart', 'added_to_cart', 'removed_from_cart' ].forEach( ( ev ) => {
		document.body.addEventListener( ev, () => {
			refresh( lastAdded );
			lastAdded = 0;
		} );
	} );

	window.addEventListener( 'pageshow', ( e ) => {
		if ( e.persisted ) {
			refresh( 0 );
		}
	} );
	refresh( 0 );
} )();
