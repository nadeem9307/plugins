<?php echo do_shortcode( '[block id="add-product-message"]' );?>
<!-- Add product -->

<div class="text-center" style="padding-top: 2rem;">
    <a  class="btn button primary" href="https://catalog.chotu.app/add-product/?captain=<?php echo $chotu_current_captain->user_login;?>">Add Product</a>
</div>

<?php //acfe_form('add-product'); ?>