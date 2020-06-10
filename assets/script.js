/**
 * Toggles the visibility of the additional terms content.
 *
 * @since       1.3.0
 * @package     Woo Additional Terms
 */
jQuery( function( $ ) {
	var woo_terms_toggle = {
		init: function() {
			$( document.body ).on( 'click', 'a.woo-additional-terms__link', this.toggle_terms );
		},

		toggle_terms: function() {
			if ( $( '.woo-additional-terms__content' ).length ) {
				$( '.woo-additional-terms__content' ).slideToggle( function() {
					var link_toggle = $( '.woo-additional-terms__link' );

					if ( $( '.woo-additional-terms__content' ).is( ':visible' ) ) {
						link_toggle.addClass( 'woo-additional-terms__link--open' );
						link_toggle.removeClass( 'woo-additional-terms__link--closed' );
					} else {
						link_toggle.removeClass( 'woo-additional-terms__link--open' );
						link_toggle.addClass( 'woo-additional-terms__link--closed' );
					}
				} );

				return false;
			}
		}
	};

	woo_terms_toggle.init();
} );