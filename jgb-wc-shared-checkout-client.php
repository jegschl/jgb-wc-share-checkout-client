<?php
/**
 * WC Shared Checkout Client
 *
 * @package       JWSCC
 * @author        Jorge Garrido
 * @version       1.0.0
 *
 * @wordpress-plugin
 * Plugin Name:   WC Shared Checkout Client
 * Plugin URI:    https://jgbit.cl/plugins/wc-share-checkout-client/
 * Description:   Connect to a WC with Shared Checkout plugin
 * Version:       1.0.0
 * Author:        Jorge Garrido
 * Author URI:    https://jgbit.cl/whami/
 * Text Domain:   jgb-wc-shared-checkout-client
 * Domain Path:   /languages
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;
// Plugin name
define( 'JWSCC_NAME',			'WC Shared Checkout Client' );

// Plugin version
define( 'JWSCC_VERSION',		'1.0.0' );

// Plugin Root File
define( 'JWSCC_PLUGIN_FILE',	__FILE__ );

// Plugin base
define( 'JWSCC_PLUGIN_BASE',	plugin_basename( JWSCC_PLUGIN_FILE ) );

// Plugin Folder Path
define( 'JWSCC_PLUGIN_DIR',	plugin_dir_path( JWSCC_PLUGIN_FILE ) );

// Plugin Folder URL
define( 'JWSCC_PLUGIN_URL',	plugin_dir_url( JWSCC_PLUGIN_FILE ) );

/**
 * Load the main class for the core functionality
 */
require_once JWSCC_PLUGIN_DIR . 'core/class-jgb-wc-shared-checkout-client.php';

/**
 * The main function to load the only instance
 * of our master class.
 *
 * @author  Jorge Garrido
 * @since   1.0.0
 * @return  object|Jgb_Wc_Shared_Checkout_Client
 */
function JWSCC() {
	return Jgb_Wc_Shared_Checkout_Client::instance();
}

JWSCC();
