<?php
/*
Plugin Name: 	Woo Additional Terms
Plugin URI:  	https://www.mypreview.one
Description: 	Add additional terms and condition checkbox into WooCommerce Checkout.
Version:     	1.0.2
Author:      	Mahdi Yazdani
Author URI:  	https://www.mypreview.one
Text Domain: 	woo-additional-terms
Domain Path: 	/languages
License:     	GPL2
License URI: 	https://www.gnu.org/licenses/gpl-2.0.html
 
Woo Additional Terms is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.
 
Woo Additional Terms is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
 
You should have received a copy of the GNU General Public License
along with Woo Additional Terms. If not, see https://www.gnu.org/licenses/gpl-2.0.html.
*/
// Prevent direct file access
defined( 'ABSPATH' ) or exit;
// Check the requirements of plugin (first step).
require_once dirname( __FILE__ ) . '/includes/requirements.php';
// WooCommerce Store Vacation Class.
require_once dirname( __FILE__ ) . '/includes/WooAdditionalTerms.php';
// Retrieve plugin option value(s).
require_once dirname( __FILE__ ) . '/includes/front-end.php';
require_once dirname( __FILE__ ) . '/includes/back-end.php';
if ( is_admin() ) :
	$woocommerce_store_vacation = new WooAdditionalTerms(__FILE__);
	load_plugin_textdomain( 'woo-additional-terms', FALSE, basename( dirname( __FILE__ ) ) . '/languages/' );
endif;