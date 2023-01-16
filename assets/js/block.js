( function ( wp, wc ) {
	'use strict';

	if ( ! wp || ! wc ) {
		return;
	}

	const el = wp.element.createElement;
	const { registerBlockType } = wp.blocks;
	const { useBlockProps } = wp.blockEditor;
	const { CheckboxControl } = wc.blocksCheckout;
	const { getSetting } = wc.wcSettings;
	const { __ } = wp.i18n;

	registerBlockType( 'mypreview/woo-additional-terms', {
		title: __( 'Additional Terms', 'woo-additional-terms' ),
		description: __( 'Placeholder block for displaying additional terms checkbox.', 'woo-additional-terms' ),
		icon: {
			foreground: '#ffffff',
			background: '#7f54b3',
			src: 'text-page',
		},
		category: 'woocommerce',
		parent: [ 'woocommerce/checkout-fields-block' ],
		attributes: {
			lock: {
				type: 'object',
				default: {
					remove: true,
					move: true,
				},
			},
			checkbox: {
				type: 'boolean',
				default: false,
			},
		},
		supports: {
			align: false,
			html: false,
			multiple: false,
			reusable: false,
		},
		edit: () => {
			// eslint-disable-next-line react-hooks/rules-of-hooks
			const blockProps = useBlockProps();
			const { notice } = getSetting( '_woo_additional_terms_data', '' );

			return el(
				'div',
				blockProps,
				el( CheckboxControl, {
					id: '_woo_additional_terms',
					label: notice,
					checked: false,
					disabled: true,
				} )
			);
		},
		save: () => {
			return el( 'div', useBlockProps.save() );
		},
	} );
} )( window.wp, window.wc );
