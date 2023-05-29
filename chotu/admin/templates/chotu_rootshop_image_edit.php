<?php
    ?>
      <tr class="form-field term-group-wrap">
    <th scope="row">
      <label for="thumbnail_id"><?php _e( 'Image', 'chotu_main' ); ?></label>
    </th>
    <td>
   	<?php $image_id = get_term_meta ( $term->term_id, '_thumbnail_id', true ); ?>
      <input type="hidden" id="thumbnail_id" name="thumbnail_id" value="<?php echo $image_id; ?>">
      <div id="thumbnail_id-wrapper">
      
        <?php if ( $image_id ) { ?>
          <?php echo wp_get_attachment_image ( $image_id, 'thumbnail' ); ?>
        <?php } ?>
      </div>
      <p>
        <input type="button" class="button button-secondary product_tag_media_button" id="product_tag_media_button" name="product_tag_media_button" value="<?php _e( 'Add Image', 'chotu_main' ); ?>" />
        <input type="button" class="button button-secondary code_media_remove" id="code_media_remove" name="code_media_remove" value="<?php _e( 'Remove Image', 'chotu_main' ); ?>" />
      </p>
    </td>
  </tr>
  