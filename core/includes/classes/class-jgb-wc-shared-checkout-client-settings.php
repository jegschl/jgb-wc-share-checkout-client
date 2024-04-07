<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

define('JGB_WSCC_SETTING_OPT_MAIN_KEY_NM','jgb_wscc_settings');
define('JGB_WSCC_SETTING_OKN_CREDENTIALS','credentials');
define('JGB_WSCC_SETTING_OKN_WC_SHARED_CHK_URL','wc_shared_checkout_url');
define('JGB_WSCC_SETTING_OKN_CORTLC','cortlc_endpoint');

/**
 * Class Jgb_Wc_Shared_Checkout_Client_Settings
 *
 * This class contains all of the plugin settings.
 * Here you can configure the whole plugin data.
 *
 * @package		JWSCC
 * @subpackage	Classes/Jgb_Wc_Shared_Checkout_Client_Settings
 * @author		Jorge Garrido
 * @since		1.0.0
 */
class Jgb_Wc_Shared_Checkout_Client_Settings{

	/**
	 * The plugin name
	 *
	 * @var		string
	 * @since   1.0.0
	 */
	private $plugin_name;

	/**
	 * Our Jgb_Wc_Shared_Checkout_Client_Settings constructor 
	 * to run the plugin logic.
	 *
	 * @since 1.0.0
	 */
	function __construct(){

		$this->plugin_name = JWSCC_NAME;
	}

	/**
	 * ######################
	 * ###
	 * #### CALLABLE FUNCTIONS
	 * ###
	 * ######################
	 */

	/**
	 * Return the plugin name
	 *
	 * @access	public
	 * @since	1.0.0
	 * @return	string The plugin name
	 */
	public function get_plugin_name(){
		return apply_filters( 'JWSCC/settings/get_plugin_name', $this->plugin_name );
	}

	public function get_wc_shared_chk_url(){
		$settings = get_option(JGB_WSCC_SETTING_OPT_MAIN_KEY_NM);
		$wsc_url = '';
		if( isset( $settings[JGB_WSCC_SETTING_OKN_WC_SHARED_CHK_URL] ) ){
			$wsc_url = $settings[JGB_WSCC_SETTING_OKN_WC_SHARED_CHK_URL];
		}
		return apply_filters( 'JGBWCSC/settings/wc_shared_checkout_url', $wsc_url );
	}

	public function get_wc_ord_creatn_uri_res_sfx(){
		
		$ss = get_option(JGB_WSCC_SETTING_OPT_MAIN_KEY_NM,[]);
		$cortlc = JWSCC_ORDER_CREATION_RESOURCE_SFX;
		if( empty( $ss ) || !is_array( $ss ) ){
			return apply_filters( 'JGBWCSC/settings/order_creation_endpoint', $cortlc );
		}
		if( !isset( $ss[JGB_WSCC_SETTING_OKN_CORTLC] ) ){
			return apply_filters( 'JGBWCSC/settings/order_creation_endpoint', $cortlc );
		}

		return $ss[ JGB_WSCC_SETTING_OKN_CORTLC ];

	}
}
