<?php
/**
 * This class is responsible for all the helper functions
 * 
 * @package ScFunnels
 */
namespace SCFunnelbuilder;

class ScFunnel_functions
{
	public static $installed_plugins;

	/**
	 * Get all the steps of the funnel
	 *
	 * @param $funnel_id
	 * 
	 * @return array|mixed
	 */
	public static function get_steps( $funnel_id ) {
		$steps = get_post_meta( $funnel_id, '_steps_order', true );
		if ( ! is_array( $steps ) ) {
			$steps = array();
		}
		return $steps;
	}

	/**
	 * Check if the associate order is from funnel or not
	 *
	 * @param \SC_Order $order
	 * 
	 * @return bool
	 * @since  1.0.0
	 */
	public static function check_if_funnel_order($order) {
		$is_funnel_order = false;
		if( $order ){
			$funnel_id = self::get_funnel_id_from_order($order->get_id());
			if($funnel_id) {
				$is_funnel_order = true;
			}
		}
		return $is_funnel_order;
	}


	/**
	 * Get accociate funnel id from order id
	 *
	 * @param $order_id
	 * 
	 * @return int
	 *
	 * @since 1.0.0
	 */
	public static function get_funnel_id_from_order($order_id) {

		$funnel_id = get_post_meta( $order_id, '_scfunnels_funnel_id', true );
		if( !$funnel_id ){
			$funnel_id = get_post_meta( $order_id, '_scfunnels_parent_funnel_id', true );
		}
		return intval( $funnel_id );
	}


	public static function is_funnel_admin_page()
	{
		if (isset($_GET['page'])) {
			$page = sanitize_text_field($_GET['page']);
			if ($page === 'sc_funnels') {
				return true;
			} elseif ($page === 'settings') {
				return true;
			} elseif ($page === 'edit_funnel') {
				return true;
			} elseif ($page === 'create_funnel') {
				return true;
			}
		}
		return false;
	}

	/**
	 * Check if the given string is a date or not
	 *
	 * @param $date
	 * 
	 * @return bool
	 * @since  1.0.0
	 */
	public static function validate_date($date)
	{
		return (bool)strtotime($date);
	}

	/**
	 * Define constant if it is not set yet
	 *
	 * @param $name
	 * @param $value
	 *
	 * @since 1.0.0
	 */
	public static function maybe_define_constant( $name, $value ) {
		if ( ! defined( $name ) ) {
			define( $name, $value );
		}
	}

	/**
	 * Set do not cache constants
	 */
	public static function do_not_cache() {
		global $post;

		if ( ! apply_filters( 'scfunnels/do_not_cache', true, $post->ID ) ) {
			return;
		}
		self::maybe_define_constant( 'DONOTCACHEPAGE', true );
		self::maybe_define_constant( 'DONOTCACHEOBJECT', true );
		self::maybe_define_constant( 'DONOTCACHEDB', true );
		nocache_headers();
	}

	/**
	 * Return formatted date from the
	 * given date object
	 *
	 * @param $date
	 * 
	 * @return false|string
	 * @since  1.0.0
	 */
	public static function get_formatted_date($date)
	{
		return date('F d, Y h:i A', strtotime($date));
	}

	/**
	 * Get funnel id
	 *
	 * @param $step_id
	 * 
	 * @return mixed
	 */
	public static function get_funnel_id_from_step($step_id) {
		$funnel_id = get_post_meta($step_id, '_funnel_id', true);
		return intval($funnel_id);
	}

	/**
	 * Get checkout id from order
	 * 
	 * @param $order_id
	 * 
	 * @return int
	 */
	public static function get_checkout_id_from_order( $order_id ) {
		$checkout_id = get_post_meta( $order_id, '_scfunnels_checkout_id', true );
		return intval( $checkout_id );
	}


	/**
	 * Get funnel from post data
	 * 
	 * @return bool|int
	 */
	public static function get_funnel_id_from_post_data()
	{

		if (isset($_POST['_scfunnels_funnel_id'])) {

			$funnel_id = filter_var(wp_unslash($_POST['_scfunnels_funnel_id']), FILTER_SANITIZE_NUMBER_INT);

			return intval($funnel_id);
		}

		return false;
	}


	/**
	 * Get funnel id from step page
	 *
	 * @return false|mixed
	 */
	public static function get_funnel_id() {
		global $post;
		$funnel_id = false;
		if ( $post ) {
			$funnel_id = get_post_meta( $post->ID, '_funnel_id', true );
		}
		return $funnel_id;
	}

	/**
	 * Unserialize data
	 *
	 * @param $data
	 * 
	 * @return mixed
	 * @since  1.0.0
	 */
	public static function unserialize_array_data($data)
	{
		$data = serialize($data);
		if (@unserialize($data) !== false) {
			return unserialize($data);
		}
		return $data;
	}


	public static function is_sc_funnel_page_template( $page_template ) {
		if ( in_array( $page_template, array( 'scfunnels_boxed', 'scfunnels_default', 'scfunnels_fullwidth_with_header_footer', 'scfunnels_fullwidth_without_header_footer', 'scfunnels_checkout' ), true ) ) {
			return true;
		}

		return false;
	}


	/**
	 * Get formatted string with phrase
	 * e.g: 1 item if singular or
	 * 2 items if plural
	 *
	 * @param Int $number
	 * @param String $singular
	 * @param String $plural
	 * 
	 * @return String
	 * @since  1.0.0
	 */
	public static function get_formatted_data_with_phrase($number, $singular = '', $plural = 's')
	{
		if ($number == 1 || $number == 0) {
			return $singular;
		}
		return $plural;
	}


	/**
	 * Get formatted string from funnel status
	 *
	 * @param $status
	 * 
	 * @return string
	 * @since  1.0.0
	 */
	public static function get_formatted_status($status)
	{
		switch ($status) {
			case 'publish':
				return 'Enabled';
				break;
			case 'draft':
				return 'Draft';
				break;
			default:
				return $status;
				break;
		}
	}


	/**
	 * Return key from the array if valued matched
	 *
	 * @param $value
	 * @param $search_key
	 * @param $array
	 * 
	 * @return int|string
	 * @since  1.0.0
	 */
	public static function array_search_by_value($value, $search_key, $array)
	{
		foreach ($array as $key => $val) {
			if(isset($val[$search_key])){
				if ($val[$search_key] == $value) {
					return $key;
				}
			}
		}
		return '';
	}


	/**
	 * Check If module exists or not
	 * 
	 * @param $id
	 * 
	 * @return bool
	 * @since  1.0.0
	 */
	public static function check_if_module_exists($id)
	{
		return 'publish' == get_post_status($id) || 'draft' == get_post_status($id);

	}

	public static function check_if_funnel_exists($id)
	{
		return true;
	}


	/**
	 * Check if the cpt is step or not
	 *
	 * @param $step_id
	 * @param string $type
	 * 
	 * @return bool
	 */
	public static function check_if_this_is_step_type_by_id($step_id, $type = 'landing')
	{
		$post_type = get_post_type($step_id);
		if (SC_FUNNEL_STEPS_POST_TYPE === $post_type) {
			if ($type === get_post_meta($step_id, '_step_type', true)) {
				return true;
			}
		}
		return false;
	}


	/**
	 * Check if funnel exists
	 *
	 * @param $funnel_id
	 * 
	 * @return bool
	 */
	public static function is_funnel_exists($funnel_id)
	{
		if (!$funnel_id) return false;
		if (FALSE === get_post_status($funnel_id)) {
			return true;
		}
		return false;
	}


	/**
	 * Function to check if the current page is a post edit page
	 *
	 * @return bool
	 *
	 * @since 1.0.0
	 */
	public static function is_step_edit_page(){

		$step_id = -1;
		if ( is_admin() && isset( $_REQUEST['action'] ) ) {
			if ( 'edit' === $_REQUEST['action'] && isset( $_GET['post'] ) ) {
				$step_id = isset( $_GET[ 'post' ] ) ? $_GET[ 'post' ] : - 1;
			}
			elseif ( isset( $_REQUEST['scfunnels_gb'] ) && isset( $_POST['post_id'] ) ){ //phpcs:ignore
				$step_id = intval( $_POST['post_id'] ); //phpcs:ignore
			}
			if ( $step_id === - 1 ) {

				return false;
			}
			$get_post_type = get_post_type( $step_id );
			if ( SC_FUNNEL_STEPS_POST_TYPE === $get_post_type ) {
				return true;
			}
		}
		return false;
	}


	/**
	 * This function will check if the edited step is
	 * the accepted step type
	 *
	 * @param $type
	 * 
	 * @return bool
	 *
	 * @since 1.0.0
	 */
	public static function if_edited_step_type_is( $type ){
		if(self::is_step_edit_page()) {
			$post_id = isset( $_GET[ 'post' ] ) ? $_GET[ 'post' ] : - 1;
			return self::check_if_this_is_step_type_by_id($post_id, $type);
		}
		return false;
	}


	/**
	 * Check if the cpt is step or not
	 *
	 * @param string $type
	 * 
	 * @return bool
	 * @since  1.0.0
	 */
	public static function check_if_this_is_step_type($type = 'landing')
	{
		$post_type = get_post_type();

		if (SC_FUNNEL_STEPS_POST_TYPE === $post_type) {
			global $post;
			if ($type === get_post_meta($post->ID, '_step_type', true)) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Get next step of the
	 * funnel
	 *
	 * @param $funnel_id
	 * @param $step_id
	 * @param bool $condition
	 * 
	 * @return Mix
	 *
	 * @since 1.0.0
	 */
	public static function get_next_step( $funnel_id, $step_id, $condition = true )
	{
		if( $funnel_id && !$step_id ) {
			return false;
		}
		$parent_step_id =  get_post_meta( $step_id, '_parent_step_id', true );
		if( $parent_step_id ){
			$step_id = $parent_step_id;
		}
		$funnel_data = self::get_funnel_data($funnel_id);
		if ( $funnel_data ) {
			$node_id        = self::get_node_id( $funnel_id, $step_id );

			$node_data      = $funnel_data['drawflow']['Home']['data'];
			$step_type =  get_post_meta( $step_id, '_step_type', true );
			foreach ( $node_data as $node_key => $node_value ) {
				if ( $node_value['id'] == $node_id ) {
					$triggers = self::get_mint_triggers();
					if( in_array( $step_type, $triggers ) ){
						$next_node_id 	= isset($node_value['outputs']['output_1']['connections'][0]['node']) ? $node_value['outputs']['output_1']['connections'][0]['node'] : '';
					}else{
						if( $condition ) {
							$next_node_id 	= isset($node_value['outputs']['output_1']['connections'][0]['node']) ? $node_value['outputs']['output_1']['connections'][0]['node'] : '';
						} else {
							$next_node_id 	= isset($node_value['outputs']['output_2']['connections'][0]['node']) ? $node_value['outputs']['output_2']['connections'][0]['node'] : '';
						}
					}
					
					if( $next_node_id ){
						$next_step_id 	= self::get_step_by_node( $funnel_id, $next_node_id );
						$next_step_type = self::get_node_type( $node_data, $next_node_id );
						if( in_array( $next_step_type, $triggers ) ){
							$next_step = self::get_next_step( $funnel_id, $next_step_id );
							return $next_step;
						}
						
						return apply_filters( 'scfunnels/next_step_data', array(
							'step_id' 	=> $next_step_id,
							'step_type' => $next_step_type,
						)); 
						
					}else{
						// if there is no thank you page
						self::redirect_to_deafult_thankyou();
					}
				}
			}
		}
		return false;
	}


	/**
	 * Redirect deafult thank you page
	 */
	public static function redirect_to_deafult_thankyou(){

		if( isset( $_POST[ 'order_id' ] ) && $_POST[ 'order_id' ] ) {
			$url = home_url().'/checkout/order-received/'.$_POST[ 'order_id' ].'/?key='.$_POST[ 'order_key' ];
			return $url;
		}else{
			add_action( 'template_redirect', function( $order_id ){
				if( isset( $_GET[ 'order-received' ] ) && $_GET[ 'order-received' ] ) {
					$url = '';
					wp_redirect( add_query_arg( 'id', $_GET[ 'order-received' ], $url ) );
					exit;
				}elseif( isset( $_POST[ 'order_id' ] ) && $_POST[ 'order_id' ] ) {
					$url = '';
					wp_redirect( add_query_arg( 'id', $_POST[ 'order_id' ], $url ) );
					exit;
				}
			});
		}


	}


	/**
	 * Redirect deafult thank you page
	 */
	public static function get_deafult_thankyou_url(){

		add_action( 'template_redirect', function( $order_id ){
			if( isset( $_GET[ 'order-received' ] ) && $_GET[ 'order-received' ] ) {
				$url = '';
				return add_query_arg( 'id', $_GET[ 'order-received' ], $url );
			}elseif( isset( $_GET[ 'scfunnel-order' ] ) && $_GET[ 'scfunnel-order' ] ) {
				$url = '';
				return add_query_arg( 'id', $_GET[ 'scfunnel-order' ], $url );
			}
		});
	}


	/**
	 * Get previous step of the
	 * funnel
	 *
	 * @param $funnel_id
	 * @param $step_id
	 * @param bool $condition
	 * 
	 * @return Mix
	 *
	 * @since 1.0.0
	 */
	public static function get_prev_step( $funnel_id, $step_id, $condition = true )
	{
		if( $funnel_id && !$step_id ) {
			return false;
		}
		$funnel_data = self::get_funnel_data($funnel_id);
		if ( $funnel_data ) {
			$node_id        = self::get_node_id( $funnel_id, $step_id );
			$node_data      = $funnel_data['drawflow']['Home']['data'];

			foreach ( $node_data as $node_key => $node_value ) {
				if ( $node_value['id'] == $node_id ) {
					if( $condition ) {
						if(!empty($node_value['inputs'])){
							$prev_node_id 	= $node_value['inputs']['input_1']['connections'][0]['node'];
						}else{
							$prev_node_id 	= '';
						}

					} else {
						if(!empty($node_value['inputs'])){
							$prev_node_id 	= $node_value['inputs']['input_2']['connections'][0]['node'];
						}else{
							$prev_node_id 	= '';
						}

					}
					$prev_step_id 	= self::get_step_by_node( $funnel_id, $prev_node_id );
					$prev_step_type = self::get_node_type( $node_data, $prev_node_id );
					if($prev_step_type == 'conditional'){
						return self::get_prev_step($funnel_id,$prev_step_id);

					}else{

						return array(
							'step_id' 	=> $prev_step_id,
							'step_type' => $prev_step_type,
						);
					}
				}
			}

		}
		return false;
	}

	/**
	 * Get node type
	 */
	public static function get_node_type($node_data, $node_id)
	{
		foreach ($node_data as $node_key => $node_value) {
			if ($node_value['id'] == $node_id) {
				return $node_value['data']['step_type'];
			}
		}
	}

	/**
	 * Get step by node
	 */
	public static function get_step_by_node($funnel_id, $node_id)
	{
		$identifier_json = get_post_meta($funnel_id, 'funnel_identifier', true);
		$identifier_json = preg_replace('/\: *([0-9]+\.?[0-9e+\-]*)/', ':"\\1"', $identifier_json);
		if ($identifier_json) {
			$identifier = json_decode($identifier_json, true);
			foreach ($identifier as $identifier_key => $identifier_value) {
				if ($identifier_key == $node_id) {
					return $identifier_value;
				}
			}
		}
		return false;
	}


	/**
	 * Get node by step
	 *
	 * @param $funnel_id
	 * @param $step_id
	 * 
	 * @return bool|int|string
	 */
	public static function get_node_id( $funnel_id, $step_id )
	{
		$identifier_json = get_post_meta( $funnel_id, 'funnel_identifier', true );
		if ($identifier_json) {
			$identifier = json_decode( $identifier_json, true );
			foreach ( $identifier as $identifier_key => $identifier_value ) {
				if ($identifier_value == $step_id) {
					return $identifier_key;
				}
			}
		}
		return false;
	}


	/**
	 * Get funnel data
	 *
	 * @param $funnel_id
	 * 
	 * @return mixed
	 *
	 * @since 1.0.0
	 */
	public static function get_funnel_data( $funnel_id ) {
		return get_post_meta( $funnel_id, 'funnel_data', true );
	}


	/**
	 * Update settings option
	 *
	 * @param $key
	 * @param $value
	 * @param bool $network
	 * 
	 * @since 1.0.0
	 */
	public static function update_admin_settings($key, $value, $network = false)
	{
		if ( $network && is_multisite() ) {
			update_site_option($key, $value);
		} else {
			update_option($key, $value);
		}
	}


	/**
	 * Get admin settings option
	 * by key
	 *
	 * @param $key
	 * @param bool $default
	 * @param bool $network
	 * 
	 * @return mixed|void
	 * @since  1.0.0
	 */
	public static function get_admin_settings($key, $default = false, $network = false)
	{
		if ($network && is_multisite()) {
			$value = get_site_option($key, $default);
		} else {
			$value = get_option($key, $default);
		}
		return $value;
	}


	/**
	 * Get general settings data
	 *
	 * @return array
	 * @since  1.0.0
	 */
	public static function get_general_settings()
	{
		$default_settings = apply_filters(
			'scfunnels_general_settings',
			[
				'builder' => 'elementor',
				'builder_id' => ''
			]
		);
		$saved_settings = self::get_admin_settings('_scfunnels_general_settings', $default_settings);
		return wp_parse_args($saved_settings, $default_settings);
	}


	/**
	 * Get permalink settings data
	 *
	 * @return array
	 * @since  1.0.0
	 */
	public static function get_permalink_settings()
	{
		$default_settings = apply_filters(
			'scfunnels_permalink_settings',
			[
				'structure' => 'default',
				'step_base' => SC_FUNNEL_STEPS_POST_TYPE,
				'funnel_base' => SC_FUNNEL_FUNNELS_POST_TYPE,
			]
		);
		$saved_settings = self::get_admin_settings('_scfunnels_permalink_settings');
		return wp_parse_args($saved_settings, $default_settings);
	}


	/**
	 * Get offer settings data
	 *
	 * @return array
	 * @since  1.0.0
	 */
	public static function get_offer_settings()
	{
		$default_settings = apply_filters( 'scfunnels/get_offer_settings', array(
				'offer_orders' => 'main-order',
				'show_supported_payment_gateway' => 'off',
				'skip_offer_step' => 'off',
				'skip_offer_step_for_free' => 'off',
			)
		);
		$saved_settings = self::get_admin_settings('_scfunnels_offer_settings');
		return wp_parse_args($saved_settings, $default_settings);
	}
	

	/**
	 * Get user roles
	 *
	 * @return array
	 * @since  1.0.0
	 */
	public static function get_user_roles(){
		global $wp_roles;
		$all_roles = $wp_roles->roles;
		return array_keys($all_roles);
	}


	/**
	 * Get the saved builder type
	 *
	 * @return mixed|void
	 * @since  1.0.0
	 */
	public static function get_builder_type()
	{
		$general_settings = self::get_general_settings();
		return $general_settings['builder'];
	}

	/**
	 * Get the saved builder type
	 *
	 * @return mixed|void
	 * @since  1.0.0
	 */
	public static function get_builder_type_id()
	{
		$general_settings = self::get_general_settings();
		return $general_settings['builder_id'];
	}


	/**
	 * Check if sc is installed
	 *
	 * @return bool
	 * @since  1.0.0
	 */
	public static function is_sc_active()
	{
		if (in_array('studiocart/studiocart.php', apply_filters('active_plugins', get_option('active_plugins')))) {
			return true;
		}elseif( function_exists('is_plugin_active') ){
			if( is_plugin_active( 'studiocart/studiocart.php' ) || is_plugin_active( 'studiocart-pro/studiocart.php' )){
				return true;
			}
		}
		return false;
	}


	/**
	 * Check if elementor is installed
	 *
	 * @return bool
	 * @since  1.0.0
	 */
	public static function is_elementor_active()
	{
		if (in_array('elementor/elementor.php', apply_filters('active_plugins', get_option('active_plugins')))) {
			return true;
		}elseif( function_exists('is_plugin_active') ){
			if( is_plugin_active( 'elementor/elementor.php' )){
				return true;
			}
		}
		return false;
	}
	/**
	 * Check if divi is installed
	 *
	 * @return bool
	 * @since  1.0.0
	 */
	public static function is_divi_active()
	{
		if (in_array('divi-builder/divi-builder.php', apply_filters('active_plugins', get_option('active_plugins')))) {
			return true;
		}elseif( function_exists('is_plugin_active') ){
			if( is_plugin_active( 'divi-builder/divi-builder' )){
				return true;
			}
		}
		return false;
	}


	/**
	 * Check if saved builder is activated
	 * or not
	 *
	 * @param $builder
	 * 
	 * @return bool
	 * @since  1.0.0
	 */
	public static function is_builder_active($builder)
	{
		switch ($builder) {
			case 'elementor':
				return self::is_elementor_active();
				break;
			case 'divi':
				return self::is_divi_active();
				break;
			default:
				return false;
				break;
		}
	}


	/**
	 * Check if the global funnel addon is activated or not
	 *
	 * @return bool
	 * @since  1.0.0
	 */
	public static function is_global_funnel_activated() {
		return apply_filters('scfunnels/is_global_funnel_activated', false);
	}


	/**
	 * Check if the funnel is global funnel
	 *
	 * @param $funnel_id
	 * 
	 * @return bool
	 */
	public static function is_global_funnel( $funnel_id ) {
		if(!$funnel_id) {
			return false;
		}
		return apply_filters( 'scfunnels/is_global_funnel', false, $funnel_id );
	}


	/**
	 * Check if the module is exists or not
	 *
	 * @param $module_name
	 * @param string $type
	 * @param bool $step
	 * @param bool $pro
	 * 
	 * @return bool
	 * @since  1.0.0
	 */
	public static function is_module_registered($module_name, $type = 'admin', $step = false, $pro = false)
	{
		$class_name = str_replace('-', ' ', $module_name);
		$class_name = 'SCFunnelbuilder\\Admin\\Modules\\Steps\\' . $class_name . '\Module';
		return class_exists($class_name);
	}


	/**
	 * Check manager permissions on REST API.
	 *
	 * @param string $object Object.
	 * @param string $context Request context.
	 * 
	 * @return bool
	 * @since  1.0.0
	 */
	public static function scfunnel_rest_check_manager_permissions($object, $context = 'read')
	{

		$objects = [
			'settings'      => 'manage_options',
			'templates'     => 'manage_options',
			'steps'         => 'manage_options',
			'products'      => 'manage_options',
		];
		return current_user_can( $objects[$object] );
	}


	/**
	 * Check if the provided plugin ($path) is installed or not
	 *
	 * @param $path
	 * 
	 * @return bool
	 * @since  1.0.0
	 */
	public static function is_plugin_installed( $path )
	{
		$plugins = get_plugins();
		return isset($plugins[$path]);
	}



	/**
	 * Check if the provided plugin ($path) is installed or not
	 *
	 * @param $path
	 * 
	 * @return bool
	 * @since  1.0.0
	 */
	public static function is_plugin_activated( $path )
	{
		include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		if(is_plugin_active( $path)) {
			return true;
		}
		return false;
	}



	/**
	 * Check plugin status by path
	 *
	 * @param $path
	 * @param $slug
	 * 
	 * @return string
	 *
	 * @since 1.0.0
	 */
	public static function get_plugin_action($path, $slug)
	{
		if( 'divi' === $slug ){
			$is_divi_theme_active = self::sc_funnel_is_theme_active( 'Divi' );
			if( $is_divi_theme_active ){
				return 'nothing';
			}
		}

		if (null == self::$installed_plugins) {
			self::$installed_plugins = get_plugins();
		}

		if (!isset(self::$installed_plugins[$path])) {
			return 'install';
		} elseif (!is_plugin_active($path)) {
			return 'activate';
		} else {
			return 'nothing';
		}
	}

	/**
	 * Check theme is active or not
	 *
	 * @param $theme_name
	 * 
	 * @return Bool
	 */
	public static function sc_funnel_is_theme_active( $theme_name ){
        $theme = wp_get_theme(); // gets the current theme
        if ( $theme_name == $theme->name || $theme_name == $theme->parent_theme ) {
            return true;
        }
        return false;
    }


	/**
     * Check plugin is installed or not
     *
     * @param $plugin_slug
	 * 
     * @return Bolean
     */
    public static function sc_funnel_check_is_plugin_installed( $plugin ){
        $installed_plugins = get_plugins();
        return array_key_exists( $plugin, $installed_plugins ) || in_array( $plugin, $installed_plugins, true );
    }


	/**
	 * Get depenedency plugins status
	 *
	 * @return mixed|void
	 *
	 * @since 1.0.0
	 */
	public static function get_dependency_plugins_status()
	{
		return apply_filters('scfunnels/dependency_plugin_list', array(
			'elementor' => array(
				'name' 			=> 'Elementor',
				'plugin_file' 	=> 'elementor/elementor.php',
				'slug' 			=> 'elementor',
				'action' 		=> self::get_plugin_action('elementor/elementor.php', 'elementor')
			),
			'gutenberg' => array(
				'name' 			=> 'Qubely',
				'plugin_file' 	=> 'qubely/qubely.php',
				'slug' 			=> 'qubely',
				'action' 		=> self::get_plugin_action('qubely/qubely.php', 'qubely')
			),
			'divi-builder' => array(
				'name' 			=> 'Divi',
				'plugin_file' 	=> 'divi-builder/divi-builder.php',
				'slug' 			=> 'divi-builder',
				'action' 		=> self::get_plugin_action('divi-builder/divi-builder.php', 'divi-builder')
			),
			'oxygen' => array(
				'name' 			=> 'Oxygen',
				'plugin_file' 	=> 'oxygen/functions.php',
				'slug' 			=> 'oxygen',
				'action' 		=> self::get_plugin_action('oxygen/functions.php', 'oxygen')
			)
		));
	}


	/**
	 * Is there any missing plugin for scfunnels
	 *
	 * @return string
	 *
	 * @since 1.0.0
	 */
	public static function is_any_plugin_missing()
	{
		if (null == self::$installed_plugins) {
			self::$installed_plugins = get_plugins();
		}
		$builder 			= self::get_builder_type();
		$dependency_plugins = self::get_dependency_plugins_status();
		$is_missing = 'no';

		if (isset($dependency_plugins[$builder])) {

			$plugin_data = $dependency_plugins[$builder];
			if ($plugin_data['action'] === 'activate' || $plugin_data['action'] === 'install') {
				$is_missing = 'yes';
			}
		}
		
		return $is_missing;
	}


	/**
	 * Recursively traverses a multidimensional array in search of a specific value and returns the
	 * array containing the value, or an
	 * null on failure.
	 *
	 * @param $search_value
	 * @param $array
	 * 
	 * @return array
	 * @since  1.0.0
	 */
	public static function recursive_multidimensional_ob_array_search_by_value($search_value, $array, $keys = array())
	{
		if (is_array($array) && count($array) > 0) {
			foreach ($array as $key => $value) {
				$temp_keys = $keys;

				// Adding current key to search path
				array_push($temp_keys, $key);

				// Check if this value is an array
				// with atleast one element
				if (is_array($value) && count($value) > 0) {
					$widget_type = isset($value['widgetType']) ? $value['widgetType'] : false;
					if ($widget_type) {
						if ($widget_type === $search_value) {
							$value['path'] = $temp_keys;
							return $value;
						} else {
							$res_path = self::recursive_multidimensional_ob_array_search_by_value(
								$search_value, $value['elements'], $temp_keys);
						}
						if ($res_path != null) {
							return $res_path;
						}
					} else {
						$res_path = self::recursive_multidimensional_ob_array_search_by_value(
							$search_value, $value['elements'], $temp_keys);
					}
					if ($res_path != null) {
						return $res_path;
					}
				}
			}
		}

		return null;
	}


	/**
	 * Get attributes for scfunnels body wrapper
	 *
	 * @param string $template
	 * 
	 * @return string
	 */
	public static function get_template_container_atts( $template = '' ) {
		$attributes  = apply_filters( 'scfunnels/page_container_atts', array() );
		$atts_string = '';
		foreach ( $attributes as $key => $value ) {
			if ( ! $value ) {
				continue;
			}
			if ( true === $value ) {
				$atts_string .= esc_html( $key ) . ' ';
			} else {
				$atts_string .= sprintf( '%s="%s" ', esc_html( $key ), esc_attr( $value ) );
			}
		}
		return $atts_string;
	}


	/**
	 * Get funnel link
	 * 
	 * @param $funnel_id
	 * 
	 * @return Mix
	 */
	public static function get_funnel_link( $funnel_id ) {
		if(!$funnel_id) {
			return;
		}
		$steps 		= self::get_steps($funnel_id);
		if( $steps && is_array($steps) ) {
			$first_step 	= reset($steps);
			$first_step_id 	=  $first_step['id'];
			return get_the_permalink($first_step_id);
		}
		return home_url();
	}

	/**
	 * Get supported builders
	 *
	 * @return array
	 */
	public static function get_supported_builders() {
		$url = SC_FUNNEL_TEMPLATE_URL.'builder';

		// $builders = array(
		// 	'elementor' 	=> 'Elementor',
		// 	'gutenberg' 	=> 'Gutenberg',
		// 	'divi-builder' 	=> 'Divi builder',
		// 	'oxygen' 		=> 'Oxygen',
		// );
		$response = self::remote_get($url);
		if(!empty($response)){
			$builders = array();
			foreach($response['data'] as $key => $builder){
				$builders[$key]['id'] = $builder['id'];
				$builders[$key]['slug'] = $builder['slug'];
				$builders[$key]['name'] = $builder['name'];
			}
		}
		return apply_filters( 'scfunnels/supported_builders', $builders );
	}

	/**
	 * Remove site cookie
	 *
	 * @param $step_id, $cookie_name, $trigger_hook, $funnel_id
	 */
	public static function unset_site_cookie( $step_id, $cookie_name, $trigger_hook = '', $funnel_id = '' ){

		if( !$funnel_id ){
			$funnel_id = self::get_funnel_id_from_step( $step_id );
		}

		if( !$funnel_id ){
			return false;
		}

        $cookie             = isset( $_COOKIE[$cookie_name] ) ? json_decode( wp_unslash( $_COOKIE[$cookie_name] ), true ) : array();
        if(!isset($cookie['funnel_id'])) {
            $cookie['funnel_id']   = $funnel_id;
        }
        $cookie['funnel_status']   = 'successful';

		if(isset( $_COOKIE[$cookie_name] )){
			if( $trigger_hook ){
				do_action( $trigger_hook, $cookie );
			}
        }
		// unsell cookie
        setcookie( $cookie_name, null, strtotime( '-1 days' ), '/', COOKIE_DOMAIN );

	}


	/**
	 * Get page builder of a specific funnel by step Id from postmeta
	 *
	 * @param $funnel_id
	 * 
	 * @return String $builder_name
	 *
	 * @since 1.0.0
	*/
	public static function get_page_builder_by_step_id( $funnel_id ){
		$steps = self::get_steps( $funnel_id );
		$builder_name = '';
		if( isset($steps[0]) ){
			$first_step_id = $steps[0]['id'];
			// check builder is elementor or not
			$elementor_page = get_post_meta( $first_step_id, '_elementor_edit_mode', true );

			// check builder is divi or not
			$divi_page = get_post_meta( $first_step_id, '_et_pb_use_builder', true );

			//check Oxygen builder is not
			$oxygen_page = get_post_meta($first_step_id, 'ct_builder_shortcodes',true);

			if( $elementor_page ) {
				$builder_name = 'elementor';
			} elseif( 'on' === $divi_page ){
				$builder_name = 'divi';
			} elseif (!empty($oxygen_page)){
				$builder_name = 'oxygen';
			}elseif (!empty($oxygen_page)){
				$builder_name = 'spectra';
			} else {
				$builder_name = 'qubely';
			}
			if( $builder_name ){
				return $builder_name;
			}
		}
		return $builder_name;
	}

	public static function oxygen_builder_version_capability(){
		if (defined("CT_VERSION") && version_compare(CT_VERSION,'3.2','>=')){
			return true;
		}
		return false;
	}


	/**
	 * Check array type ( multi-dimentional or one dimentional )
	 *
	 * @param Array
	 * 
	 * @return Bool
	 */
	public static function check_array_is_multidimentional( $multi_array = null ){

		if( $multi_array ){
			foreach($multi_array as $array){
				if(is_array($array)){
					return true;
				}else{
					return false;
				}
			}
			return true;
		}
		return false;
	}


	/**
	 * Check if funnel canvas or not
	 *
	 * @return bool
	 * @since  1.0.0
	 */
	public static function is_funnel_canvas_window() {
		if( is_admin() && isset( $_GET['page'] ) && 'edit_funnel' === $_GET['page'] ) {
			return true;
		}
		return false;
	}


	/**
     * Get all funnel from post
     *
	 * @return $funnels
	 * @since  1.0.0
     */
    public static function get_all_funnels( $status = 'publish' ){
        
		if( 'all' == $status ){
			$arg = [
				'post_type'   	=> SC_FUNNEL_FUNNELS_POST_TYPE,
				'fields'        => 'ID',
				"orderby" 		=> "date",
				"order" 		=> 'ASC',
			];
		}else{
			$arg = [
				'post_type'   	=> SC_FUNNEL_FUNNELS_POST_TYPE,
				'post_status'   => $status,
				'fields'        => 'ID',
				"orderby" 		=> "date",
				"order" 		=> 'ASC',
			];
		}
		
        $funnels     = get_posts( $arg );
        return $funnels;
    }


	/**
     * Get all funnel from post
     *
	 * @return $funnels
	 * @since  1.0.0
     */
    public static function get_all_steps(){

        $steps     = get_posts(
            array(
                'post_type'     => 'scfunnel_steps',
                'post_status'   => 'publish',
                'fields'        => 'ID',
                "orderby" => "date",
                "order" => 'ASC',
            )
        );
        return $steps;
    }


	/**
     * Add type meta for each funnel
     *
     * @since 1.0.0
     */
    public static function add_type_meta(){

		$is_added = get_option( '_scfunnel_added_type_meta' );
		if( !$is_added ){
			update_option( '_scfunnel_added_type_meta', 'yes' );
			$funnels = self::get_all_funnels();
			foreach($funnels as $funnel){
				$type = get_post_meta( $funnel->ID, '_scfunnel_funnel_type', true );
				if( !$type ){
					update_post_meta( $funnel->ID, '_scfunnel_funnel_type', 'sc' );
				}
			}
		}

    }


	/**
	 * Enable lms settings
	 *
	 * @return Bool
	 * @since  1.0.0
	 */
	public static function is_enable_lms_settings(){
		$status  		= get_option( 'scfunnels_pro_lms_license_status' );
        if( $status === 'active' ){
			return apply_filters('scfunnels/is_enable_lms_settings', false);
		}
		return false;
	}

	/**
	 * Check if lms addon activated or not
	 *
	 * @return Bool
	 * @since  1.0.0
	 */
	public static function is_lms_addon_active(){

		if (in_array('scfunnels-pro-lms/scfunnels-pro-lms.php', apply_filters('active_plugins', get_option('active_plugins')))) {
			return true;
		}elseif( function_exists('is_plugin_active') ){
			if( is_plugin_active( 'scfunnels-pro-lms/scfunnels-pro-lms.php' ) ){
				return true;
			}
		}
		return false;
	}


	/**
	 * Get checkout tabs column name
	 *
	 * @return Array
	 * @since  1.0.0
	 */
	public static function get_checkout_columns( $step_id ){

		$funnel_id = get_post_meta( $step_id, '_funnel_id', true );
		$columns = [
			'product-name' 			=> 'Product',
			'product-price' 		=> 'Unit Price',
			'calculate-operator' 	=> '',
			'product-quantity' 		=> 'Quantity',
			'total-price' 			=> 'Total Price',
			'product-action' 		=> 'Actions',
		];

		return apply_filters('scfunnels/checkout_columns', $columns, $funnel_id);
	}


	/**
	 * Supported order bump position
	 *
	 * @return Array
	 * @since  1.0.0
	 */
	public static function supported_orderbump_position( $step_id ){
		$funnel_id = get_post_meta( $step_id, '_funnel_id', true );

		$positions = [
			[
				'name'  => 'Before Order Details',
				'value' => 'before-order',
			],
			[
				'name'  => 'After Order Details',
				'value' => 'after-order',
			],
			[
				'name'  => 'Before Checkout Details',
				'value' => 'before-checkout',
			],
			[
				'name'  => 'After Customer Details',
				'value' => 'after-customer-details',
			],
			[
				'name'  => 'Before Payment Options',
				'value' => 'before-payment',
			],
			[
				'name'  => 'After Payment Options',
				'value' => 'after-payment',
			],
			[
				'name'  => 'Pop-up offer',
				'value' => 'popup',
			],
		];
		if( $funnel_id ){
			return apply_filters('scfunnels/ob_positions', $positions, $funnel_id);
		}

		return $positions;

	}

	/**
	 * Get time zone in string
	 *
	 * @return String
	 * @since  1.0.0
	 */
	public static function sc_funnel_timezone_string() {

		// If site timezone string exists, return it.
		$timezone = get_option( 'timezone_string' );
		if ( $timezone ) {
			return $timezone;
		}

		// Get UTC offset, if it isn't set then return UTC.
		$utc_offset = floatval( get_option( 'gmt_offset', 0 ) );
		if ( ! is_numeric( $utc_offset ) || 0.0 === $utc_offset ) {
			return 'UTC';
		}

		// Adjust UTC offset from hours to seconds.
		$utc_offset = (int) ( $utc_offset * 3600 );

		// Attempt to guess the timezone string from the UTC offset.
		$timezone = timezone_name_from_abbr( '', $utc_offset );
		if ( $timezone ) {
			return $timezone;
		}

		// Last try, guess timezone string manually.
		foreach ( timezone_abbreviations_list() as $abbr ) {
			foreach ( $abbr as $city ) {
				// WordPress restrict the use of date(), since it's affected by timezone settings, but in this case is just what we need to guess the correct timezone.
				if ( (bool) date( 'I' ) === (bool) $city['dst'] && $city['timezone_id'] && intval( $city['offset'] ) === $utc_offset ) { // phpcs:ignore WordPress.DateTime.RestrictedFunctions.date_date
					return $city['timezone_id'];
				}
			}
		}

		// Fallback to UTC.
		return 'UTC';
	}



	/**
	 * Get timezone offset in seconds.
	 *
	 * @since  1.0.0
	 * @return float
	 */
	public static function sc_funnel_timezone_offset() {
		$timezone = get_option( 'timezone_string' );
		if ( $timezone ) {
			$timezone_object = new DateTimeZone( $timezone );
			return $timezone_object->getOffset( new DateTime( 'now' ) );
		} else {
			return floatval( get_option( 'gmt_offset', 0 ) ) * HOUR_IN_SECONDS;
		}
	}


	/**
	 * Check if integrations addon activated or not
	 *
	 * @return Bool
	 * @since  1.0.0
	 */
	public static function is_integrations_addon_active(){
		if (in_array('scfunnels-pro-integrations/scfunnels-pro-integrations.php', apply_filters('active_plugins', get_option('active_plugins')))) {
			return true;
		}elseif( function_exists('is_plugin_active') ){
			if( is_plugin_active( 'scfunnels-pro-integrations/scfunnels-pro-integrations.php' ) ){
				return true;
			}
		}
		return false;
	}


	/**
	 * Get template types
	 *
	 * @return array
	 * @since  1.0.01
	 */
	public static function get_template_types() {
		$general_settings = ScFunnel_functions::get_general_settings();
		if( self::is_sc_active() ){
			$types = array(
				array(
					'slug'   => 'sc',
					'label'  => 'Woo Templates',
				),
			);
		}
		return apply_filters( 'scfunnels/modify_template_type', $types );

	}


	/**
	 * Get global funnel type
	 *
	 * @return Mix
	 */
	public static function get_global_funnel_type(){
		$general_settings = self::get_general_settings();
		if( isset($general_settings['funnel_type']) ){
			return $general_settings['funnel_type'];
		}
		return false;
	}


	/**
	 * May be allow to create sales funnel
	 *
	 * @return Bool
	 */
	public static function maybe_allow_sales_funnel(){
		if( self::is_sc_active() ){
			return true;
		}
		return false;
	}

	/**
	 * Retrieve all user role
	 *
	 * @return Array
	 */

	public static function get_all_user_roles(){
		global $wp_roles;

		$all_roles = isset($wp_roles->roles) ? $wp_roles->roles : [];
		$editable_roles = apply_filters('editable_roles', $all_roles);

		return $editable_roles;
	}


	/**
	 * Retrive all the product sc_product_cat
	 *
	 * @return array
	 * @since  1.0.01
	 */
	public static function get_all_tags(){
		$term_array = array();
		if( self::is_sc_active() ){
			$terms = get_terms( 'sc_product_cat' );
			if ( ! empty( $terms ) && ! is_wp_error( $terms ) ){
				foreach ( $terms as $term ) {

					$term_array[] = [
						'id' => isset($term->term_id) ? $term->term_id : "",
						'name' => isset($term->name) ? $term->name : "",
					];
				}
			}
		}
		return $term_array;
	}

	/**
	 * Sanitize request data
	 * 
	 * @param $data
	 * 
	 * @return Mix
	 */
	public static function get_sanitized_get_post( $data = [] )
	{
		if ( is_array( $data ) && !empty( $data ) ) {
			return filter_var_array( $data, FILTER_SANITIZE_FULL_SPECIAL_CHARS );
		}
		return array(
			'get' => filter_input_array(INPUT_GET, FILTER_SANITIZE_FULL_SPECIAL_CHARS),
			'post' => filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS),
			'request' => filter_var_array( $_REQUEST, FILTER_SANITIZE_FULL_SPECIAL_CHARS),
		);
	}



	/**
	 * Get all the published funnel lists
	 * 
	 * @return Array $funnels 
	 * @since  1.0.0
	 */
	public static function get_funnel_list(){
		$funnels     = get_posts(
            array(
                'post_type'     => SC_FUNNEL_FUNNELS_POST_TYPE,
                'post_status'   => 'publish',
                'numberposts'   => -1,
                'fields'        => 'ID',
                "orderby" => "date",
                "order" => 'ASC',
            )
        );
		$formatted_funnel_data[' '] = __('Select funnel','scfunnel');
		if( is_array($funnels) ){
			foreach( $funnels as $funnel ){
				$is_gbf = get_post_meta( $funnel->ID, 'is_global_funnel', true );
				if( !$is_gbf || 'no' === $is_gbf ){
					$formatted_funnel_data[self::get_funnel_link( $funnel->ID )] = $funnel->post_title;
				}
			}
		}
        return $formatted_funnel_data;
	}

	/**
	 * Check the log status is enabled or not
	 * 
	 * @return Bool
	 * @since  1.0.0
	 */
	public static function maybe_logger_enabled(){
		$general_settings = get_option( '_scfunnels_general_settings' );
		if( $general_settings && isset($general_settings['enable_log_status']) ){
			return 'on' === $general_settings['enable_log_status'];
		}
		return false;
	}
	

	/**
	 * Get supported step type
	 * Landing, Checkout, Thankyou steps are supported for free
	 * 
	 * @return Array $types
	 * @since  1.0.0
	 */
	public static function get_supported_step_type(){
		$types = [
			[
				'type' => 'landing',
				'name' => 'Landing',
			],
			[
				'type' => 'checkout',
				'name' => 'Checkout',
			],
			[
				'type' => 'thankyou',
				'name' => 'Thankyou',
			],
		];

		return apply_filters( 'scfunnels/supported_step_type', $types );
	}


	/**
	 * Supported steps for allow settings
	 * 
	 * @return array
	 * @since  1.0.0
	 */
	public static function get_settings_steps(){
		$settings_steps  =[
			'opt-in',
			'landing',
			'checkout',
			'upsell',
			'downsell',
			'thankyou',
		];

		return apply_filters( 'scfunnels/supported_settings_steps', $settings_steps );
	}
	/**
	 * Get selected steps
	 * 
	 * @param string $type
	 * @param int $funnel_id
	 * 
	 * @return array
	 */
	public static function get_selected_steps( $type, $funnel_id ){
		if( $funnel_id ){
			$steps = get_post_meta( $funnel_id, '_steps', true );
			if( $steps && is_array($steps) ){
				$i = 1;
				$formatted_steps = [];
				$step_types = [$type];
				if( 'landing' == $type ){
					$step_types = [ $type, 'custom' ];
				}
				foreach($steps as $step ){
					if( in_array($step['step_type'], $step_types) ){
						$data = [
							'id' 	=> $i,
							'title' => $step['name'],
							'value' => $step['id'],
						];
						array_push( $formatted_steps, $data );
						$i++;
					}
				}
				
				return $formatted_steps;
			}
		}
		return [];
	}

	/**
	 * Get the first step of a funnel
	 * 
	 * @param int $funnel_id
	 * @return array
	 * @since 2.8.0
	 */
	public static function get_first_step( $funnel_id ){
		$_steps 		= get_post_meta( $funnel_id, '_steps', true ); //get step order
		$first_step = [
            'id' => '',
            'step_type' => '',
        ];
		if( is_array($_steps) ){
			foreach( $_steps as $_step ){
				if( isset($_step['step_type'], $_step['id'] ) && 'landing' ==  $_step['step_type'] ){
					if( !$first_step['id']  ){
						$first_step['id'] = $_step['id'];
						$first_step['step_type'] = $_step['step_type'];
					}else{
						if( 'landing' == $first_step['step_type'] ) {
							$prev_post_date = get_the_date( 'd-m-y h:i:s', $first_step['id'] );
							$current_post_date = get_the_date( 'd-m-y h:i:s', $_step['id'] );
							if( $current_post_date < $prev_post_date ){
								$first_step['id'] = $_step['id'];
							}
						}else{
							$first_step['id'] = $_step['id'];
							$first_step['step_type'] = $_step['step_type'];
						}
					}
				}
	
				if( $first_step['step_type'] !== 'landing'){
					if( isset($_step['step_type'], $_step['id'] ) && 'opt_in' ==  $_step['step_type'] ){
						if( !$first_step['id']   ){
							$first_step['id'] = $_step['id'];
							$first_step['step_type'] = $_step['step_type'];
						}else{
							if( 'opt_in' == $first_step['step_type'] ) {
								$prev_post_date = get_the_date( 'd-m-y h:i:s', $first_step['id'] );
								$current_post_date = get_the_date( 'd-m-y h:i:s', $_step['id'] );
								if( $current_post_date < $prev_post_date ){
									$first_step['id'] = $_step['id'];
								}
							}else{
								$first_step['id'] = $_step['id'];
								$first_step['step_type'] = $_step['step_type'];
							}
						}
					}elseif( isset($_step['step_type'], $_step['id'] ) && 'checkout' ==  $_step['step_type'] ){
						if( !$first_step['id']   ){
							$first_step['id'] = $_step['id'];
							$first_step['step_type'] = $_step['step_type'];
						}else{
							if( 'checkout' == $first_step['step_type'] ) {
								$prev_post_date = get_the_date( 'd-m-y h:i:s', $first_step['id'] );
								$current_post_date = get_the_date( 'd-m-y h:i:s', $_step['id'] );
								if( $current_post_date < $prev_post_date ){
									$first_step['id'] = $_step['id'];
								}
							}else{
								$first_step['id'] = $_step['id'];
								$first_step['step_type'] = $_step['step_type'];
							}
							
						}
					}elseif( isset($_step['step_type'], $_step['id'] ) && 'upsell' ==  $_step['step_type'] ){
						if( !$first_step['id']   ){
							$first_step['id'] = $_step['id'];
							$first_step['step_type'] = $_step['step_type'];
						}else{
							if( 'upsell' == $first_step['step_type'] ) {
								$prev_post_date = get_the_date( 'd-m-y h:i:s', $first_step['id'] );
								$current_post_date = get_the_date( 'd-m-y h:i:s', $_step['id'] );
								if( $current_post_date < $prev_post_date ){
									$first_step['id'] = $_step['id'];
								}
							}else{
								$first_step['id'] = $_step['id'];
								$first_step['step_type'] = $_step['step_type'];
							}
							
						}
					}
					elseif( isset($_step['step_type'], $_step['id'] ) && 'downsell' ==  $_step['step_type'] ){
						if( !$first_step['id']   ){
							$first_step['id'] = $_step['id'];
							$first_step['step_type'] = $_step['step_type'];
						}else{
							if( 'downsell' == $first_step['step_type'] ) {
								$prev_post_date = get_the_date( 'd-m-y h:i:s', $first_step['id'] );
								$current_post_date = get_the_date( 'd-m-y h:i:s', $_step['id'] );
								if( $current_post_date < $prev_post_date ){
									$first_step['id'] = $_step['id'];
								}
							}else{
								$first_step['id'] = $_step['id'];
								$first_step['step_type'] = $_step['step_type'];
							}
							
						}
					}elseif( isset($_step['step_type'], $_step['id'] ) && 'thankyou' ==  $_step['step_type'] ){
						if( !$first_step['id']   ){
							$first_step['id'] = $_step['id'];
							$first_step['step_type'] = $_step['step_type'];
						}else{
							if( 'thankyou' == $first_step['step_type'] ) {
								$prev_post_date = get_the_date( 'd-m-y h:i:s', $first_step['id'] );
								$current_post_date = get_the_date( 'd-m-y h:i:s', $_step['id'] );
								if( $current_post_date < $prev_post_date ){
									$first_step['id'] = $_step['id'];
								}
							}else{
								$first_step['id'] = $_step['id'];
								$first_step['step_type'] = $_step['step_type'];
							}
							
						}
					}
				}
				
			}
		}
		return $first_step['id'];
	}


	/**
	 * Get all post from url
  	 *
	 * @param $url
	 * @param $args
	 * 
	 * @return array
	 */
    public static function remote_get($url, $args = [])
    {
        $response = wp_remote_get($url, $args);

		if ( is_wp_error( $response ) || ! is_array( $response ) || ! isset( $response['body'] ) ) {
			return [
				'success' => false,
				'message' => $response->get_error_message(),
				'data'    => $response,
			];
		}

		// Decode the results.
		$results = json_decode( $response['body'], true );

		if ( ! is_array( $results ) ) {
			return new \WP_Error( 'unexpected_data_format', 'Data was not returned in the expected format.' );
		}

        return [
            'success' => true,
            'message' => 'Data successfully retrieved',
            'data'    => json_decode(wp_remote_retrieve_body($response), true),
        ];
    }
	
	/**
	 * get_post_id_by_meta_key_meta_value
	 *
	 * @param  mixed $meta_key
	 * @param  mixed $meta_value
	 * @return void
	 */
	public static function get_post_id_by_meta_key_meta_value($meta_key,$meta_value){
		global $wpdb;
		return $wpdb->get_var( "select post_id from $wpdb->postmeta where meta_key ='".$meta_key."' AND meta_value ='".$meta_value."'");	
	}	
	/**
	 * get_meta_key_by_post_id_meta_value
	 *
	 * @param  mixed $post_id
	 * @param  mixed $meta_value
	 * @return void
	 */
	public static function get_meta_key_by_post_id_meta_value($post_id,$meta_value){
		global $wpdb;
		return $wpdb->get_var( "select meta_key from $wpdb->postmeta where post_id ='".$post_id."' AND meta_value ='".$meta_value."'");	
	}
	
	/**
	 * delete_post_meta_by_value
	 *
	 * @param  mixed $post_id
	 * @param  mixed $meta_value
	 * @return void
	 */
	public static function delete_post_meta_by_value($post_id,$meta_value){
		global $wpdb;
		return $wpdb->get_var( "delete from $wpdb->postmeta where post_id ='".$post_id."' AND meta_value ='".$meta_value."'");	
	}
	/**
	 * delete_connected_edges
	 *
	 * @param  mixed $funnel_id
	 * @param  mixed $parent_node_id
	 * @param  mixed $current_node_id
	 * @param  mixed $variation_value
	 * @return void
	 */	
	/**
	 * delete_connected_edges
	 *
	 * @param  mixed $funnel_id
	 * @param  mixed $parent_node_id
	 * @param  mixed $current_node_id
	 * @param  mixed $variation_value
	 * @return void
	 */
	public static function delete_connected_edges($funnel_id,$parent_node_id,$current_node_id,$variation_value){
		if($variation_value){
			delete_post_meta($funnel_id,$parent_node_id.'_'.$variation_value);
       		delete_post_meta($funnel_id,'parent_'.$funnel_id.'_'.$current_node_id);
       		delete_post_meta($funnel_id,$variation_value.'%');
       		delete_post_meta($funnel_id,$variation_value);
		}
	}
	
	/**
	 * delete_updowncondtion_node_meta
	 *
	 * @param  mixed $funnel_id
	 * @param  mixed $step_id
	 * @param  mixed $data
	 * @return void
	 */
	public static function delete_updowncondtion_node_meta($funnel_id,$step_id,$data){
		if(!empty($data)){
			foreach ($data as $k => $val) {
				if(is_array($val)){
					delete_post_meta($funnel_id, $step_id.'_'.$val['value']);
				}  
			}

		}
	}

}
