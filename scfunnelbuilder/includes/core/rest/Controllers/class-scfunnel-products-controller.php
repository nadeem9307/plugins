<?php
/**
 * Product controller
 * 
 * @package SCFunnelbuilder\Rest\Controllers
 */
namespace SCFunnelbuilder\Rest\Controllers;

use WP_Error;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;
use SCFunnelbuilder\ScFunnel_functions;
class ScProductsController extends ScFunnel_REST_Controller
{

	/**
	 * Endpoint namespace.
	 *
	 * @var string
	 */
	protected $namespace = 'scfunnels';

	/**
	 * Route base.
	 *
	 * @var string
	 */
	protected $rest_base = 'v1';

	/**
	 * Check if user has valid permission
	 *
	 * @param $request
	 * 
	 * @return bool|WP_Error
	 * @since  1.0.0
	 */
	public function update_items_permissions_check($request)
	{
		return true;
	}

	/**
	 * Makes sure the current user has access to READ the settings APIs.
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 * 
	 * @return WP_Error|boolean
	 * @since  3.0.0
	 */
	public function get_items_permissions_check($request)
	{

		return true;
	}


	/**
	 * Register rest routes
	 *
	 * @since 1.0.0
	 */
	public function register_routes()
	{
		register_rest_route(
			$this->namespace, '/' . $this->rest_base . '/get_products'. '/(?P<step_id>\d+)' , array(
				array(
					'methods'               => WP_REST_Server::READABLE,
					'callback'              => array( $this, 'get_products' ),
					'permission_callback'   => array( $this, 'get_items_permissions_check' ),
				),
			)
		);

		register_rest_route($this->namespace,'/' . $this->rest_base . '/getProducts/', [
			[
				'methods' => \WP_REST_Server::READABLE,
				'callback' => [
					$this,
					'get_sc_products'
				],
				'permission_callback' => [
					$this,
					'get_items_permissions_check'
				],
			],
		]);
		register_rest_route(
			$this->namespace, '/' . $this->rest_base . '/get_product_info/', array(
				array(
					'methods'               => WP_REST_Server::READABLE,
					'callback'              => array( $this, 'get_product_pricing' ),
					'permission_callback'   => array( $this, 'get_items_permissions_check' ),
				),
			)
		);
		register_rest_route(
			$this->namespace, '/' . $this->rest_base . '/get_conditions_confirmation/', array(
				array(
					'methods'               => WP_REST_Server::READABLE,
					'callback'              => array( $this, 'get_sc_product_type' ),
					'permission_callback'   => array( $this, 'get_items_permissions_check' ),
				),
			)
		);
	}

	/**
	 * Get all Products.
	 *
	 * @param string $request Data.
	 * 
	 * @return array|WP_Error
	 */
	public function get_sc_products($request)
	{
		$data = [];
		if (in_array('studiocart/studiocart.php', SC_FUNNEL_ACTIVE_PLUGINS) || in_array('studiocart-pro/studiocart.php', SC_FUNNEL_ACTIVE_PLUGINS)) {
			$all_ids = get_posts([
				'post_type' => 'sc_product',
				'numberposts' => -1,
				'post_status' => 'publish',
				'fields' => 'ids',
			]);
			foreach ($all_ids as $id) {
				$product = get_post($id);
				if( $product ){
						$value = $id;
						$label = get_the_title($id);
						$result = [
							'value' => $value,
							'label' => $label,
						];
						$data[] = $result;
					}
				}
		}
		return $data;
	}


	/**
	 * Prepare a single setting object for response.
	 *
	 * @since 3.0.0
	 */
	public function get_products($request) {
        $cache_key 		= 'scfunnels_checkout_products_'.$request['step_id'].'_'. SCFUNNELBUILDER_VERSION;
		$data 			= get_transient($cache_key);
      
       	if ( $data ) {
            $response = $data;
		}else{
            $step_id 	=  $request['step_id'];
            $funnel_id  = ScFunnel_functions::get_funnel_id_from_step($step_id);
            $type 		= get_post_meta($funnel_id, '_scfunnel_funnel_type', true);
            $response = [];
        }
		return $this->prepare_item_for_response( $response, $request );
	}

	/**
	 * Prepare a single setting object for response.
	 *
	 * @param object          $item Setting object.
	 * @param WP_REST_Request $request Request object.
	 * 
	 * @return WP_REST_Response $response Response data.
	 * @since  3.0.0
	 */
	public function prepare_item_for_response( $item, $request ) {
		$data     = $this->add_additional_fields_to_object( $item, $request );
		$response = rest_ensure_response( $data );
		return $response;
	}
	
	/**
	 * get_product_pricing
	 *
	 * @param  mixed $request
	 * @return void
	 */
	public function get_product_pricing($request){
		$product_id = $request['productId'];
		if( !isset( $request['productId'] ) ){
			return new WP_Error( 'error', 'Product Id Does not exist', array( 'status' => 400 ) );
        }
		$product_id = intval($product_id);
        $product_plan_data = get_post_meta($product_id, '_sc_pay_options', true);
		$data = [];
		return rest_ensure_response($product_plan_data);
	}	
	/**
	 * get_sc_product_type
	 *
	 * @param  mixed $request
	 * @return void
	 */
	public function get_sc_product_type($request) {

        if(!isset($request['productId'])) {
            return;
        }
		$options[] = array('value' => 'newany','lable'=>__('Any','scfunnelbuilder'));
        $id = intval($_GET['productId']);
        if($request['product_type'] == "bump"){
			if($ob_id = get_post_meta($id, '_sc_ob_product', true)) {
				$ob_id = intval($ob_id);
				if(!get_post_meta($id, '_sc_ob_replace', true)){ // don't add as an option if the bump replaces the main product
					$main_bump['value'] =  __('main','scfunnelbuilder');
					$main_bump['label'] =  __('Main Bump','scfunnelbuilder') . ' ('.get_the_title($ob_id).')';
					$options[] = $main_bump;
				}
			}
			
			if($bumps = get_post_meta($id, '_sc_order_bump_options', true)) {
				foreach($bumps as $k=>$bump) {
					if(isset($bump['ob_product'])){
						$ob_id = intval($bump['ob_product']);
						$result['value'] = $k+1;
						$result['label'] = sprintf(__("Add'l Bump %d (%s)","scfunnelbuilder"), $k+1, get_the_title($ob_id));
						$options[] = $result;
					}
				}
			}
		}elseif($request['product_type'] == "upsell"){
			$options = $this->get_offer();
		}elseif($request['product_type'] == "downsell"){
			$options = $this->get_offer();
		}else{
			$product_plan_data = get_post_meta($id, '_sc_pay_options', true);
			foreach ( $product_plan_data as $val ) {
				$result = [
					'value' => $val['option_id'],
					'label' => $val['option_id'],
				];
				$options[] = $result;
            }
		}
        
		return $options;
    }
	private function get_offer(){
		$options = array(
			array(
				'value' => 'newany',
				'label' =>  __('Any','scfunnelbuilder')
			),
			array(
				'value' => 1,
				'label' =>  __('Offer 1','scfunnelbuilder')
			),
			array(
				'value' => 2,
				'label' =>  __('Offer 2','scfunnelbuilder')
			),
			array(
				'value' => 3,
				'label' =>  __('Offer 3','scfunnelbuilder')
			),
			array(
				'value' => 4,
				'label' =>  __('Offer 4','scfunnelbuilder')
			),
			array(
				'value' => 5,
				'label' =>  __('Offer 5','scfunnelbuilder')
			)
		);
		return $options;
	}
}
