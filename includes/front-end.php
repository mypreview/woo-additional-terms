<?php
/**
 * Retrieve plugin option value(s).
 *
 * @author      Mahdi Yazdani
 * @package     Woo Additional Terms
 * @since       1.0
 */
/**
 * Display Additioanl Terms checkbox in checkout page.
 *
 *@since 1.0
 */
if (!function_exists('woo_additional_terms_checkout_checkbox')): 
	function woo_additional_terms_checkout_checkbox() {
		// Retrieve plugin option value(s)
		$woo_additional_terms_page = ( get_option( 'woo_additional_terms_page' ) ) ? esc_attr( get_option( 'woo_additional_terms_page' ) ) : '';
		$woo_additional_terms_title = ( get_option( 'woo_additional_terms_title' ) ) ? esc_attr( get_option( 'woo_additional_terms_title' ) ) : '';
		$woo_additional_terms_notice = ( get_option( 'woo_additional_terms_notice' ) ) ? esc_attr( get_option( 'woo_additional_terms_notice' ) ) : '';
		$woo_additional_terms_link_shortcode = '%link%';
		if( isset($woo_additional_terms_notice) && !empty($woo_additional_terms_notice) ):
			echo '<p class="form-row terms">' . PHP_EOL;
			echo '<input type="checkbox" class="input-checkbox" name="woo-additional-terms-checkbox" id="woo-additional-terms-checkbox" />' . PHP_EOL;
			if( strpos($woo_additional_terms_notice, $woo_additional_terms_link_shortcode) !== false && isset($woo_additional_terms_page, $woo_additional_terms_title) && !empty($woo_additional_terms_page) && !empty($woo_additional_terms_title) ) :
				echo '<label for="woo-additional-terms-checkbox" class="checkbox">' . str_replace( '%link%', '<a href="' . get_permalink($woo_additional_terms_page) . '" target="_blank">' . $woo_additional_terms_title . '</a>', $woo_additional_terms_notice ) . ' <span class="required">*</span></label>' . PHP_EOL;
			else:
				echo '<label for="woo-additional-terms-checkbox" class="checkbox">' . str_replace( '%link%', '', $woo_additional_terms_notice ) . ' <span class="required">*</span></label>' . PHP_EOL;
			endif;
			echo '</p>' . PHP_EOL;
		endif;
	}
endif;
add_action('woocommerce_review_order_before_submit', 'woo_additional_terms_checkout_checkbox', 5);
/**
 * Display custom error message whenever customer didn't accept additional terms.
 *
 *@since 1.0
 */
if (!function_exists('woo_additional_terms_checkout_notice_error')): 
	function woo_additional_terms_checkout_notice_error() {
		// Retrieve plugin option value(s)
		$woo_additional_terms_notice = ( get_option( 'woo_additional_terms_notice' ) ) ? esc_attr( get_option( 'woo_additional_terms_notice' ) ) : '';
		$woo_additional_terms_notice_error = ( get_option( 'woo_additional_terms_notice_error' ) ) ? esc_attr( get_option( 'woo_additional_terms_notice_error' ) ) : __('You must accept our additional terms.', 'woo-additional-terms');
	    if ( !isset($woo_additional_terms_notice, $_POST['woo-additional-terms-checkbox']) && !empty($woo_additional_terms_notice) ):
	    	wc_add_notice( $woo_additional_terms_notice_error, 'error' );
	    endif;
	}
endif;
add_action('woocommerce_checkout_process', 'woo_additional_terms_checkout_notice_error');
/**
 * Update the order meta with Woo Additional Terms value.
 *
 *@since 1.0
 */
if (!function_exists('woo_additional_terms_field_update_order_meta')): 
	function woo_additional_terms_field_update_order_meta( $order_id ) {
		// Retrieve plugin option value(s)
		$woo_additional_terms_notice = ( get_option( 'woo_additional_terms_notice' ) ) ? esc_attr( get_option( 'woo_additional_terms_notice' ) ) : '';
	    if ( isset($woo_additional_terms_notice, $_POST['woo-additional-terms-checkbox']) && !empty($woo_additional_terms_notice) ):
	        update_post_meta( $order_id, 'woo-additional-terms-checkbox', sanitize_text_field( $_POST['woo-additional-terms-checkbox'] ) );
	    endif;
	}
endif;
add_action( 'woocommerce_checkout_update_order_meta', 'woo_additional_terms_field_update_order_meta' );