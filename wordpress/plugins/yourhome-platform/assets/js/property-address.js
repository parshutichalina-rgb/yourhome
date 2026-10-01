( function ( wp ) {
	'use strict';
	const el = wp.element.createElement;
	const __ = wp.i18n.__;
	function PropertyAddress() {
		const state = wp.data.useSelect( function ( select ) {
			const editor = select( 'core/editor' );
			return { postType: editor.getCurrentPostType(), meta: editor.getEditedPostAttribute( 'meta' ) || {} };
		}, [] );
		const editPost = wp.data.useDispatch( 'core/editor' ).editPost;
		if ( state.postType !== 'property' ) { return null; }
		const fields = [
			{ key: 'yourhome_street', label: __( 'Street address', 'yourhome-platform' ), required: true },
			{ key: 'yourhome_city', label: __( 'City', 'yourhome-platform' ), required: true },
			{ key: 'yourhome_country', label: __( 'Country code', 'yourhome-platform' ), required: true },
			{ key: 'yourhome_postcode', label: __( 'Postcode', 'yourhome-platform' ), required: false }
		];
		return el( wp.editor.PluginDocumentSettingPanel, { name: 'yourhome-property-address', title: __( 'Property address', 'yourhome-platform' ), icon: 'location' },
			el( 'p', null, __( 'Street address, city and country code are required before publication.', 'yourhome-platform' ) ),
			fields.map( function ( field ) {
				return el( wp.components.TextControl, {
					key: field.key, label: field.label + ( field.required ? ' *' : '' ), required: field.required,
					value: state.meta[ field.key ] || '',
					onChange: function ( value ) { editPost( { meta: Object.assign( {}, state.meta, { [ field.key ]: value } ) } ); }
				} );
			} ),
			el( wp.components.ToggleControl, { label: __( 'Fictional demo address', 'yourhome-platform' ), checked: !!state.meta.yourhome_demo_address, onChange: function ( value ) { editPost( { meta: Object.assign( {}, state.meta, { yourhome_demo_address: value } ) } ); } } )
		);
	}
	wp.plugins.registerPlugin( 'yourhome-property-address', { render: PropertyAddress } );
} )( window.wp );
