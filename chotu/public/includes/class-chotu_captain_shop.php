<?php
/**
 * Chotu_Shop
 */

use chillerlan\QRCode\{QRCode, QROptions};
use chillerlan\QRCode\Output\QROutputInterface;
class Chotu_Shop_Public
{
    /**
     * init
     *
     * @return void
     */
    public function init(){
        add_filter( 'author_rewrite_rules', array($this, 'chotu_rewrite_author_url' ) );
        add_filter( 'woocommerce_breadcrumb_home_url',array( $this, 'chotu_change_breadcrumb_home_url') );
        add_action( 'template_redirect', array( $this, 'chotu_redirect_shop_page') );
        add_filter( 'woocommerce_continue_shopping_redirect', array( $this, 'chotu_change_continue_shopping_redirect') );
        add_filter( 'woocommerce_return_to_shop_redirect', array( $this, 'chotu_change_shop_redirect') );
        add_action( 'wp_head', array( $this, 'chotu_add_wp_head_data') );
        add_action( 'wp_footer', array( $this, 'chotu_add_captain_shop_footer' ), 10 );


    }
    /**
     * matches the URL of 10-digits to a captain
     * if we create a page called 9876543210, it assumes it is a captain page.
     * https://developer.wordpress.org/reference/hooks/author_rewrite_rules/
     */
    public function chotu_rewrite_author_url($author_rewrite) {
        global $wpdb;
        $author_rewrite = array();
        $authors = $wpdb->get_results("SELECT user_login AS username from $wpdb->users");
        foreach ($authors as $author) {
            $author_rewrite["({$author->username})/page/?([0-9]+)/?$"] = 'index.php?author_name=$matches[1]&paged=$matches[2]';
            $author_rewrite["({$author->username})/?$"] = 'index.php?author_name=$matches[1]';
        }
        return $author_rewrite;
    }

    /**
     * to redirect to captain home page when clicked on home if the captain cookie is set
     * https://woocommerce.com/document/customise-the-woocommerce-breadcrumb/
     */
    public function chotu_change_breadcrumb_home_url($home_url) {
        global $chotu_status, $chotu_current_captain;
        switch ($chotu_status) {
            case "B":
            case "C":
                return $chotu_current_captain->user_url;
                break;
            case "A":
            case "D":    
                break;
        }
        return get_site_url(2,'','https');
    }

    /**
     * main woocommerce shop url redirect to /start url
     * /s=pista is the search results page on shop template.
     * https://developer.wordpress.org/reference/hooks/template_redirect/
     */
    public function chotu_redirect_shop_page() {
        if (is_shop() && !isset($_GET['s'])) {
            $url = get_site_url(2,'','https');
            wp_redirect($url, '301');
            exit;
        }
        
        /***
         * if my-shop cookie set and page or url is open redirect to captain shop url
         */
        if (is_page('open') && isset($_COOKIE['my-shop'])) {
            $url = $_COOKIE['my-shop'];
            wp_redirect($url, '301');
            exit;
        }
    }

    /**
     * redirect continue shopping to oye (no cookie) or captain shop(cookie set)
     * https://developer.wordpress.org/reference/hooks/template_redirect/
     */
    public function chotu_change_continue_shopping_redirect($default) {
        global $chotu_status, $chotu_current_captain;
        if ($chotu_status == "B") {
            return $chotu_current_captain->user_url;
        } else {
            return get_site_url(2,'','https');
        }
    }

    /**
     * redirect return to shop to oye (no cookie) or captain shop(cookie set)
     * https://developer.wordpress.org/reference/hooks/template_redirect/
     */
    public function chotu_change_shop_redirect() {
        global $chotu_status, $chotu_current_captain;
        if ($chotu_status == "B") {
            return $chotu_current_captain->user_url;
        } else {
            return get_site_url(2,'','https');
        }
    }

    /**
     * set the OG tags to the captain shop - OG:title, OG:image etc.,
     * if captain is not premium, add the google adsense code in the header.
     */
    public function chotu_add_wp_head_data() {
        
        if(is_author()){
            global $chotu_current_captain;
            $title          =  $chotu_current_captain->display_name;
            $permalink      = $chotu_current_captain->user_url;
            $image          = $chotu_current_captain->captain_cover_pic;
            $description    = $chotu_current_captain->description;
            chotu_set_og_data($title, $permalink, $image, $description );

            /*****************add google adsense script also***************/
            if(!$chotu_current_captain->is_premium()){
                echo chotu_get_option('google_adsense');
            }
        }
    }

    /**
     * add captain shop footer: share, allcat, home and cart/edit
     */
    public function chotu_add_captain_shop_footer(){
        global $chotu_status, $chotu_current_captain;
        switch ($chotu_status) {
            case "A":
            case "D":
                break;
            case "B":
            case "C":
                if(!is_page('bill')){
                    chotu_public_template('captain_shop_footer',array());
                }
                break;  
        }

    }
    
}
add_action('init', 'load_chotu_shop_class');
/**
 * load_chotu_shop_class
 * load class init function by defining class object
 * @return void
 */
function load_chotu_shop_class(){
    $chotu_shop = new Chotu_Shop_Public();
    $chotu_shop->init();
}