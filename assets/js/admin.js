/* global jQuery */

( function ( wp, $ ) {
	'use strict';

	if ( ! wp ) {
		return;
	}

	const admin = {
		/**
		 * Cache.
		 *
		 * @since 1.0.0
		 */
		cache() {
			this.els = {};
			this.vars = {};
			this.els.$required = $( '[name="woo_additional_terms_options[required]"]' );
			this.els.$error = $( '[name="woo_additional_terms_options[error]"]' );
		},

		/**
		 * Initialize.
		 *
		 * @since 1.0.0
		 */
		init() {
			this.cache();
			this.bindEvents();
			this.handleRequiredToggle(); // Check on page load.
		},

		/**
		 * Bind events.
		 *
		 * @since 1.0.0
		 *
		 * @return {void}
		 */
		bindEvents() {
			this.els.$required.on( 'change', this.handleRequiredToggle );
		},

		/**
		 * Handle required toggle.
		 *
		 * @since 1.0.0
		 *
		 * @return {void}
		 */
		handleRequiredToggle() {
			const isChecked = admin.els.$required.is( ':checked' );
			admin.els.$error.closest( 'tr' ).toggle( isChecked );
		},
	};

	admin.init();
} )( window.wp, jQuery );
