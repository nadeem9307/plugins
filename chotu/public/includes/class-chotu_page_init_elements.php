<?php
use JeroenDesloovere\VCard\VCard;

class Chotu_Page_Init_Elements
{
    public function init()
    {
        /**
         * https://developer.wordpress.org/reference/hooks/wp/
         * https://developer.wordpress.org/reference/hooks/wp_login/
         */
        
        add_action( 'wp', array($this, 'chotu_set_captain_cookie' ), 1);
        add_action( 'wp', array( $this, 'chotu_set_shop_url' ), 3);
        add_action( 'wp', array( $this, 'chotu_check_status' ), 4);
        add_action( 'wp', array( $this, 'chotu_restrict_captain' ), 6);
        add_filter( 'redirect_canonical', array( $this, 'chotu_disable_partial_search' ) );
        add_filter( 'gettext', array( $this, 'chotu_change_translated_text' ), 20, 3);
        
    }
    /**
     * disable all the partial search URLs. 
     * DISABLE THIS: chotu.com/foo redirects to chotu.com/food-delivery
     */
    public function chotu_disable_partial_search($redirect_url) {
        if (is_404()) {
            return false;
        }
        return $redirect_url;
    }

    /**
     * Translate Text
     * change text on different pages:
     *      1. you cannot add another "%s" to your cart. > "%s" is already in your bag.
     *	    2. update cart > update note
    *	    3. Display name > My Shop Name
    */ 
    public function chotu_change_translated_text( $translated, $text, $domain ){
        switch (strtolower($translated)) {
            case 'you cannot add another "%s" to your cart.':
                // product single page
                $translated = '"%s" is already in your shopping bag.';
                break;
            case 'update cart':
                // cart page
                $translated = 'Update note';
                break;
            case 'Display name':
                // my account page, logged in
                $translated = 'My Shop Name';
                break;
            case 'my account':
                // my account page, logged in
                $translated = 'My Shop';
                break;
        }
            return $translated;
    }
    /**
     * chotu_set_captain_cookie
     * when a user visits a captain shop:
     *   1. unset the previous captain cookie first
     *   2. set it when the captain shop is opened through URL like:
     *          * $_GET['captain'] = /?captain= (URL prameter)
     * if user not logged in:
     *      if current page is author page, set captain cookie to the user_login
     *      if url has ?captain= param, set that captain cookie to the user_login
     * @return void
     */
    public function chotu_set_captain_cookie(){
        if(!is_user_logged_in()){
            $current_page = get_queried_object();
            $captain = '';
            if (isset($current_page->user_login)){
                $captain = $current_page->user_login;
            }elseif (isset($_GET['captain'])){
                if (username_exists( $_GET['captain'] )){
                    $captain = $_GET['captain'];
                }
            }
            
            if(!empty($captain)){
                if(isset($_COOKIE['captain'])){
                    if($captain !== $_COOKIE['captain']){
                        chotu_reset_captain();
                    }
                }
                setcookie('captain', $captain, (time() + 259200), "/");
                $_COOKIE['captain'] = $captain;
            }
        }
    }

    /**
     * chotu_check_status
     * to set the chotu status and unset the captain cookie on captain login
     * A: no login, no cookie | Redirect to /start directly
     * B: no login, captain cookie set
     * C: login, captain
     * D: login, admin
     * @return void
     */
    public function chotu_check_status(){
        global $chotu_status, $current_user, $chotu_current_captain;
       
        if (is_user_logged_in()) {
            $user_roles = $current_user->roles;
            if (in_array('captain', $user_roles)) {
                $chotu_status = "C";
                chotu_reset_captain();
                $chotu_current_captain = new Captain_User($current_user->ID);
            } else {
                chotu_reset_captain();
                $chotu_status = "D";
                $chotu_current_captain = new Captain_User($current_user->ID);
            }
        }  else {
            $default_language = get_chotu_default_language();
            $url = get_site_url(2,$default_language,'https');
            if (isset($_COOKIE['captain'])) {
                if ($user = get_user_by( 'login', $_COOKIE['captain'] )) {
                    $chotu_status = "B";
                    $chotu_current_captain = new Captain_User($user->ID);
                } else {
                    $chotu_status = "A";
                    $chotu_current_captain = "";
                    chotu_reset_captain();
                    if(!is_page(array('bill', 'open','my-account','qr'))){
                       
                        wp_redirect($url, 301);
                        exit();
                    }
                }
            } else {
                $chotu_status = "A";
                $chotu_current_captain = "";
                chotu_reset_captain();
                if(!is_page(array('bill', 'open','my-account','qr'))){
                    wp_redirect($url, 301);
                    exit();
                }
            }
        }
    }
    
    /**
     * chotu_set_shop_url
     * to remove the "author" mentioned in the captain shop URL
     * @return void
     */
    public function chotu_set_shop_url(){
        global $wp_rewrite;
            if ('author' == $wp_rewrite->author_base) {
                flush_rewrite_rules();
                $wp_rewrite->author_base = null;
            }
    }
    
    /**
     * chotu_restrict_captain
     * to restrict a logged in captain from opening a different captain shop and redirect to his/her home page    
     * @return void
     */
    public function chotu_restrict_captain(){
        global $current_user, $chotu_status;
        switch ($chotu_status) {
            case "A":
            case "B":
            case "D":
                break;
            case "C":
                $current_page = get_queried_object();
                if (isset($current_page->user_login)) {
                    if ($current_user->user_login != $current_page->user_login) {
                        $url = $current_user->user_url;
                        wp_safe_redirect($url);
                        exit();
                    }
                }
                break;
        }
    }

}
$chotu_init_element = new Chotu_Page_Init_Elements();
$chotu_init_element->init();

/**
 * process the URL params
 * url params not found at the time of init, hence this is outside
 */
add_action('wp', 'chotu_process_URLparams');
function chotu_process_URLparams()
{
    if (isset($_GET['error'])) {
        $captain_onbn = chotu_get_option('captain_onboarding_number');
        wc_add_notice('<div style="margin-top:50px">Your password has changed. Please login by clicking 👇 <BR><span class="cta_button"><a class="show-for-small" href="https://wa.me/' . $captain_onbn . '/?text=open">Login</a></span><BR>Please enter send on WhatsApp.</div>', 'notice');
    }
    if (isset($_GET['success'])) {
        wc_print_notice(__("Your login has been successful", "woocommerce"), "success");
    }
    if (isset($_GET['reset_cookie'])) {
        chotu_reset_captain();
    }
    if (isset($_GET['download_vcard'])) {
        // define vcard
        ob_get_clean();
        if (isset($_COOKIE['captain'])) {
            ob_get_clean();
            $vcard = new VCard();
            $user = get_user_by('login', $_COOKIE['captain']);
            // add personal data
            $vcard->addName($user->display_name);
            $vcard->addNote('chotu');
            $vcard->addPhoneNumber($user->user_login, 'WORK');
            $vcard->addURL($user->user_url);
            $vcard->getOutput();
            echo $vcard->download();
            exit();
        }
    }
}

