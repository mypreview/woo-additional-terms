<?php
/**
 * The Template for displaying onboarding (welcome) admin notice.
 *
 * @since 1.0.0
 *
 * @package woo-additional-terms
 */

defined( 'ABSPATH' ) || exit;
defined( 'WC_VERSION' ) || exit;

?>

<div id="woo-additional-terms-dismiss-onboarding" class="notice notice-info is-dismissible" data-action="onboarding">
	<p>
		<i class="dashicons dashicons-admin-settings"></i>
		<?php
		printf(
			/* translators: 1: Plugin name, 2: Open anchor tag, 3: Close anchor tag, 4: Open anchor tag, 5: Close anchor tag. */
			esc_html_x( 'Thanks for installing %1$s plugin! To get started, visit the %2$sdocumentation%3$s or %4$splugin’s settings page%5$s.', 'admin notice', 'woo-additional-terms' ),
			sprintf(
				'<strong>%s</strong>',
				esc_html_x( 'Additional Terms for WooCommerce', 'plugin name', 'woo-additional-terms' )
			),
			sprintf(
				'<a href="%s" target="_blank" rel="noopener noreferrer nofollow">',
				esc_url( $args['help_uri'] )
			),
			'</a>',
			sprintf(
				'<a href="%s" class="notice-dismiss-later" target="_self">',
				esc_url( $args['settings_uri'] )
			),
			'</a>'
		);
		?>
	</p>
</div>

<?php
/* Omit closing PHP tag at the end of PHP files to avoid "headers already sent" issues. */
