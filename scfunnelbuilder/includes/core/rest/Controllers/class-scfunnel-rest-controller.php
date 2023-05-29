<?php
/**
 * SCFunnelbuilder rest api controllers
 * 
 * @package SCFunnelbuilder\Rest\Controller
 */
namespace SCFunnelbuilder\Rest\Controllers;

use WP_REST_Controller;

abstract class ScFunnel_REST_Controller extends WP_REST_Controller {
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
     * Prepare links for the request.
     *
     * @param string $setting_id Setting ID.
     * @param string $group_id Group ID.
     * 
     * @return array Links for the given setting.
     * @since  3.0.0
     */
    protected function prepare_links( $setting_id ) {
        $base  = str_replace( '(?P<settings_id>[\w-]+)', $setting_id, $this->rest_base );
        $links = array(
            'self'       => array(
                'href' => get_rest_url( sprintf( '/%s/%s/%s', $this->namespace, $base, $setting_id ) ),
            ),
            'collection' => array(
                'href' => get_rest_url( sprintf( '/%s/%s', $this->namespace, $base ) ),
            ),
        );
        return $links;
    }
    protected function get_template_type_id($slug){
        $template_type = '';
        switch ($slug) {
            case 'opt_in':
                $template_type = 4;
                break;
            case 'landing':
                $template_type = 5;
                break;
            case 'checkout':
                $template_type = 1;
                break;
            case 'upsell':
                $template_type = 6;
                break;
            case 'downsell':
                $template_type = 6;
                break;
            case 'thankyou':
                $template_type = 7;
                break;
            
            default:
                $template_type = 0;
                break;
        }
        return $template_type;

    }
}
