<?php
/**
 * The Template for displaying upsell sidebar.
 *
 * @since 1.0.0
 *
 * @package woo-additional-terms
 */

defined( 'ABSPATH' ) || exit;
defined( 'WC_VERSION' ) || exit;

?>
<div class="woo-additional-terms-get-pro">
	<div class="woo-additional-terms-get-pro-logo"></div>
	<h2 class="woo-additional-terms-get-pro-title">
		<?php echo esc_html_x( 'Additional Terms Pro', 'upsell', 'woo-additional-terms' ); ?>
	</h2>
	<ul class="woo-additional-terms-get-pro-features">
		<li>
			<?php echo esc_html_x( 'Unlimited T&C checkboxes', 'upsell', 'woo-additional-terms' ); ?>
		</li>
		<li>
			<?php echo esc_html_x( 'Define smart conditions', 'upsell', 'woo-additional-terms' ); ?>
		</li>
		<li>
			<?php echo esc_html_x( 'Acceptance summary', 'upsell', 'woo-additional-terms' ); ?>
		</li>
		<li>
			<?php echo esc_html_x( '24/7 priority support', 'upsell', 'woo-additional-terms' ); ?>
		</li>
	</ul>
	<p class="woo-additional-terms-get-pro-cta">
		<a href="<?php echo esc_url( $args['uri'] ); ?>" target="_blank" rel="noopener noreferrer nofollow">
			<?php echo esc_html_x( 'Go PRO for More Options', 'upsell', 'woo-additional-terms' ); ?>
		</a>
	</p>
	<div class="woo-additional-terms-get-pro-rate">
		<a href="https://wordpress.org/support/plugin/woo-additional-terms/reviews?filter=5" target="_blank" rel="noopener noreferrer nofollow">
			<strong>
				<?php echo esc_html_x( 'Read reviews from real users', 'upsell', 'woo-additional-terms' ); ?>
			</strong>
			<div>
				<span class="dashicons dashicons-wordpress"></span>
				<span class="dashicons dashicons-star-filled"></span>
				<span class="dashicons dashicons-star-filled"></span>
				<span class="dashicons dashicons-star-filled"></span>
				<span class="dashicons dashicons-star-filled"></span>
				<span class="dashicons dashicons-star-empty"></span>
				<span class="woo-additional-terms-get-pro-rating-text">4.0 / 5</span>
			</div>
		</a>
	</div>
</div>

<?php
/* Omit closing PHP tag at the end of PHP files to avoid "headers already sent" issues. */
