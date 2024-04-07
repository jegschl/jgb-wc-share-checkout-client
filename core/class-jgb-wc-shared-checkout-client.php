<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;
if ( ! class_exists( 'Jgb_Wc_Shared_Checkout_Client' ) ) :

	/**
	 * Main Jgb_Wc_Shared_Checkout_Client Class.
	 *
	 * @package		JWSCC
	 * @subpackage	Classes/Jgb_Wc_Shared_Checkout_Client
	 * @since		1.0.0
	 * @author		Jorge Garrido
	 */
	final class Jgb_Wc_Shared_Checkout_Client {

		/**
		 * The real instance
		 *
		 * @access	private
		 * @since	1.0.0
		 * @var		object|Jgb_Wc_Shared_Checkout_Client
		 */
		private static $instance;

		/**
		 * JWSCC helpers object.
		 *
		 * @access	public
		 * @since	1.0.0
		 * @var		object|Jgb_Wc_Shared_Checkout_Client_Helpers
		 */
		public $helpers;

		/**
		 * JWSCC settings object.
		 *
		 * @access	public
		 * @since	1.0.0
		 * @var		object|Jgb_Wc_Shared_Checkout_Client_Settings
		 */
		public $settings;

		/**
		 * Throw error on object clone.
		 *
		 * Cloning instances of the class is forbidden.
		 *
		 * @access	public
		 * @since	1.0.0
		 * @return	void
		 */
		public function __clone() {
			_doing_it_wrong( __FUNCTION__, __( 'You are not allowed to clone this class.', 'jgb-wc-shared-checkout-client' ), '1.0.0' );
		}

		/**
		 * Disable unserializing of the class.
		 *
		 * @access	public
		 * @since	1.0.0
		 * @return	void
		 */
		public function __wakeup() {
			_doing_it_wrong( __FUNCTION__, __( 'You are not allowed to unserialize this class.', 'jgb-wc-shared-checkout-client' ), '1.0.0' );
		}

		/**
		 * Main Jgb_Wc_Shared_Checkout_Client Instance.
		 *
		 * Insures that only one instance of Jgb_Wc_Shared_Checkout_Client exists in memory at any one
		 * time. Also prevents needing to define globals all over the place.
		 *
		 * @access		public
		 * @since		1.0.0
		 * @static
		 * @return		object|Jgb_Wc_Shared_Checkout_Client	The one true Jgb_Wc_Shared_Checkout_Client
		 */
		public static function instance() {
			if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Jgb_Wc_Shared_Checkout_Client ) ) {
				self::$instance					= new Jgb_Wc_Shared_Checkout_Client;
				self::$instance->base_hooks();
				self::$instance->includes();
				self::$instance->helpers		= new Jgb_Wc_Shared_Checkout_Client_Helpers();
				self::$instance->settings		= new Jgb_Wc_Shared_Checkout_Client_Settings();

				//Fire the plugin logic
				new Jgb_Wc_Shared_Checkout_Client_Run();

				/**
				 * Fire a custom action to allow dependencies
				 * after the successful plugin setup
				 */
				do_action( 'JWSCC/plugin_loaded' );
			}

			return self::$instance;
		}

		/**
		 * Include required files.
		 *
		 * @access  private
		 * @since   1.0.0
		 * @return  void
		 */
		private function includes() {
			require_once JWSCC_PLUGIN_DIR . 'core/includes/classes/class-jgb-wc-shared-checkout-client-helpers.php';
			require_once JWSCC_PLUGIN_DIR . 'core/includes/classes/class-jgb-wc-shared-checkout-client-settings.php';

			require_once JWSCC_PLUGIN_DIR . 'core/includes/classes/class-jgb-wc-shared-checkout-client-run.php';
		}

		/**
		 * Add base hooks for the core functionality
		 *
		 * @access  private
		 * @since   1.0.0
		 * @return  void
		 */
		private function base_hooks() {
			add_action( 'plugins_loaded', array( self::$instance, 'load_textdomain' ) );
		}

		/**
		 * Loads the plugin language files.
		 *
		 * @access  public
		 * @since   1.0.0
		 * @return  void
		 */
		public function load_textdomain() {
			load_plugin_textdomain( 'jgb-wc-shared-checkout-client', FALSE, dirname( plugin_basename( JWSCC_PLUGIN_FILE ) ) . '/languages/' );
		}

	}

endif; // End if class_exists check.