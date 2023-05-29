<?php
/**
 * Funnel controller
 * 
 * @package scfunnels\Rest\Controllers
 */
namespace SCFunnelbuilder\Rest\Controllers;

use Error;
use WP_Error;
use WP_REST_Request;
use SCFunnelbuilder\ScFunnel_functions;
use SCFunnelbuilder\SCFunnelbuilder as SCFunnel;

class PercentageSplitController extends ScFunnel_REST_Controller
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
     * Register rest routes
     *
     * @since 1.0.0
     */
    public function register_routes()
    {
        register_rest_route($this->namespace, '/' . $this->rest_base . '/create_percentage_split_settings/', [
            [
                'methods' => \WP_REST_Server::EDITABLE,
                'callback' => [
                    $this,
                    'create_percentage_split_settings'
                ],
                'permission_callback' => [
                    $this,
                    'update_items_permissions_check'
                ] ,
            ],
        ]);
        register_rest_route($this->namespace, '/' . $this->rest_base . '/update_percentage_split_node/', [
            [
                'methods' => \WP_REST_Server::EDITABLE,
                'callback' => [
                    $this,
                    'update_percentage_split_node'
                ],
                'permission_callback' => [
                    $this,
                    'update_items_permissions_check'
                ] ,
            ],
        ]);
        register_rest_route($this->namespace, '/' . $this->rest_base . '/get_percentage_split_setting/', [
            [
                'methods' => \WP_REST_Server::EDITABLE,
                'callback' => [
                    $this,
                    'get_percentage_split_setting'
                ],
                'permission_callback' => [
                    $this,
                    'update_items_permissions_check'
                ] ,
            ],
        ]);
        register_rest_route($this->namespace, '/' . $this->rest_base . '/get_percentage_split_data/', [
            [
                'methods' => \WP_REST_Server::READABLE,
                'callback' => [
                    $this,
                    'get_percentage_split_data'
                ],
                'permission_callback' => [
                    $this,
                    'update_items_permissions_check'
                ] ,
            ],
        ]);
        register_rest_route($this->namespace, '/' . $this->rest_base . '/delete_percentage_connection/', array(
			array(
				'methods' => \WP_REST_Server::READABLE,
				'callback' => [
					$this,
					'delete_percentage_connection'
				],
				'permission_callback' => [
					$this,
					'update_items_permissions_check'
				] ,
			)
		));
    }    
    /**
     * create_percentage_split_settings
     *
     * @param  mixed $request
     * @return void
     */
    public function create_percentage_split_settings(WP_REST_Request $request){
        $funnel_id = $request['funnel_id'];
        $condition_data['variations'] = $request['variations'];
        $step_node_id = $request['step_id'];
        if($funnel_id == '' || $step_node_id == ''){
            return new WP_Error( 'error', 'not created', array( 'status' => 400 ) );
        }
        update_post_meta($funnel_id, $step_node_id, $condition_data);
        
        $response = array(
            'status' => true,
            'step_id' => $step_node_id,
        );
        return $this->prepare_item_for_response( $response, $request );
    }  
    /**
     * update_percentage_split_node
     *
     * @param  mixed $request
     * @return void
     */
    public function update_percentage_split_node(WP_REST_Request $request){
        $funnel_id = $request['funnel_id'];
        $source_node_id = $request['source_node_id'];
        $target_node_id = $request['target_node_id'];
        $variation_value = $request['variation_value'];
        if($funnel_id == '' || $source_node_id == '' || $variation_value == ''){
            return new WP_Error( 'error', 'not updated', array( 'status' => 400 ) );
        }
        $percentage_node = array(
            'source_node_id'=>$source_node_id,
            'target_node_id'=>$target_node_id,
            'variation_value'=>$variation_value,
        );
        if(!empty($percentage_node)){
            update_post_meta($funnel_id, $variation_value, $percentage_node);
            update_post_meta($funnel_id,'parent_'.$funnel_id.'_'.$target_node_id,$source_node_id);
            $response = array(
                'status' => true,
                'source_node_id'=> $source_node_id,
                'target_node_id'=> $target_node_id,
                'variation_value'=> $variation_value,
            );
        }else{
            return new WP_Error( 'error', 'not updated', array( 'status' => 400 ) );
        }
        
		return $this->prepare_item_for_response( $response, $request );
    }    
    /**
     * get_percentage_split_setting
     *
     * @param  mixed $request
     * @return void
     */
    public function get_percentage_split_setting(WP_REST_Request $request){
        $funnel_id = $request['funnel_id'];
        $condition_data = $request['conditions'];
        $step_node_id = $request['step_node_id'];
        if($funnel_id == '' || $step_node_id == ''){
            return new WP_Error( 'error', 'not created', array( 'status' => 400 ) );
        }
        update_post_meta($funnel_id, $step_node_id, $condition_data);
		$response = array(
			'status' => true,
			'step_id' => $step_node_id,
		);
		return $this->prepare_item_for_response( $response, $request );
    }

    public function delete_percentage_connection(WP_REST_Request $request){
        $funnel_id = $request['funnel_id'];
        $variation_value = $request['variation_value'];
        $step_node_id = $request['step_node_id'];
        if(isset($step_node_id)){
            $data = explode("-",$step_node_id);
            if($data[1] == 'conditional_split'  || $data[1] == 'upsell' || $data[1] == 'downsell'){
                delete_post_meta($funnel_id, $data[0].'_'.$variation_value);
                $response  = array(
                'success' => true,
                'message' => "Removed",
            );
            }else{
                delete_post_meta($funnel_id, $variation_value.'%');
                $response  = array(
                    'success' => true,
                    'message' => "Removed",
                );
            }
        }else{
            if(delete_post_meta($funnel_id, $variation_value)){
            $response  = array(
                'success' => true,
                'message' => "Removed",
            );
        }else{
            return new WP_Error( 'error', 'Not Removed', array( 'status' => 400 ) );
        }
        }
        
        return $this->prepare_item_for_response( $response, $request );
    }


    /**
     * get_percentage_split_data
     *
     * @param  mixed $request
     * @return void
     */
    public function get_percentage_split_data(WP_REST_Request $request){

        $funnel_id = $request['funnel_id'];
        $step_node_id = $request['step_node_id'];
        if($funnel_id == '' || $step_node_id == ''){
            return new WP_Error( 'error', 'funnel id or step node id not found.', array( 'status' => 400 ) );
        }
        $variations = get_post_meta($funnel_id, $step_node_id, true);
        $response = array(
            'status' => true,
            'step_node_id' => $step_node_id,
            'data' => $variations,
        );
        return $this->prepare_item_for_response( $response, $request );
    }

     /**
     * Prepare a single setting object for response.
     *
     * @param object $item Setting object.
     * @param WP_REST_Request $request Request object.
     * 
     * @return \WP_REST_Response $response Response data.
     * @since  1.0.0
     */
    public function prepare_item_for_response($item, $request)
    {
        $data = $this->add_additional_fields_to_object($item, $request);
        return rest_ensure_response($data);
    }

}