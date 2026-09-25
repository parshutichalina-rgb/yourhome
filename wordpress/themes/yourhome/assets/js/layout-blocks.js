( function ( blocks, blockEditor, element, i18n ) {
	'use strict';

	const el = element.createElement;
	const InnerBlocks = blockEditor.InnerBlocks;
	const useBlockProps = blockEditor.useBlockProps;
	const __ = i18n.__;

	blocks.registerBlockType( 'yourhome/header', {
		edit: function () {
			const blockProps = useBlockProps( { className: 'yourhome-header' } );

			return el(
				'div',
				blockProps,
				el(
					'div',
					{ className: 'yourhome-header__inner' },
					el( InnerBlocks, {
						allowedBlocks: [ 'core/group', 'core/site-logo', 'core/site-title', 'core/navigation', 'core/buttons', 'core/social-links' ],
						template: [
							[ 'core/group', { align: 'wide', layout: { type: 'flex', flexWrap: 'wrap', justifyContent: 'space-between' } }, [
								[ 'core/site-title', { level: 0 } ],
								[ 'core/navigation', { overlayMenu: 'mobile' } ],
							] ],
						],
						templateLock: false,
					} )
				)
			);
		},
		save: function () {
			const blockProps = useBlockProps.save( { className: 'yourhome-header' } );

			return el(
				'div',
				blockProps,
				el( 'div', { className: 'yourhome-header__inner' }, el( InnerBlocks.Content ) )
			);
		},
	} );

	blocks.registerBlockType( 'yourhome/footer', {
		edit: function () {
			const blockProps = useBlockProps( { className: 'yourhome-footer' } );

			return el(
				'div',
				blockProps,
				el(
					'div',
					{ className: 'yourhome-footer__inner' },
					el( InnerBlocks, {
						allowedBlocks: [ 'core/group', 'core/columns', 'core/column', 'core/site-logo', 'core/site-title', 'core/navigation', 'core/paragraph', 'core/heading', 'core/buttons', 'core/social-links' ],
						template: [
							[ 'core/group', { align: 'wide', layout: { type: 'flex', flexWrap: 'wrap', justifyContent: 'space-between' } }, [
								[ 'core/site-title', { level: 0, isLink: true } ],
								[ 'core/paragraph', { content: __( 'Built with WordPress and Gutenberg.', 'yourhome' ) } ],
							] ],
						],
						templateLock: false,
					} )
				)
			);
		},
		save: function () {
			const blockProps = useBlockProps.save( { className: 'yourhome-footer' } );

			return el(
				'div',
				blockProps,
				el( 'div', { className: 'yourhome-footer__inner' }, el( InnerBlocks.Content ) )
			);
		},
	} );
} )( window.wp.blocks, window.wp.blockEditor, window.wp.element, window.wp.i18n );
