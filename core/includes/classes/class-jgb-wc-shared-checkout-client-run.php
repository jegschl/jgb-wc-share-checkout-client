<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Class Jgb_Wc_Shared_Checkout_Client_Run
 *
 * Thats where we bring the plugin to life
 *
 * @package		JWSCC
 * @subpackage	Classes/Jgb_Wc_Shared_Checkout_Client_Run
 * @author		Jorge Garrido
 * @since		1.0.0
 */
class Jgb_Wc_Shared_Checkout_Client_Run{

	/**
	 * Our Jgb_Wc_Shared_Checkout_Client_Run constructor 
	 * to run the plugin logic.
	 *
	 * @since 1.0.0
	 */
	function __construct(){
		$this->add_hooks();
	}

	/**
	 * ######################
	 * ###
	 * #### WORDPRESS HOOKS
	 * ###
	 * ######################
	 */

	/**
	 * Registers all WordPress and plugin related hooks
	 *
	 * @access	private
	 * @since	1.0.0
	 * @return	void
	 */
	private function add_hooks(){
	
		add_action( 'plugin_action_links_' . JWSCC_PLUGIN_BASE, array( $this, 'add_plugin_action_link' ), 20 );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_backend_scripts_and_styles' ), 20 );
		
		add_action('template_redirect', 'Jgb_Wc_Shared_Checkout_Client_Helpers::redirect_from_checkout');
		add_action('template_redirect', [Jgb_Wc_Shared_Checkout_Client::instance()->helpers,'manage_wc_ordr_creatn_request'] );
		add_action('template_redirect', [Jgb_Wc_Shared_Checkout_Client::instance()->helpers,'manage_wc_prods_updates_request'] );
	}

	/**
	 * ######################
	 * ###
	 * #### WORDPRESS HOOK CALLBACKS
	 * ###
	 * ######################
	 */

	/**
	* Adds action links to the plugin list table
	*
	* @access	public
	* @since	1.0.0
	*
	* @param	array	$links An array of plugin action links.
	*
	* @return	array	An array of plugin action links.
	*/
	public function add_plugin_action_link( $links ) {

		$links['our_shop'] = sprintf( '<a href="%s" title="Custom Link" style="font-weight:700;">%s</a>', 'https://test.test', __( 'Custom Link', 'jgb-wc-shared-checkout-client' ) );

		return $links;
	}

	/**
	 * Enqueue the backend related scripts and styles for this plugin.
	 * All of the added scripts andstyles will be available on every page within the backend.
	 *
	 * @access	public
	 * @since	1.0.0
	 *
	 * @return	void
	 */
	public function enqueue_backend_scripts_and_styles() {
		wp_enqueue_script( 'jwscc-backend-scripts', JWSCC_PLUGIN_URL . 'core/includes/assets/js/backend-scripts.js', array(), JWSCC_VERSION, false );
		wp_localize_script( 'jwscc-backend-scripts', 'jwscc', array(
			'plugin_name'   	=> __( JWSCC_NAME, 'jgb-wc-shared-checkout-client' ),
		));
	}

}
