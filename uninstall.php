<?php
/**
 * Unistall Woo Additional Terms.
 * Fired when the plugin is uninstalled.
 *
 * @package     Woo Additional Terms
 * @since       1.2.1
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

delete_option( '_woo_additional_terms_page_id' );
delete_option( '_woo_additional_terms_notice' );
delete_option( '_woo_additional_terms_error' );
// For site options in Multisite.
delete_site_option( '_woo_additional_terms_page_id' );
delete_site_option( '_woo_additional_terms_notice' );
delete_site_option( '_woo_additional_terms_error' );
