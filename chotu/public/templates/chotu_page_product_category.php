<!-- template to load image and title on product category page -->
<?php 
global $chotu_current_captain;
?>
<div class="cover_pic">
    <img src="<?php echo $args['image'];?>" class="img-responsive" alt="Image"
        data-id="<?php echo $args['term']->term_id?>">
</div>
<div class="category_top_details">
    <h1><?php echo chotu_get_title( 'term', $chotu_current_captain->captain_language, ($args['term']->term_id));?></h1>
    <?php echo $args['term']->description;?>
</div>