<?php
/**
 * Store step data
 * 
 * @package 
 */
namespace SCFunnelbuilder\Store_Data;
use SCFunnelbuilder\Metas\ScFunnel_Step_Meta_keys;
use SCFunnelbuilder\ScFunnel_functions;
class ScFunnel_Steps_Store_Data extends ScFunnel_Abstract_Store_data implements ScFunnel_Store_Data
{
    protected $id;

    protected $internal_keys = [];

    protected $funnel_id;

    protected $type;

    protected $step_title;

    protected $meta_values;


    public function create()
    {
      
    }


    /**
     * Create individual steps
     *
     * @param $funnel_id
     * @param string $title
     * @param string $type
     * @param string $post_content
     * @param bool $clone
     * 
     * @return int|\WP_Error
     */
    public function create_step($funnel_id, $title = 'Landing', $type = 'landing', $post_content = '', $clone = false)
    {
        // dd($post_content);
        $step_id = wp_insert_post(
            apply_filters(
                'scfunnels/scfunnels_new_step_params',
                [
                    'post_type' => SC_FUNNEL_STEPS_POST_TYPE,
                    'post_title' => wp_strip_all_tags($title),
                    'post_content' => $post_content,
                    'post_status' => 'publish',
                ]
            ),
            true
        );
        if($type !='upsell' || $type != 'downsell'){
            update_post_meta($title,'funnel_id',$funnel_id);
            update_post_meta($title,'funnel_step',$title);
            update_post_meta($title,'funnel_step_type',$type);
        }
        if ($step_id && !is_wp_error($step_id)) {
            $this->funnel_id = $funnel_id;
            $this->type = $type;
            $this->set_id($step_id);
            $this->set_default_props();

        }

        do_action('scfunnels_after_step_creation');
        update_post_meta($step_id, '_wp_page_template', 'scfunnels_default');
        return $step_id;
    }


    public function set_default_props()
    {
        $this->set_keys();
        foreach ($this->internal_keys as $meta_key => $value) {
            $this->update_meta($this->id, $meta_key, $value);
        }
    }


    /**
     * Set data
     * 
     * @param \WP_Post $step
     */
    public function set_data(\WP_Post $step)
    {
        $this->set_id($step->ID);
        $this->step_title = $step->post_title;
        $this->funnel_id = $this->get_meta($this->id, '_funnel_id');
        $this->type = $this->get_meta($this->id, '_step_type');
        $meta_keys = ScFunnel_Step_Meta_keys::get_meta_keys($this->type);
        foreach ($meta_keys as $meta_key => $value) {
            $this->internal_keys[$meta_key] = $this->get_meta($this->id, $meta_key);
        }
    }


    /**
     * Read the step and its meta data
     * from DB
     *
     * @param $id
     * 
     * @since 1.0.0
     */
    public function read($id)
    {
        $step = get_post($id);
        if ($step) {
            $this->set_data($step);
        }
    }


    /**
     * Delete step from DB
     *
     * @param $id
     * 
     * @return bool|void
     */
    public function delete($id)
    {
        $step_type = get_post_meta($id,'_step_type',true);
        if($step_type == 'upsell' || $step_type == 'downsell'){
            $funnel_id = get_post_meta($id,'_funnel_id',true);
            $step_node_id = get_post_meta($id,'step_node_id',true);
            $data = get_post_meta($funnel_id,$step_type.'_'.$step_node_id,true);
            ScFunnel_functions::delete_updowncondtion_node_meta($funnel_id,$step_node_id,$data);
            delete_post_meta($funnel_id,$step_type.'_'.$step_node_id);
            $parent_node = ScFunnel_functions::get_meta_key_by_post_id_meta_value($funnel_id,$step_node_id);
            if($parent_node){
                delete_post_meta($funnel_id,'parent_'.$funnel_id.'_'.$parent_node);
                delete_post_meta($funnel_id,$step_type.'_'.$step_node_id);
            }
           
        }
		wp_delete_post($id);
        return true;
    }


    /**
     * Set step basic keys as meta on
     * DB for further use
     *
     * @since 1.0.0
     */
    public function set_keys()
    {
        $this->internal_keys['_step_type'] = $this->type;
        $this->internal_keys['_funnel_id'] = $this->funnel_id;
    }


    /**
     * Get title of the step
     *
     * @return string
     * @since  1.0.0
     */
    public function get_title()
    {
        $title = $this->step_title;
        if ($title == '') {
            $title = $this->get_type();
        }
        return $title;
    }

    public function get_funnel_id()
    {
        return $this->funnel_id;
    }

    public function get_type()
    {
        return $this->type;
    }

    public function get_next_step($order, $current)
    {
        $current_key = array_search($current, array_column($order, 'id'));
        if (isset($order[$current_key + 1])) {
            $next_key = $order[$current_key + 1];
            $next_id = $next_key['id'];
            return $next_id;
        } else {
            return false;
        }
    }


    /**
     * Get meta value by key
     *
     * @param $key
     * 
     * @return mixed
     */
    public function get_internal_metas_by_key($key)
    {
        if (isset($this->internal_keys[$key])) {
            return $this->internal_keys[$key];
        }
        return null;
    }


    /**
     * Import post metas
     *
     * @param $step_id
     * @param array $post_metas
     * 
     * @since 1.0.0
     */
    public function import_metas($step_id, $post_metas = [])
    {
        foreach ($post_metas as $meta_key => $meta_value) {
            $meta_value = isset($meta_value[0]) ? $meta_value[0] : '';
            if ($meta_value) {
                if (is_serialized($meta_value, true)) {
                    $raw_data = maybe_unserialize(stripslashes($meta_value));
                } elseif (is_array($meta_value)) {
                    $raw_data = json_decode(stripslashes($meta_value), true);
                } else {
                    $raw_data = $meta_value;
                }

                if ( '_elementor_data' === $meta_key ) {
                    if (is_array($raw_data)) {
                        $raw_data = wp_slash(wp_json_encode($raw_data));
                    } else {
                        $raw_data = wp_slash($raw_data);
                    }
                }

                if ($meta_key != 'order-bump-settings' && $meta_key != '_w_checkout_products' && $meta_key != 'order-bump') {
                  $this->update_meta($step_id, $meta_key, $raw_data);
                }
            }
        }
        $this->update_meta($step_id, '_is_imported', 'yes');
    }
    public function delete_variations($funnel_id,$step_id){
        $variation_data = get_post_meta($funnel_id,$step_id,true);
       
        if(!empty($variation_data)){
            if(isset($variation_data['variations'])){
                foreach ($variation_data['variations'] as $k => $value) {
                    if(is_array($value)){
                        delete_post_meta($funnel_id, $value['value']);
                        ScFunnel_functions::delete_post_meta_by_value($funnel_id,$step_id);
                    }
                }
            }
        }
        $updoe = get_post_meta($funnel_id,'conditional_'.$step_id,true);
        if(!empty($updoe)){
            foreach ($updoe as $k => $val) {
                if(is_array($val)){
                    delete_post_meta($funnel_id, $step_id.'_'.$val['value']);
                }  
            }
            delete_post_meta($funnel_id, 'conditional_'.$step_id);
        }
        
    }
}
