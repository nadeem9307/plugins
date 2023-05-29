<?php
/**
 * Funnel controller
 * 
 * @package scfunnels\Rest\Controllers
 */
namespace SCFunnelbuilder\Rest\Controllers;

use WP_Error;
use WP_REST_Request;
use SCFunnelbuilder\ScFunnel_functions;
use SCFunnelbuilder\SCFunnelbuilder as SCFunnel;
class ScFunnelController extends ScFunnel_REST_Controller
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
        register_rest_route($this->namespace, '/' . $this->rest_base . '/saveFunnel/', [
            [
                'methods' => \WP_REST_Server::EDITABLE,
                'callback' => [
                    $this,
                    'save_funnel_data'
                ],
                 'permission_callback' => [
                     $this,
                     'update_items_permissions_check'
                 ] ,
            ],
        ]);

        register_rest_route($this->namespace, '/' . $this->rest_base . '/saveConditionalNode/', [
            [
                'methods' => \WP_REST_Server::EDITABLE,
                'callback' => [
                    $this,
                    'save_conditional_node'
                ],
                 'permission_callback' => [
                     $this,
                     'update_items_permissions_check'
                 ] ,
            ],
        ]);
        register_rest_route($this->namespace, '/' . $this->rest_base . '/get_conditional_node/', [
            [
                'methods' => \WP_REST_Server::READABLE,
                'callback' => [
                    $this,
                    'get_conditional_node'
                ],
                 'permission_callback' => [
                     $this,
                     'get_items_permissions_check'
                 ] ,
            ],
        ]);
        register_rest_route($this->namespace, '/' . $this->rest_base . '/getFunnel/', [
            [
                'methods' => \WP_REST_Server::READABLE,
                'callback' => [
                    $this,
                    'get_funnel_data'
                ],
                'permission_callback' => [
                    $this,
                    'update_items_permissions_check'
                ],
            ],
        ]);

        register_rest_route($this->namespace, '/' . $this->rest_base . '/getStepType/', [
            [
                'methods' => \WP_REST_Server::READABLE,
                'callback' => [
                    $this,
                    'get_step_type'
                ],
                 'permission_callback' => [
                     $this,
                     'update_items_permissions_check'
                 ] ,
            ],
        ]);

        register_rest_route($this->namespace, '/' . $this->rest_base . '/getFunnelInfo/', [
            [
                'methods' => \WP_REST_Server::READABLE,
                'callback' => [
                    $this,
                    'get_funnel_info'
                ],
                 'permission_callback' => [
                     $this,
                     'update_items_permissions_check'
                 ],
            ],
        ]);


        register_rest_route($this->namespace, '/' . $this->rest_base . '/getallfunnels/', array(
			array(
				'methods' => \WP_REST_Server::READABLE,
				'callback' => [
					$this,
					'get_all_funnels'
				],
				'permission_callback' => [
					$this,
					'update_items_permissions_check'
				] ,
			),
		));
        
        register_rest_route($this->namespace, '/' . $this->rest_base . '/create_step/', array(
			'args' => array(
				'funnel_id' => array(
					'description' => __('Funnel ID.', 'scfunnelbuilder'),
					'type' => 'string',
				)
			),
			array(
				'methods' => \WP_REST_Server::EDITABLE,
				'callback' => [
					$this,
					'create_step'
				],
				'permission_callback' => [
					$this,
					'update_items_permissions_check'
				] ,
			)
		));
        register_rest_route($this->namespace, '/' . $this->rest_base . '/delete_step/', array(
			array(
				'methods' => \WP_REST_Server::READABLE,
				'callback' => [
					$this,
					'delete_step'
				],
				'permission_callback' => [
					$this,
					'update_items_permissions_check'
				] ,
			)
		));

		register_rest_route($this->namespace, '/' . $this->rest_base . '/update_steps/', array(
			array(
				'methods' => \WP_REST_Server::EDITABLE,
				'callback' => [
					$this,
					'update_step_meta'
				],
				'permission_callback' => [
					$this,
					'update_items_permissions_check'
				] ,
			)
		));

        register_rest_route($this->namespace, '/' . $this->rest_base . '/get_all_pages/', array(
				array(
					'methods'               => \WP_REST_Server::READABLE,
					'callback'              => [
                        $this,
                        'get_pages'
                    ],
					'permission_callback'   => [
                        $this,
                        'update_items_permissions_check'
                    ],
				),
			)
		);

        register_rest_route($this->namespace, '/' . $this->rest_base . '/update_funnel/', [
            [
                'methods' => \WP_REST_Server::EDITABLE,
                'callback' => [
                    $this,
                    'update_funnel_title'
                ],
                 'permission_callback' => [
                     $this,
                     'update_items_permissions_check'
                 ] ,
            ],
        ]);

        register_rest_route($this->namespace, '/' . $this->rest_base . '/update_conditional_node/', [
            [
                'methods' => \WP_REST_Server::EDITABLE,
                'callback' => [
                    $this,
                    'update_conditional_node'
                ],
                'permission_callback' => [
                    $this,
                    'update_items_permissions_check'
                ] ,
            ],
        ]);


    }

    /**
     * Check if funnel data exists or not
     **/
    public function get_all_funnels()
    {
        $args = array(
            'post_type' => 'scfunnel_steps',
            'numberposts' => -1
        );
        $funnels = get_posts($args);

        if ($funnels) {
            if (count($funnels) > 0) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get conditional node
     *
     * @param WP_REST_Request $request request.
     * 
     * @return array|WP_Error
     */
    public function get_conditional_node(WP_REST_Request $request)
    {
        $funnel_id       = $request['funnel_id'];
        $step_node_id    = $request['step_node_id'];
        $condition       = get_post_meta($funnel_id, $step_node_id, true);
        if($funnel_id == '' || $step_node_id == ''){
            return new WP_Error( 'error', 'funnel id or step node id not found', array( 'status' => 400 ) );
        }
       
        if ($condition) {
            $response = array(
                'status' => 'success',
                'data' => $condition,
                'step_node_id' => $step_node_id
            );
           
        } else {
            return new WP_Error( 'error', 'No data found', array( 'status' => 400 ) );
           
        }
        return $this->prepare_item_for_response($response, $request);
    }

    /**
     * Save conditional node
     *
     * @param string $request request.
     * 
     * @return array|WP_Error
     */
    public function save_conditional_node(WP_REST_Request $request)
    {
        $funnel_id = $request['funnel_id'];
        $condition_data = $request['conditions'];
        $condition_type = $request['condition_type'];
        $step_node_id = $request['step_node_id'];
        if($funnel_id == '' || $step_node_id == ''){
            return new WP_Error( 'error', 'not created', array( 'status' => 400 ) );
        }
        if(!empty($condition_data)){
            $condition_data = array(
                'name' => '',
                'condition_type'=> $condition_type,
                'conditions' => $condition_data,
                'confirmation_type' => 'page',
            );
        }
        $conditional = array(array('label'=>'Yes','value'=>'YES'),array('label'=>'No','value'=>'NO'));
        update_post_meta($funnel_id, $step_node_id, $condition_data);
        update_post_meta($funnel_id, 'conditional_'.$step_node_id,$conditional);
		$response = array(
			'status' => true,
			'step_id' => $step_node_id,
		);
		return $this->prepare_item_for_response( $response, $request );
    }

    /**
     * Get step_type.
     *
     * @param string $request request.
     * 
     * @return array|WP_Error
     */
    public function get_step_type($request)
    {
        $step_type = '';
        $step_id = $request['step_id'];
        $step_type = get_post_meta($step_id, '_step_type', true);
        return $step_type;
    }

	/**
	 * Get the funnel title and link
	 *
	 * @param $request
     * 
	 * @return \WP_REST_Response
	 */
    public function get_funnel_info(WP_REST_Request $request )
    {
        $funnel_id = $request['funnel_id'];
        $title 		= html_entity_decode(get_the_title($funnel_id));
        $steps = get_post_meta( $funnel_id, '_steps_order', true );
		$response['success'] = false;
        if ($steps) {
            if ( isset($steps[0]) && $steps[0]['id'] ) {
				$response['link'] = get_post_permalink($steps[0]['id']);
                $response['title']= $title;
				$response['success'] = true;
            }
        }
		return $this->prepare_item_for_response($response, $request);
    }


	/**
	 * Get funnel data.
	 *
	 * @param $request
     * 
	 * @return \WP_REST_Response
	 * @throws \Exception
	 */
    public function get_funnel_data(WP_REST_Request $request) {
        
        $funnel_id 			= $request['funnel_id'];
        if (empty($funnel_id)) {
            return new WP_Error( 'error', 'No funnel id found', array( 'status' => 400 ) );
        }
        $funnel_data 		= get_post_meta( $funnel_id, 'funnel_data', true );
        $funnel_identifier 	= get_post_meta( $funnel_id, 'funnel_identifier', true );
        $_steps_order 		= get_post_meta( $funnel_id, '_steps_order', true );
        $status				= get_post_status( $funnel_id );
        $steps_order 		= array();
        $response 			= array();
        $is_order_bump = false;

        $title 		= html_entity_decode(get_the_title($funnel_id));
		$response['success'] = false;
        $link = '';
        $first_step_id = ScFunnel_functions::get_first_step( $funnel_id );
        $link = get_post_permalink($funnel_id);
        $link = apply_filters( 'scfunnels/modify_funnel_view_link', $link, $first_step_id, $funnel_id );
        $response['title']= $title;
        $response['link'] = $response['link'];
        $response['success'] = true;
        
        if ($_steps_order) {
            foreach ($_steps_order as $step) {
                $steps_order[] = $step;
            }
        }

        $funnel_data = $this->get_formatted_funnel_data( $funnel_data ,$funnel_id );
        for ($i = 0; $i < count($steps_order); $i++) {
        	$_temp_step 			= $steps_order[$i];
        	$_temp_step['visit'] 	= 0;
        	$_temp_step['conversion'] 	= 0;
			$_temp_step['name'] 	= get_the_title($steps_order[$i]['id']);
            $_step_type 			= get_post_meta( $steps_order[$i]['id'], '_step_type', true );
          
            $steps_order[$i] = apply_filters( 'scfunnels/step_data', $_temp_step, $steps_order[$i]['id'] );
        }
        
        if ($funnel_data) {
            $response = array(
                'status' 			=> 'success',
                'funnel_data' 		=> $funnel_data,
                'funnel_identifier' => $funnel_identifier,
                'steps_order' 		=> $steps_order,
				'funnel_status'		=> $status,
                'title'             => $title,
                'link'              => $link,
                'is_ob'             => $is_order_bump
            );

            $response = apply_filters( 'scfunnels/update_funnel_data_response', $response );
        } else {
            if( $title && $status ){
                $response = array(
                    'status'            => 'scratch-funnel',
                    'title'             => $title,
                    'funnel_status'		=> $status,
                );
            } else {
                return new WP_Error( 'error', 'something went wrong!', array( 'status' => 400 ) );
            }
        }
        
        return $this->prepare_item_for_response($response, $request);
    }


	/**
	 * Get formatted funnel data
	 *
	 * @param $funnel_data
     * 
	 * @return mixed
	 *
	 * @since 2.0.5
	 */
    private function get_formatted_funnel_data( $funnel_data,$fnl_id = 0) {
        // dd( $funnel_data);
		if( isset( $funnel_data) ) {
			foreach ( $funnel_data as $key => $data ) {
				$step_data 		= $data['data'];
                if(isset($step_data['sourceVisible'])){
                    $step_type		= $step_data['slug_type'];
                    if('conditional_split' !== $step_type && 'percentage_split' !== $step_type && 'upsell' !== $step_type && 'downsell' !== $step_type) {
                        $step_id 		= $step_data['step_id'] ?? 0;
                        $funnel_id      = ScFunnel_functions::get_funnel_id_from_step($step_id);
                        $edit_post_link = admin_url().'post.php?post='.$step_id.'&action=edit';
                        $view_link		= get_the_permalink( $step_id );
                        
                        $title			= get_the_title( $step_id );
                        $slug			=  get_post_field( 'post_name', $step_id );
                        $funnel_data[$key]['data']['step_edit_link'] = $edit_post_link;
                        $funnel_data[$key]['data']['step_id'] = $step_id;
                        $funnel_data[$key]['data']['step_slug'] = $slug;
                        $funnel_data[$key]['data']['step_view_link'] =rtrim( $view_link, '/' );
                        $funnel_data[$key]['data']['step_title'] =$title;
                    }
                    if("percentage_split" == $step_type){
                        $funnel_data[$key]['data']['step_id'] = $data['id'];
                        $percentage_array = get_post_meta($fnl_id,$data['id'],true);
                        $percesn= array();
                        if(!empty($percentage_array)){
                            foreach ($percentage_array['variations'] as $k => $value) {
                                $disable = get_post_meta($fnl_id,$value['value'],true) ? 'true': 'false';
                                $percesn[$k]['label'] = $value['label'];
                                $percesn[$k]['value'] = $value['value'];
                                $percesn[$k]['disabled'] = $disable;
                            }
                        }else{
                            $percesn= array(array('label'=>'Yes','value'=>'YES', 'disabled'=> 'false'),array('label'=>'No','value'=>'NO','disabled'=> 'false'));
                        }
                        $funnel_data[$key]['data']['percentage_split'] = $percesn;
                    }
                    if("conditional_split" == $step_type){
                        $funnel_data[$key]['data']['step_id'] = $data['id'];
                        $conditional = get_post_meta($fnl_id, 'conditional_'.$data['id'],true);
                        
                        if(!empty($conditional)){
                            foreach ($conditional as $ke => $value) {
                                $dis = get_post_meta($fnl_id,$data['id'].'_'.$value['value'],true) ? 'true': 'false';
                                $conditional[$ke]['disabled'] = $dis;
                            }
                        }else{
                            $conditional = array(array('label'=>'Yes','value'=>'YES', 'disabled'=> 'false'),array('label'=>'No','value'=>'NO','disabled'=> 'false')); 
                        }
                        $funnel_data[$key]['data']['conditional_split'] = $conditional;
                    }
                     if("upsell" == $step_type || "downsell" == $step_type){
                        // $funnel_data[$key]['data']['step_edit_link'] = $edit_post_link;
                        $funnel_data[$key]['data']['step_id'] = $step_data['step_id'];
                        $upsell = get_post_meta($fnl_id, $step_type.'_'.$data['id'],true);
                        $always = false;
                        $yes_no = false;
                        if(!empty($upsell)){
                            foreach ($upsell as $j => $value) {
                                if($j == 0 || $j == 1){
                                    $db_calue = get_post_meta($fnl_id, $data['id'].'_'.$value['value'],true);
                                    if($db_calue){
                                        $always = true;
                                    }else{
                                        get_post_meta($fnl_id, $data['id'].'_ALWAYS',true) ? $yes_no = true : '';
                                    }
                                }
                                $disable = get_post_meta($fnl_id,$data['id'].'_'.$value['value'],true) ? 'true': 'false';
                                $upsell[$j]['disabled'] = $disable;
                                if($j == 2 && $always == true){
                                    $upsell[$j]['disabled'] = 'true';
                                }
                                if($j != 2 && $yes_no == true){
                                    $upsell[$j]['disabled'] = 'true';
                                }
                            }
                        }else{
                         $upsell = array(array('label'=>'Yes','value'=>'YES', 'disabled'=> 'false'),array('label'=>'No','value'=>'NO','disabled'=> 'false'),array('label'=>'Always','value'=>'ALWAYS','disabled'=> 'false'));
                        }
                        $funnel_data[$key]['data']['upDownsell'] = $upsell;
                    }
                }
			}
		}
		return $funnel_data;
	}

    /**
     * Save funnel data.
     *
     * @param string $request request.
     * 
     * @return array|WP_Error
     */
    public function save_funnel_data(WP_REST_Request $request)
    {
        $funnel_id              	= $request['funnel_id'];
        if($funnel_id == ''){
            return new WP_Error( 'error', 'No funnel id found ', array( 'status' => 400 ) );
        }
        $funnel_enabled             = $request['funnel_enabled'];
        $funnel_data            	= $request['funnel_data'];
        $funnel_identifier      	= $request['funnel_identifier'];
        $should_update_steps_order	= $request['should_update_steps_order'];
        $should_update_steps		= $request['should_update_steps'];
        // $funnel_data            	= array();
        $_steps                 	= $funnel_data;
		$response                 	= array(
			'success'	=> true,
			'link'		=> home_url()
		);
        
        $funnel_data = apply_filters('scfunnels/modify_funnel_data', $_steps);
        update_post_meta($funnel_id, 'funnel_data', $funnel_data);
        update_post_meta($funnel_id, 'funnel_identifier', $funnel_identifier);
        update_post_meta($funnel_id, 'funnel_enabled', $funnel_enabled);

        if( $should_update_steps ) {
			$steps = $this->get_steps( $funnel_data );
			update_post_meta( $funnel_id, '_steps', $steps );
		}
        $_steps_order 	= $this->get_steps_order( $funnel_data );
        $key = array_search('checkout', array_column($_steps_order, 'step_type'));
        if( false !== $key ){
            $funnel_type = get_post_meta( $funnel_id, '_scfunnel_funnel_type', true );
            if( 'sc' === $funnel_type ){
                if( ScFunnel_functions::is_sc_active() ){
                    update_post_meta( $funnel_id, '_scfunnel_funnel_type', 'sc' );
                }
            }
        }
        
		if( $should_update_steps_order ) {
			$steps_order 	= array();
			foreach ($_steps_order as $_step){
				if(count($_step)) {
					$steps_order[] = $_step;
				}
			}
			if(count($steps_order)) {
				update_post_meta( $funnel_id, '_steps_order', $steps_order );
				
			} else {
				delete_post_meta( $funnel_id, '_steps_order' );
			}
		}

		$steps = get_post_meta( $funnel_id, '_steps_order', true );
        $funnel_data 		= get_post_meta( $funnel_id, 'funnel_data', true );
        $funnel_data = $this->get_formatted_funnel_data( $funnel_data,$funnel_id );
		$response['link'] = esc_url( get_post_permalink( ScFunnel_functions::get_first_step( $funnel_id ) ));
        $response['step_id'] = ScFunnel_functions::get_first_step( $funnel_id );
        $response['funnel_data'] = $funnel_data;
        $response['funnel_id'] = $funnel_id;
		$response['success'] = true;
		$response['funnel_type'] = get_post_meta( $funnel_id, '_scfunnel_funnel_type', true );
        $response = apply_filters( 'scfunnels/update_funnel_link', $response );
        do_action( 'scfunnels/after_save_funnel_data', $funnel_id );        
        return rest_ensure_response($response);
    }


	/**
	 * Get steps
	 *
	 * @param $funnel_flow_data
     * 
	 * @return array
	 *
	 * @since 2.0.5
	 */
	private function get_steps( $funnel_flow_data ) {
		$steps 			= array();
		if( isset( $funnel_flow_data ) ) {
			foreach ( $funnel_flow_data as $key => $data ) {
                if(isset($data['data']['sourceVisible'])){
                    $step_data 	= $data['data'];
                        $step_id 	= isset($step_data['step_id']) ? $step_data['step_id'] : 0;
                        $step_type 	= $step_data['slug_type'];
                        $step_name	= $step_data['step_title'];//sanitize_text_field(get_the_title($step_data['step_id']));
                        $steps[]	= array(
                            'id'		=> $step_id,
                            'step_type'	=> $step_type,
                            'name'		=> $step_name,
                            'step_node_id'=> $data['id'],
                        );
                   
                }
			}
		}
		return $steps;
	}


	/**
	 * Get steps order
	 *
	 * @param $funnel_flow_data
     * 
	 * @return array
	 *
	 * @since 2.0.5
	 */
    private function get_steps_order( $funnel_flow_data ) {
		$nodes			= array();
		$step_order		= array();
		$first_node_id	= '';
		$start_node 	= array();
		if( isset( $funnel_flow_data ) ) {

			/**
			 * If has only one step, that only step will be the first step, no conditions should be checked.
			 * just return the step order
			 */
			if( 1 === count( $funnel_flow_data ) ) {
                
				$node_id 	= array_keys($funnel_flow_data)[0];
				$data 		= $funnel_flow_data[$node_id];
				$step_data 	= $data['data'];
                if(isset($step_data['sourceVisible'])){
                    $step_id 	= isset($step_data['step_id']) ? $step_data['step_id']: 0;
                    $step_type 	= $step_data['slug_type'];
                    $step_order[] 	= array(
                        'id'		=> $step_id,
                        'step_type'	=> $step_type,
                        'name'		=> sanitize_text_field( get_the_title( $step_id ) ),
                        'step_node_id'	=> $data['id']
                    );
                }
				return $step_order;

			}
           
			/**
			 * First we will find the first node (the node which has only output connection but no input connection will be considered as first node) and the list of nodes array which has the
			 * step information includes output connection and input connection and it will be stored on $nodes
			 */
			foreach ( $funnel_flow_data as $key => $data ) {
                $step_data 	=  $data['data'];
                
                if(isset($step_data['sourceVisible'])){
                    $step_id = isset($step_data['step_id']) ? $step_data['step_id'] : 0;
                    $step_type 	= $step_data['slug_type'];
                    $step_id 	= $step_type !== 'conditional_split' ?  $step_id : 0;
                    if(isset( $data['computedPosition']['x'] ) &&  $data['handleBounds']['source'] ) {
                        
                        if('conditional_split' === $step_type) {
                            continue;
                        }
                        if('percentage_split' === $step_type) {
                            continue;
                        }
                        $step_order[] = array(
                            'id' 		=> $step_id,
                            'step_type' => $step_type,
                            'name' 		=> sanitize_text_field(get_the_title($step_id)),
                            'step_node_id'	=> $data['id']
                        );
                    }
                }
			}
			$step_order = $this->array_insert($step_order, $start_node, 0);
		}
		return $step_order;
	}


	/**
	 * Array insert element on position
	 *
	 * @param $original
	 * @param $inserted
	 * @param int $position
     * 
	 * @return mixed
	 */
	private function array_insert(&$original, $inserted, $position) {
		array_splice($original, $position, 0, array($inserted));
		return $original;
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

      /**
     * Get create_step
     * 
     * @param WP_REST_Request $request
     * 
     * @return Array
     * 
     * @since 1.0.0
     */
    public function create_step(WP_REST_Request $request)
    {
        $funnel_id = $request['funnel_id'];
        $step_type = $request['slug_type'];
        $step_node_id = $request['step_node_id'];
        $step_name = isset($request['step_name']) ? $request['step_name']: $step_type;

        if($funnel_id == '' || $step_type == ''){
            return new WP_Error( 'error', 'funnel id not found', array( 'status' => 400 ) );
        }
        $design_type = 'product';
        if($request['design_type'] == "default"){
            $step_product_id = $request['productId'];
            $design_type     = 'product';
        }elseif($request['design_type'] == "use_existing"){
            $step_product_id = $request['pageId'];
            $step_name = get_post_field( 'post_name', $step_product_id);
            $design_type     = 'page';
        }else{
            $step_product_id = $request['productId'];
            $design_type     = 'product';
        }
        $scfunnel  = new SCFunnel();
        $funnel = $scfunnel->funnel_store;
        $step = $scfunnel->step_store;

        $step_id = $step->create_step( $funnel_id, $step_name, $step_type );
        $step->set_id($step_id);
        if ($step_id && ! is_wp_error($step_id)) {
            $funnel->set_id($funnel_id);
            $step_edit_link = get_edit_post_link($step_id);
            if($step_edit_link == NULL){
                $step_edit_link = admin_url().'post.php?post='.$step_id.'&action=edit';
            }
            if($step_type == 'upsell' || $step_type == 'downsell'){
                $step_product_id = $request['step_product_id'];
                $product_pricing_plan = $request['product_pricing_plan'] ?? '';
                if($product_pricing_plan == '' ){
                    return new WP_Error( 'error', 'product pricing plan not found', array( 'status' => 400 ) );
                }
                $design_type = 'product_page_price';
                update_post_meta($step_id,'product_pricing_plan',$product_pricing_plan);
                $conditional = array(array('label'=>'Yes','value'=>'YES'),array('label'=>'No','value'=>'NO'),array('label'=>'Always','value'=>'ALWAYS'));
                update_post_meta($funnel_id, $step_type.'_'.$step_node_id,$conditional);
            }
            $step_view_link = get_post_permalink($step_id);
            update_post_meta($step_id,'design_selection_type',$design_type);
            update_post_meta($step_id,'step_product_id',$step_product_id);
            update_post_meta($step_id,'step_node_id',$step_node_id);
            $response  = array(
                'success'          		=> true,
                'step_id'          		=> $step_id,
                'step_node_id'          => $step_node_id,
                'step_edit_link'   		=> $step_edit_link,
                'step_view_link'   		=> rtrim( $step_view_link, '/' ),
                'step_title'       		=> get_the_title($step_id),
                'step_slug'       		=> get_post_field( 'post_name', $step_id),
                'design_type'       	=> $design_type,
                'step_product_id'       => $step_product_id,
                'product_pricing_plan'  => $product_pricing_plan ?? '',
                'conversion'       		=> 0,
                'conversion_rate'       => 0,
                'visit'       			=> 0,
            );
          
        } else {
            return new WP_Error( 'error', '', array( 'status' => 400 ) );
        }
        return rest_ensure_response($response);
    }
    
    /**
     * delete_step
     * @param WP_REST_Request $request
     * @param  mixed $request
     * @return void
     */
    public function delete_step(WP_REST_Request $request)
    {
        $scfunnel  = new SCFunnel();
        $step = $scfunnel->step_store;
        $funnel_id = sanitize_text_field($request['funnel_id']);
        $step_id   = sanitize_text_field($request['step_id']);
        $variation_value = $request['variation_value'] ? $request['variation_value']: '';
        $step_type = get_post_meta($step_id, '_step_type', true);
        if($request['step_id'] == '' || $request['funnel_id'] == ''){
            return new WP_Error( 'error', 'Not Removed', array( 'status' => 400 ) );
        }
        do_action( 'scfunnels/before_delete_step', $step_id );
        if (is_numeric($step_id)) {
            $current_node_id = get_post_meta($step_id,'step_node_id',true);
            $parent_node_id = get_post_meta($funnel_id,'parent_'.$funnel_id.'_'.$current_node_id,true);
            ScFunnel_functions::delete_connected_edges($funnel_id,$parent_node_id,$current_node_id,$variation_value);
            $step->read($step_id);
            $funnel_id = $step->get_funnel_id();
            $response = $step->delete($step_id);
            $funnel = $scfunnel->funnel_store;
            $funnel->read($funnel_id);
          if ($response) {
              $redirect_link = add_query_arg(
                  [
                      'page'      => SC_FUNNEL_EDIT_FUNNEL_SLUG,
                      'id'        => $funnel_id,
                      'step_id'   => 0//$first_active_step
                  ],
                  admin_url('admin.php')
              );
              $response  = array(
                  'success' => true,
                  'message' => "Removed",
              );
          }else{
            return new WP_Error( 'error', 'Not Removed', array( 'status' => 400 ) );
          }
        }elseif(is_string($step_id)){
            $step->delete_variations($funnel_id,$step_id);
            delete_post_meta($funnel_id, $step_id);
            $response  = array(
                'success' => true,
                'message' => "Removed",
            );
        }
        return rest_ensure_response($response);
    }

    /**
     * get_all_pages
     * @param WP_REST_Request $request
     * @param  mixed $request
     * @return void
     */
    public function get_pages(WP_REST_Request $request)
    {
        $data = [];
		$default = [
			// 'value' => null,
			// 'label' => 'Select a Page'
		];
		$data[] = $default;
		$all_ids = get_posts([
            'post_type' => 'page',
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
		return $data;
    }
    
    /**
     * update_funnel_title
     *
     * @param  mixed $request
     * @return @return array|WP_Error
     */
    public function update_funnel_title(WP_REST_Request $request){
        $data = $request->get_params();
        $response = array();
        if( isset($data['funnel_id'], $data['funnel_name']) ){
            $funnel_id 		= sanitize_text_field($data['funnel_id']);
            $updated_name 	= sanitize_text_field($data['funnel_name']);
            $scfunnel       = new SCFunnel();
            $funnel         = $scfunnel->funnel_store;
            $funnel->set_id($funnel_id);
            $funnel->update_funnel_name($updated_name);
            flush_rewrite_rules();
            $response  = array(
                'success' => true,
                'funnelID' 	=> $funnel_id,
                'name' 		=> $updated_name,
            );
        }else{
            return new WP_Error( 'error', 'funnel id or funnel name blank.', array( 'status' => 400 ) );
        }
        
        return rest_ensure_response($response);
    }

    
	/**
	 * Update step meta
	 * 
	 * @param WP_REST_Request $request
	 * 
	 * @return \WP_REST_Response
	 *
	 * @since 1.0.0
	 */
	public function update_step_meta( WP_REST_Request $request ) {
        $settings   = $request->get_params();
		$step_id    = $settings['step_id'] ? $settings['step_id'] : '';
		$funnel_id  = $settings['funnel_id'] ? $settings['funnel_id'] : '';
		$step_title = $settings['step_title'] ? $settings['step_title'] : '';
		$slug       = $settings['step_slug'] ? $settings['step_slug'] : '';
		$step_product_id = $settings['step_product_id'] ? $settings['step_product_id'] : '';
		$design_type = $settings['design_type'] ? $settings['design_type'] : '';
        $step_product_plan = $settings['step_product_plan'] ? $settings['step_product_plan'] : '';
        
        if($step_id == ''|| $funnel_id == ''){
            return new WP_Error( 'error', 'step id or funnel id not found', array( 'status' => 400 ) );
        }
        if($slug == 'upsell' || $slug == 'downsell'){
            if($step_product_plan == '' || $step_product_id == '' || $step_title == ''){
                return new WP_Error( 'error', 'required fields empty.', array( 'status' => 400 ) );
            }
        }
        if($step_title != ''){
            wp_update_post([
                "ID" 			=> $step_id,
                "post_title" 	=> wp_strip_all_tags( $settings['step_title'] ),
                "post_name" 	=> sanitize_title($slug),
            ]);
        }
        if($step_product_id !=''){
            update_post_meta($step_id,'step_product_id',$step_product_id);
        }
        if($step_product_plan !=''){
            update_post_meta($step_id,'step_product_plan',$step_product_plan);
        }
        $step_edit_link = get_edit_post_link($step_id);
        if($step_edit_link == NULL){
            $step_edit_link = admin_url().'post.php?post='.$step_id.'&action=edit';
        }
		$response = array(
			'success'		        => true,
			'step_product_id'       => $step_product_id,
            'step_id'          		=> $step_id,
            'step_edit_link'   		=> $step_edit_link,
            'step_view_link'   		=> rtrim( get_the_permalink($step_id), '/' ),
            'step_title'       		=> get_the_title($step_id),
            'step_slug'       		=> get_post_field( 'post_name', $step_id),
            'design_type'       	=> $design_type,
            'step_product_id'       => $step_product_id,
            'product_pricing_plan'  => $step_product_plan,
		);
		return $this->prepare_item_for_response( $response, $request );
	}

    /**
     * update_conditional_node
     *
     * @param  mixed $request
     * @return void
     */
    public function update_conditional_node(WP_REST_Request $request){
        $funnel_id = $request['funnel_id'];
        $source_node_id = $request['source_node_id'];
        $target_node_id = $request['target_node_id'];
        $variation_value = strtoupper($request['variation_value']);
        if($funnel_id == '' || $source_node_id == '' || $variation_value == ''){
            return new WP_Error( 'error', 'not updated', array( 'status' => 400 ) );
        }
        if(get_post_meta($funnel_id, $source_node_id.'_'.$variation_value, true)){
            return new WP_Error( 'error', 'already added.', array( 'status' => 400 ) );
        }
        $percentage_node = array(
            'source_node_id'=> $source_node_id,
            'target_node_id'=> $target_node_id,
            'variation_value'=> $variation_value,
        );
        if(!empty($percentage_node)){
            update_post_meta($funnel_id, $source_node_id.'_'.$variation_value, $percentage_node);
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

}
