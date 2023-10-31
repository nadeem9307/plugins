<?php
if (!defined('WPINC')) {
    die;
}

add_shortcode('rootshopHTML', 'chotu_rootshop_shortcode');
/**
 * chotu_rootshop_shortcode
 *
 * @param  mixed $atts
 * @param  mixed $content
 * @return void
 */
function chotu_rootshop_shortcode($atts, $content = null)
{
    global $post;

    extract(shortcode_atts(array(
        'id' => '',
    ),
        $atts
    )
    );
    // Abort if ID is empty.
    if (empty($id)) {
        return '<p><mark>No Rootshop ID is set</mark></p>';
    }
    $the_post = chotu_get_cpost($id, 'rootshop');
    $html = '';
    if ($the_post) {
        $html = get_post_meta($the_post->ID, 'rootshopHTML', true);
    }
    return do_shortcode($html);
}
add_shortcode('rootshopStart', 'chotu_start_rootshop_shortcode');
/**
 * chotu_start_rootshop_shortcode
 *
 * @param  mixed $atts
 * @return void
 */
function chotu_start_rootshop_shortcode($atts)
{
    $id = $atts['id'];
    
    // Abort if ID is empty.
    if (empty($id)) {
        return '<p><mark>No Rootshop ID is set</mark></p>';
    }
    
    // Abort if ID does not belong to a rootshop.
    if (get_post_type($id) != 'rootshop'){
        return '<p><mark>There is no shop for this ID</mark></p>';
    }
    
    $rootshop = get_post($id);

    $captain_onbn = chotu_append_isd_code(get_option('captain_onboarding_number'));
    $wa_link = "https://wa.me/". $captain_onbn ."/?text=start%20".  $rootshop->post_name. "%20";
    echo    '<a class="" href="#language-selector-'. $rootshop->ID .'">
                <div style="width: 100%; height:10%; 
                position: fixed; bottom: 0; right:0;
                background-color: #FFFFFF;
                z-index: 9999999999999999;
                display: flex; justify-content: center;">
                    <img src="https://chotu.com/wp-content/uploads/start.png" alt="start"/>
                </div>
            </a>';

    ob_start();
    echo '<div id="language-selector-'. $rootshop->ID .'"" class="lightbox-by-id lightbox-content lightbox-white mfp-hide" style="max-width:600px ;padding:20px"> 
    <h3>Select local language</h3>
    <style>
    .grid-container {
        display: grid;
        grid-template-columns: 50% 50%;
        padding: 1rem;
    }
    .card {
        background-color: var(--primary);
        color: white;
        padding: 0.5rem;
        height: auto;
        margin: 0.5rem;
        text-align: center;
        border-radius: 5px;
    }
    </style>
    <div class="grid-container">
    <a href="'.$wa_link.'hi"><div class="card">हिन्दी</div></a>
    <a href="'.$wa_link.'bn"><div class="card">বাংলা</div></a>
    <a href="'.$wa_link.'mr"><div class="card">मराठी</div></a>
    <a href="'.$wa_link.'te"><div class="card">తెలుగు</div></a>
    <a href="'.$wa_link.'ta"><div class="card">தமிழ்</div></a>
    <a href="'.$wa_link.'gu"><div class="card">ગુજરાતી</div></a>
    <a href="'.$wa_link.'kn"><div class="card">ಕನ್ನಡ</div></a>
    <a href="'.$wa_link.'ml"><div class="card">മലയാളം</div></a>
    <a href="'.$wa_link.'pa"><div class="card">ਪੰਜਾਬੀ</div></a>
    <a href="'.$wa_link.'en"><div class="card">English</div></a>
    </div>

    <div>
       '.do_shortcode('[block id="dnd-opens-wa-message-onboard"]').'
        
    </div></div>';
    ;
    $output = ob_get_clean();
    return $output;
}

add_shortcode('CallWAShare','chotu_call_and_whatsapp_share');
function chotu_call_and_whatsapp_share(){
    ob_start();
    global $chotu_status, $chotu_current_captain;
    if($chotu_status == "B"){
        $captain = get_user_by('id',$chotu_current_captain);
            if(!empty($captain)){
            ?>
            <!-- <p class="cta_button" style="text-align:center;">
                
            </p> -->
            <p class="cta_button" style="text-align:center;">
                <a href="?download_vcard=true" id="" style="display:inline;"><i class="fa-solid fa-user-plus"></i></a>&nbsp;&nbsp;
                <a style="display:inline;" href="tel:<?php echo $captain->user_login;?>">
                    <i class="icon-phone" style="color:#ffffff;"></i>
                </a>&nbsp;&nbsp;
                <a style="display:inline;" href="whatsapp://send?phone=91<?php echo $captain->user_login; ?>&text=🙏%0a<?php echo $captain->display_name;?>%0a<?php echo $captain->user_url;?>%0a">
                    <i class="icon-whatsapp" style="color:#ffffff;"></i>
                </a>
            </p>
            <?php
        }
    }
    $output = ob_get_clean();
    return $output;
    
}