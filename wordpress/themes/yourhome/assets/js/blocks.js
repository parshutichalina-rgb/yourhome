( function ( blocks, blockEditor, components, element, i18n, ServerSideRender ) {
	'use strict';

	const el = element.createElement;
	const InnerBlocks = blockEditor.InnerBlocks;
	const InspectorControls = blockEditor.InspectorControls;
	const MediaUpload = blockEditor.MediaUpload;
	const MediaUploadCheck = blockEditor.MediaUploadCheck;
	const RichText = blockEditor.RichText;
	const useBlockProps = blockEditor.useBlockProps;
	const Button = components.Button;
	const PanelBody = components.PanelBody;
	const RangeControl = components.RangeControl;
	const TextControl = components.TextControl;
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

	blocks.registerBlockType( 'yourhome/new-properties', {
		edit: function ( props ) {
			const blockProps = useBlockProps( { className: 'yourhome-editor-server-preview' } );

			return el( element.Fragment, null,
				el( InspectorControls, null, el( PanelBody, { title: __( 'Display settings', 'yourhome' ) },
					el( TextControl, { label: __( 'Eyebrow', 'yourhome' ), value: props.attributes.eyebrow, onChange: function ( eyebrow ) { props.setAttributes( { eyebrow: eyebrow } ); } } ),
					el( TextControl, { label: __( 'Heading', 'yourhome' ), value: props.attributes.heading, onChange: function ( heading ) { props.setAttributes( { heading: heading } ); } } ),
					el( TextControl, { label: __( 'Introduction', 'yourhome' ), value: props.attributes.intro, onChange: function ( intro ) { props.setAttributes( { intro: intro } ); } } ),
					el( TextControl, { label: __( 'Catalog link label', 'yourhome' ), value: props.attributes.linkLabel, onChange: function ( linkLabel ) { props.setAttributes( { linkLabel: linkLabel } ); } } ),
					el( TextControl, { label: __( 'Catalog link URL', 'yourhome' ), type: 'url', value: props.attributes.linkUrl, onChange: function ( linkUrl ) { props.setAttributes( { linkUrl: linkUrl } ); } } ),
					el( RangeControl, { label: __( 'Number of properties', 'yourhome' ), min: 1, max: 12, value: props.attributes.count, onChange: function ( count ) { props.setAttributes( { count: count } ); } } ),
					el( TextControl, { label: __( 'Empty state message', 'yourhome' ), value: props.attributes.emptyMessage, onChange: function ( emptyMessage ) { props.setAttributes( { emptyMessage: emptyMessage } ); } } )
				) ),
				el( 'div', blockProps, el( ServerSideRender, { block: 'yourhome/new-properties', attributes: props.attributes } ) )
			);
		},
		save: function () { return null; },
	} );

	blocks.registerBlockType( 'yourhome/background-link', {
		edit: function ( props ) {
			const imageStyle = props.attributes.imageUrl ? { '--yourhome-background-link-image': 'url(' + props.attributes.imageUrl + ')' } : {};
			const blockProps = useBlockProps( { className: 'yourhome-background-link', style: imageStyle } );
			return el( element.Fragment, null,
				el( InspectorControls, null, el( PanelBody, { title: __( 'Link settings', 'yourhome' ) },
					el( TextControl, { label: __( 'Link URL', 'yourhome' ), value: props.attributes.url, onChange: function ( url ) { props.setAttributes( { url: url } ); } } ),
					el( MediaUploadCheck, null, el( MediaUpload, { allowedTypes: [ 'image' ], value: props.attributes.imageId, onSelect: function ( media ) { props.setAttributes( { imageId: media.id, imageUrl: media.url } ); }, render: function ( upload ) { return el( Button, { variant: 'secondary', onClick: upload.open }, props.attributes.imageUrl ? __( 'Replace background image', 'yourhome' ) : __( 'Choose background image', 'yourhome' ) ); } } ) )
				) ),
				el( 'section', blockProps,
					el( 'div', { className: 'yourhome-background-link__anchor' },
						el( 'span', { className: 'yourhome-background-link__content' },
							el( RichText, { tagName: 'span', className: 'yourhome-background-link__eyebrow', value: props.attributes.eyebrow, placeholder: __( 'Eyebrow…', 'yourhome' ), onChange: function ( eyebrow ) { props.setAttributes( { eyebrow: eyebrow } ); } } ),
							el( RichText, { tagName: 'strong', className: 'yourhome-background-link__heading', value: props.attributes.heading, placeholder: __( 'Heading…', 'yourhome' ), onChange: function ( heading ) { props.setAttributes( { heading: heading } ); } } ),
							el( RichText, { tagName: 'span', className: 'yourhome-background-link__label', value: props.attributes.label, placeholder: __( 'Link label…', 'yourhome' ), onChange: function ( label ) { props.setAttributes( { label: label } ); } } )
						)
					)
				)
			);
		},
		save: function () { return null; },
	} );

	blocks.registerBlockType( 'yourhome/faq', {
		edit: function ( props ) {
			const items = props.attributes.items.length ? props.attributes.items : [ { question: '', answer: '' } ];
			const blockProps = useBlockProps( { className: 'yourhome-faq' } );
			function updateItem( index, key, value ) {
				props.setAttributes( { items: items.map( function ( item, itemIndex ) { return itemIndex === index ? Object.assign( {}, item, { [ key ]: value } ) : item; } ) } );
			}
			return el( 'section', blockProps,
				el( 'div', { className: 'yourhome-faq__inner' },
					el( 'div', { className: 'yourhome-faq__content' },
						el( RichText, { tagName: 'p', className: 'yourhome-eyebrow', value: props.attributes.eyebrow, placeholder: __( 'Eyebrow…', 'yourhome' ), onChange: function ( eyebrow ) { props.setAttributes( { eyebrow: eyebrow } ); } } ),
						el( RichText, { tagName: 'h2', value: props.attributes.heading, placeholder: __( 'Heading…', 'yourhome' ), onChange: function ( heading ) { props.setAttributes( { heading: heading } ); } } ),
						el( 'div', { className: 'yourhome-faq__items' }, items.map( function ( item, index ) { return el( 'details', { key: index, open: true },
							el( 'summary', null, el( RichText, { tagName: 'span', value: item.question, placeholder: __( 'Question…', 'yourhome' ), onChange: function ( value ) { updateItem( index, 'question', value ); } } ) ),
							el( RichText, { tagName: 'p', value: item.answer, placeholder: __( 'Answer…', 'yourhome' ), onChange: function ( value ) { updateItem( index, 'answer', value ); } } ),
							items.length > 1 && el( Button, { className: 'yourhome-faq__remove', isDestructive: true, onClick: function () { props.setAttributes( { items: items.filter( function ( unused, itemIndex ) { return itemIndex !== index; } ) } ); } }, __( 'Remove', 'yourhome' ) )
						); } ) ),
						el( Button, { className: 'yourhome-faq__add', variant: 'secondary', onClick: function () { props.setAttributes( { items: items.concat( [ { question: '', answer: '' } ] ) } ); } }, __( 'Add question', 'yourhome' ) )
					)
				)
			);
		},
		save: function () { return null; },
	} );

	blocks.registerBlockType( 'yourhome/contact-us', {
		edit: function ( props ) {
			const blockProps = useBlockProps( { className: 'yourhome-contact-us' } );
			return el( element.Fragment, null,
				el( InspectorControls, null, el( PanelBody, { title: __( 'Contact details', 'yourhome' ) },
					el( TextControl, { label: __( 'Email', 'yourhome' ), type: 'email', value: props.attributes.email, onChange: function ( email ) { props.setAttributes( { email: email } ); } } ),
					el( TextControl, { label: __( 'Phone', 'yourhome' ), type: 'tel', value: props.attributes.phone, onChange: function ( phone ) { props.setAttributes( { phone: phone } ); } } ),
					el( TextControl, { label: __( 'Button label', 'yourhome' ), value: props.attributes.buttonLabel, onChange: function ( buttonLabel ) { props.setAttributes( { buttonLabel: buttonLabel } ); } } )
				) ),
				el( 'section', blockProps,
					el( 'div', { className: 'yourhome-contact-us__inner' },
						el( 'div', null,
							el( RichText, { tagName: 'p', className: 'yourhome-eyebrow', value: props.attributes.eyebrow, placeholder: __( 'Eyebrow…', 'yourhome' ), onChange: function ( eyebrow ) { props.setAttributes( { eyebrow: eyebrow } ); } } ),
							el( RichText, { tagName: 'h2', value: props.attributes.heading, placeholder: __( 'Heading…', 'yourhome' ), onChange: function ( heading ) { props.setAttributes( { heading: heading } ); } } ),
							el( RichText, { tagName: 'p', value: props.attributes.text, placeholder: __( 'Description…', 'yourhome' ), onChange: function ( text ) { props.setAttributes( { text: text } ); } } )
						),
						el( 'div', { className: 'yourhome-contact-us__actions' },
							props.attributes.email && props.attributes.buttonLabel && el( 'span', { className: 'wp-element-button' }, props.attributes.buttonLabel ),
							props.attributes.email && el( 'span', null, props.attributes.email ),
							props.attributes.phone && el( 'span', null, props.attributes.phone )
						)
					)
				)
			);
		},
		save: function () { return null; },
	} );

	blocks.registerBlockType( 'yourhome/property-catalog', {
		edit: function ( props ) {
			return el( element.Fragment, null,
				el( InspectorControls, null, el( PanelBody, { title: __( 'Catalog content', 'yourhome' ) },
					el( TextControl, { label: __( 'Eyebrow', 'yourhome' ), value: props.attributes.eyebrow, onChange: function ( eyebrow ) { props.setAttributes( { eyebrow: eyebrow } ); } } ),
					el( TextControl, { label: __( 'Heading', 'yourhome' ), value: props.attributes.heading, onChange: function ( heading ) { props.setAttributes( { heading: heading } ); } } ),
					el( TextControl, { label: __( 'Introduction', 'yourhome' ), value: props.attributes.intro, onChange: function ( intro ) { props.setAttributes( { intro: intro } ); } } ),
					el( TextControl, { label: __( 'Filter button label', 'yourhome' ), value: props.attributes.submitLabel, onChange: function ( submitLabel ) { props.setAttributes( { submitLabel: submitLabel } ); } } ),
					el( TextControl, { label: __( 'Clear filters label', 'yourhome' ), value: props.attributes.clearLabel, onChange: function ( clearLabel ) { props.setAttributes( { clearLabel: clearLabel } ); } } ),
					el( TextControl, { label: __( 'Empty state heading', 'yourhome' ), value: props.attributes.emptyHeading, onChange: function ( emptyHeading ) { props.setAttributes( { emptyHeading: emptyHeading } ); } } ),
					el( TextControl, { label: __( 'Empty state text', 'yourhome' ), value: props.attributes.emptyText, onChange: function ( emptyText ) { props.setAttributes( { emptyText: emptyText } ); } } ),
					el( TextControl, { label: __( 'Empty state button label', 'yourhome' ), value: props.attributes.emptyButtonLabel, onChange: function ( emptyButtonLabel ) { props.setAttributes( { emptyButtonLabel: emptyButtonLabel } ); } } )
				) ),
				el( 'div', useBlockProps( { className: 'yourhome-editor-server-preview' } ), el( ServerSideRender, { block: 'yourhome/property-catalog', attributes: props.attributes, urlQueryArgs: { post_id: props.context.postId } } ) )
			);
		},
		save: function () { return null; },
	} );
} )( window.wp.blocks, window.wp.blockEditor, window.wp.components, window.wp.element, window.wp.i18n, window.wp.serverSideRender );
