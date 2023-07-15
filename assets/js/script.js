/* global jQuery */

( function ( $ ) {
	'use strict';

	const script = {
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

		init() {
			this.cache();
			$( document.body ).on( 'click', `a.${ this.vars.embed }`, this.handleEmbedToggle );
		},

		handleEmbedToggle( event ) {
			event.preventDefault();

			const $this = $( this );
			const $wrapper = $this.closest( script.vars.wrapper );

			if ( ! $wrapper.length ) {
				return false;
			}

			const $content = $wrapper.find( `> .${ script.vars.content }` );

			$content.slideToggle( function () {
				const isVisible = $content.is( ':visible' );
				$this.toggleClass( script.vars.openClassName, isVisible );
				$this.toggleClass( script.vars.closeClassName, ! isVisible );
			} );

			return false;
		},
	};

	script.init();
} )( jQuery );
