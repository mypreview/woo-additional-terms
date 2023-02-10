( function ( wp, wc ) {
	'use strict';

	if ( ! wp || ! wc ) {
		return;
	}

	const el = wp.element.createElement;
	const { registerBlockType } = wp.blocks;
	const { useBlockProps } = wp.blockEditor;
	const { Disabled, Notice } = wp.components;
	const { SVG, Path } = wp.primitives;
	const { CheckboxControl } = wc.blocksCheckout;
	const { ADMIN_URL, getSetting } = wc.wcSettings;
	const { __ } = wp.i18n;

	registerBlockType( 'mypreview/woo-additional-terms', {
		title: __( 'Additional Terms', 'woo-additional-terms' ),
		description: __( 'Placeholder block for displaying additional terms checkbox.', 'woo-additional-terms' ),
		icon: {
			foreground: '#ffffff',
			background: '#7f54b3',
			src: el(
				SVG,
				{ xmlns: 'http://www.w3.org/2000/svg', viewBox: '0 0 24 24' },
				el( Path, {
					d: 'M4 20h9v-1.5H4V20zm0-5.5V16h16v-1.5H4zm.8-4l.7.7 2-2V12h1V9.2l2 2 .7-.7-2-2H12v-1H9.2l2-2-.7-.7-2 2V4h-1v2.8l-2-2-.7.7 2 2H4v1h2.8l-2 2z',
				} )
			),
		},
		category: 'woocommerce',
		parent: [ 'woocommerce/checkout-fields-block' ],
		attributes: {
			lock: {
				type: 'object',
				default: {
					remove: true,
					move: false,
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

			return notice
				? el(
						Disabled,
						{},
						el(
							'div',
							blockProps,
							el(
								CheckboxControl,
								{
									id: '_woo_additional_terms',
									checked: false,
								},
								el( 'span', {
									style: { fontSize: 16 },
									dangerouslySetInnerHTML: {
										__html: notice,
									},
								} )
							)
						)
				  )
				: el(
						Notice,
						{
							isDismissible: false,
							status: 'warning',
							actions: [
								{
									className: 'wc-block-checkout__terms_notice-button',
									label: __(
										'Setup an additional Terms and Conditions page',
										'woo-additional-terms'
									),
									onClick: () =>
										window.open(
											`${ ADMIN_URL }admin.php?page=wc-settings&tab=woo-additional-terms`,
											'_blank'
										),
								},
							],
						},
						el(
							'p',
							{},
							__( 'You don’t have additional Terms and Conditions page set up.', 'woo-additional-terms' )
						)
				  );
		},
		save: () => el( 'div', useBlockProps.save() ),
	} );
} )( window.wp, window.wc );
