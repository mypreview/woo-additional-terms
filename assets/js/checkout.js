/* eslint-disable react-hooks/rules-of-hooks */

( function ( wp, wc ) {
	'use strict';

	if ( ! wp || ! wc ) {
		return;
	}

	const el = wp.element.createElement;
	const { withInstanceId } = wp.compose;
	const { useDispatch, useSelect } = wp.data;
	const { useEffect, useState } = wp.element;
	const { __ } = wp.i18n;
	const { getSetting } = wc.wcSettings;
	const { VALIDATION_STORE_KEY } = wc.wcBlocksData;
	const { CheckboxControl, registerCheckoutBlock } = wc.blocksCheckout;

	registerCheckoutBlock( {
		metadata: {
			name: 'mypreview/woo-additional-terms',
			parent: [ 'woocommerce/checkout-fields-block' ],
		},
		component: withInstanceId( ( { instanceId, checkoutExtensionData } ) => {
			const { content, notice } = getSetting( '_woo_additional_terms_data', '' );

			if ( ! notice ) {
				return null;
			}

			const validationErrorId = `_woo_additional_terms_data_${ instanceId }`;
			const { setExtensionData } = checkoutExtensionData;
			const [ checked, setChecked ] = useState( false );
			const { setValidationErrors, clearValidationError } = useDispatch( VALIDATION_STORE_KEY );
			const error = useSelect( ( select ) =>
				select( VALIDATION_STORE_KEY ).getValidationError( validationErrorId )
			);
			const hasError = !! ( error?.message && ! error?.hidden );

			useEffect( () => {
				setExtensionData( '_woo_additional_terms', 'wat_checkbox', checked );

				if ( checked ) {
					clearValidationError( validationErrorId );
				} else {
					setValidationErrors( {
						[ validationErrorId ]: {
							message: __(
								'Please read and accept the additional terms and conditions to proceed with your order.',
								'woo-additional-terms'
							),
							hidden: true,
						},
					} );
				}

				return () => {
					clearValidationError( validationErrorId );
				};
			}, [ setExtensionData, checked, validationErrorId, clearValidationError, setValidationErrors ] );

			return el(
				'div',
				{
					className: 'woocommerce-terms-and-conditions-wrapper woo-additional-terms',
				},
				el( 'div', {
					dangerouslySetInnerHTML: {
						__html: content,
					},
				} ),
				el(
					CheckboxControl,
					{
						checked,
						hasError,
						id: '_woo_additional_terms',
						name: '_woo_additional_terms',
						onChange: () => setChecked( ( value ) => ! value ),
					},
					el( 'span', {
						dangerouslySetInnerHTML: {
							__html: notice,
						},
					} )
				)
			);
		} ),
	} );
} )( window.wp, window.wc );
