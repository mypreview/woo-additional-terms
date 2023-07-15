/* global jQuery */

( function ( $ ) {
	'use strict';

	const script = {
		/**
		 * Cache.
		 *
		 * @since 1.0.0
		 *
		 * @return {void}
		 */
		cache() {
			this.vars = {};
			this.els = {};
			this.vars.selector = 'woo-additional-terms';
			this.vars.embed = `${ this.vars.selector }__link[data-action="embed"]`;
			this.vars.content = `${ script.vars.selector }__content`;
			this.vars.openClassName = `${ this.vars.selector }__link--open`;
			this.vars.closeClassName = `${ this.vars.selector }__link--closed`;
			this.vars.wrapper = `.woocommerce-terms-and-conditions-wrapper.${ this.vars.selector }`;
		},

		/**
		 * Initialize.
		 *
		 * @since 1.0.0
		 *
		 * @return {void}
		 */
		init() {
			this.cache();
			this.bindEvents();
		},

		/**
		 * Bind events.
		 *
		 * @since 1.0.0
		 *
		 * @return {void}
		 */
		bindEvents() {
			$( document.body ).on( 'click', `a.${ this.vars.embed }`, this.handleEmbedToggle );
		},

		/**
		 * Handle embed toggle.
		 *
		 * @since 1.0.0
		 *
		 * @param {Event} event Event.
		 *
		 * @return {boolean} False.
		 */
		handleEmbedToggle( event ) {
			// Prevent default.
			event.preventDefault();

			const $this = $( this );
			const $wrapper = $this.closest( script.vars.wrapper );

			// Exit early if the wrapper doesn't exist.
			if ( ! $wrapper.length ) {
				return false;
			}

			const $content = $wrapper.find( `> .${ script.vars.content }` );

			$content.slideToggle( () => {
				const isVisible = $content.is( ':visible' );

				$this.toggleClass( script.vars.openClassName, isVisible );
				$this.toggleClass( script.vars.closeClassName, ! isVisible );
			} );

			return false;
		},
	};

	script.init();
} )( jQuery );
