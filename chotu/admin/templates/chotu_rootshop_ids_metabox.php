<?php global $post;

$rootshop_showSearchKart = get_post_meta( $post->ID, 'rootshop_showSearchKart', true );
$rootshop_Editable = get_post_meta( $post->ID, 'rootshop_Editable', true );
?>

<div class="form-field">
    <h4 for="rootshop_productIDs"><?php echo _e('ProductIDs','chotu');?>&nbsp;: </h4>
    <textarea rows="5" cols="80" name="rootshop_productIDs" id="rootshop_ProductIDs"><?php echo get_post_meta( $post->ID, 'rootshop_productIDs', true )?></textarea>
</div>
<br>
<div class="form-field">
    <p>
        <input type="checkbox" name="rootshop_showSearchKart" id="rootshop_showSearchKart" value="1" <?php echo ($rootshop_showSearchKart == 1) ? 'checked' : ''?>>
        <label for="rootshop_showSearchKart"><?php echo _e('Show Search Bar and Kart Icon','chotu');?>&nbsp; </label>
    </p>
</div>
<br>

<div class="form-field">
    <p>
        <input type="checkbox" name="rootshop_Editable" id="rootshop_Editable" value="1" <?php echo ($rootshop_Editable == 1) ? 'checked' : ''?>>
        <label for="rootshop_Editable"><?php echo _e('Can the Captain edit this shop?','chotu');?>&nbsp; </label>
    </p>
</div>

