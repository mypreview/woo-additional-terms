/* global jQuery, ajaxurl, watVars */

( function ( wp, $ ) {
	const wooTermsAdmin = {
		cache() {
			this.vars = {};
			this.vars.upsell = '.notice-info.woocommerce-message.is-dismissible .notice-dismiss';
		},

		init() {
			this.cache();
			$( document.body ).on( 'click', this.vars.upsell, this.handleOnDismiss );
		},

		handleOnDismiss() {
			$.ajax( {
				type: 'POST',
				url: ajaxurl,
				data: {
					_ajax_nonce: watVars.dismiss_nonce,
					action: 'woo_additional_terms_dismiss_upsell',
				},
				dataType: 'json',
			} );
		},
	};

	wooTermsAdmin.init();
} )( window.wp, jQuery );
