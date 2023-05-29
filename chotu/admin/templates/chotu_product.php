<?php
$post = $term;
wp_nonce_field( basename( __FILE__ ), 'product_meta_box_nonce' );

?>
<div class="form-field">
    <h4 for="product_keywords"><?php echo _e('Search Keywords','chotu');?>&nbsp;: </h4>
    <textarea rows="2" cols="90" name="product_keywords" id="product_keywords"><?php echo get_post_meta( $post->ID, 'product_keywords', true );?></textarea>
</div>