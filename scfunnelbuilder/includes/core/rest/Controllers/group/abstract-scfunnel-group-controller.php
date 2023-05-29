<?php
/**
 * SCFunnelbuilder type abstract controller
 * 
 * @package SCFunnelbuilder\Controller
 */
namespace SCFunnelbuilder\Controller;
abstract class ScFunnel_Controller_Group
{
    /**
     * Get sc_product from steps
     */
    abstract public function get_items( $step_id );


    /**
     * Get sc_product from steps
     */
    abstract public function get_ob_settings( $all_settings );


    /**
     * Get sc_product from steps
     */
    abstract public function update_ob_settings( $all_settings );

    

}