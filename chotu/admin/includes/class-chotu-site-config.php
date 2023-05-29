<?php

/**
 * Chotu_Site_Config
 */
class Chotu_Site_Config
{    
    /**
     * page_title
     *
     * @var mixed
     */
    public $page_title;    
    /**
     * menu_title
     *
     * @var mixed
     */
    public $menu_title;    
    /**
     * menu_slug
     *
     * @var mixed
     */
    public $menu_slug;    
    /**
     * capability
     *
     * @var mixed
     */
    public $capability;
    
    /**
     * __construct
     *
     * @return void
     */
    public function __construct() {
        $this->page_title = 'Site Config Page';
        $this->menu_title = 'Site Config';
        $this->menu_slug  = 'theme_options_page';
        $this->capability = 'manage_options';

        // This action is used to add extra submenus and menu options to the admin panel’s menu structure. 
        /**
         * https://developer.wordpress.org/reference/hooks/admin_menu/
         */
        add_action('admin_menu', array($this, 'chotu_add_site_config_setting_menu'));


        // Fires as an admin screen or script is being initialized.
        /**
         * https://developer.wordpress.org/reference/hooks/admin_init/
         */
        add_action('admin_init',array($this, 'chotu_set_site_config_custom_setting'));
    }


    // to add new chotu specific site config menu in the wp-admin side bar
    public function chotu_add_site_config_setting_menu(){
        if ( current_user_can( 'manage_options' ) ) {
            add_menu_page( $this->page_title, $this->menu_title, $this->capability, $this->menu_slug, array($this, 'chotu_site_config_callback_for_settings_page'), $icon_url = '', $position = 110 );
        }  
    }

    // to load the site config template passed as an arguement
    public function chotu_site_config_callback_for_settings_page(){
        chotu_admin_template('chotu_site_config.php',array());
    }


    // to create chotu specific fields in the site config menu
    /**
     * https://developer.wordpress.org/reference/functions/register_setting/
     */
    public function chotu_set_site_config_custom_setting(){
        
        
       
       /************************************* */
        add_settings_section('chotu_theme_options','API Keys', null, 'theme_options_page');

        add_settings_field('locationiq-api-key','LocationIQ API Key', array($this , 'chotu_set_site_config_input_text'), 'theme_options_page','chotu_theme_options',array(
            'locationiq_api_key'
        ));
        add_settings_field('hubspot-api-token-key','Hubspot API Token Key', array($this , 'chotu_set_site_config_input_text'), 'theme_options_page','chotu_theme_options',array(
            'hubspot_api_token_key'
        ));
        add_settings_field('chotu-captain-api-key','Chotu Captain Api Key', array($this , 'chotu_set_site_config_input_text'), 'theme_options_page','chotu_theme_options',array(
            'chotu_captain_api_key'
        ));
        register_setting('chotu_theme_options', 'locationiq_api_key');
        register_setting('chotu_theme_options', 'hubspot_api_token_key');
        register_setting('chotu_theme_options', 'chotu_captain_api_key');
        /******************************** */
        add_settings_section('chotu_theme_defaults','chotu Defaults', null, 'theme_options_page');
        add_settings_field('captain-onboarding-number','Captain Onboarding Number', array($this , 'chotu_set_site_config_input_number'), 'theme_options_page','chotu_theme_defaults',array(
            'captain_onboarding_number'
        ));
        add_settings_field('location-access-message','Location Access Message', array($this , 'chotu_set_site_config_input_textarea'), 'theme_options_page','chotu_theme_defaults',array(
            'location_access_message'
        ));
        add_settings_field('location-access-error','Location Access Error', array($this , 'chotu_set_site_config_input_textarea'), 'theme_options_page','chotu_theme_defaults',array(
            'location_access_error'
        ));
        add_settings_field('captain-default-cover-pic','Captain Default Cover Pic', array($this , 'chotu_set_site_config_input_file'), 'theme_options_page','chotu_theme_defaults',array('captain_default_cover_pic',400,210));
        add_settings_field('captain-default-display-pic','Captain Default Display Pic', array($this , 'chotu_set_site_config_input_file'), 'theme_options_page','chotu_theme_defaults',array('captain_default_display_pic',150,150));

        register_setting('chotu_theme_options', 'captain_onboarding_number');
        register_setting('chotu_theme_options', 'location_access_message');
        register_setting('chotu_theme_options', 'location_access_error');
        register_setting("chotu_theme_options", "captain_default_cover_pic", array($this , "chotu_handle_captain_default_cover_pic_upload")); 
        register_setting("chotu_theme_options", "captain_default_display_pic", array($this , "chotu_handle_captain_default_display_pic_upload"));
    }


    // to add input type text for each field passed as an arguement in the site config menu
    public function chotu_set_site_config_input_text($args){
        $option = get_option($args[0]);
        echo '<input type="text" id="'. $args[0] .'" name="'. $args[0] .'" value="' . $option . '" />';
       
    }  
    
    // to add input type number for each field passed as an arguement in the site config menu
    public function chotu_set_site_config_input_number($args){
        $option = get_option($args[0]);
        echo '<input type="number" id="'. $args[0] .'" name="'. $args[0] .'" value="' . $option . '" />';
       
    }    
  
     // to add text area for each field passed as an arguement in the site config menu
    public function chotu_set_site_config_input_textarea($args){
        $option = get_option($args[0]);
        echo '<textarea rows="3" cols="50" name="'. $args[0] .'">'.$option.'</textarea>';
    }

     // to add input type file for each field passed as an arguement in the site config menu
    public function chotu_set_site_config_input_file($args){
        $option = get_option($args[0]);
        echo '<input type="file" name="'. $args[0] .'">';
        echo '<img src="'.$option.'" width="'.$args[1].'" height="'.$args[2].'" style="object-fit: cover">';
    }

    // to set the default cover pic for captain on onboarding
    public function chotu_handle_captain_default_cover_pic_upload($option){
        $option = get_option('captain_default_cover_pic');
        if(!empty($_FILES["captain_default_cover_pic"]["tmp_name"]))
        {       
            if ( ! function_exists( 'wp_handle_upload' ) ) {
                require_once( ABSPATH . 'wp-admin/includes/file.php' );
            }
            $urls = wp_handle_upload($_FILES["captain_default_cover_pic"], array('test_form' => false));
            $temp = $urls['url'];
            return $temp;
        }    
        return $option;     
    }
    /**
     * chotu_handle_captain_default_display_pic_upload
     *
     * @param  mixed $option
     * @return url
     */
   
    public function chotu_handle_captain_default_display_pic_upload($option){
        $option = get_option('captain_default_display_pic');
        if(!empty($_FILES["captain_default_display_pic"]["tmp_name"]))
        {           
            require_once( ABSPATH . 'wp-admin/includes/file.php' );
            $urls = wp_handle_upload($_FILES["captain_default_display_pic"], array('test_form' => false));
            $temp = $urls['url'];
            return $temp;
        }      
        return $option;     
    }
    
}
new Chotu_Site_Config();