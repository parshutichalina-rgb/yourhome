( function () {
	'use strict';

	const root = document.documentElement;
	const header = document.querySelector( 'header.wp-block-template-part, .yourhome-layout-slot--header' );
	const updateHeaderHeight = function () {
		if ( header ) {
			root.style.setProperty( '--yourhome-header-height', header.getBoundingClientRect().height + 'px' );
		}
	};

	updateHeaderHeight();
	window.addEventListener( 'resize', updateHeaderHeight, { passive: true } );
	if ( header && 'ResizeObserver' in window ) {
		new ResizeObserver( updateHeaderHeight ).observe( header );
	}

	if ( ! ( 'IntersectionObserver' in window ) || window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches ) {
		return;
	}

	const targets = document.querySelectorAll( [
		'.yourhome-section-heading',
		'.yourhome-property-card',
		'.yourhome-background-link__content',
		'.yourhome-faq__content',
		'.yourhome-contact-us__inner',
		'.yourhome-catalog__header',
		'.yourhome-catalog__filters',
	].join( ',' ) );

	if ( 0 === targets.length ) {
		return;
	}

	const observer = new IntersectionObserver( function ( entries ) {
		entries.forEach( function ( entry ) {
			if ( ! entry.isIntersecting ) {
				return;
			}

			entry.target.classList.add( 'is-visible' );
			observer.unobserve( entry.target );
		} );
	}, { rootMargin: '0px 0px -8% 0px', threshold: 0.12 } );

	targets.forEach( function ( target, index ) {
		target.classList.add( 'yourhome-reveal' );
		target.style.setProperty( '--yourhome-reveal-order', index % 3 );
		observer.observe( target );
	} );
}() );
