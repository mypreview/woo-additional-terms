<?php
/**
 * Woo Additional Terms Class.
 *
 * @author      Mahdi Yazdani
 * @package     Woo Additional Terms
 * @since       1.0
 */
// Prevent direct file access
if ( ! defined( 'ABSPATH' ) ) exit;
if ( !class_exists( 'WooAdditionalTerms' ) ) :
	class WooAdditionalTerms {
		private $file;
		public function __construct($file) {
			$this->file = $file;
			add_action( 'woocommerce_payment_gateways_settings', array( $this, 'woo_additional_terms_setting' ), 10 );
			add_filter( 'plugin_action_links_' . plugin_basename( $this->file ), array( $this, 'woo_additional_terms_settings_link' ), 10 );
		}
		public function woo_additional_terms_setting( $settings ) {
			$updated_settings = array();
			foreach ( $settings as $section ) :
				if ( isset( $section['id'] ) && 'checkout_page_options' == $section['id'] && isset( $section['type'] ) && 'sectionend' == $section['type'] ) :
					$updated_settings[] = array(
						'name'     => __( 'Additional Terms', 'woo-additional-terms' ),
						'desc' => __( 'If you define a "Additional Terms" page the customer will be asked if they accept them when checking out.', 'woo-additional-terms' ),
						'id'       => 'woo_additional_terms_page',
						'default'  => '',
						'class'    => 'wc-enhanced-select-nostd',
						'css'      => 'min-width:300px;',
						'type'     => 'single_select_page',
						'desc_tip' => true,
						'autoload' => false
					);
					$updated_settings[] = array(
						'title'    => __( 'Additional Terms Title', 'woo-additional-terms' ),
						'desc'     => __( 'Enter you custom title for additional terms, the title will appear in link shortcode and order details page.', 'woo-additional-terms' ),
						'id'       => 'woo_additional_terms_title',
						'type'     => 'text',
						'default'  => '',
						'desc_tip' => true,
						'css'      => 'min-width:350px;'
					);
					$updated_settings[] = array(
						'title'    => __( 'Additional Terms Notice', 'woo-additional-terms' ),
						'desc'     => __( 'Use %link% for appending terms page link into notice content.', 'woo-additional-terms' ),
						'id'       => 'woo_additional_terms_notice',
						'type'     => 'text',
						'default'  => '',
						'desc_tip' => __( 'Enter you custom notice for additional terms, the customer will be asked if they accept them when checking out.', 'woo-additional-terms' ),
						'css'      => 'min-width:350px;'
					);
					$updated_settings[] = array(
						'title'    => __( 'Additional Terms Notice Error', 'woo-additional-terms' ),
						'desc'     => __( 'Display friendly notice whenever customer doesn\'t accept additional terms.', 'woo-additional-terms' ),
						'id'       => 'woo_additional_terms_notice_error',
						'type'     => 'text',
						'default'  => __('You must accept our additional terms.', 'woo-additional-terms'),
						'desc_tip' => true,
						'css'      => 'min-width:350px;'
					);
				endif;
				$updated_settings[] = $section;
			endforeach;
			return $updated_settings;
		}
		/**
		 * Display plugin settings link in plugins table page.
		 *
		 *@since 1.0
		 */
		public function woo_additional_terms_settings_link($links) {
			// Add settings link to plugin list table
			$settings_link = '<a href="admin.php?page=wc-settings&tab=checkout">' . __( 'Settings', 'woocommerce-store-vacation' ) . '</a>';
  			array_push( $links, $settings_link );
  			return $links;
		}
	}
endif;