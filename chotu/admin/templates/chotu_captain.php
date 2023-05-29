<?php 
$shop = $term;
// $captain_shop_feed_self = get_user_meta($shop->ID,'captain_shop_feed_self',true);
$captain_shop_feed_history = get_user_meta($shop->ID,'captain_shop_feed_history',true);
$captain_shop_feed_oncreate = get_user_meta($shop->ID,'captain_shop_feed_oncreate',true);
$captain_rootshop = get_user_meta($shop->ID,'captain_rootshop',true);
$rootshop = chotu_get_cpost($captain_rootshop,'rootshop');
$captain_language = get_user_meta($shop->ID,'captain_language',true);
?>
<table class="form-table">
<tr>
    <th><label for="captain_shop_feed_history"><?php _e("Captain Shop Feed History"); ?></label></th>
    <td>
      <textarea name="captain_shop_feed_history" rows="5" cols="40" class=""><?php echo $captain_shop_feed_history;?></textarea>
    </td>
</tr>
<tr>
    <th><label for="captain_shop_feed_oncreate"><?php _e("Captain Shop Feed Oncreate"); ?></label></th>
    <td>
       <textarea name="captain_shop_feed_oncreate" rows="5" cols="40" class=""><?php echo $captain_shop_feed_oncreate;?></textarea>
    </td>
</tr>
<tr>
    <th><label for="captain_rootshop"><?php _e("Captain Rootshop"); ?></label></th>
    <td>
    <label for="captain_rootshop"><strong><?php echo $rootshop->post_title.'('.$captain_rootshop.')';?></strong></label>
    </td>
</tr>
<tr>
    <th><label for="captain_language"><?php _e("Captain language"); ?></label></th>
    <td>
    <label for="captain_language"><strong><?php echo $captain_language;?></strong></label>
    </td>
</tr>

</table>