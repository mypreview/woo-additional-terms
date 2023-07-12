<?php
/**
 * The Template for displaying settings page sidebar.
 *
 * @since 1.0.0
 *
 * @package woo-additional-terms
 */

defined( 'ABSPATH' ) || exit;
defined( 'WC_VERSION' ) || exit;

?>
<div class="woo-additional-terms-page-sidebar">
	<?php
	/**
	 * Fires inside the sidebar.
	 *
	 * @since 1.0.0
	 */
	do_action( 'woo_additional_terms_settings_sidebar' );
	?>
</div>

<?php
/* Omit closing PHP tag at the end of PHP files to avoid "headers already sent" issues. */
