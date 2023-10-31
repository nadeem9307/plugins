<?php
  if (is_captain_logged_in()) {
    $up_btn = '';
    $subscription = chotu_get_captain_subscription($captain_id);
    
    if(!empty($subscription)){
      $subs_product_id = chotu_get_subscription_product_id($subscription->ID);
      switch_to_blog(2);
          $subs_exist = get_post($subs_product_id);
      restore_current_blog();
      if($subs_exist){
        $up_btn = '<a href="'.get_site_url(2).'/?subscription_id='.$subscription->ID.'" class="button primary" style="padding: 0 1rem; margin:auto; border-radius: 99px; max-width:60%; display: inherit;">Upgrade Now</a><br >' ;
      }
    }
    if ($chotu_current_captain->is_premium() == false){
      
      echo do_shortcode( '<div class="site-notice" style="background-color: var(--white);">
      <div class="site-notice-dismiss" style="transform:translateY(-130%) translateX(1250%)"><i class="fa-regular fa-2xl fa-circle-xmark"></i></div><p>[block id="captain-upgrade-message"]</p></div>' );
      echo '<div style="text-align: center;"><p>Your premium plan ends in :</p>Expired</div>';
      echo $up_btn;
    }else{
      switch_to_blog(2);
      $next_payment_date = get_post_meta($subscription->ID,'_schedule_next_payment',true);
      restore_current_blog();
      if($next_payment_date !=0 && $next_payment_date !=''){
        
        if (strtotime($next_payment_date) < strtotime("+7 day")){
          echo '<input type="hidden" id="next_payment_date" name="next_payment_date" value="'.$next_payment_date.'">';
          echo do_shortcode( '<div class="site-notice" style="background-color: var(--white);">
          <div class="site-notice-dismiss" style="transform:translateY(-130%) translateX(1250%)"><i class="fa-regular fa-2xl fa-circle-xmark"></i></div><p>[block id="captain-upgrade-message"]</p></div>' );
          echo '<div style="text-align: center;">Your premium plan ends in:</div><div id="countdown_timer" class=""></div>';
          echo $up_btn;
        }
      }
    }
  }
?>