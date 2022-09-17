/* global jQuery */

/**
 * Toggles the visibility of the additional terms content.
 *
 * @since    1.3.3
 */
( function ( $ ) {
	const wooTermsToggle = {
		init() {
			$( document.body ).on( 'click', 'a.woo-additional-terms__link', this.toggleTerms );
		},

		toggleTerms() {
			if ( $( '.woo-additional-terms__content' ).length ) {
				$( '.woo-additional-terms__content' ).slideToggle( function () {
					const linkToggle = $( '.woo-additional-terms__link' );

					if ( $( '.woo-additional-terms__content' ).is( ':visible' ) ) {
						linkToggle.addClass( 'woo-additional-terms__link--open' );
						linkToggle.removeClass( 'woo-additional-terms__link--closed' );
					} else {
						linkToggle.removeClass( 'woo-additional-terms__link--open' );
						linkToggle.addClass( 'woo-additional-terms__link--closed' );
					}
				} );

				return false;
			}
		},
	};

	wooTermsToggle.init();
} )( jQuery );
