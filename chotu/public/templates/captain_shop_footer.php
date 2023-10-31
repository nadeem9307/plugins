<?php
global $chotu_status, $chotu_current_captain;
$url_param = '';
$footer_icon_active = '';
if (!str_contains($_SERVER['REQUEST_URI'], $chotu_current_captain->user_login)){
    $url_param = $chotu_current_captain->user_login;
}
if((is_account_page() && is_wc_endpoint_url('edit-account')) || is_cart()){
    $footer_icon_active = 'footer_active';
}
$captain_shop_edit_url = home_url('/') . 'my-account/edit-account';
$captain_home_url = $chotu_current_captain->user_url;
$share_url = "whatsapp://send?text=" . urlencode(home_url($_SERVER['REQUEST_URI']) . $url_param);?>
<div style="position: fixed; bottom:0; z-index:999; left:0; right:0; height: 10%; background-color: #ffffff; border-top: 0.1rem solid var(--primary);">
    <table style="height: 100%;" class="chotu_footer_icons">
        <tr>
            <td class="footer_icon" style="width:25%; text-align:center; padding: 0!important;">
                <a class="" href="<?php echo $share_url; ?>">
                    <i class="fa-solid fa-share-nodes"
                        style="color: var(--primary); font-size: 2rem; vertical-align: middle;"></i>
                </a>
            </td>
            <td class="footer_icon" style="width:25%; text-align:center; padding: 0!important;">
                <a class="all_cat_link" href="<?php echo $captain_home_url; ?>/#allcat" onclick="ChotuHashRedirect('<?php echo $captain_home_url; ?>/#allcat')">
                    <i class="fa-solid fa-tags"
                        style="color: var(--primary); font-size: 2rem; vertical-align: middle;"></i>
                </a>
            </td>
            <td class="footer_icon" style="width:25%; text-align:center; padding: 0!important;">
                <a class="all_cat_link" href="<?php echo $captain_home_url; ?>/#top" onclick="ChotuHashRedirect('<?php echo $captain_home_url; ?>/#top')">
                    <i class="fa-solid fa-store"
                        style="color: var(--primary); font-size: 2rem; vertical-align: middle;"></i>
                </a>
            </td>
            <td class="footer_icon <?php echo $footer_icon_active;?>" style="width:25%; text-align:center; padding: 0!important;">
                <?php
                if($chotu_status == "C"){?>
                        <a class="" href="<?php echo $captain_shop_edit_url; ?>">
                        <i class="fa-solid fa-user-pen"
                            style="color: var(--primary); font-size: 2rem; vertical-align: middle;"></i>
                    </a>
                    <?php 
                }else{
                    chotu_show_cart_icon();
                }
                ?>
            </td>
        </tr>
    </table>
</div>

