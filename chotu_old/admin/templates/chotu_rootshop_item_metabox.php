<?php global $post;

$rootshopHTML = get_post_meta( $post->ID, 'rootshopHTML', true );
$rootshop_default_description = get_post_meta( $post->ID, 'rootshop_default_description', true );
?>
<div class="form-field">
    <h4 for="rootshopHTML"><?php echo _e('Rootshop HTML','chotu');?>&nbsp;:</h4>
    <?php wp_editor( $rootshopHTML, 'rootshopHTML' );?>
</div>

<div class="form-field">
    <h4 for="rootshop_default_description"><?php echo _e('Rootshop Default Description','chotu');?>&nbsp;: </h4>
    <?php wp_editor( $rootshop_default_description, 'rootshop_default_description' );?>
</div>
<div class="form-field">
    <h4 for="rootshop_default_announcement"><?php echo _e('Rootshop Default Announcement','chotu');?>&nbsp;: </h4>
    <textarea rows="2" cols="80" name="rootshop_default_announcement" id="rootshop_default_announcement"><?php echo get_post_meta( $post->ID, 'rootshop_default_announcement', true )?></textarea>
</div>

<div class="form-field">
    <h4 for="rootshop_keywords"><?php echo _e('Search Keywords','chotu');?>&nbsp;: </h4>
    <textarea rows="2" cols="80" name="rootshop_keywords" id="rootshop_keywords"><?php echo get_post_meta( $post->ID, 'rootshop_keywords', true );?></textarea>
</div>
