( function ( blocks, blockEditor, components, element, i18n ) {
	'use strict';

	const el = element.createElement;
	const Fragment = element.Fragment;
	const InnerBlocks = blockEditor.InnerBlocks;
	const InspectorControls = blockEditor.InspectorControls;
	const RichText = blockEditor.RichText;
	const TextControl = components.TextControl;
	const PanelBody = components.PanelBody;
	const useBlockProps = blockEditor.useBlockProps;
	const __ = i18n.__;

	blocks.registerBlockType( 'yourhome/hero', {
		apiVersion: 3,
		title: __( 'YourHome Hero', 'yourhome' ),
		category: 'design',
		icon: 'cover-image',
		attributes: {
			eyebrow: { type: 'string', default: 'Find your next home' },
			heading: { type: 'string', default: 'A better way to discover property' },
			content: { type: 'string', default: 'Explore homes with clear details and a focused search experience.' },
			buttonLabel: { type: 'string', default: 'Browse properties' },
			buttonUrl: { type: 'string', default: '#properties' },
		},
		supports: {
			align: [ 'wide', 'full' ],
			anchor: true,
			html: false,
			spacing: { margin: true, padding: true },
		},
		edit: function ( props ) {
			const attributes = props.attributes;
			const blockProps = useBlockProps( { className: 'yourhome-hero' } );

			return el(
				Fragment,
				null,
				el(
					InspectorControls,
					null,
					el(
						PanelBody,
						{ title: __( 'Call to action', 'yourhome' ) },
						el( TextControl, {
							label: __( 'Link URL', 'yourhome' ),
							value: attributes.buttonUrl,
							onChange: function ( buttonUrl ) {
								props.setAttributes( { buttonUrl: buttonUrl } );
							},
						} )
					)
				),
				el(
					'section',
					blockProps,
					el(
						'div',
						{ className: 'yourhome-hero__inner' },
						el( RichText, {
							tagName: 'p',
							className: 'yourhome-hero__eyebrow',
							value: attributes.eyebrow,
							onChange: function ( eyebrow ) {
								props.setAttributes( { eyebrow: eyebrow } );
							},
						} ),
						el( RichText, {
							tagName: 'h1',
							className: 'yourhome-hero__heading',
							value: attributes.heading,
							onChange: function ( heading ) {
								props.setAttributes( { heading: heading } );
							},
						} ),
						el( RichText, {
							tagName: 'p',
							className: 'yourhome-hero__content',
							value: attributes.content,
							onChange: function ( content ) {
								props.setAttributes( { content: content } );
							},
						} ),
						el( RichText, {
							tagName: 'span',
							className: 'yourhome-hero__button',
							value: attributes.buttonLabel,
							onChange: function ( buttonLabel ) {
								props.setAttributes( { buttonLabel: buttonLabel } );
							},
						} )
					)
				)
			);
		},
		save: function ( props ) {
			const attributes = props.attributes;
			const blockProps = useBlockProps.save( { className: 'yourhome-hero' } );

			return el(
				'section',
				blockProps,
				el(
					'div',
					{ className: 'yourhome-hero__inner' },
					el( RichText.Content, { tagName: 'p', className: 'yourhome-hero__eyebrow', value: attributes.eyebrow } ),
					el( RichText.Content, { tagName: 'h1', className: 'yourhome-hero__heading', value: attributes.heading } ),
					el( RichText.Content, { tagName: 'p', className: 'yourhome-hero__content', value: attributes.content } ),
					attributes.buttonLabel && attributes.buttonUrl
						? el( RichText.Content, {
							tagName: 'a',
							className: 'yourhome-hero__button',
							href: attributes.buttonUrl,
							value: attributes.buttonLabel,
						} )
						: null
				)
			);
		},
	} );

	blocks.registerBlockType( 'yourhome/section', {
		apiVersion: 3,
		title: __( 'YourHome Section', 'yourhome' ),
		category: 'design',
		icon: 'layout',
		attributes: {
			heading: { type: 'string', default: 'Section heading' },
		},
		supports: {
			align: [ 'wide', 'full' ],
			anchor: true,
			html: false,
			spacing: { margin: true, padding: true },
		},
		edit: function ( props ) {
			const blockProps = useBlockProps( { className: 'yourhome-section' } );

			return el(
				'section',
				blockProps,
				el(
					'div',
					{ className: 'yourhome-section__inner' },
					el( RichText, {
						tagName: 'h2',
						className: 'yourhome-section__heading',
						value: props.attributes.heading,
						onChange: function ( heading ) {
							props.setAttributes( { heading: heading } );
						},
					} ),
					el( InnerBlocks, {
						template: [ [ 'core/paragraph', { placeholder: __( 'Add section content…', 'yourhome' ) } ] ],
						templateLock: false,
					} )
				)
			);
		},
		save: function ( props ) {
			const blockProps = useBlockProps.save( { className: 'yourhome-section' } );

			return el(
				'section',
				blockProps,
				el(
					'div',
					{ className: 'yourhome-section__inner' },
					el( RichText.Content, {
						tagName: 'h2',
						className: 'yourhome-section__heading',
						value: props.attributes.heading,
					} ),
					el( InnerBlocks.Content )
				)
			);
		},
	} );
} )( window.wp.blocks, window.wp.blockEditor, window.wp.components, window.wp.element, window.wp.i18n );
