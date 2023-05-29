<?php
/**
 * Create class object of funnel group and return object
 *
 * @package
 */


/**
 * Create class object of funnel group and return object
 *
 * @package
 */
class ScFunnel_Controller_Group_Factory {

    public static function build($module)
    {
        $class_name = "SCFunnelbuilder\\Controller\\ScFunnel_Controller_".ucfirst($module);
        if (!class_exists(ucfirst($class_name))) {
            throw new \Exception('Invalid Condition Module.');
        }else {
            return new $class_name();
        }
    }
}