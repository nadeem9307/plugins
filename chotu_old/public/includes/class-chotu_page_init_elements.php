<?php
use JeroenDesloovere\VCard\VCard;
class Chotu_Page_Init_Elements{
    public function __construct(){
      /**
       * https://developer.wordpress.org/reference/hooks/wp/
       */
      add_action( 'wp', array($this, 'chotu_set_captain_cookie' ), 1);
      add_action( 'wp', array($this, 'chotu_check_status' ), 2);
      add_action( 'wp', array($this, 'chotu_set_shop_url' ), 3);
      add_action( 'wp', array($this, 'chotu_whatsapp_login' ), 4);
      /**
       * https://developer.wordpress.org/reference/hooks/wp_login/
       */
      add_action( 'wp_login', array($this, 'chotu_captain_login' ), 10, 2);
      add_action( 'wp', array($this, 'chotu_restrict_captain' ), 6);
    }

    // when a user visits a captain shop:
    // 1. unset the previous captain cookie first 
    // 2. set it when the captain shop is opened through URL
    // $_GET['captain'] = /?captain= (URL prameter)
    public function chotu_set_captain_cookie(){
      global $current_user, $wp, $wp_query, $_COOKIE;
      $current_page = get_queried_object();
      //if URL has captain_ID AND captain is NOT logged in
      if(isset($_GET['captain']) && !is_user_logged_in()){
          if(chotu_get_captain_id($_GET['captain'])){             //if the captain in URL param is valid captain
            setcookie('captain', '', time() - 3600, '/');                  //delete the cookie
            setcookie( 'captain', $_GET['captain'],(time()+259200), "/"); // sets new cookie
            $_COOKIE['captain'] = $_GET['captain'];               // set captain cookie in global
            chotu_check_cart_captain($_GET['captain']);
            chotu_set_visited_captain_shop_history();                           // check if captain changed, delete wishlist, create new
         }
      }else if(isset($current_page->user_login) && !is_user_logged_in()){ // captain shop page, captain NOT logged in
        if(in_array('captain',$current_page->roles)){
          $user_login = $current_page->user_login;
          setcookie('captain', '', time() - 3600, '/');
          setcookie( 'captain', $user_login,(time()+259200), "/");
          $_COOKIE['captain'] = $user_login;
          chotu_check_cart_captain();
          chotu_set_visited_captain_shop_history($user_login);
        }  
        
      }
    }


    // to set the chotu status and unset the captain cookie on captain login
    public function chotu_check_status(){
      global $chotu_status,$current_user,$chotu_current_captain;
      $captain = false;
      if(is_user_logged_in()){
        $user_roles = $current_user->roles;
        if(in_array('captain',$user_roles)){
          $captain = true;
        }
      }
      if(is_user_logged_in()){
        if($captain){
          $chotu_status = "C";
          $chotu_current_captain = $current_user->ID;
        }else{
          $chotu_status = "D";
          $chotu_current_captain = "";
        }
      }else{
        if(isset($_COOKIE['captain'])){
          if(chotu_get_captain_id($_COOKIE['captain'])){
            $chotu_status = "B";
            $chotu_current_captain = chotu_get_captain_id($_COOKIE['captain']);
          }else{
            $chotu_status = "A";
            $chotu_current_captain = "";
            chotu_reset_captain();
            
          }
        }else{
          $chotu_status = "A";
          $chotu_current_captain = "";
        }
      }
    }


    // the login URL is validated and captain is redirected to home page on login
    public function chotu_whatsapp_login(){
        /* create author nonce for login and redirect to my-account page*/ 
        if(isset($_GET['auth']) && (isset($_GET['mynonce']))) {
            $user = get_user_by('login', $_GET['auth']);
            $verify_nonce = get_user_meta($user->ID,'verify_nonce',true);
            $verify_nonce_expiry = get_user_meta($user->ID,'verify_nonce_expiry',true);
            $date = date('Y-m-d H:i:s');
            if ($verify_nonce == $_GET['mynonce'] && $verify_nonce_expiry >= $date) {
              wp_clear_auth_cookie();
              delete_user_meta($user->ID,'verify_nonce',$verify_nonce);
              delete_user_meta($user->ID,'verify_nonce_expiry',$verify_nonce_expiry);
              wp_set_current_user( $user->ID, $user->user_login );
              wp_set_auth_cookie( $user->ID,true );
              do_action( 'wp_login', $user->user_login, $user );
              wp_redirect( home_url('/').$user->user_login.'/?success=true' );
              exit;
            }
            wp_redirect( home_url() .'?error=session_timeout');
            //exit;
        }
        
    }


    // to remove the "author" mentioned in the captain shop URL
    public function chotu_set_shop_url() {
        global $wp_rewrite,$current_user;
        if (is_admin()){
            /* make the /author/ base as null*/ 
            if( 'author' == $wp_rewrite->author_base ){
            flush_rewrite_rules();
            $wp_rewrite->author_base = null;
            } 
        }else if( 'author' == $wp_rewrite->author_base ) {
            flush_rewrite_rules();
            $wp_rewrite->author_base = null;
        }
    }
    // to set unset the captain cookie on captain login and redirect to captain home page
    public function chotu_captain_login($user_login, $user){
      global $current_user,$chotu_status;
        // $WCWL_Session = new YITH_WCWL_Session();
        // $WCWL_Session->forget_session();      // on captain login, delete the wishlist cookie
        chotu_reset_captain();  // on captain login, delete the captain cookie
        if($user){
          $user_roles = $user->roles;
          if(in_array('captain',$user_roles)){
            $url = home_url('/').$user_login.'/';
            
            wp_safe_redirect($url);
            exit();
          }
        }
    }
    //  to restrict a logged in captain from opening a different captain shop and redirect to his/her home page
    public function chotu_restrict_captain(){
        global $chotu_current_captain, $current_user,$chotu_status;
        switch ($chotu_status) {
          case "A":
            break;
          case "B":
            break;
          case "C":
            $current_page = get_queried_object();
            if(isset($current_page->user_login)){
              if($current_user->user_login != $current_page->user_login){
                $url = home_url('/').$current_user->user_login.'/';
                wp_safe_redirect($url);
                exit();
              }
            }
            break;
          case "D":
            break;
        }
    }
}
new Chotu_Page_Init_Elements();
add_action('init','chotu_process_URLparams');
function chotu_process_URLparams(){
  if(isset($_GET['error'])){
    $captain_onbn = get_option('captain_onboarding_number');
    wc_add_notice('<div style="margin-top:50px">This login link has expired. Please click  👇 <span class="cta_button"><a class="show-for-small" href="https://wa.me/'.$captain_onbn.'/?text=open">Open</a></span></div>','notice');
  }
  if(isset($_GET['success'])){
    wc_print_notice( __("Your login has been successful", "woocommerce"), "success" );
  }
  if(isset($_GET['reset_cookie'])){ 
    chotu_reset_captain();
  }
  if(isset($_GET['download_vcard'])){
        // define vcard
        ob_get_clean();
        if(isset($_COOKIE['captain'])){
          ob_get_clean();
          $vcard 	= new VCard();
          $user 	= get_user_by('login',$_COOKIE['captain']);
          // add personal data
          $vcard->addName($user->display_name);
          $vcard->addNote('chotu');
          $vcard->addPhoneNumber($user->user_login, 'WORK');
          $vcard->addURL($user->user_url);
          $vcard->getOutput();
          echo $vcard->download();
          exit();
        }
        //echo $vcard;

  }
}