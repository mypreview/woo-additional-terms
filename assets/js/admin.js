/* global jQuery, ajaxurl, watVars */

( function ( wp, $ ) {
	const watAdmin = {
		cache() {
			this.vars = {};
			this.vars.rate = '#woo-additional-terms-dismiss-rate .notice-dismiss';
			this.vars.upsell = '#woo-additional-terms-dismiss-upsell .notice-dismiss';
		},

		init() {
			this.cache();
			$( document.body ).on( 'click', this.vars.rate, ( event ) => this.handleOnDismiss( event, 'rate' ) );
			$( document.body ).on( 'click', this.vars.upsell, ( event ) => this.handleOnDismiss( event, 'upsell' ) );
		},

		handleOnDismiss( event, action ) {
			event.preventDefault();

			$.ajax( {
				type: 'POST',
				url: ajaxurl,
				data: {
					_ajax_nonce: watVars.dismiss_nonce,
					action: `woo_additional_terms_dismiss_${ action }`,
				},
				dataType: 'json',
			} );
		},
	};

	watAdmin.init();
} )( window.wp, jQuery );
