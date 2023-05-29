<?php
/**
 * Settings module
 * 
 * @package
 */

namespace SCFunnelbuilder\Modules\Admin\Settings;

use SCFunnelbuilder\Admin\Module\ScFunnel_Admin_Module;
use SCFunnelbuilder\Traits\SingletonTrait;
use SCFunnelbuilder\ScFunnel_functions;

class Module extends ScFunnel_Admin_Module
{
    use SingletonTrait;

    protected $validations;

    protected $prefix = '_scfunnels_';

    protected $general_settings;


    protected $settings_meta_keys = [
        '_scfunnels_funnel_type' => 'sales',
        '_scfunnels_builder' => 'elementor',
    ];
   
   

    public function is_sc_installed()
    {
        $path    = 'studiocart/studiocart.php';
        
        $plugins = get_plugins();

        return isset($plugins[ $path ]);
    }


    public function is_elementor_installed()
    {
        $path    = 'elementor/elementor.php';
        $plugins = get_plugins();

        return isset($plugins[ $path ]);
    }


    public function enqueue_scripts()
    {
        wp_enqueue_script('settings', plugin_dir_url(__FILE__) . 'js/settings.js', ['jquery'], SCFUNNELBUILDER_VERSION, true);
    }


    public function get_view()
    {
    }

    /**
     * Init ajax hooks for
     * saving metas
     *
     * @since 1.0.0
     */
    public function init_ajax()
    {
        $this->validations = [
            'logged_in' => true,
            'user_can' => 'manage_options',
        ];
        sc_ajax_helper()->handle('update-general-settings')
            ->with_callback([ $this, 'update_general_settings' ])
            ->with_validation($this->validations);
    }


    /**
     * Update handler for settings
     * page
     *
     * @param $payload
     * 
     * @return array
     * @since  1.0.0
     */
    public function update_general_settings($payload)
    {
        $general_settings  = [
            'funnel_type'               => $payload['funnel_type'],
            'builder'                   => $payload['builder'],
            'builder_id'                => $payload['builder_id'],
        ];
        foreach ($payload as $key => $value) {
            if($key == 'builder'){
                $cache_key = 'scfunnels_remote_template_data_' . SCFUNNELBUILDER_VERSION;
                delete_transient($cache_key);
            }
        }
       
        ScFunnel_functions::update_admin_settings($this->prefix.'general_settings', $general_settings);
        return [
            'success' => true
        ];
    }
    /**
     * Get settings by meta key
     *
     * @param $key
     * 
     * @return mixed|string
     * @since  1.0.0
     */
    public function get_settings_by_key($key)
    {
        return isset($this->settings_meta_keys[$key]) ? $this->settings_meta_keys[$key]: '';
    }

    public function get_name()
    {
        return __('settings','scfunnelbuilder');
    }

}
