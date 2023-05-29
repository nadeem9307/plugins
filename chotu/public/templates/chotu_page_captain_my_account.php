<?php
/*
// shows the location access button in captain/my-account/edit-account page.
*/ 
  if(get_user_meta($args['user_id'],'captain_lat_long',true) == ""){
    ?>
    <p class="form-row form-row-last" data-priority="">
     <button type="button" class="woocommerce-Button button" onclick="getLocation()"><i class="fa-solid fa-location-dot"></i></button>
  </p>
  <?php
  $location_access_message = get_option('location_access_message');
  echo '<script>alert("'.$location_access_message.'")</script>';
}
?>


