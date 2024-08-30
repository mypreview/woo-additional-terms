<?php
/**
 * The Template for displaying additional terms and conditions checkbox on the checkout page.
 *
 * @since 1.0.0
 *
 * @package woo-additional-terms
 */

defined( 'ABSPATH' ) || exit;
defined( 'WC_VERSION' ) || exit;

// List of allowed anchor attributes.
// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
$allowed_anchor_attributes = array(
	'a' => array(
		'href'                  => array(),
		'target'                => array(),
		'class'                 => array(),
		'data-action'           => array(),
		'data-fancybox'         => array(),
		'data-animation-effect' => array(),
		'data-width'            => array(),
		'data-height'           => array(),
		'data-src'              => array(),
	),
);
?>

<div class="woocommerce-terms-and-conditions-wrapper woo-additional-terms">
	<?php if ( ! empty( $args['page_content'] ) ) : ?>
	<div
		id="woo-additional-terms-content"
		class="woo-additional-terms__content woo-additional-terms__content--<?php echo esc_attr( $args['display_action'] ); ?>"
	><?php echo wp_kses_post( $args['page_content'] ); ?></div>
	<?php endif; ?>
	<p class="form-row <?php echo esc_attr( wc_string_to_bool( $args['is_required'] ) ? 'validate-required' : 'novalidate' ); ?>">
		<label class="woocommerce-form__label woocommerce-form__label-for-checkbox checkbox">
		<input
			type="checkbox"
			class="woocommerce-form__input woocommerce-form__input-checkbox input-checkbox"
			name="_woo_additional_terms"
			id="_woo_additional_terms"
			value="1"
		/>
			<span class="woocommerce-terms-and-conditions-checkbox-text">
				<?php echo wp_kses( $args['checkbox_label'], $allowed_anchor_attributes ); ?>
			</span>
			<?php
			if ( wc_string_to_bool( $args['is_required'] ) ) :
				?>
				<abbr
					class="required"
					title="<?php esc_attr_e( 'Required', 'woo-additional-terms' ); ?>"
				>*</abbr>
			<?php endif; ?>
		</label>
	</p>
</div>

<?php
/* Omit closing PHP tag at the end of PHP files to avoid "headers already sent" issues. */
