<div class="cover_pic">
    <img src="<?php echo $args['image'];?>" class="img-responsive" alt="Image" data-id="<?php echo $args['term']->term_id?>">
</div>
<div class="category_top_details">
    <h1><?php echo $args['term']->name;?></h1>
    <?php echo $args['term']->description;?>
</div>