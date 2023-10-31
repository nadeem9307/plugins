<?php

/**
 * The public-facing functionality of the plugin.
 * All functions defined here in this file should be added to includes/class-chotu.php
 * @link       chotu.com
 * @since      1.0.0
 *
 * @package    Chotu_main
 * @subpackage Chotu_main/public
 */

 //sindhu-comments - Remove Oye related function
 //No purpose of this file, functions can be moved to respective related classes
/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 * All AJAX functions are defined here
 *
 * @package    Chotu_main
 * @subpackage Chotu_main/public
 * @author     Mohd Nadeem <mohdnadeemzonv@gmail.com>
 */
class Chotu_Public
{
    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string    $plugin_name       The name of the plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct($plugin_name, $version)
    {

        $this->plugin_name = $plugin_name;
        $this->version = $version;

    }

    /**
     * Register the stylesheets for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_styles()
    {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Chotu_main_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Chotu_main_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/chotu-public.css', array(), $this->version, 'all');

    }

    /**
     * Register the JavaScript for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts()
    {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Chotu_main_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Chotu_main_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */
        $localize = array(
            'ajaxurl' => admin_url('admin-ajax.php'),
        );
        wp_register_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/chotu_public.js', '', '', true);
        wp_localize_script('chotu', 'chotu', $localize);
        wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/chotu_public.js', array('jquery'), $this->version, false);

    }

    /**
     * chotu_return_shop_template
     *
     * @param  mixed $template
     * @return void
     * to load the template for captain shop from public folder
     */
    public function chotu_return_shop_template($template)
    {
        $file = '';
        if (is_author()) {
            $author = get_user_by('slug', get_query_var('author_name'));
            if (in_array('captain', $author->roles)) {
                $file = 'captain_shop.php'; // the name of your custom template
                $find[] = $file;
                $find[] = plugin_dir_path(__FILE__) . 'public/templates/' . $file; // name of folder it could be in, in user's theme
            }
        }
        if (is_page('qr')) { // Replace with your page's slug
            $file = 'page-qr.php'; // the name of your custom template
            $find[] = $file;
            $find[] = plugin_dir_path(__FILE__) . 'public/templates/' . $file;
        }
        if ($file) {
            $template = locate_template(array_unique($find));
            if (!$template) {
                // if not found in theme, will use your plugin version
                $template = untrailingslashit(plugin_dir_path(__FILE__)) . '/templates/' . $file;
            }
        }
        return $template;
    }

    /**
     * chotu_set_captain_login_expiration
     *
     * @param  mixed $seconds
     * @param  mixed $user_id
     * @param  mixed $remember
     * @return void
     * to set captain login expiration
     * once captain is logged in, login will be valid for 30 mins unless captain logs out
     */
    public function chotu_set_captain_login_expiration($seconds, $user_id, $remember)
    {
        $captain = get_user_by('ID', $user_id);
        $user_roles = $captain->roles;
        if (in_array('captain', $user_roles)) {
           $seconds = 1800; //30 minutes for captain;
        } else {
            $seconds = 172800; //2 days for others;
        }
       return $seconds;
    }

    /**
     * chotu_update_captain_pics
     * update captain cover pic and captain profile pic using ajax method.
     * using ajax, hence writing this function here. ajax is not found elsewhere
     * @return void
     */
    public function chotu_update_captain_pics()
    {
        global $current_user;
        $attach_url = '';
        if (is_captain_logged_in()) {
            if (isset($_FILES['captain_cover_pic']) && $_FILES['captain_cover_pic']['name'] != '') {
                $captain_cover_pic = get_user_meta($current_user->ID, 'captain_cover_pic', true);
                $att_id = chotu_find_image_post_id(trim($captain_cover_pic));
                wp_delete_attachment($att_id, true);
                $attach_url = chotu_images_upload($_FILES['captain_cover_pic'], 'url');
                update_user_meta($current_user->ID, 'captain_cover_pic', $attach_url);
            }
            if (isset($_FILES['captain_display_pic']) && $_FILES['captain_display_pic']['name'] != '') {
                $captain_display_pic = get_user_meta($current_user->ID, 'captain_display_pic', true);
                $att_id = chotu_find_image_post_id(trim($captain_display_pic));
                wp_delete_attachment($att_id, true);
                $attach_url = chotu_images_upload($_FILES['captain_display_pic'], 'url');
                update_user_meta($current_user->ID, 'captain_display_pic', $attach_url);
            }
            if (isset($_FILES['captain_gallery'])) {
                $filename = array();
                $captain_gallery = get_user_meta($current_user->ID, 'captain_gallery', true);
                if ($captain_gallery) {
                    $countfiles = count($_FILES['captain_gallery']['name']);
                    $total_files = count($captain_gallery) + $countfiles;
                    if ($total_files < 21) {
                        for ($i = 0; $i < $countfiles; $i++) {
                            $filename['name'] = $_FILES['captain_gallery']['name'][$i];
                            $filename['type'] = $_FILES['captain_gallery']['type'][$i];
                            $filename['tmp_name'] = $_FILES['captain_gallery']['tmp_name'][$i];
                            $filename['error'] = $_FILES['captain_gallery']['error'][$i];
                            $filename['size'] = $_FILES['captain_gallery']['size'][$i];
                            $attach_url = chotu_images_upload($filename, 'captain_gallery');
                            array_push($captain_gallery, $attach_url);
                        }
                        update_user_meta($current_user->ID, 'captain_gallery', $captain_gallery);
                    } else {
                        return wp_send_json_error( array('message' => '☹️ Upload Max 20 Files allowed'));
                    }
                } else {
                    $captain_gallery = array();
                    $countfiles = count($_FILES['captain_gallery']['name']);
                    if ($countfiles < 21) {
                        for ($i = 0; $i < $countfiles; $i++) {
                            $filename['name'] = $_FILES['captain_gallery']['name'][$i];
                            $filename['type'] = $_FILES['captain_gallery']['type'][$i];
                            $filename['tmp_name'] = $_FILES['captain_gallery']['tmp_name'][$i];
                            $filename['error'] = $_FILES['captain_gallery']['error'][$i];
                            $filename['size'] = $_FILES['captain_gallery']['size'][$i];
                            $attach_url = chotu_images_upload($filename, 'captain_gallery');
                            array_push($captain_gallery, $attach_url);
                        }
                        update_user_meta($current_user->ID, 'captain_gallery',$captain_gallery);
                    } else {
                        return wp_send_json_error(array('message' => '☹️ Upload Max 20 Files allowed'));
                    }
                }
            }

        }
        if ($attach_url) {
            return wp_send_json_success(array('message' => '👍'));
        }
        return wp_send_json_error(array('message' => '☹️ Something went wrong'));
        wp_die();
    }

    /**
     * chotu_remove_gallery_image
     * remove rootshop gallery image from captain meta only 
     * @return void
     */
    public function chotu_remove_gallery_image()
    {
        global $current_user;
        $rootshop_gallery = array();
        $captain_gallery = $current_user->captain_gallery;

        $key = array_search($_POST['attachment_id'], $captain_gallery);
        unset($captain_gallery[$key]);
        update_user_meta($current_user->ID, 'captain_gallery', $captain_gallery);

        if(!in_array($_POST['attachment_id'], $rootshop_gallery)) {
            if(!wp_delete_attachment($_POST['attachment_id'])) {
              return wp_send_json_error(array('message' => '☹️ Something went wrong'));
            }
        }  
        return wp_send_json_success(array('message' => '👍 removed from gallery'));
        wp_die();
    }

}