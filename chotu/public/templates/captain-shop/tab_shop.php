<?php
if($chotu_current_captain->is_premium()){ 
    echo apply_filters('the_content', $chotu_current_captain->captain_premium_HTML);
    echo '<div id="allcat"><BR><BR><BR>';
    $chotu_current_captain->show_favorite_categories();
    echo '</div>';
}else{
    echo apply_filters('the_content', $chotu_current_captain->captain_free_html);
    echo '<div id="allcat"><BR><BR><BR>';
    echo do_shortcode( '[block id="end-user-sorry-message"]' );
    echo '</div>';
}
?>
