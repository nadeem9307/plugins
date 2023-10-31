<div class="image_gallery row" id="">
    <?php
    if (!empty($captain_gallery)) {
        foreach ($captain_gallery as $key => $gallery) {
            $image = wp_get_attachment_image_url($gallery, 'full');
            if ($image) {
                ?>
                <div id="col-<?php echo $gallery; ?>" class="col medium-6 small-12 large-6">
                    <div class="col-inner">
                        <div class="img has-hover x md-x lg-x y md-y lg-y"
                            id="image_<?php echo $gallery; ?>">
                            <!-- if captain logged in, they can delete the image -->
                            <?php if (is_captain_logged_in()) {?><span
                                class="primary-color remove_gallery"
                                onClick="ChotuRemoveImage(<?php echo $gallery; ?>)"><i
                                    class="fa-regular fa-2xl fa-circle-xmark"></i></span><?php }?>
                            <a class="image-lightbox lightbox-gallery" auto_open="true"
                                auto_timer="0" title="" href="<?php echo $image; ?>">
                                <div class="img-inner image-glow dark">
                                    <img class="image_object_cover" src="<?php echo $image; ?>">
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
                <?php
            }
        }
    }
    ?>
</div>