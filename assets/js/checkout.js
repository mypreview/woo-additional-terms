/* eslint-disable camelcase, react-hooks/rules-of-hooks */

( function ( wp, wc ) {
	'use strict';

	if ( ! wp || ! wc ) {
		return;
	}

	const el = wp.element.createElement;
	const { withInstanceId } = wp.compose;
	const { useDispatch, useSelect } = wp.data;
	const { useEffect, useState } = wp.element;
	const { getSetting } = wc.wcSettings;
	const { VALIDATION_STORE_KEY } = wc.wcBlocksData;
	const { CheckboxControl, registerCheckoutBlock } = wc.blocksCheckout;

	registerCheckoutBlock( {
		metadata: {
			name: 'mypreview/woo-additional-terms',
			parent: [ 'woocommerce/checkout-fields-block' ],
		},
		component: withInstanceId( ( { instanceId, checkoutExtensionData } ) => {
			const data = getSetting( '_woo_additional_terms_data', '' );
			const { setExtensionData } = checkoutExtensionData;

			// Bail early if the checkbox label is empty.
			if ( ! data?.checkbox_label ) {
				useEffect( () => {
					setExtensionData( '_woo_additional_terms', 'data', '' );
				}, [] );
				return null;
			}

			const { checkbox_label, is_required, display_action, page_content, error_message } = data;
			const validationErrorId = `_woo_additional_terms_data_${ instanceId }`;
			const [ checked, setChecked ] = useState( false );
			const { setValidationErrors, clearValidationError } = useDispatch( VALIDATION_STORE_KEY );
			const error = useSelect( ( select ) =>
				select( VALIDATION_STORE_KEY ).getValidationError( validationErrorId )
			);
			const hasError = !! ( error?.message && ! error?.hidden );

			useEffect( () => {
				setExtensionData( '_woo_additional_terms', 'data', checked ? 'yes' : 'no' );

				if ( ! is_required ) {
					return;
				}

				if ( checked ) {
					clearValidationError( validationErrorId );
					return;
				}

				setValidationErrors( {
					[ validationErrorId ]: {
						message: error_message,
						hidden: true,
					},
				} );

				return () => {
					clearValidationError( validationErrorId );
				};
			}, [
				setExtensionData,
				checked,
				validationErrorId,
				clearValidationError,
				setValidationErrors,
				is_required,
				error_message,
			] );

			return el(
				'div',
				{
					className: 'woocommerce-terms-and-conditions-wrapper woo-additional-terms',
				},
				el( 'div', {
					id: 'woo-additional-terms-content',
					className: `woo-additional-terms__content woo-additional-terms__content--${ display_action }`,
					dangerouslySetInnerHTML: {
						__html: page_content,
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
							__html: checkbox_label,
						},
					} )
				)
			);
		} ),
	} );
} )( window.wp, window.wc );
