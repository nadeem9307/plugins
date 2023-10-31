<?php
/**
 * Author Page (CAPTAIN) front-end
 */

get_header();
global $chotu_current_captain;

$curauth = (isset($_GET['author_name'])) ? get_user_by('slug', $author_name) : get_userdata(intval($author));
$captain_id = $curauth->ID;
$subscription = chotu_get_captain_subscription($captain_id);
if(empty($subscription)){
    $url = get_site_url(2,'/shop','https');
    //wp_redirect( $location:string, $status:integer, $x_redirect_by:string )
    echo '<script>window.location.href = "'.$url.'";</script>';
}
$captain_cover_pic = $chotu_current_captain->captain_cover_pic;
if (empty($captain_cover_pic)) {
    $captain_cover_pic = chotu_get_option('captain_default_cover_pic');
}

$captain_display_pic = $chotu_current_captain->captain_display_pic;
if (empty($captain_display_pic)) {
    $captain_display_pic = chotu_get_option('captain_default_display_pic');
}

$captain_gallery = $chotu_current_captain->captain_gallery ? $chotu_current_captain->captain_gallery : array();

$active = 'active';
include_once 'captain-shop/css.php';
include_once 'captain-shop/header.php';
?>
<div class="row page-wrapper">
    <div id="content" class="large-12 col" role="main">
        <?php
        include_once 'captain-shop/cta_pwa.php';
        include_once 'captain-shop/cta_upgrade.php';
       ?>
        
        <div class="col medium-12 large-12">
            <div class="col-inner">
                <div class="tabbed-content">
                    <?php include_once 'captain-shop/tabs.php';?>
                    <div class="tab-panels">
                        <div class="panel entry-content <?php echo $active; ?>" id="shop">
                            <?php  include_once 'captain-shop/tab_shop.php';?>
                        </div>
                        <div class="panel entry-content" id="offers">
                            <div class="text-inner">
                                <div class="woocommerce-input-wrapper">
                                    <?php
                                if (count($captain_gallery) < 20 && is_captain_logged_in()) {
                                    echo do_shortcode('[block id="dnd-captain-upload-message"]<br>');
                                    ?>
                                    <input type="file" class="input-text " name="captain_gallery[]" multiple
                                        id="captain_gallery">
                                    <div id="captain_gallery_render"></div>
                                    <?php
                                    }
                                    ?>
                                </div>
                                <?php include_once 'captain-shop/tab_gallery.php'; ?>
                                <!-- </p> -->
                            </div>
                        </div>
                        <div class="panel entry-content" id="hello">
                            <?php include_once 'captain-shop/tab_hello.php'?>
                        </div>
                        <div class="panel entry-content" id="add">
                            <?php include_once 'captain-shop/tab_add.php'; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php include_once 'captain-shop/notice_offers.php';?>
    </div>
</div>
<?php 
do_action('flatsome_after_page');
get_footer();
include_once 'captain-shop/js.php';