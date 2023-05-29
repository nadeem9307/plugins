<?php

/**
 * Chotu_Captain_Public
 * my-account page on Captain Login
 */
class Chotu_Captain_My_Account_Public{
    
    /**
     * __construct
     *
     * @return void
     */
    public function __construct(){

        //  to remove and rename the menu items of Captain's woocommerce my-account page
        /**
         * https://wp-kama.com/plugin/woocommerce/hook/woocommerce_account_menu_items
         */
        add_filter( 'woocommerce_account_menu_items', function( $items ) {
            unset($items['downloads']);
            // unset($items['edit-account']);
            unset($items['dashboard']);
            unset($items['orders']);
            unset($items['edit-address']);
            unset($items['customer-logout']);
            $items['edit-account'] =  __( 'Edit My Shop');
            return $items;
        });

        // for captain to log out from shop and redirect to captain shop page
        /**
         * https://developer.wordpress.org/reference/functions/wp_logout/
         */
        add_action( 'wp_logout', function($user_id){
            $user = get_user_by('ID', $user_id);
            $home = home_url('/');
            if($user){
                $user_roles = $user->roles;
                if(in_array('captain',$user_roles)){
                    $home .= $user->user_login.'/';
                    wp_redirect($home);
                } 
            }
            wp_redirect($home);
            exit();
        } );

        
        // to add new fields like description, display address etc which captain can edit on his My Account Page
        /**
         * https://wp-kama.com/plugin/woocommerce/hook/woocommerce_edit_account_form
         */
        add_action( 'woocommerce_edit_account_form', function(){
            woocommerce_form_field(
            'captain_announcement',
            array(
                'type'        => 'text',
                'required'    => false,
                'label'       => 'Announcement',
                'placeholder' => 'Announcement'
            ),
            get_user_meta( get_current_user_id(), 'captain_announcement', true ) 
            );
            woocommerce_form_field(
                'description',
                array(
                  'type'        => 'textarea',
                  'required'    => false,
                  'label'       => 'Shop Description',
                  'placeholder' => 'Shop Description'
                ),
                get_user_meta( get_current_user_id(), 'description', true ) 
            );
            woocommerce_form_field(
            'captain_display_address',
            array(
                'type'        => 'textarea',
                'required'    => false,
                'label'       => 'Display Address',
                'placeholder' => 'Display Address'
            ),
            get_user_meta( get_current_user_id(), 'captain_display_address', true ) // get the data
            );
            woocommerce_form_field(
            'captain_lat_long',
            array(
                'type'        => 'hidden',
                'required'    => true,
            ),
                get_user_meta( get_current_user_id(), 'captain_lat_long', true ) 
            );
            // $captain_cover_pic= get_user_meta(get_current_user_id(),'captain_cover_pic',true);
            // $captain_display_pic= get_user_meta(get_current_user_id(),'captain_display_pic',true);
            chotu_public_template('chotu_page_captain_my_account',array('user_id'=> get_current_user_id()));
        } );

        // to update the meta value for keys like display adddress, description etc as mentioned below
        /**
         * https://wp-kama.com/plugin/woocommerce/hook/woocommerce_save_account_details
         */
        add_action( 'woocommerce_save_account_details', function( $user_id ) {
            update_user_meta( $user_id, 'captain_display_address', wc_clean( $_POST[ 'captain_display_address' ] ) );
            update_user_meta( $user_id, 'description', wc_clean( $_POST[ 'description' ] ) );
            update_user_meta( $user_id, 'captain_lat_long', wc_clean( $_POST[ 'captain_lat_long' ] ) );
            update_user_meta( $user_id, 'captain_announcement', wc_clean( $_POST[ 'captain_announcement' ] ) );
            if (!empty($_POST[ 'captain_lat_long' ])) {
                $lat_long = explode(",", $_POST[ 'captain_lat_long' ]);
                $key      = get_option('locationiq_api_key');
                $data     = json_decode(file_get_contents('https://us1.locationiq.com/v1/reverse?key=' . $key . '&lat=' . $lat_long[0] . '&lon=' . $lat_long[1] . '&format=json'), true);
                update_user_meta($user_id, 'captain_display_address', $data['display_name']);
            }
            /*
            update_user_meta( $user_id, 'captain_area_served', wc_clean( $_POST[ 'captain_area_served' ] ) );
            
            update_user_meta( $user_id, 'captain_pincode', wc_clean( $_POST[ 'captain_pincode' ] ) );
          
            if(isset($_FILES['captain_cover_pic']) && $_FILES['captain_cover_pic']['type'] !=''){
              $attach_id = chotu_myaccount_images_upload($_FILES['captain_cover_pic'],'cp');
              update_user_meta($user_id, 'captain_cover_pic', $attach_id);
            }
            if(isset($_FILES['captain_display_pic']) && $_FILES['captain_display_pic']['name'] !=''){
              $attach_id = chotu_myaccount_images_upload($_FILES['captain_display_pic'],'dp');
              update_user_meta($user_id, 'captain_display_pic', $attach_id);
            }
            */
        } );


        // to rename the fields below with the ones that are more meaning from chotu perspective
        /**
         * https://developer.wordpress.org/reference/hooks/gettext/
         */
        add_filter( 'gettext', function( $translation, $original ){
            global $wp; 
            if ( 'Display name' == $original ) {
               return 'My Shop Name';
            }
            return $translation;
        }, 10, 2 );

        // to unset some fields and make fields like display address etc required when captain is editing the 
        // form on My Account Page
        /**
         * https://wp-kama.com/plugin/woocommerce/hook/woocommerce_save_account_details_required_fields
         */
        add_filter( 'woocommerce_save_account_details_required_fields', function( $required_fields ){
            unset( $required_fields['account_first_name'] );
            unset( $required_fields['account_last_name'] );
            unset( $required_fields['account_email'] );
            return $required_fields;
        } , 99 );

        //  to unset the billing address and return shipping address
        add_filter( 'woocommerce_my_account_get_addresses', function($address,$customer_id){
            unset($address['billing']);
            return $address;
        }, 10, 2 );

        //  css to hide the below fields on the My Account Page
        /**
         * https://wp-kama.com/plugin/woocommerce/hook/woocommerce_my_account_get_addresses
         */
        add_action( 'wp_head', function(){?>
            <style>label[for="account_first_name"], #account_first_name,label[for="account_last_name"], #account_last_name, label[for="account_email"], #account_email {display: none;}.woocommerce-EditAccountForm fieldset {display: none;}.woocommerce-MyAccount-content .woocommerce-Address-title h3, .optional {display: none;}</style>
            <?php 
        } );

        // to display login successful message on captain login through whatsapp message
        /**
         * https://wp-kama.com/plugin/woocommerce/hook/woocommerce_before_edit_account_form
         */
        add_action( 'woocommerce_before_edit_account_form', function(){
            if(isset($_GET['success'])){
                wc_print_notice( __("Your login has been successful", "woocommerce"), "success" );
            }
        } );
        add_filter( 'wp_nav_menu_items',function( $items, $args ) {
            global $chotu_status, $chotu_current_captain;
            if($chotu_status == "B"){
                $captain_onbn = chotu_append_isd_code(get_option('captain_onboarding_number'));
                $rootshop_data = chotu_get_rootshop_data('');
                $top_item = '';
                if(isset($rootshop_data['rootshop']->post_name)){
                    $top_item .= '<li class="menu-item menu-item-type-post_type menu-item-object-page"><a href="'.get_permalink($rootshop_data['rootshop']->ID).'?reset_cookie=true">Start my&nbsp;'.$rootshop_data['rootshop']->post_title.'</a></li>';
                }
                $top_item .= '<li class="menu-item menu-item-type-post_type menu-item-object-page"><a href="'. site_url('/start?reset_cookie=true') .'">Start My Shop(other)</a></li>';
                $top_item .= '<li class="menu-item menu-item-type-post_type menu-item-object-page"><a href="'. site_url('/oye?reset_cookie=true') .'">Visit another Shop</a></li>';
                $top_item .= $items;
                return $top_item;
            }
            
            return $items;
        }, 10, 2 );
 
    }
    
   
}
new Chotu_Captain_My_Account_Public();
