<div class="overlay-tools">
    <?php if ($chotu_current_captain->captain_offers) { ?>
    <div class="site-notice" data-id="<?php echo esc_attr( md5( $chotu_current_captain->captain_offers ) ); ?>" style="position: fixed;
    bottom: 10%; z-index: 999;left: 0; right: 0; height: 10%; background-color: #f6f6f6; color: var(--primary);">
        <p><?php echo esc_html( $chotu_current_captain->captain_offers ); ?></p>
        <div class="site-notice-dismiss">
            <i class="fa-regular fa-2xl fa-circle-xmark"></i>
        </div>
    </div>
    <?php }
    ?>
</div>