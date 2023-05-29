<?php
/**
 * Store CPT
 * 
 * @package
 */
namespace SCFunnelbuilder\Store_Data;

use SCFunnelbuilder\Exception\ScFunnel_Data_Exception;

abstract class ScFunnel_Abstract_Store_data implements ScFunnel_Store_Data
{
    protected $id;
    abstract public function set_data(\WP_Post $post);

    public function set_id($id)
    {
        $this->id = $id;
    }

    public function get_id()
    {
        return $this->id;
    }

    public function delete($id)
    {
    }

    public function get_meta($id, $key)
    {
        return get_post_meta($id, $key, true);
    }

    public function update_meta($id, $key, $value)
    {
        if(is_array($value)) {
            if(count($value)) {
                update_post_meta($id, $key, $value);
            }else {
                delete_post_meta( $id, $key );
            }
        } elseif (!empty($value)) {
            update_post_meta($id, $key, $value);
        } else {
            delete_post_meta( $id, $key );
        }
    }

    public function delete_meta()
    {

    }


    public function read($id)
    {

    }


    /**
     * When invalid data is found exception will
     * be thrown without reading from DB
     *
     * @param $code
     * @param $message
     * @param int $http_status_code
     * @param array $data
     * 
     * @throws ScFunnel_Data_Exception
     * @since  1.0.0
     */
    protected function error($code, $message, $http_status_code = 400, $data = [])
    {
        throw new ScFunnel_Data_Exception($code, $message, $http_status_code, $data);
    }
    
}
