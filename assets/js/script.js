/* global jQuery */

( function ( $ ) {
	'use strict';

	const wat = {
		cache() {
			this.vars = {};
			this.els = {};
			this.vars.selector = 'woo-additional-terms';
			this.vars.embed = `${ this.vars.selector }__link`;
			this.vars.content = `${ wat.vars.selector }__content`;
			this.vars.openClassName = `${ this.vars.selector }__link--open`;
			this.vars.closeClassName = `${ this.vars.selector }__link--closed`;
			this.vars.wrapper = `.woocommerce-terms-and-conditions-wrapper.${ this.vars.selector }`;
		},

		init() {
			this.cache();
			this.events();
		},

		events() {
			$( document.body ).on( 'click', `a.${ this.vars.embed }`, this.handleEmbedToggle );
		},

		handleEmbedToggle( event ) {
			event.preventDefault();

			const $this = $( this );
			const $wrapper = $this.closest( wat.vars.wrapper );
			const $content = $wrapper.find( `.${ wat.vars.content }` );

			if ( $wrapper.length ) {
				$content.slideToggle( function () {
					if ( $content.is( ':visible' ) ) {
						$this.addClass( wat.vars.openClassName );
						$this.removeClass( wat.vars.closeClassName );
					} else {
						$this.removeClass( wat.vars.openClassName );
						$this.addClass( wat.vars.closeClassName );
					}
				} );

				return false;
			}
		},
	};

	wat.init();
} )( jQuery );
