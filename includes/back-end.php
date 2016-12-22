<?php
/**
 * Retrieve plugin option value(s).
 *
 * @author      Mahdi Yazdani
 * @package     Woo Additional Terms
 * @since       1.0
 */
/**
 * Display additional terms field value on order details page.
 *
 *@since 1.0
 */
if (!function_exists('woo_additional_terms_field_value_admin_order_meta')): 
	function woo_additional_terms_field_value_admin_order_meta( $order ){
		// Retrieve order meta value(s)
		$woo_additional_terms_title = ( get_option( 'woo_additional_terms_title' ) ) ? esc_attr( get_option( 'woo_additional_terms_title' ) ) : __('Additional Terms', 'woo-additional-terms');
		$woo_additional_terms = ( !empty( get_post_meta($order->id, 'woo-additional-terms-checkbox', true )) && get_post_meta( $order->id, 'woo-additional-terms-checkbox', true ) === 'on' ) ? __('Accepted', 'woo-additional-terms') : '';
		echo '<p style="clear: both;">' . PHP_EOL;
		echo '<strong>' . $woo_additional_terms_title .  '</strong><br/>' . PHP_EOL;
		echo $woo_additional_terms;
		echo '</p>' . PHP_EOL;
	}
endif;
add_action( 'woocommerce_admin_order_data_after_billing_address', 'woo_additional_terms_field_value_admin_order_meta', 10, 1 );