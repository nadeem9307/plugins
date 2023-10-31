<div class="text-inner text-center" style="padding-top: 1rem;">
<?php       
    echo do_shortcode( '[generate_qr url="'.$chotu_current_captain->user_url.'"]' );
    $chotu_current_captain->show_contact();
    ?>
    <div class="is-divider divider clearfix"></div>
        <?php echo apply_filters('the_content', $chotu_current_captain->description, true); ?>
    <div class="is-divider divider clearfix"></div>
    <p><?php
        echo $chotu_current_captain->captain_display_address;
        ?>
    </p>
</div>