/**
 * Primary navigation behaviour: mobile toggle + keyboard support for submenus.
 *
 * Dependency-free. Enhances the markup emitted by wp_nav_menu() in header.php.
 */

const DESKTOP_QUERY = '(min-width: 48em)';

/**
 * Walk up from a focused/blurred link, toggling `.focus` on ancestor <li>s so
 * submenus stay open while a descendant has focus.
 *
 * @param {FocusEvent} event Focus or blur event.
 */
function handleSubmenuFocus( event ) {
	let el = event.currentTarget;

	while ( el && ! el.classList.contains( 'main-navigation' ) ) {
		if ( 'li' === el.tagName.toLowerCase() ) {
			el.classList.toggle( 'focus' );
		}
		el = el.parentElement;
	}
}

/**
 * Wire up the primary navigation.
 */
function initNavigation() {
	const nav = document.getElementById( 'site-navigation' );

	if ( ! nav ) {
		return;
	}

	const button = nav.querySelector( '.menu-toggle' );
	const menu = nav.querySelector( 'ul' );

	if ( ! button || ! menu ) {
		if ( button ) {
			button.style.display = 'none';
		}
		return;
	}

	if ( ! menu.id ) {
		menu.id = 'primary-menu';
	}

	const close = () => {
		if ( ! nav.classList.contains( 'is-toggled' ) ) {
			return;
		}
		nav.classList.remove( 'is-toggled' );
		button.setAttribute( 'aria-expanded', 'false' );
	};

	button.setAttribute( 'aria-expanded', 'false' );

	button.addEventListener( 'click', () => {
		const opened = nav.classList.toggle( 'is-toggled' );
		button.setAttribute( 'aria-expanded', opened ? 'true' : 'false' );
	} );

	// Escape closes and returns focus to the toggle.
	nav.addEventListener( 'keydown', ( event ) => {
		if (
			'Escape' === event.key &&
			nav.classList.contains( 'is-toggled' )
		) {
			close();
			button.focus();
		}
	} );

	// A click/tap outside the nav closes it.
	document.addEventListener( 'click', ( event ) => {
		if ( ! nav.contains( event.target ) ) {
			close();
		}
	} );

	// Resizing up to the desktop layout resets the toggle state.
	window
		.matchMedia( DESKTOP_QUERY )
		.addEventListener( 'change', ( event ) => {
			if ( event.matches ) {
				close();
			}
		} );

	nav.querySelectorAll( '.menu-item-has-children > a' ).forEach( ( link ) => {
		link.addEventListener( 'focus', handleSubmenuFocus );
		link.addEventListener( 'blur', handleSubmenuFocus );
	} );
}

if ( 'loading' !== document.readyState ) {
	initNavigation();
} else {
	document.addEventListener( 'DOMContentLoaded', initNavigation );
}
