<?php

/**
 * Chotu_Captain_Public
 * my-account page on Captain Login
 */
class Chotu_Captain_My_Account_Public
{    
    /**
     * init
     * add filter and hook here
     * @return void
     */
    public function init(){
        add_filter( 'woocommerce_get_endpoint_url', array( $this, 'chotu_woocommerce_get_endpoint_url' ), 10, 4);
        add_filter( 'wp_nav_menu_items', array( $this, 'chotu_add_wp_nav_menu_items' ), 10, 2);
        add_filter( 'woocommerce_account_menu_items', array( $this, 'chotu_unset_woo_menus' ) );
        add_action( 'wp_logout', array( $this, 'chotu_logout_redirect' ) );
        add_action( 'woocommerce_edit_account_form', array( $this, 'chotu_edit_my_account_form' ));
        add_action( 'woocommerce_save_account_details', array( $this, 'chotu_save_my_account_details' ) );
        add_filter( 'woocommerce_save_account_details_required_fields', array( $this, 'chotu_unset_account_details_required_fields'), 99 );
        add_action( 'woocommerce_account_reset-pin_endpoint', array( $this, 'chotu_password_reset_template' ) );
        add_filter( 'woocommerce_get_endpoint_url', array( $this, 'chotu_view_my_shop_link' ), 10, 4);
        add_action( 'template_redirect', array( $this, 'chotu_save_reset_pin' ) );
        add_filter( 'wp_authenticate_user', array( $this, 'chotu_captain_login_authentication' ) );
    }
    /**
     * add the proper logout url with nonce etc.,
     */
    public function chotu_woocommerce_get_endpoint_url($url, $endpoint, $value, $permalink){
        if ( $endpoint === 'customer-logout' ) {
            $url = wp_logout_url(home_url());
        }
        return $url;
    }
    /**
     * Add logout in the C - menu
     */
    public function chotu_add_wp_nav_menu_items($items, $args){
        global $chotu_current_captain;
        if ($args->theme_location == 'captain_loggedin_menu') {
            $items .= '<li> <a href="'.wp_logout_url(home_url()).'">Logout</a></li>';
        }
        // if($args->theme_location == 'enduser_shop_menu'){
        //     // $view_my_qr_image = chotu_view_shop_qr_code($chotu_current_captain->user_url);
        //     $items .= '<li> <a href="'.get_site_url(1,'view-my-qr/?captain='.$chotu_current_captain->user_login,'https').'" target="_blank">View my QR</a></li>';
        // }
        return $items;
    }
    /**
     * to remove and rename the menu items of Captain's woocommerce my-account page
     */
    public function chotu_unset_woo_menus($items) {
        global $current_user;
        unset($items['downloads']);
        unset($items['dashboard']);
        unset($items['orders']);
        unset($items['edit-address']);
        // unset($items['customer-logout']);
        $items['edit-account']  = __('Edit My Shop');
        $items['reset-pin']     = __('Reset PIN');
        $items['view-my-qr']    = __('View my Shop');
        return $items;
    }
    /**
     * Redirect a logged-out captain to captain shop page
     * https://developer.wordpress.org/reference/functions/wp_logout/
     */
    public function chotu_logout_redirect($user_id) {
       $home = home_url('/');
       $user = get_user_by('ID', $user_id);
       if(!empty($user)){
           if (in_array('captain',$user->roles)) {
               $user = get_user_by('ID', $user_id);
               wp_redirect($user->user_url);
               exit;
           }
       }
       wp_redirect($home);
       exit;
    }
    /**
     * to add new fields like description, display address etc which captain can edit on his My Account Page
     * https://wp-kama.com/plugin/woocommerce/hook/woocommerce_edit_account_form
     */
    public function chotu_edit_my_account_form() {
        global $chotu_current_captain;
        woocommerce_form_field( 'captain_offers',
            array(
                'type' => 'text',
                'required' => false,
                'label' => 'FLASH! Offers!',
                'placeholder' => 'any offers? sale? discount? news?',
            ), $chotu_current_captain->captain_offers
        );
        // woocommerce_form_field( 'captain_VPA',
        //     array(
        //         'type' => 'text',
        //         'required' => false,
        //         'label' => 'Captain VPA',
        //         'placeholder' => 'xyz@ybl',
        //     ), $chotu_current_captain->captain_VPA
        // );
        woocommerce_form_field( 'description',
            array(
                'type' => 'textarea',
                'required' => false,
                'label' => 'About my shop',
                'placeholder' => 'What is special? Payment, delivery terms',
            ), $chotu_current_captain->description
        );
        woocommerce_form_field( 'captain_display_address',
            array(
                'type' => 'textarea',
                'required' => false,
                'label' => 'My address',
                'placeholder' => 'Address',
            ), $chotu_current_captain->captain_display_address
        );
        woocommerce_form_field( 'captain_language',
            array(
                'type' => 'select',
                'required' => false,
                'label' => 'Shop Language',
                'options'     => array(
                    'hi' => __('हिन्दी'),
                    'bn' => __('বাংলা'),
                    'mr' => __('मराठी'),
                    'te' => __('తెలుగు'),
                    'ta' => __('தமிழ்'),
                    'gu' => __('ગુજરાતી'),
                    'kn' => __('ಕನ್ನಡ'),
                    'ml' => __('മലയാളം'),
                    'pa' => __('ਪੰਜਾਬੀ'),
                    'ur' => __('اردو'),
                    'or' => __('ଓଡ଼ିଆ'),
                    'as' => __('অসমীয়া'),
                    'en' => __('English'),
                    ),
            ), $chotu_current_captain->captain_language
        );
    }
    /**
     * to update the meta value for keys like display adddress, description etc as mentioned below
     * https://wp-kama.com/plugin/woocommerce/hook/woocommerce_save_account_details
     */
    public function chotu_save_my_account_details($user_id) {
        $user = get_userdata($user_id);

        update_user_meta($user_id, 'captain_display_address', wc_clean($_POST['captain_display_address']));
        update_user_meta($user_id, 'description', wc_clean($_POST['description']));
        update_user_meta($user_id, 'captain_offers', wc_clean($_POST['captain_offers']));
        update_user_meta($user_id, 'captain_language', wc_clean($_POST['captain_language']));
        // update_user_meta($user_id, 'captain_VPA', wc_clean($_POST['captain_VPA']));

        wp_safe_redirect($user->user_url);
        exit();
    }
    /** 
     * form on My Account Page
     * to unset some fields and make fields like display address etc required when captain is editing the
     * https://wp-kama.com/plugin/woocommerce/hook/woocommerce_save_account_details_required_fields
     */
    public function chotu_unset_account_details_required_fields($required_fields) {
        unset($required_fields['account_first_name']);
        unset($required_fields['account_last_name']);
        unset($required_fields['account_email']);
        return $required_fields;
    }    
    /**
     * chotu_password_reset_template
     * add reset pin template html view form
     * @return void
     */
    public function chotu_password_reset_template(){
        echo wc_get_template('myaccount/form-reset-pin.php');
    }
    /**
     * chotu_view_my_shop_link
     * add link to redirect shop home page under hello tab
     * @return void
     */
    public function chotu_view_my_shop_link($url, $endpoint, $value, $permalink){
        global $chotu_current_captain;
        if ($endpoint == 'view-my-qr') {
            $url = $chotu_current_captain->user_url.'/#hello'; // Replace with your custom URL
        }
        return $url;
    }
    
    /**
     * chotu_save_reset_pin
     * validate and save reset pin form data
     * @return void
     */
    public function chotu_save_reset_pin(){
        $nonce_value = wc_get_var( $_REQUEST['save-reset-pin-details-nonce'], wc_get_var( $_REQUEST['_wpnonce'], '' ) ); // @codingStandardsIgnoreLine.
        // dd($nonce_value);
		// if ( ! wp_verify_nonce( $nonce_value, 'save_reset_pin' ) ) {
		// 	return;
		// }

		if ( empty( $_POST['action'] ) || 'save_reset_pin' !== $_POST['action'] ) {
			return;
		}
        $pass1                = ! empty( $_POST['password_1'] ) ? $_POST['password_1'] : ''; 
		$pass2                = ! empty( $_POST['password_2'] ) ? $_POST['password_2'] : '';
        $save_pass            = true;
        if ( empty( $pass1 ) && empty( $pass2 ) ) {
			wc_add_notice( __( 'Please fill out all reset PIN fields.', 'woocommerce' ), 'error' );
			$save_pass = false;
		} 
        $user_id = get_current_user_id();
        // Handle required fields.
        if ( ( ! empty( $pass1 ) || ! empty( $pass2 ) ) && $pass1 !== $pass2 ) {
			wc_add_notice( __( 'New passwords do not match.', 'woocommerce' ), 'error' );
            $save_pass  = false;
        }
        if ( wc_notice_count( 'error' ) === 0 ) {
            update_user_meta($user_id, 'captain_pin',$pass2);
            wc_add_notice( __( 'PIN set done successfully.', 'woocommerce' ) );
        }
       
        wp_safe_redirect( wc_get_endpoint_url( 'reset-pin', '', wc_get_page_permalink( 'myaccount' ) ) );
			exit;
    }

    /**
     * chotu_captain_login_authentication
     * my-account login page authentocation filter
     * authenticate password by custom meta 'captain_pin' field instead of user password 
     * @return void
     */
    public function chotu_captain_login_authentication($errors) {

        if (isset($_POST['username']) && isset($_POST['pin'])) {
            $username = sanitize_text_field($_POST['username']);
            $password = $_POST['password'];
            $user     = get_user_by('login', $username);
            if ($user) {
                // Get the user's custom field (e.g., PIN)
                $stored_captain_pin = get_user_meta($user->ID, 'captain_pin', true);
                // Compare the entered custom field value with the stored value
                if ($password == $stored_captain_pin) {
                    // Authentication successful
                    wp_set_current_user($user->ID, $user->user_login);
                    wp_set_auth_cookie($user->ID);
                    setcookie( 'my-shop', $user->user_url, time() + 365 * 24 * 60 * 60, '/');
                    do_action( 'wp_login', $user->user_login, true);
                    wp_redirect(home_url('/my-account/')); // Redirect to the "My Account" page or other location
                    exit;
                }else{
                    $errors = new WP_Error();
                    $errors->add('authentication_failed', 'PIN incorrect.');
                
                    return $errors;
                }
            }
        }

        // Failed login
        return $errors;
    }
}
add_action('init', 'chotu_captain_myaccount_load');
/**
 * chotu_captain_myaccount_load
 * load class init function
 * @return void
 */
function chotu_captain_myaccount_load(){
    /**
     * add new wocommerce endpoints reset-pin page and view my qr page
     */
    add_rewrite_endpoint( 'reset-pin', EP_ROOT | EP_PAGES );
    add_rewrite_endpoint( 'view-my-qr', EP_ROOT | EP_PAGES );
    
    /**
     * instantiated the class and call the init funtion to run the class methods.
     */
    $captain_my_account = new Chotu_Captain_My_Account_Public();
    $captain_my_account->init();
}