<!-- template to load title on product tag page -->
<?php 
global $chotu_current_captain;
?>
<div class="tag_top_details">
    <h1 style="padding-top:10%;"><?php echo chotu_get_title( 'term', $chotu_current_captain->captain_language, ($args['term']->term_id));?>
    </h1>
    <?php echo $args['term']->description;?>
</div>