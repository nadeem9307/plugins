<?php
/**
 * Create funnel
 * 
 * @package
 */
namespace SCFunnelbuilder\Modules\Admin\CreateFunnel;

use SCFunnelbuilder\Admin\Module\ScFunnel_Admin_Module;
use SCFunnelbuilder\Traits\SingletonTrait;
use SCFunnelbuilder\SCFunnelbuilder;
use SCFunnelbuilder\ScFunnel_functions;

class Module extends ScFunnel_Admin_Module
{
    use SingletonTrait;

    private $builder;

    public function get_view()
    {
        
        $this->builder = ScFunnel_functions::get_builder_type();
        if (ScFunnel_functions::is_builder_active($this->builder)) {
            require_once SCFUNNEL_DIR . '/admin/modules/createFunnel/views/view.php';
        } else {
            require_once SCFUNNEL_DIR . '/admin/modules/createFunnel/views/builder-not-activated.php';
        }
    }

    public function init_ajax()
    {

        sc_ajax_helper()->handle('create-funnel')
            ->with_callback([ $this, 'create_funnel' ])
            ->with_validation($this->get_validation_data());
        sc_ajax_helper()->handle('import-funnel')
            ->with_callback([ $this, 'import_funnel' ])
            ->with_validation($this->get_validation_data());
    }


    /**
     * Create funnel by ajax request
     *
     * @return array
     * @since  1.0.0
     */
    public function create_funnel( $payload )
    {
        $scfunnel  = new SCFunnelbuilder();
        $funnel = $scfunnel->funnel_store;
        $funnel_id = $funnel->create($payload['funnel_name']);
        update_post_meta( $funnel_id, '_scfunnel_funnel_type', 'sc' );
        if ( $funnel_id ) {
            $general_settings = get_option( '_scfunnels_general_settings' );
            if( isset($general_settings['funnel_type']) ){
                if( 'sc' == $general_settings['funnel_type'] ){
                    $general_settings['funnel_type'] = 'sales';
                    update_option( '_scfunnels_general_settings', $general_settings ); 
                }
                if( 'sales' == $general_settings['funnel_type'] ){
                    if( ScFunnel_functions::is_sc_active() && isset($payload['type']) && 'sc' === $payload['type'] ){
                        update_post_meta( $funnel_id, '_scfunnel_funnel_type', 'sc' );
                    }elseif( isset($payload['type']) && 'lead' === $payload['type'] ){
                        update_post_meta( $funnel_id, '_scfunnel_funnel_type', 'lead' );
                    }
                }else{
                    if( isset($payload['type']) && 'lead' === $payload['type'] ){
                        update_post_meta( $funnel_id, '_sc_funnel_type', 'lead' );
                    }
                }
            }
        }

        $link = add_query_arg(
            [
                'page' => 'edit_funnel',
                'id' => $funnel_id,
            ],
            admin_url('admin.php')
        );

        return [
            'success' => true,
            'funnelID' => $funnel_id,
            'redirectUrl' => $link,
        ];
    }

    public function get_name()
    {
        return 'create-funnel';
    }
     /**
     * Create funnel by import template using ajax request
     *
     * @return array
     * @since  1.0.0
     */
    public function import_funnel($payload ){
        $scfunnel  = new SCFunnelbuilder();
        $funnel = $scfunnel->funnel_store;
        $funnel_id = $funnel->create($payload['funnel_name']);
        update_post_meta( $funnel_id, '_scfunnel_funnel_type', 'sc' );
        if ( $funnel_id ) {
            $general_settings = get_option( '_scfunnels_general_settings' );
            if( isset($general_settings['funnel_type']) ){
                if( 'sc' == $general_settings['funnel_type'] ){
                    $general_settings['funnel_type'] = 'sales';
                    update_option( '_scfunnels_general_settings', $general_settings ); 
                }
                if( 'sales' == $general_settings['funnel_type'] ){
                    if( ScFunnel_functions::is_sc_active() && isset($payload['type']) && 'sc' === $payload['type'] ){
                        update_post_meta( $funnel_id, '_scfunnel_funnel_type', 'sc' );
                    }elseif( isset($payload['type']) && 'lead' === $payload['type'] ){
                        update_post_meta( $funnel_id, '_scfunnel_funnel_type', 'lead' );
                    }
                }else{
                    if( isset($payload['type']) && 'lead' === $payload['type'] ){
                        update_post_meta( $funnel_id, '_sc_funnel_type', 'lead' );
                    }
                }
            }
        }
        $payload['funnel_id'] = $funnel_id;
        $this->update_import_data($payload);
        $link = add_query_arg(
            [
                'page' => 'edit_funnel',
                'id' => $funnel_id,
            ],
            admin_url('admin.php')
        );

        return [
            'success' => true,
            'funnelID' => $funnel_id,
            'redirectUrl' => $link,
        ];
    }
    
    /**
     * update_import_data
     *
     * @param  mixed $args
     * @return void
     */
    public function update_import_data($args){
        $response = ScFunnel_functions::remote_get($args['download_link']);
        if($response['success']){
            $title = $args['funnel_name'];
            $post_content_data = $response['data']['content'] ?? '';
            $builder = ScFunnel_functions::get_builder_type();
            $funnel_id = $args['funnel_id'];
            // re-signing the shortcode signature keys if builder type is oxygen
            if( 'oxygen' === ScFunnel_functions::get_builder_type() ) {
                $ct_shortcodes 	= get_post_meta( $funnel_id, 'ct_builder_shortcodes', true );
                $ct_shortcodes 	= parse_shortcodes($ct_shortcodes, false, false);
                $shortcodes = parse_components_tree($ct_shortcodes['content']);
                update_post_meta($funnel_id, 'ct_builder_shortcodes', $shortcodes);
            }
            if ( 'divi' === ScFunnel_functions::get_builder_type() ) {
                if ( isset( $response['data']['data'] ) && ! empty( $response['data']['data'] ) ) {
                    $post_content = array_column($response['data']['data'],'post_content');
                    update_post_meta( $funnel_id, 'divi_content', $$post_content[0] );
                    wp_update_post(
                        array(
                            'ID' 			=> $funnel_id,
                            'post_content' 	=> $post_content[0]
                        )
                    );
                }
            }
            if ( 'spectra' === ScFunnel_functions::get_builder_type() ) {
                if ( isset( $response['data']['rawData'] ) && ! empty( $response['data']['rawData'] ) ) {
                    wp_update_post(
                        array(
                            'ID' => $funnel_id,
                            'post_content' => $response['data']['rawData']
                        )
                    );
                }
            }
            if ( 'qubely' === ScFunnel_functions::get_builder_type() ) {
                if ( isset( $response['data']['rawData'] ) && ! empty( $response['data']['rawData'] ) ) {
                    wp_update_post(
                        array(
                            'ID' => $funnel_id,
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
                    update_post_meta($funnel_id, '_elementor_data', $post_content_data);
                    update_post_meta($funnel_id, '_elementor_edit_mode', 'builder');
                    update_post_meta($funnel_id, '_wp_page_template', 'elementor_canvas');
                }
            }
            update_post_meta($funnel_id, '_is_imported', 'yes');
            
            return true;
        }
        return false;
    }
}
