( function ( blocks, blockEditor, element, i18n ) {
	'use strict';

	const el = element.createElement;
	const InnerBlocks = blockEditor.InnerBlocks;
	const RichText = blockEditor.RichText;
	const useBlockProps = blockEditor.useBlockProps;
	const __ = i18n.__;

	blocks.registerBlockType( 'yourhome/section', {
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
						onChange: function ( heading ) { props.setAttributes( { heading: heading } ); },
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
				el( 'div', { className: 'yourhome-section__inner' },
					el( RichText.Content, { tagName: 'h2', className: 'yourhome-section__heading', value: props.attributes.heading } ),
					el( InnerBlocks.Content )
				)
			);
		},
	} );
} )( window.wp.blocks, window.wp.blockEditor, window.wp.element, window.wp.i18n );
