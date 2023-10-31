<div class="captain_page_header">
    <div class="cover_pic">
        <img id="bs_cover_pic" src="<?php echo $captain_cover_pic; ?>" class="img-responsive" alt="Image">
        <?php
    if (is_captain_logged_in()) {?>
        <input type="file" name="bs_cover_pic" class="opacity_disable" accept="image/gif, image/jpeg, image/png">
        <i class="fa-solid fa-camera bs_cover_pic" style="color: var(--secondary);float: right;"></i>
        <?php }
    ?>
    </div>
    <div class="profile_pic">
        <img id="bs_profile_pic" src="<?php echo $captain_display_pic; ?>" class="img-responsive" alt="Image">
        <?php
    if (is_captain_logged_in()) {?>
        <input type="file" name="bs_profile_pic" class="opacity_disable" accept="image/gif, image/jpeg, image/png">
        <i class="fa-solid fa-camera bs_profile_pic" style="color: var(--secondary);"></i>
        <?php 
    }
    ?>
    </div>
</div>
<br />