<?php
/**
 * Remote funnels controller
 * 
 * @package SCFunnelbuilder\Rest\Controllers
 */
namespace SCFunnelbuilder\Rest\Controllers;

use WP_Error;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;
use SCFunnelbuilder\SCFunnelbuilder as SCFunnel;
use SCFunnelbuilder\ScFunnel_functions;

class ScRemoteTemplatesController extends ScFunnel_REST_Controller {

    public static $funnel_api_url 				= SC_FUNNEL_TEMPLATE_URL.'posts';
    public static $funnel_collection_api_url 	= SC_FUNNEL_TEMPLATE_URL.'collection';
    public static $funnel_steps_api_url 		= SC_FUNNEL_TEMPLATE_URL.'posts';
	public static $all_funnels_api_url			= SC_FUNNEL_TEMPLATE_URL.'posts';
	public static $template_media_api_url		= SC_FUNNEL_TEMPLATE_URL.'media';
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
    protected $rest_base = 'v1/';

    public function update_items_permissions_check( $request ) {
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
    public function get_items_permissions_check( $request ) {
        return true;
    }



    public function register_routes()
    {

        register_rest_route(
            $this->namespace, '/' . $this->rest_base . 'get_templates' , array(
                array(
                    'methods'               => WP_REST_Server::READABLE,
                    'callback'              => array( $this, 'get_templates' ),
                    'permission_callback'   => array( $this, 'get_items_permissions_check' ),
                ),
            )
        );

        register_rest_route(
            $this->namespace, '/' . $this->rest_base . 'get_template_type_id' , array(
                array(
                    'methods'               => WP_REST_Server::READABLE,
                    'callback'              => array( $this, 'get_template_type_id' ),
                    'permission_callback'   => array( $this, 'get_items_permissions_check' ),

                )
            )
        );
        register_rest_route(
            $this->namespace, '/' . $this->rest_base . 'import_step' , array(
                array(
                    'methods'               => WP_REST_Server::EDITABLE,
                    'callback'              => array( $this, 'import_step_data' ),
                    'permission_callback'   => array( $this, 'get_items_permissions_check' ),

                )
            )
        );
    }
    public function get_templates(WP_REST_Request $request){
        $step_type = $request['slug_type'];
        if($step_type == ''){
            return [];
        }
        $funnel_template_type = 'sc';
        $templates 				= $this->get_funnels_data($step_type, $step=false , [] ,false);
        $templates['success'] 	= true;
		return $this->prepare_item_for_response( $templates, $request );

    }
   
    /**
     * Get funnel templates data
	 *
     * @param array $args
     * @param bool $force_update
	 * 
     * @return array|mixed|void
     * @since  1.0.0
     */
    public function get_funnels_data($type = false, $step = false, $args = [], $force_update = false)
    {
        
		self::get_funnels($type,$step,$args, $force_update);
		$template_data 	= get_option(SC_FUNNEL_TEMPLATES_OPTION_KEY.'_'.$type);

        if (empty($template_data)) {
            return [];
        }
        return $template_data;
    }

     /**
	 * Get all funnels of the specific builder
  	 *
	 * @param bool $type
	 * @param bool $isStep
	 * @param array $args
	 * @param bool $force_update
	 * 
	 * @return bool|mixed|void
	 */
    private function get_funnels($type = false, $isStep= false,$args = [], $force_update = false)
    {
        $builder_type 	= ScFunnel_functions::get_builder_type_id();
		$cache_key 		= 'scfunnels_remote_template_data_'.$type.'_'. SC_FUNNEL_BUILDER_VERSION;
		
        $data 			= false;
       	if ( $data ) {
       		return;
		}
		if ($type && ($force_update || false === $data) ) {
			$timeout = ($force_update) ? 40 : 55;
			// get all templates
			$params = [
				'per_page'  		=> 100,
				'offset'  			=> 0,
				'builder'  			=> $builder_type,
				'template_type'		=> $this->get_template_type_id($type)
			];
			$url = add_query_arg($params, self::$all_funnels_api_url);

			$template_data = ScFunnel_functions::remote_get($url, [
				'timeout'       => $timeout,
			]);
           
			if ( !$template_data['success'] ) {
				set_transient( $cache_key, [], 24 * HOUR_IN_SECONDS );
				return false;
			}

			// fetch the funnel categories from the remote server
			$params = [
				'per_page'  		=> 100,
			];
			$url = add_query_arg($params, self::$funnel_collection_api_url);
			$categories_data = ScFunnel_functions::remote_get( $url, [
				'timeout'       => $timeout,
			]);
			if ( !$categories_data['success'] ) {
				set_transient($cache_key, [], 24 * HOUR_IN_SECONDS);
				return false;
			}

			$data['templates'] 	= $template_data['data'];
			$data['categories'] = $categories_data['data'];
			update_option(SC_FUNNEL_TEMPLATES_OPTION_KEY.'_'.$type, $data, 'no');
			set_transient($cache_key, $data, 24 * HOUR_IN_SECONDS);
			return false;
		}
    }

    public function import_step_data($request){
        $params = $request->get_params();
        if($params['slug_type'] !=''){
            return $this->import_step($params);
        }
        
    }

    /**
     * Prepare a single setting object for response.
     *
     * @param object          $item Setting object.
     * @param WP_REST_Request $request Request object.
     * 
     * @return WP_REST_Response $response Response data.
     * @since  1.0.0
     */
    public function prepare_item_for_response( $item, $request ) {
        $data     = $this->add_additional_fields_to_object( $item, $request );
        $response = rest_ensure_response( $data );
        return $response;
    }

    /**
	 * Import step
	 *
	 * @param array $args
     * 
	 * @return array
	 */
    public function import_step( $args = [] )
    {
        $response = ScFunnel_functions::remote_get($args['download_url']);
        $title = $args['title'];
        $step_product_id = $args['step_product_id'];
        $step_node_id = $args['step_node_id'];
        $design_type = $args['design_type'];
        $post_content_data = $response['data']['content'] ?? '';
        $builder = ScFunnel_functions::get_builder_type();
        $funnel_id = $args['funnelID'];
        $scfunnel  = new SCFunnel();
        $funnel    = $scfunnel->funnel_store;
        $step =     $scfunnel->step_store;
        if (empty($args['funnelID'])) {
            return new WP_Error( 'error', 'No funnel id found', array( 'status' => 400 ) );
        }
        if($args['slug_type'] == 'opt_in' || $args['slug_type'] == 'checkout'){
            if($step_product_id == ''){
                return new WP_Error( 'error', 'No product id found', array( 'status' => 400 ) );
            }
            if($design_type == 'create_new'){
                $design_type = 'product_page';
            } 
        }
        if($args['slug_type'] == 'landing' || $args['slug_type'] == 'thankyou'){
            $design_type = 'page'; 
        }
        $step_id = $step->create_step($args['funnelID'], $title, $args['slug_type'], $post_content='');
        update_post_meta($step_id,'step_product_id',$step_product_id);
        if($args['slug_type'] == 'upsell' || $args['slug_type'] == 'downsell'){
            $design_type = 'product_page_price';
            $product_pricing_plan = $args['product_pricing_plan'] ?? '';
            if($product_pricing_plan == ''){
                return new WP_Error( 'error', 'product pricing plan not found', array( 'status' => 400 ) );
            }
            update_post_meta($step_id,'product_pricing_plan',$product_pricing_plan);
        }
        // re-signing the shortcode signature keys if builder type is oxygen
        if( 'oxygen' === ScFunnel_functions::get_builder_type() ) {
        	$ct_shortcodes 	= get_post_meta( $step_id, 'ct_builder_shortcodes', true );
			$ct_shortcodes 	= parse_shortcodes($ct_shortcodes, false, false);
			$shortcodes = parse_components_tree($ct_shortcodes['content']);
			update_post_meta($step_id, 'ct_builder_shortcodes', $shortcodes);
		}
		if ( 'divi' === ScFunnel_functions::get_builder_type() ) {
			if ( isset( $response['data']['data'] ) && ! empty( $response['data']['data'] ) ) {
                $post_content = array_column($response['data']['data'],'post_content');
				update_post_meta( $step_id, 'divi_content', $$post_content[0] );
				wp_update_post(
					array(
						'ID' 			=> $step_id,
						'post_content' 	=> $post_content[0]
					)
				);
			}
		}
        if ( 'spectra' === ScFunnel_functions::get_builder_type() ) {
			if ( isset( $response['data']['rawData'] ) && ! empty( $response['data']['rawData'] ) ) {
				wp_update_post(
					array(
						'ID' => $step_id,
						'post_content' => $response['data']['rawData']
                	)
				);
			}
        }
        if ( 'qubely' === ScFunnel_functions::get_builder_type() ) {
			if ( isset( $response['data']['rawData'] ) && ! empty( $response['data']['rawData'] ) ) {
				wp_update_post(
					array(
						'ID' => $step_id,
						'post_content' => $response['data']['rawData']
                	)
				);
			}
        }
        if ( 'elementor' === ScFunnel_functions::get_builder_type() ) {
			if ( isset( $response['data']['content'] ) && ! empty( $response['data']['content'] ) ) {
				wp_update_post(
					array(
						'ID' => $funnel_id,
						'post_content' => $response['data']['rawData']
                	)
				);
                update_post_meta($step_id, '_elementor_data', $post_content_data);
                update_post_meta($step_id, '_elementor_edit_mode', 'builder');
                update_post_meta($step_id, '_wp_page_template', 'elementor_canvas');
                do_action('update_product_id_builder_data',$funnel_id,$step_id);
			}
        }
        // $step_id = $step->create_step( $funnel_id, $step_name, $step_type );
        
        $step->set_id($step_id);
        if ($step_id && ! is_wp_error($step_id)) {
            $funnel->set_id($funnel_id);
            $step_edit_link = get_edit_post_link($step_id);
            if($step_edit_link == NULL){
                $step_edit_link = admin_url().'post.php?post='.$step_id.'&action=edit';
            }
            $step_view_link = get_post_permalink($step_id);
            update_post_meta($step_id,'design_selection_type',$design_type);
            
            update_post_meta($step_id, '_is_imported', 'yes');
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
                'product_pricing_plan'  => $product_pricing_plan,
                'conversion'       		=> 0,
                'conversion_rate'       => 0,
                'visit'       			=> 0,
            );
          
        } else {
            return new WP_Error( 'error', 'step not created', array( 'status' => 400 ) );
        }
        return rest_ensure_response($response);
    }

    
}
