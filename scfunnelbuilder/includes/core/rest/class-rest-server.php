<?php
/**
 * Server
 * 
 * @package 
 */
namespace SCFunnelbuilder\Rest;

class Rest_Server
{

    /**
     * The single instance of the class.
     *
     * @var object
     */
    protected static $instance = null;

    /**
     * REST API namespaces and endpoints.
     *
     * @var array
     */
    protected $controllers = [];

    /**
     * Get class instance.
     *
     * @return object Instance.
     */
    final public static function instance()
    {
        if (null === static::$instance) {
            static::$instance = new static();
        }
        return static::$instance;
    }

    /**
     * Hook into WordPress ready to init the REST API as needed.
     */
    public function init()
    {
      
        add_action('rest_api_init', [ $this, 'register_rest_routes' ], 10);
    }

    /**
     * Register REST API routes.
     */
    public function register_rest_routes()
    {
        header( 'Access-Control-Allow-Headers: Authorization, Content-Type, X-WP-Wpml-Language', true );
        header("Access-Control-Allow-Origin: *");
        foreach ($this->get_rest_namespaces() as $namespace => $controllers) {
            foreach ($controllers as $controller_name => $controller_class) {
                $controller_class_name = "SCFunnelbuilder\\Rest\\Controllers\\".$controller_class;
                $this->controllers[ $namespace ][ $controller_name ] = new $controller_class_name();
                $this->controllers[ $namespace ][ $controller_name ]->register_routes();
            }
        }
    }


    /**
     * Get API namespaces - new namespaces should be registered here.
     *
     * @return array List of Namespaces and Main controller classes.
     */
    protected function get_rest_namespaces()
    {
        return [
            'scfunnels' => $this->get_controllers(),
        ];
    }

    /**
     * List of controllers in the wc/v1 namespace.
     *
     * @return array
     */
    protected function get_controllers()
    {
        return apply_filters( 'scfunnels/rest_api_controllers', array(
			'percentage_split'      => 'PercentageSplitController',
            'funnel_control'        => 'ScFunnelController',
			'remote_funnel'         => 'ScRemoteTemplatesController',
			'products'         		=> 'ScProductsController',
		));
    }

    /**
     * Return the path to the package.
     *
     * @return string
     */
    public static function get_path()
    {
        return dirname(__DIR__);
    }
}
