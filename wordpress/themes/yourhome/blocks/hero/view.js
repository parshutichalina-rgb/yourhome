import { getContext, getElement, store, withSyncEvent } from '@wordpress/interactivity';

const { actions, state } = store( 'yourhome/hero', {
	state: {
		get isActiveSlide() {
			const context = getContext();
			return context.slideIndex === context.activeIndex;
		},
		get isSlideHidden() {
			return ! state.isActiveSlide;
		},
		get isActiveDot() {
			const context = getContext();
			return context.targetIndex === context.activeIndex ? 'true' : undefined;
		},
		get statusText() {
			const context = getContext();
			return `${ context.activeIndex + 1 } / ${ context.count }`;
		},
		get pauseLabel() {
			const context = getContext();
			if ( ! context.autoplay || context.reducedMotion ) {
				return 'Automatic playback disabled';
			}
			return context.paused ? 'Play slides' : 'Pause slides';
		},
		get isPauseDisabled() {
			const context = getContext();
			return ! context.autoplay || context.reducedMotion;
		},
		get liveMode() {
			return getContext().liveMode;
		},
	},
	actions: {
		setIndex( index, announce = false ) {
			const context = getContext();
			context.activeIndex = ( index + context.count ) % context.count;
			if ( announce ) {
				context.liveMode = 'polite';
				window.setTimeout( () => { context.liveMode = 'off'; }, 1000 );
			}
		},
		next() {
			actions.setIndex( getContext().activeIndex + 1 );
		},
		previous() {
			actions.setIndex( getContext().activeIndex - 1 );
		},
		handleNext: withSyncEvent( () => {
			actions.setIndex( getContext().activeIndex + 1, true );
		} ),
		handlePrevious: withSyncEvent( () => {
			actions.setIndex( getContext().activeIndex - 1, true );
		} ),
		goTo: withSyncEvent( () => {
			actions.setIndex( getContext().targetIndex, true );
		} ),
		togglePause: withSyncEvent( () => {
			const context = getContext();
			context.paused = ! context.paused;
		} ),
		handlePause: withSyncEvent( () => {
			if ( getContext().pauseOnHover ) {
				getContext().hoverPaused = true;
			}
		} ),
		handleResume: withSyncEvent( () => {
			if ( getContext().pauseOnHover ) {
				getContext().hoverPaused = false;
			}
		} ),
		handleKeydown: withSyncEvent( ( event ) => {
			if ( event.key === 'ArrowRight' ) {
				event.preventDefault();
				actions.setIndex( getContext().activeIndex + 1, true );
			} else if ( event.key === 'ArrowLeft' ) {
				event.preventDefault();
				actions.setIndex( getContext().activeIndex - 1, true );
			}
		} ),
	},
	callbacks: {
		*init() {
			const context = getContext();
			const { ref } = getElement();
			context.reducedMotion = window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;
			if ( context.reducedMotion || context.count < 2 ) {
				context.paused = true;
			}
			ref?.style.setProperty( '--yourhome-hero-interval', `${ context.interval }ms` );

			while ( true ) {
				yield new Promise( ( resolve ) => window.setTimeout( resolve, context.interval ) );
				if ( context.autoplay && ! context.paused && ! context.hoverPaused && ! context.reducedMotion ) {
					actions.next();
				}
			}
		},
	},
} );
