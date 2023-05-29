<?php
/**
 * Funnel module
 * 
 * @package
 */
namespace SCFunnelbuilder\Modules\Admin\Funnel;

use SCFunnelbuilder\Admin\Module\ScFunnel_Admin_Module;
use SCFunnelbuilder\Traits\SingletonTrait;
use SCFunnelbuilder\SCFunnelbuilder as ScFunnel;
use SCFunnelbuilder\ScFunnel_functions;
// use WC_Countries;
class Module extends ScFunnel_Admin_Module
{

    use SingletonTrait;

    private $id;

    protected $funnel;

    protected $step_module = null;

    protected $step_type;
  
    public function init($id)
    {
        $scfunnel_object =  new ScFunnel();
        $this->id = $id;
        $this->funnel =$scfunnel_object->funnel_store;
        $this->funnel->set_id($id);
    }



    public function init_ajax()
    {
		sc_ajax_helper()->handle('save-steps-order')
			->with_callback([ $this, 'save_steps_order' ])
			->with_validation($this->get_validation_data());

        sc_ajax_helper()->handle('funnel-name-change')
            ->with_callback([ $this, 'funnel_name_change' ])
            ->with_validation($this->get_validation_data());

        // sc_ajax_helper()->handle('clone-funnel')
        //     ->with_callback([ $this, 'clone_funnel' ])
        //     ->with_validation($this->get_validation_data());

        sc_ajax_helper()->handle('delete-funnel')
            ->with_callback([ $this, 'delete_funnel' ])
            ->with_validation($this->get_validation_data());

		sc_ajax_helper()->handle('change-funnel-status')
			->with_callback([ $this, 'update_funnel_status' ])
			->with_validation($this->get_validation_data());

        sc_ajax_helper()->handle('funnel-drag-order')
            ->with_callback([ $this, 'funnel_drag_order' ])
            ->with_validation($this->get_validation_data());

    }


    /**
     * Return funnel object
     *
     * @return ScFunnel_Funnel_Store_Data
     * @since  1.0.0
     */
    public function get_funnel()
    {
        return $this->funnel;
    }


    /**
     * Show funnel canvas if the following conditions met
     *      a. if funnel exits
     *          show steps if -
     *              a. step_id exits in url
     *              b. step exits
     *              c. this funnel contains the step
     *  otherwise show 404 page
     *
     * @throws \Exception
     * @since  1.0.0
     */
    public function get_view()
    {
        if (ScFunnel_functions::check_if_module_exists($this->funnel->get_id())) {
            $step_id = filter_input(INPUT_GET, 'step_id', FILTER_SANITIZE_STRING);
            $this->funnel->read($this->id);
            $funnel = $this->get_funnel();

            if (
                $step_id
                && ScFunnel_functions::check_if_module_exists($step_id)
                && $this->funnel->check_if_step_in_funnel($step_id)
            ) {
                $this->step_type = 'landing';
            } else {
                $step_id = $funnel->get_first_step_id();
                $this->step_type = $funnel->get_first_step_type();
            }
        
            $is_module_registered = ScFunnel_functions::is_module_registered($this->step_type, 'steps', true, true);

            if ($this->step_type) {
                if ($is_module_registered) {
                    $this->step_module = $this->scfunnel_object->module_manager->get_admin_modules($this->step_type);
                    $this->step_module->init($step_id);
                }
            }
            require_once SC_FUNNEL_DIR . '/admin/modules/funnel/views/view.php';
        } else {
            require_once SC_FUNNEL_DIR . '/admin/partials/404.php';
        }
    }


	/**
	 * Save steps order
	 *
	 * @param $payload
     * 
	 * @return array
	 *
	 * @since 2.0.5
	 */
    public function save_steps_order( $payload ) {
		$funnel_id 		= isset( $payload['funnelID'] ) ? $payload['funnelID'] : 0;
		$input_node  	= $payload['inputNode'];
		$output_node  	= $payload['outputNode'];
		if( $funnel_id ) {
			$funnel_data = get_post_meta( $funnel_id, 'funnel_data', true );

		}
		return array(
			'success' => true
		);
	}

    /**
     * Funnel drag order
     * 
     * @param Array $payload
     * 
     * @return Array
     * @since  2.0.4
     */
    public function funnel_drag_order($payload)
    {
        $funnel_id = $payload['funnel_id'];
        $orders = $payload['order'];
        $existing_order = get_post_meta($funnel_id, '_steps_order', true);
        $step_names = apply_filters('scfunnels_steps', [
            'landing'       => __('Landing', 'scfunnelbuilder'),
            'thankyou'      => __('Thank You', 'scfunnelbuilder'),
            'checkout'      => __('Checkout', 'scfunnelbuilder'),
            'upsell'        => __('Upsell', 'scfunnelbuilder'),
            'downsell'      => __('Downsell', 'scfunnelbuilder'),
        ]);
        $modified_order = [];
        foreach ($orders as $order) {
            $order = str_replace('setp-list-', '', $order);
            $step_type = get_post_meta($order, '_step_type', true);
            $step_array = [
                'id' => $order,
                'step_type' => $step_type,
                'name' => $step_names[$step_type],
            ];
            $modified_order[] = $step_array;
        }
        $modified_order = array_values(array_filter($modified_order));
        update_post_meta($funnel_id, '_steps_order', $modified_order);
        return [
            'success' => true,
        ];
    }


    /**
     * Delete funnel and all the
     * data
     *
     * @param $payload
     * 
     * @return array
     * @since  1.0.0
     */
    public function delete_funnel($payload)
    {
        $scfunnel_object =  new ScFunnel();
        // dd($payload);
        $funnel_id = sanitize_text_field($payload['funnel_id']);
        $funnel =  $scfunnel_object->funnel_store;
        $funnel->read($funnel_id);

        if ($funnel->get_step_ids()) {
            foreach ($funnel->get_step_ids() as $step_id) {
                $step =  $scfunnel_object->step_store;
                $step->delete($step_id);
            }
        }
        do_action('scfunnels/before_delete_funnel', $funnel_id );
        $response = $funnel->delete($funnel_id);
        if ($response) {
            $redirect_link = add_query_arg(
                [
                    'page' => SC_FUNNEL_MAIN_PAGE_SLUG,
                ],
                admin_url('admin.php')
            );
            return [
                'success' => true,
                'redirectUrl' => $redirect_link,
            ];
        }
    }

    /**
     * Update funnel status
     * 
     * @param Array $payload
     * 
     * @return Array
     */
    public function update_funnel_status( $payload ) {
		if ( ! isset( $payload['funnel_id'] ) ) {
			return array(
				'message' => __( 'No funnel id found', 'scfunnelbuilder' )
			);
		}
        if($payload['funnel_status'] == 'enable'){
            $payload['funnel_status'] = 'publish';
        }

		$funnel_id 	= sanitize_text_field($payload['funnel_id']);
		$status		= sanitize_text_field($payload['funnel_status']);
		$steps 		= get_post_meta( $funnel_id, '_steps_order', true );
		if( $steps ) {
			foreach ($steps as $step) {
				$step_data = array(
					'ID'			=> $step['id'],
					'post_status' 	=> $status
				);
				wp_update_post($step_data);
			}
		}

		$funnel_data = array(
			'ID'			=> $funnel_id,
			'post_status' 	=> $status
		);
		wp_update_post($funnel_data);

		return [
			'success'	=> true,
			'funnel_id'	=> $funnel_id,
			'message'	=> __('Funnel status has been updated.', 'scfunnelbuilder'),
			'redirect_url'	=> admin_url('admin.php?page=sc_funnels')
        ];
	}


    /**
     * Change funnel name
     *
     * @param $payload
     * 
     * @return array
     * @since  1.0.0
     */
    public function funnel_name_change($payload)
    {
        if( isset($payload['funnel_id'], $payload['funnel_name']) ){
            $funnel_id 		= sanitize_text_field($payload['funnel_id']);
            $updated_name 	= sanitize_text_field($payload['funnel_name']);
            $funnel 		= $this->scfunnel_object->funnel_store;
            $funnel->set_id($funnel_id);
            $funnel->update_funnel_name($updated_name);
            flush_rewrite_rules();
            return [
                'success' 	=> true,
                'funnelID' 	=> $funnel_id,
                'name' 		=> $updated_name,
            ];
        }
        
        return [
            'success' 	=> false,
        ];
        
    }


    /**
     * Get module name
     * 
     * @return String
     */
    public function get_name()
    {
        return __('funnel','scfunnelbuilder');
    }


}
