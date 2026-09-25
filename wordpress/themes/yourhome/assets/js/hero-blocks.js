( function ( blocks, blockEditor, components, element, i18n ) {
	'use strict';

	const el = element.createElement;
	const Fragment = element.Fragment;
	const InnerBlocks = blockEditor.InnerBlocks;
	const InspectorControls = blockEditor.InspectorControls;
	const MediaUpload = blockEditor.MediaUpload;
	const MediaUploadCheck = blockEditor.MediaUploadCheck;
	const PanelColorSettings = blockEditor.PanelColorSettings;
	const RichText = blockEditor.RichText;
	const useBlockProps = blockEditor.useBlockProps;
	const Button = components.Button;
	const PanelBody = components.PanelBody;
	const RangeControl = components.RangeControl;
	const SelectControl = components.SelectControl;
	const TextControl = components.TextControl;
	const ToggleControl = components.ToggleControl;
	const __ = i18n.__;
	const slidePositions = [
		'top-left', 'top-center', 'top-right',
		'center-left', 'center', 'center-right',
		'bottom-left', 'bottom-center', 'bottom-right',
	];

	function normalizeSlidePosition( position ) {
		return slidePositions.indexOf( position ) === -1 ? 'center-left' : position;
	}

	function normalizeTextAlign( textAlign ) {
		return [ 'left', 'center', 'right' ].indexOf( textAlign ) === -1 ? 'left' : textAlign;
	}

	function setBlockStyleColor( props, key, value ) {
		const style = Object.assign( {}, props.attributes.style || {} );
		style.color = Object.assign( {}, style.color || {} );

		if ( value ) {
			style.color[ key ] = value;
		} else {
			delete style.color[ key ];
		}

		props.setAttributes( { style: style } );
	}

	function getSlideBlockOptions( attributes ) {
		const overlayOpacity = Number.isFinite( attributes.overlayOpacity ) ? attributes.overlayOpacity : 78;
		const opacity = Math.max( 20, Math.min( 95, overlayOpacity ) ) / 100;
		const options = {
			className: 'yourhome-hero-slide is-position-' + normalizeSlidePosition( attributes.contentPosition ),
			style: {
				'--yourhome-slide-align': normalizeTextAlign( attributes.textAlign ),
				'--yourhome-slide-overlay-color': attributes.overlayColor || '#183249',
				'--yourhome-slide-overlay-opacity': opacity,
				'--yourhome-slide-accent': attributes.accentColor || '#eee9df',
				'--yourhome-slide-button-background': attributes.buttonBackgroundColor || '#f7f6f2',
				'--yourhome-slide-button-text': attributes.buttonTextColor || '#183249',
			},
		};

		return options;
	}

	function getPreviousLuminousSlideBlockOptions( attributes ) {
		const overlayOpacity = Number.isFinite( attributes.overlayOpacity ) ? attributes.overlayOpacity : 78;
		return {
			className: 'yourhome-hero-slide is-position-' + normalizeSlidePosition( attributes.contentPosition ),
			style: {
				'--yourhome-slide-align': normalizeTextAlign( attributes.textAlign ),
				'--yourhome-slide-overlay-color': attributes.overlayColor || '#071a27',
				'--yourhome-slide-overlay-opacity': Math.max( 20, Math.min( 95, overlayOpacity ) ) / 100,
				'--yourhome-slide-accent': attributes.accentColor || '#57f2c3',
				'--yourhome-slide-button-background': attributes.buttonBackgroundColor || '#57f2c3',
				'--yourhome-slide-button-text': attributes.buttonTextColor || '#071a27',
			},
		};
	}

	function getPreviousStyledSlideBlockOptions( attributes ) {
		const overlayOpacity = Number.isFinite( attributes.overlayOpacity ) ? attributes.overlayOpacity : 78;
		return {
			className: 'yourhome-hero-slide is-position-' + normalizeSlidePosition( attributes.contentPosition ),
			style: {
				'--yourhome-slide-align': normalizeTextAlign( attributes.textAlign ),
				'--yourhome-slide-overlay-color': attributes.overlayColor || '#15324b',
				'--yourhome-slide-overlay-opacity': Math.max( 20, Math.min( 95, overlayOpacity ) ) / 100,
				'--yourhome-slide-accent': attributes.accentColor || '#f0bd73',
				'--yourhome-slide-button-background': attributes.buttonBackgroundColor || '#d59035',
				'--yourhome-slide-button-text': attributes.buttonTextColor || '#15324b',
			},
		};
	}

	const previousStyledSlideAttributes = {
		imageId: { type: 'number' },
		imageUrl: { type: 'string', default: '' },
		imageAlt: { type: 'string', default: '' },
		eyebrow: { type: 'string', default: 'Find your next home' },
		heading: { type: 'string', default: 'A better way to discover property' },
		content: { type: 'string', default: 'Explore homes with clear details and a focused search experience.' },
		buttonLabel: { type: 'string', default: 'Browse properties' },
		buttonUrl: { type: 'string', default: '#properties' },
		textAlign: { type: 'string', default: 'left' },
		contentPosition: { type: 'string', default: 'center-left' },
		overlayColor: { type: 'string', default: '#15324b' },
		overlayOpacity: { type: 'number', default: 78 },
		accentColor: { type: 'string', default: '#f0bd73' },
		buttonBackgroundColor: { type: 'string', default: '#d59035' },
		buttonTextColor: { type: 'string', default: '#15324b' },
		backgroundColor: { type: 'string' },
		textColor: { type: 'string' },
		gradient: { type: 'string' },
		className: { type: 'string' },
		style: { type: 'object' },
	};

	const previousLuminousSlideAttributes = Object.assign( {}, previousStyledSlideAttributes, {
		overlayColor: { type: 'string', default: '#071a27' },
		accentColor: { type: 'string', default: '#57f2c3' },
		buttonBackgroundColor: { type: 'string', default: '#57f2c3' },
		buttonTextColor: { type: 'string', default: '#071a27' },
	} );

	function saveStyledSlide( props, getOptions ) {
		const attributes = props.attributes;
		const blockProps = useBlockProps.save( getOptions( attributes ) );

		return el(
			'article',
			blockProps,
			attributes.imageUrl ? el( 'img', { className: 'yourhome-hero-slide__media', src: attributes.imageUrl, alt: attributes.imageAlt } ) : null,
			el( 'div', { className: 'yourhome-hero-slide__overlay', 'aria-hidden': 'true' } ),
			el(
				'div',
				{ className: 'yourhome-hero-slide__content' },
				el( RichText.Content, { tagName: 'p', className: 'yourhome-hero-slide__eyebrow', value: attributes.eyebrow } ),
				el( RichText.Content, { tagName: 'h2', className: 'yourhome-hero-slide__heading', value: attributes.heading } ),
				el( RichText.Content, { tagName: 'p', className: 'yourhome-hero-slide__body', value: attributes.content } ),
				attributes.buttonLabel && attributes.buttonUrl ? el( RichText.Content, { tagName: 'a', className: 'yourhome-hero-slide__button', href: attributes.buttonUrl, value: attributes.buttonLabel } ) : null
			)
		);
	}

	const previousSlideAttributes = {
		imageId: { type: 'number' },
		imageUrl: { type: 'string', default: '' },
		imageAlt: { type: 'string', default: '' },
		eyebrow: { type: 'string', default: 'Find your next home' },
		heading: { type: 'string', default: 'A better way to discover property' },
		content: { type: 'string', default: 'Explore homes with clear details and a focused search experience.' },
		buttonLabel: { type: 'string', default: 'Browse properties' },
		buttonUrl: { type: 'string', default: '#properties' },
		textAlign: { type: 'string', default: 'center' },
	};

	function previousSlideSave( props ) {
		const attributes = props.attributes;
		const blockProps = useBlockProps.save( { className: 'yourhome-hero-slide', style: { '--yourhome-slide-align': attributes.textAlign } } );

		return el(
			'article',
			blockProps,
			attributes.imageUrl ? el( 'img', { className: 'yourhome-hero-slide__media', src: attributes.imageUrl, alt: attributes.imageAlt } ) : null,
			el( 'div', { className: 'yourhome-hero-slide__overlay', 'aria-hidden': 'true' } ),
			el(
				'div',
				{ className: 'yourhome-hero-slide__content' },
				el( RichText.Content, { tagName: 'p', className: 'yourhome-hero-slide__eyebrow', value: attributes.eyebrow } ),
				el( RichText.Content, { tagName: 'h2', className: 'yourhome-hero-slide__heading', value: attributes.heading } ),
				el( RichText.Content, { tagName: 'p', className: 'yourhome-hero-slide__body', value: attributes.content } ),
				attributes.buttonLabel && attributes.buttonUrl ? el( RichText.Content, { tagName: 'a', className: 'yourhome-hero-slide__button', href: attributes.buttonUrl, value: attributes.buttonLabel } ) : null
			)
		);
	}

	blocks.registerBlockType( 'yourhome/hero-slide', {
		edit: function ( props ) {
			const attributes = props.attributes;
			const blockProps = useBlockProps( getSlideBlockOptions( attributes ) );

			return el(
				Fragment,
				null,
				el(
					InspectorControls,
					null,
					el(
						PanelBody,
						{ title: __( 'Slide settings', 'yourhome' ) },
						el( SelectControl, {
							label: __( 'Content position', 'yourhome' ),
							value: normalizeSlidePosition( attributes.contentPosition ),
							options: [
								{ label: __( 'Top left', 'yourhome' ), value: 'top-left' },
								{ label: __( 'Top center', 'yourhome' ), value: 'top-center' },
								{ label: __( 'Top right', 'yourhome' ), value: 'top-right' },
								{ label: __( 'Center left', 'yourhome' ), value: 'center-left' },
								{ label: __( 'Center', 'yourhome' ), value: 'center' },
								{ label: __( 'Center right', 'yourhome' ), value: 'center-right' },
								{ label: __( 'Bottom left', 'yourhome' ), value: 'bottom-left' },
								{ label: __( 'Bottom center', 'yourhome' ), value: 'bottom-center' },
								{ label: __( 'Bottom right', 'yourhome' ), value: 'bottom-right' },
							],
							onChange: function ( contentPosition ) { props.setAttributes( { contentPosition: contentPosition } ); },
						} ),
						el( SelectControl, {
							label: __( 'Text alignment', 'yourhome' ),
							value: normalizeTextAlign( attributes.textAlign ),
							options: [
								{ label: __( 'Left', 'yourhome' ), value: 'left' },
								{ label: __( 'Center', 'yourhome' ), value: 'center' },
								{ label: __( 'Right', 'yourhome' ), value: 'right' },
							],
							onChange: function ( textAlign ) { props.setAttributes( { textAlign: textAlign } ); },
						} ),
						el( TextControl, {
							label: __( 'Button URL', 'yourhome' ),
							value: attributes.buttonUrl,
							onChange: function ( buttonUrl ) { props.setAttributes( { buttonUrl: buttonUrl } ); },
						} ),
						el( RangeControl, {
							label: __( 'Overlay opacity', 'yourhome' ),
							value: attributes.overlayOpacity,
							min: 20,
							max: 95,
							onChange: function ( overlayOpacity ) { props.setAttributes( { overlayOpacity: overlayOpacity } ); },
						} ),
						el(
							MediaUploadCheck,
							null,
							el( MediaUpload, {
								allowedTypes: [ 'image' ],
								value: attributes.imageId,
								onSelect: function ( media ) {
									props.setAttributes( { imageId: media.id, imageUrl: media.url, imageAlt: media.alt || '' } );
								},
								render: function ( mediaProps ) {
									return el( Button, { variant: 'secondary', onClick: mediaProps.open }, attributes.imageUrl ? __( 'Replace image', 'yourhome' ) : __( 'Choose image', 'yourhome' ) );
								},
							} )
						)
					),
					el( PanelColorSettings, {
						title: __( 'Hero colors', 'yourhome' ),
						initialOpen: false,
						colorSettings: [
							{ value: attributes.style && attributes.style.color ? attributes.style.color.text : undefined, onChange: function ( textColor ) { setBlockStyleColor( props, 'text', textColor ); }, label: __( 'Text', 'yourhome' ) },
							{ value: attributes.style && attributes.style.color ? attributes.style.color.background : undefined, onChange: function ( backgroundColor ) { setBlockStyleColor( props, 'background', backgroundColor ); }, label: __( 'Slide background', 'yourhome' ) },
							{ value: attributes.overlayColor, onChange: function ( overlayColor ) { props.setAttributes( { overlayColor: overlayColor || '' } ); }, label: __( 'Overlay', 'yourhome' ) },
							{ value: attributes.accentColor, onChange: function ( accentColor ) { props.setAttributes( { accentColor: accentColor || '' } ); }, label: __( 'Eyebrow', 'yourhome' ) },
							{ value: attributes.buttonBackgroundColor, onChange: function ( buttonBackgroundColor ) { props.setAttributes( { buttonBackgroundColor: buttonBackgroundColor || '' } ); }, label: __( 'Button background', 'yourhome' ) },
							{ value: attributes.buttonTextColor, onChange: function ( buttonTextColor ) { props.setAttributes( { buttonTextColor: buttonTextColor || '' } ); }, label: __( 'Button text', 'yourhome' ) },
						],
					} )
				),
				el(
					'article',
					blockProps,
					attributes.imageUrl ? el( 'img', { className: 'yourhome-hero-slide__media', src: attributes.imageUrl, alt: attributes.imageAlt } ) : null,
					el( 'div', { className: 'yourhome-hero-slide__overlay', 'aria-hidden': 'true' } ),
					el(
						'div',
						{ className: 'yourhome-hero-slide__content' },
						el( RichText, { tagName: 'p', className: 'yourhome-hero-slide__eyebrow', value: attributes.eyebrow, onChange: function ( eyebrow ) { props.setAttributes( { eyebrow: eyebrow } ); } } ),
						el( RichText, { tagName: 'h2', className: 'yourhome-hero-slide__heading', value: attributes.heading, onChange: function ( heading ) { props.setAttributes( { heading: heading } ); } } ),
						el( RichText, { tagName: 'p', className: 'yourhome-hero-slide__body', value: attributes.content, onChange: function ( content ) { props.setAttributes( { content: content } ); } } ),
						el( RichText, { tagName: 'span', className: 'yourhome-hero-slide__button', value: attributes.buttonLabel, onChange: function ( buttonLabel ) { props.setAttributes( { buttonLabel: buttonLabel } ); } } )
					)
				)
			);
		},
		save: function ( props ) { return saveStyledSlide( props, getSlideBlockOptions ); },
		deprecated: [ {
			attributes: previousLuminousSlideAttributes,
			save: function ( props ) { return saveStyledSlide( props, getPreviousLuminousSlideBlockOptions ); },
			migrate: function ( attributes ) { return attributes; },
		}, {
			attributes: previousStyledSlideAttributes,
			save: function ( props ) { return saveStyledSlide( props, getPreviousStyledSlideBlockOptions ); },
			migrate: function ( attributes ) { return attributes; },
		}, {
			attributes: previousSlideAttributes,
			save: previousSlideSave,
			migrate: function ( attributes ) {
				return Object.assign( {}, attributes, {
					contentPosition: 'center',
					overlayColor: '#183249',
					overlayOpacity: 78,
					accentColor: '#eee9df',
					buttonBackgroundColor: '#f7f6f2',
					buttonTextColor: '#183249',
				} );
			},
		} ],
	} );

	const legacyAttributes = {
		eyebrow: { type: 'string', default: 'Find your next home' },
		heading: { type: 'string', default: 'A better way to discover property' },
		content: { type: 'string', default: 'Explore homes with clear details and a focused search experience.' },
		buttonLabel: { type: 'string', default: 'Browse properties' },
		buttonUrl: { type: 'string', default: '#properties' },
	};

	const previousSliderAttributes = {
		autoplay: { type: 'boolean', default: true },
		interval: { type: 'number', default: 6000 },
		transition: { type: 'string', default: 'fade' },
		showArrows: { type: 'boolean', default: true },
		showPagination: { type: 'boolean', default: true },
		pauseOnHover: { type: 'boolean', default: true },
		align: { type: 'string' },
		anchor: { type: 'string' },
		className: { type: 'string' },
		style: { type: 'object' },
	};

	function previousSliderSave( props ) {
		const blockProps = useBlockProps.save( { className: 'yourhome-hero is-transition-' + props.attributes.transition } );
		return el( 'section', blockProps, el( 'div', { className: 'yourhome-hero__track' }, el( InnerBlocks.Content ) ) );
	}

	function legacySave( props ) {
		const attributes = props.attributes;
		const blockProps = useBlockProps.save( { className: 'yourhome-hero' } );
		return el( 'section', blockProps, el( 'div', { className: 'yourhome-hero__inner' },
			el( RichText.Content, { tagName: 'p', className: 'yourhome-hero__eyebrow', value: attributes.eyebrow } ),
			el( RichText.Content, { tagName: 'h1', className: 'yourhome-hero__heading', value: attributes.heading } ),
			el( RichText.Content, { tagName: 'p', className: 'yourhome-hero__content', value: attributes.content } ),
			attributes.buttonLabel && attributes.buttonUrl ? el( RichText.Content, { tagName: 'a', className: 'yourhome-hero__button', href: attributes.buttonUrl, value: attributes.buttonLabel } ) : null
		) );
	}

	blocks.registerBlockType( 'yourhome/hero', {
		edit: function ( props ) {
			const attributes = props.attributes;
			const blockProps = useBlockProps( { className: 'yourhome-hero is-transition-' + attributes.transition } );

			return el(
				Fragment,
				null,
				el( InspectorControls, null,
					el( PanelBody, { title: __( 'Slider settings', 'yourhome' ) },
						el( ToggleControl, { label: __( 'Autoplay', 'yourhome' ), checked: attributes.autoplay, onChange: function ( autoplay ) { props.setAttributes( { autoplay: autoplay } ); } } ),
						el( RangeControl, { label: __( 'Interval (seconds)', 'yourhome' ), value: attributes.interval / 1000, min: 3, max: 15, onChange: function ( seconds ) { props.setAttributes( { interval: seconds * 1000 } ); } } ),
						el( SelectControl, { label: __( 'Transition', 'yourhome' ), value: attributes.transition, options: [ { label: __( 'Fade', 'yourhome' ), value: 'fade' }, { label: __( 'Slide', 'yourhome' ), value: 'slide' } ], onChange: function ( transition ) { props.setAttributes( { transition: transition } ); } } ),
						el( ToggleControl, { label: __( 'Show arrows', 'yourhome' ), checked: attributes.showArrows, onChange: function ( showArrows ) { props.setAttributes( { showArrows: showArrows } ); } } ),
						el( ToggleControl, { label: __( 'Show pagination', 'yourhome' ), checked: attributes.showPagination, onChange: function ( showPagination ) { props.setAttributes( { showPagination: showPagination } ); } } ),
						el( ToggleControl, { label: __( 'Pause on hover', 'yourhome' ), checked: attributes.pauseOnHover, onChange: function ( pauseOnHover ) { props.setAttributes( { pauseOnHover: pauseOnHover } ); } } )
					)
				),
				el( 'section', blockProps, el( InnerBlocks, { allowedBlocks: [ 'yourhome/hero-slide' ], template: [ [ 'yourhome/hero-slide' ] ], renderAppender: InnerBlocks.ButtonBlockAppender } ) )
			);
		},
		save: function ( props ) {
			const blockProps = useBlockProps.save( { className: 'yourhome-hero is-transition-' + props.attributes.transition } );
			return el( 'section', blockProps, el( InnerBlocks.Content ) );
		},
		deprecated: [ {
			attributes: previousSliderAttributes,
			save: previousSliderSave,
			migrate: function ( attributes, innerBlocks ) { return [ attributes, innerBlocks ]; },
		}, {
			attributes: legacyAttributes,
			save: legacySave,
			migrate: function ( attributes ) {
				return [ {}, [ blocks.createBlock( 'yourhome/hero-slide', Object.assign( {}, attributes, { textAlign: 'center', contentPosition: 'center' } ) ) ] ];
			},
		} ],
	} );
} )( window.wp.blocks, window.wp.blockEditor, window.wp.components, window.wp.element, window.wp.i18n );
