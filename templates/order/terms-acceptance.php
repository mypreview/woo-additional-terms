<?php
/**
 * The Template for displaying terms acceptance status.
 *
 * @since 1.0.0
 *
 * @package woo-additional-terms
 */

defined( 'ABSPATH' ) || exit;
defined( 'WC_VERSION' ) || exit;

// Bailout, if no value found.
if ( empty( $args['meta'] ) ) {
	return;
}

// This block is intended for ensuring backward compatibility with versions older than 1.6.0.
// It's worth noting that in previous versions, the acceptance value was stored within an array.
if ( is_array( $args['meta'] ) ) {
	// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
	$args['meta'] = 'yes';
}
?>

<?php /* incorrect CSS class added here, so it adopts styling we want. */ ?>
<div class="address">
	<p>
		<strong style="display:flex;gap:5px;">
			<?php esc_html_e( 'Additional terms and conditions:', 'woo-additional-terms' ); ?>
			<span class="status-<?php echo esc_attr( wc_string_to_bool( $args['meta'] ) ? 'enabled' : 'disabled' ); ?>"></span>
		</strong>
	</p>
</div>

<?php
/* Omit closing PHP tag at the end of PHP files to avoid "headers already sent" issues. */
