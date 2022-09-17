<?php
/**
 * Unistall Woo Additional Terms.
 * Fired when the plugin is uninstalled.
 *
 * @package    Woo Additional Terms
 * @since      1.3.3
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

$woo_additional_terms_page_id = '_woo_additional_terms_page_id';
$woo_additional_terms_notice  = '_woo_additional_terms_notice';
$woo_additional_terms_error   = '_woo_additional_terms_error';

delete_option( $woo_additional_terms_page_id );
delete_option( $woo_additional_terms_notice );
delete_option( $woo_additional_terms_error );
// For site options in Multisite.
delete_site_option( $woo_additional_terms_page_id );
delete_site_option( $woo_additional_terms_notice );
delete_site_option( $woo_additional_terms_error );
