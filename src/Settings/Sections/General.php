<?php
/**
 * The plugin settings fields.
 *
 * @author MyPreview (Github: @mahdiyazdani, @gooklani, @mypreview)
 *
 * @since 1.4.0
 *
 * @package woo-additional-terms
 */

namespace Woo_Additional_Terms\Settings\Sections;

use Woo_Additional_Terms\WooCommerce;

/**
 * Class Settings fields.
 */
class General extends Section {

	/**
	 * Retrieve the settings fields for the general (default) settings tab.
	 *
	 * @since 1.4.0
	 *
	 * @return array
	 */
	public function get_fields() {

		return array(
			'section_title' => array(
				'id'   => 'woo-additional-terms-general',
				'type' => 'title',
				'name' => _x( 'Woo Additional Terms', 'settings section name', 'woo-additional-terms' ),
				'desc' => _x( 'Close your store temporarily by scheduling your vacation time. While your shop will remain online and accessible to visitors, new order operations will pause, and your checkout will be disabled.', 'settings field description', 'woo-additional-terms' ),
			),
			'status' => array(
				'name'     => _x( 'Status', 'settings field name', 'woo-additional-terms' ),
				'desc'     => _x( 'The options are &quot;Active&quot; (enable this terms checkbox), or &quot;Disabled&quot; (deactivate (draft) this terms checkbox).', 'settings field description', 'woo-additional-terms' ),
				'type'     => 'select',
				'class'    => 'wc-enhanced-select',
				'id'       => 'woo_additional_terms_options[status]',
				'options'  => array(
					'1' => _x( 'Active', 'settings field option', 'woo-additional-terms' ),
					'0' => _x( 'Disabled', 'settings field option', 'woo-additional-terms' ),
				),
				'default'  => '1',
				'desc_tip' => true,
			),
			'page' => array(
				'name'     => _x( 'Terms Page', 'settings field name', 'woo-additional-terms' ),
				'desc'     => _x( 'If you define a "Terms" page the customer will be asked if they accept additional terms when checking out.', 'settings field description', 'woo-additional-terms' ),
				'type'     => 'single_select_page_with_search',
				'class'    => 'wc-page-search',
				'css'      => 'min-width:300px;',
				'id'       => 'woo_additional_terms_options[page]',
				'args'     => array(
					'exclude' =>
						array(
							wc_get_page_id( 'cart' ),
							wc_get_page_id( 'checkout' ),
							wc_get_page_id( 'myaccount' ),
						),
				),
				'desc_tip' => true,
				'autoload' => false,
			),
			'action' => array(
				'name'     => _x( 'Display Action', 'settings field name', 'woo-additional-terms' ),
				'desc'     => _x( 'The options are &quot;Embed&quot; (same behavior as WooCommerce T&C), &quot;Modal&quot; (opens page content in a modal), or &quot;New tab&quot; (opens linked page in a new tab).', 'settings field description', 'woo-additional-terms' ),
				'type'     => 'select',
				'class'    => 'wc-enhanced-select',
				'id'       => 'woo_additional_terms_options[action]',
				'options'  => array(
					'embed'  => _x( 'Embed above the checkbox', 'settings field option', 'woo-additional-terms' ),
					'modal'  => _x( 'Open page content in a modal', 'settings field option', 'woo-additional-terms' ),
					'newtab' => _x( 'Open linked page in a new tab', 'settings field option', 'woo-additional-terms' ),
				),
				'default'  => 'embed',
				'desc_tip' => true,
				'autoload' => false,
			),
			'notice' => array(
				'name'              => _x( 'Terms Text', 'settings field name', 'woo-additional-terms' ),
				'desc'              => _x( 'Add some text about your store additional terms to show during checkout.', 'settings field description', 'woo-additional-terms' ),
				'placeholder'       => _x( 'I have read and agree to the website {{additional-terms}}', 'settings field placeholder', 'woo-additional-terms' ),
				'type'              => 'textarea',
				'id'                => 'woo_additional_terms_options[notice]',
				'desc_tip'          => true,
				'autoload'          => false,
				'custom_attributes' => array(
					'rows' => 4,
					'cols' => 50,
				),
			),
			'smart_tag_info' => array(
				'type' => 'info',
				'text' => sprintf( /* translators: 1: Open paragraph tag, 2: Terms smart tag, 3: Close paragraph tag. */
					esc_html_x( '%1$sYou may utilize the %2$s smart tag to automatically populate the terms page name in your notice content.%3$s', 'settings field description', 'woo-additional-terms' ),
					'<p class="description">',
					'<code>{{additional-terms}}</code>',
					'</p>'
				),
			),
			'required' => array(
				'name'     => _x( 'Required', 'settings field name', 'woo-additional-terms' ),
				'desc'     => _x( 'Enable this to make the additional terms checkbox required.', 'settings field description', 'woo-additional-terms' ),
				'type'     => 'checkbox',
				'id'       => 'woo_additional_terms_options[required]',
				'default'  => 'yes',
			),
			'error' => array(
				'name'              => _x( 'Error Message', 'settings field name', 'woo-additional-terms' ),
				'desc'              => _x( 'Add an error message to show if the customer does not accept the additional terms.', 'settings field description', 'woo-additional-terms' ),
				'placeholder'       => _x( 'Please accept the additional terms to continue.', 'settings field placeholder', 'woo-additional-terms' ),
				'default'           => _x( 'Please accept the additional terms to continue.', 'settings field default', 'woo-additional-terms' ),
				'type'              => 'textarea',
				'id'                => 'woo_additional_terms_options[error]',
				'desc_tip'          => true,
				'autoload'          => false,
				'custom_attributes' => array(
					'rows' => 4,
					'cols' => 50,
				),
			),
			'section_end' => array(
				'type' => 'sectionend',
			),
		);
	}
}
