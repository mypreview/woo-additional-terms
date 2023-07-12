<?php
/**
 * The Template for displaying the docs sidebar.
 *
 * @since 1.0.0
 *
 * @package woo-additional-terms
 */

defined( 'ABSPATH' ) || exit;
defined( 'WC_VERSION' ) || exit;

?>
<div class="woo-additional-terms-docs">
	<h2 class="woo-additional-terms-docs-title">
		<?php echo esc_html_x( 'Learn Settings', 'upsell', 'woo-additional-terms' ); ?>
	</h2>
	<p>
		<?php echo esc_html_x( 'Click on the link below to explore a wealth of information about the plugin’s settings, including step-by-step tutorials, configuration options, and troubleshooting guides. Everything you need to harness the full potential of the plugin is just a click away!', 'upsell', 'woo-additional-terms' ); ?>
	</p>
	<p class="woo-additional-terms-docs-cta">
		<a href="<?php echo esc_url( $args['uri'] ); ?>" target="_blank" rel="noopener noreferrer nofollow">
			<?php echo esc_html_x( 'Visit the documentation', 'upsell', 'woo-additional-terms' ); ?>
		</a>
	</p>
</div>

<?php
/* Omit closing PHP tag at the end of PHP files to avoid "headers already sent" issues. */
