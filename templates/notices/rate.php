<?php
/**
 * The Template for displaying rate (review on wp.org) admin notice.
 *
 * @since 1.4.0
 *
 * @package woo-additional-terms
 */

defined( 'ABSPATH' ) || exit;
defined( 'WC_VERSION' ) || exit;

?>
<div id="woo-additional-terms-dismiss-rate" class="notice notice-alt is-dismissible" data-action="rate">
	<p>
		<i class="dashicons dashicons-star-filled"></i>
		<strong>
			<?php
			printf(
				/* translators: 1: Activation duration, 2: Plugin name */
				esc_html_x( '%1$s have passed since you started using %2$s.', 'admin notice', 'woo-additional-terms' ),
				esc_html( $args['usage_timestamp'] ),
				esc_html_x( 'Additional Terms for WooCommerce', 'plugin name', 'woo-additional-terms' )
			);
			?>
		</strong>
	</p>
	<p>
		<?php echo esc_html_x( ' Would you kindly consider leaving a review and letting us know how the plugin has helped your business? Your feedback is greatly appreciated!', 'admin notice', 'woo-additional-terms' ); ?>
	</p>
	<p>
		<a href="https://wordpress.org/support/plugin/woo-additional-terms/reviews?filter=5#new-post" class="button-primary notice-dismiss-later" target="_blank" rel="noopener noreferrer nofollow">
			&#9733;
			<?php echo esc_html_x( 'Give 5 Stars', 'admin notice', 'woo-additional-terms' ); ?> &#8594;
		</a>
		<button class="button-link notice-dismiss-later">
			<?php echo esc_html_x( 'Maybe later', 'admin notice', 'woo-additional-terms' ); ?>
		</button>
		<button class="button-link already-rated">
			<?php echo esc_html_x( 'I already did!', 'admin notice', 'woo-additional-terms' ); ?>
		</button>
	</p>
</div>

<?php
/* Omit closing PHP tag at the end of PHP files to avoid "headers already sent" issues. */
