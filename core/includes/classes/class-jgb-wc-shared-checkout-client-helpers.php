<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

define( 'JWSCC_SHARED_CHECKOUT_RESOURCE_SFX','jgb-wscc-lpdartc'); //lpdartc ( Load Product Data And Redirect To Checkout ).
define( 'JWSCC_ORDER_CREATION_RESOURCE_SFX','jgb-wscc-woc'); //woocommerce order creation  ( Load Product Data And Redirect To Checkout ).
define( 'JWSCC_WCPRODS_UPDS_RESOURCE_SFX','jgb-wscc-wpus'); //Woocommerce Products UpdateS
define( 'JWSCC_WCORDR_MKNM_WSC_ORIGINAL_ID','wcs_original_id');
/**
 * Class Jgb_Wc_Shared_Checkout_Client_Helpers
 *
 * This class contains repetitive functions that
 * are used globally within the plugin.
 *
 * @package		JWSCC
 * @subpackage	Classes/Jgb_Wc_Shared_Checkout_Client_Helpers
 * @author		Jorge Garrido
 * @since		1.0.0
 */
class Jgb_Wc_Shared_Checkout_Client_Helpers{

	/**
	 * ######################
	 * ###
	 * #### CALLABLE FUNCTIONS
	 * ###
	 * ######################
	 */

	public static function get_shared_checkout_url(){
		$bu = self::get_shared_checkout_base_url();
		return trailingslashit( $bu ) . '/' . JWSCC_SHARED_CHECKOUT_RESOURCE_SFX;
	}

	public static function get_shared_checkout_base_url(){
		return apply_filters('JWSCC/shared_checkout_base_url','http://fpph.local');
	}

	public static function get_wc_cart_data_url_encoded(){
		$cart_items = WC()->cart->get_cart();

		$pcpd = [];
		// Verificar si el carro de compras no está vacío
		if (!empty($cart_items)) {
			// Iterar sobre los elementos del carro de compras
			foreach ($cart_items as $cart_item_key => $cart_item) {
				// Obtener información del producto
				$product = $cart_item['data'];
				$pcpd[] = [
					'sku' => $product->get_sku(),
					'qty' => $cart_item['quantity']
				];
			}
			$pcpd = apply_filters( 'JWSCC/prepare_wc_cart_data_before_encode', $pcpd );
			$pcpd_json_encoded = json_encode( $pcpd );
			$pcpd_base64_encoded = base64_encode( $pcpd_json_encoded);
			$pcpd_url_encoded = urlencode( $pcpd_base64_encoded );
			return $pcpd_url_encoded;
		}

		return null;
	}

	//add_action('template_redirect', 'redireccionar_desde_checkout');
	public static function redirect_from_checkout() {
		if (is_checkout() && !is_wc_endpoint_url('order-received')) {
			$url = self::get_shared_checkout_url();
			$separador = (parse_url($url, PHP_URL_QUERY) == NULL) ? '?' : '&';

			$params = [
				'pcd' => self::get_wc_cart_data_url_encoded()
			];

			// Construir la cadena de parámetros de consulta
			$query_params = http_build_query($params);

			// Agregar los parámetros de consulta a la URL
			$url_con_parametros = $url . $separador . $query_params;

			wp_redirect($url_con_parametros);
			exit;
		}
	}


	public function register_endpoints(){
		$wscc_woc_uri_res_sfx = Jgb_Wc_Shared_Checkout_Client::instance()->settings->get_wc_ord_creatn_uri_res_sfx();
		// $wsc_uri_res_sfx debería valer "jgb-wscc-lpdartc"
		if( ( $wscc_woc_uri_res_sfx !== false ) && is_string( $wscc_woc_uri_res_sfx ) ){
			add_rewrite_endpoint($wscc_woc_uri_res_sfx, EP_ROOT );
			
			flush_rewrite_rules();
		}

		
	}

	public function get_product_by_sku( $sku, $only_id = true ) {

		global $wpdb;
	
		$product_id = $wpdb->get_var( $wpdb->prepare( "SELECT post_id FROM $wpdb->postmeta WHERE meta_key='_sku' AND meta_value='%s' LIMIT 1", $sku ) );
	
		if ( $product_id ) {
			if( $only_id ){
				return $product_id;
			} else {
				return new WC_Product( $product_id );
			}
		}
	
		return null;
	}

	public function verify_original_order_id_exist( $oid ){
		$a = [
			'meta_key' => JWSCC_WCORDR_MKNM_WSC_ORIGINAL_ID,
			'meta_value' => $oid,
			'meta_compare' => '='
		];

		$orders = wc_get_orders( $a );

		if( count( $orders ) > 0 ){
			return $orders[0];
		}

		return null;
	}
	 
	// Recibe los datos para crear la Orden y redirigir al thankyoupage del cliente de checkout compartido.
	public function manage_wc_ordr_creatn_request(){

		global $wp_query;

		$qpnm = Jgb_Wc_Shared_Checkout_Client::instance()->settings->get_wc_ord_creatn_uri_res_sfx();
 
		// if this is not a request for WOC or a singular object then bail
		if ( $wp_query->query_vars['name'] != $qpnm )
			return;


		$od_uel_ecd = $_GET['od'];
		$od_base64_ecd = urldecode( $od_uel_ecd );
		$od_json_ecd = base64_decode( $od_base64_ecd );
		$od = json_decode( $od_json_ecd, true );

		if( count( $od ) > 0 ){

			$previows_order = $this->verify_original_order_id_exist( $od['wsc_order_id'] );

			if( !is_null( $previows_order ) ){
				$url = $previows_order->get_checkout_order_received_url();
				wp_redirect( $url, 302 );
				exit;
			}

			$address = $od['address'];

			$items = $od['items'];

			// Se crea la orden con los datos.
			$order = wc_create_order();

			foreach( $items as $itm ){
				$prod = $this->get_product_by_sku( $itm['sku'], false );
				$order->add_product( $prod,  intval( $itm['qty'] ) );
				
			}
			$order->calculate_totals();

			$order->set_address( $address, 'billing' );
			$order->set_address( $address, 'shipping' );

			$order->set_payment_method( $od['payment_method']['payment_method'] );
			$order->set_payment_method_title( $od['payment_method']['title'] );
			$order->set_transaction_id( $od['payment_method']['transaction_id'] );

			$order->update_meta_data( JWSCC_WCORDR_MKNM_WSC_ORIGINAL_ID, $od['wsc_order_id'] );

			$order->save();

			$wc_ckt_url = $order->get_checkout_order_received_url();

			wp_redirect( $wc_ckt_url, 302 );
		}
		exit;
	}

	public function manage_wc_prods_updates_request(){

		global $wp_query;

		// if this is not a request for WPUS or a singular object then bail
		if ( $wp_query->query_vars['name'] != JWSCC_WCPRODS_UPDS_RESOURCE_SFX )
			return;

		/*
			Operation Data GET query parameter definition 
			
			1.- request-operation: 
				'wc-admin': Update from WC Product admin.
				'wc-import': Update from WC product update tool.
				'wc-sell': Update from WC sell.
			
			2.- products-data: Array width 
				SKU (mandatory), 
				Product name, 
				product regular price,
				Product stock

			3.- send-response (1 || 0)
				Send response in json format.

			
		*/

		$pd_uel_ecd = $_GET['operation-data'];
		$pd_base64_ecd = urldecode( $pd_uel_ecd );
		$pd_json_ecd = base64_decode( $pd_base64_ecd );
		$pd = json_decode( $pd_json_ecd, true );

		if( count( $pd['products-data'] ) > 0 ){
			$log = [];
			foreach( $pd as $p ){
				$log[] = $this->update_product( $p );
			}
		}

		if( isset( $_GET['send-response'] ) && $_GET['send-response'] == '1' ){
			$r = [
				'requestOperation' => $pd['resuqest-operation'],
				'logs' => $log,
				'sendResponse' => $pd['send-response']
			];
		}

		header('Content-type: application/json');
		echo json_encode($r);
		exit;
	}

	private function update_product( $data ){
		$r = [
			'error' => false
		];

		if( !isset( $data['sku'] ) ){
			$r['error'] = true;
			$r['err_details'] = [
				'code' => 1,
				'msg' => 'sku not defined'
			];
			return $r;
		}

		$l = [];
		$fu = [];

		foreach( $data['fields'] as $f ){
			$p = $this->get_product_by_sku( $data['sku'], false );
			if( !is_null( $p ) ){
				// actualizar información del producto.

				$mandatory_update = false; 

				if( isset( $f['name'] ) ){
					$l['old_v'] = $p->get_name();
					$p->set_name( $f['name'] );
					$l['new_v'] = $f['name'];
					$fu['name'] = $l;
					$mandatory_update = true;
				}

				if( isset( $f['price'] ) ){
					$l['old_v'] = $p->get_name();
					$p->set_regular_price( $f['price'] );
					$l['new_v'] = $f['price'];
					$fu['price'] = $l;
					$mandatory_update = true;
				}

				if( isset( $f['stock'] ) ){
					$l['old_v'] = $p->get_name();
					$p->set_stock_quantity( $f['stock'] );
					$l['new_v'] = $f['stock'];
					$fu['stock'] = $l;
					$mandatory_update = true;
				}

				if( $mandatory_update ){
					$p->save();
					
				}

				$r['sku'] = $data['sku'];
				$r['field_updates'] = $fu;
				
			} else {
				$r['error']  =  true;
				$r['err_details'] = [
					'code' => 2,
					'msg' => "product with SKU {$data['sku']} not exist"
				];
			}
		}

	}
}
