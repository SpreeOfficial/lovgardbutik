/**
 * "Sådan bestiller du" som pop-up – kun på mobil, kun første gang.
 * Når kunden trykker "Ok – Luk guiden", gemmes en cookie i et år,
 * så guiden aldrig vises igen på den enhed.
 */
( function () {
	const COOKIE = 'lov_guide_set';
	if ( ! window.matchMedia( '(max-width: 860px)' ).matches ) {
		return;
	}
	if ( document.cookie.split( '; ' ).some( ( c ) => c.indexOf( COOKIE + '=' ) === 0 ) ) {
		return;
	}
	const source = document.querySelector( '.lov-steps' );
	if ( ! source || typeof HTMLDialogElement === 'undefined' ) {
		return;
	}

	const dialog = document.createElement( 'dialog' );
	dialog.className = 'lov-guide';
	dialog.setAttribute( 'aria-labelledby', 'lov-guide-title' );
	const inner = document.createElement( 'div' );
	inner.className = 'lov-guide-inner';

	const title = document.createElement( 'h2' );
	title.id = 'lov-guide-title';
	title.textContent = 'Sådan bestiller du';
	inner.appendChild( title );

	// Samme tre trin som på siden, så teksten kun skal rettes ét sted.
	source.querySelectorAll( '.lov-step' ).forEach( ( step ) => {
		inner.appendChild( step.cloneNode( true ) );
	} );

	const ok = document.createElement( 'button' );
	ok.type = 'button';
	ok.className = 'lov-guide-ok';
	ok.textContent = 'Ok – Luk guiden';
	inner.appendChild( ok );
	dialog.appendChild( inner );
	document.body.appendChild( dialog );

	function closeForGood() {
		const secure = location.protocol === 'https:' ? '; Secure' : '';
		document.cookie = COOKIE + '=1; Max-Age=31536000; Path=/; SameSite=Lax' + secure;
		if ( dialog.classList.contains( 'is-closing' ) ) {
			return;
		}
		const reduce = window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;
		if ( reduce ) {
			dialog.close();
			return;
		}
		// Lad ud-animationen køre færdig, før vinduet lukkes (med en sikkerhed, hvis den ikke kører).
		dialog.classList.add( 'is-closing' );
		let done = false;
		const finish = () => {
			if ( done ) {
				return;
			}
			done = true;
			dialog.classList.remove( 'is-closing' );
			dialog.close();
		};
		dialog.addEventListener( 'animationend', ( e ) => {
			if ( e.target === dialog ) {
				finish();
			}
		}, { once: true } );
		setTimeout( finish, 300 );
	}
	ok.addEventListener( 'click', closeForGood );
	// Esc lukker også – og tæller som "Ok", så guiden ikke dukker op igen.
	dialog.addEventListener( 'cancel', ( e ) => {
		e.preventDefault();
		closeForGood();
	} );

	dialog.showModal();
	ok.focus();
} )();
