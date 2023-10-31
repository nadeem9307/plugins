<?php
/*
Author Page (CAPTAIN) front-end
*/
get_header();

global $wp,$chotu_current_captain;
  $curauth = (isset($_GET['author_name'])) ? get_user_by('slug', $author_name) : get_userdata(intval($author));
  $captain_id                       = $curauth->ID;
  $captain_phone_number             = $curauth->user_login;
  $captain_cover_pic                = get_user_meta($captain_id,'captain_cover_pic',true);
    if(empty($captain_cover_pic)){
      $captain_cover_pic     = get_option('captain_default_cover_pic');
    }
  $captain_display_pic              = get_user_meta($captain_id,'captain_display_pic',true);
    if(empty($captain_display_pic)){
      $captain_display_pic    = get_option('captain_default_display_pic');
  }
  $captain_announcement             = get_user_meta($captain_id,'captain_announcement', true);

  $captain_shop_feed_history        = get_user_meta($captain_id,'captain_shop_feed_history',true);
  $captain_shop_feed_oncreate       = get_user_meta($captain_id,'captain_shop_feed_oncreate',true);
  
  $captain_description              = get_user_meta($captain_id,'description', true);
  $captain_display_address          = get_user_meta($captain_id,'captain_display_address', true);
  
  
  $captain_gallery                  = unserialize(get_user_meta($captain_id, 'captain_gallery',true)) ? unserialize(get_user_meta($captain_id, 'captain_gallery',true)) : array();

?>
<style>
  .nav-tabs+.tab-panels {
    border: unset;
    padding: 5px 4px 0px 0px;
  }
  .tabbed-content .nav-tabs>li>a {
    padding-left: 2.6rem!important;
    padding-right: 2.6rem!important;
  }
  .tabbed-content .nav>li>a {
    font-size: 1rem;
}
.opacity_disable {
    opacity: 0;
    position: absolute;
    left: 0;
    right: 0;
}
.bs_profile_pic {
    position: absolute;
    background-color: white;
    padding: 7px;
    border-radius: 40px;
    border: 2px solid #ffffff;
    top: 50px;
    right: -11px;
    cursor: pointer;
}
.bs_cover_pic {
    position: absolute;
    right: 0;
    bottom: 0;
    background-color: white;
    padding: 7px 8px;
    border-radius: 50px;
    cursor: pointer;
    z-index: 9;
}
.remove_gallery{
  position: absolute;
  top: -9px;
  right: 0;
  bottom: 0;
  z-index: 99;
  cursor: pointer;
}
.image_object_cover,.mfp-figure, img.mfp-img{
  object-fit: cover;
  aspect-ratio: 9/16;
}
.cta_button a, .cta_button_border a {
    padding: 0.1rem 1rem !important;
}
</style>
<div class="captain_page_header">
<div class="cover_pic">
    <img id="bs_cover_pic" src="<?php echo $captain_cover_pic;?>" class="img-responsive" alt="Image">
    <?php 
    if(chotu_check_is_captain()){ ?>
    <input type="file" name="bs_cover_pic" class="opacity_disable" accept="image/gif, image/jpeg, image/png">
      <i class="fa-solid fa-camera bs_cover_pic" style="color: var(--secondary);float: right;"></i>
    <?php }
    ?>
</div>  
<div class="profile_pic">
  <img id="bs_profile_pic" src="<?php echo $captain_display_pic;?>" class="img-responsive" alt="Image">
  <?php 
  if(chotu_check_is_captain()){ ?>
  <input type="file" name="bs_profile_pic" class="opacity_disable" accept="image/gif, image/jpeg, image/png">
    <i class="fa-solid fa-camera bs_profile_pic" style="color: var(--secondary);"></i>
  <?php }
  ?>
</div>
</div>
<div class="row page-wrapper">
<div id="content" class="large-12 col" role="main">
  <div class="overlay-tools">
    <?php if($captain_announcement){
       echo '<marquee>'.$captain_announcement.'</marquee>';
    } ?>
   
  </div>
  <div id="col-760104850" class="col medium-12 large-12">
   <div class="col-inner">
      <div class="tabbed-content">
         <ul class="nav nav-tabs nav-uppercase nav-size-normal nav-left">
            <li class="tab has-icon"><a class="about_icons" href="#hello"><span style="color: var(--primary);"><i class="fa-solid fa-user hide"></i> <i class="fa-regular fa-user"></i></span></a></li>
            <li class="tab has-icon active"><a class="shop_icons" href="#shop"><span style="color: var(--primary);"><i class="fa-solid fa-face-smile"></i><i class="fa-regular fa-face-smile hide"></i></span></a></li>
            <li class="tab has-icon"><a class="offers_icons" href="#offers"><span style="color: var(--primary);"><i class="fa-solid fa-circle-check hide"></i> <i class="fa-regular fa-circle-check"></i></span></a></li>
         </ul>
         <div class="tab-panels">
            <div class="panel entry-content" id="hello">
              <div class="text-inner text-center">
                <div class="description">
                  <?php echo apply_filters('the_content', $captain_description, true);?>
                  <div class="is-divider divider clearfix"></div>
                  <p><?php
                    if($captain_display_address){
                      echo $captain_display_address;
                    }else{
                      echo apply_filters('the_content', do_shortcode('[block id="dnd-captain-default-address"]'));
                    }
                   ?></p>
                  <!-- <p><?php //echo $captain_pincode;?></p> -->
                </div>
              </div>
            </div>
            <div class="panel entry-content active" id="shop">
                <?php
                $favorites = get_user_favorites($captain_id, $site_id=0);
                chotu_show_favorites($favorites);
                echo apply_filters('the_content', $captain_shop_feed_history, true);
                echo apply_filters('the_content', $captain_shop_feed_oncreate, true);
                  ?>
              <!-- </div> -->
            </div>
            <div class="panel entry-content" id="offers">
              <div class="text-inner">
                <h1 class="uppercase"><?php //echo _e('Offers');?></h1>  
                <!-- <p class="form-row form-row-first" id="captain_cover_pic_field" data-priority=""> -->
                  <div class="woocommerce-input-wrapper">
                  <?php
                      if(sizeof($captain_gallery) < 5 && chotu_check_is_captain()){
                        echo do_shortcode('[block id="dnd-captain-upload-message"]<br>');
                        ?>
                        <input type="file" class="input-text " name="captain_gallery[]" multiple id="captain_gallery">
                        <div id="captain_gallery_render"></div>
                      <?php
                      }
                      ?>
                  </div>
                  <div class="image_gallery row" id="">
                    <?php
                      if(!empty($captain_gallery)){
                        foreach ($captain_gallery as $key => $gallery) {
                          $image = wp_get_attachment_image_url($gallery,'full');
                          if($image){
                            ?>
                            <div id="col-<?php echo $gallery;?>" class="col medium-6 small-12 large-6">
                              <div class="col-inner">
                                <div class="img has-hover x md-x lg-x y md-y lg-y" id="image_<?php echo $gallery;?>">
                                <?php if(chotu_check_is_captain()){?><span class="primary-color remove_gallery" onClick="RemoveImage(<?php echo $gallery;?>)"><i class="fa-regular fa-circle-xmark"></i></span><?php }?>
                                <a class="image-lightbox lightbox-gallery" auto_open="true" auto_timer="0" title="" href="<?php echo $image;?>">
                                  <div class="img-inner image-glow dark">
                                    <img class="image_object_cover" src="<?php echo $image;?>" >
                                  </div>
                                </a>
                                </div>
                              </div>
                            </div>
                          <?php
                          }
                        }
                      }
                    ?>
                  </div>
                <!-- </p> -->
              </div>
            </div>
          </div>
        </div>
      </div>
    </div> 
  </div>
</div>
<?php do_action( 'flatsome_after_page' ); ?>
<?php get_footer(); ?>
<script>
  jQuery('.bs_profile_pic').on('click',function(){
			console.log('sda');
			jQuery('input[name="bs_profile_pic"]').trigger('click');
	});
 
  
  jQuery('.bs_cover_pic').on('click',function(){
    console.log('bs_cover_pic');
    jQuery('input[name="bs_cover_pic"]').trigger('click');
  });
  jQuery('input[name="bs_profile_pic"]').change(function () {
  //console.log(this.files);
    var reader = new FileReader();
    reader.onload = function (e) {
    jQuery('#bs_profile_pic').attr('src', e.target.result) ;
    };
    reader.readAsDataURL(this.files[0]);
    ChotuUploadImage(this.files[0],'captain_display_pic');
  });
	
  jQuery('input[name="bs_cover_pic"]').change(function () {
  //console.log(this.files);
    var reader = new FileReader();
    reader.onload = function (e) {
    jQuery('#bs_cover_pic').attr('src', e.target.result) ;
    };
    reader.readAsDataURL(this.files[0]);
    ChotuUploadImage(this.files[0],'captain_cover_pic');
  });
  jQuery('#captain_gallery').change(function(e){
    var file_array = [];
    var file = this.files[0];
    var current_length  = jQuery('.image_gallery > div').length;
    var validImageTypes = ["image/gif","image/png", "image/jpeg", "image/webp","image/jpg"];
    var input           = document.getElementById('captain_gallery');
    var total_length    = parseInt(input.files.length)+ parseInt(current_length);
    console.log(total_length);
    if(total_length > 5){
      alert('☹️ Upload Max 5 Files allowed');
      input.value = '';
      return false;
    }else{
      for (var i = 0; i < input.files.length; i++) {
        console.log(e.target.files[i]['type']);
        if (jQuery.inArray(e.target.files[i]['type'], validImageTypes) < 0) {
            alert('☹️ File type not supported!');
            window.location.reload(true);
            return false;
        }
      (function(input) {
        var img = document.createElement("img");
        var reader = new FileReader();
        reader.onload = function(e) {
          img.src = e.target.result;
          document.getElementById('captain_gallery_render').appendChild(img);
        }
        reader.readAsDataURL(file);
        file_array[i] = e.target.files[i];
      })(e.target.files[i]); // pass current File to closure 
    }
      // let reader = new FileReader();
      // reader.onload = function (e) {
      // jQuery('#captain_gallery').attr('src', e.target.result) ;
      // };
      //reader.readAsDataURL(file);
    }
    ChotuUploadImage(file_array,'captain_gallery');
  });
  function ChotuUploadImage(files,image){
    var gl_flag = false;
    let captain_gallery = ['gallery'];
    var formdata = new FormData();
    if(jQuery.isArray(files)){
      for (var i = 0; i < files.length; i++) {
        gl_flag = true;
        formdata.append('captain_gallery[]', files[i]);
      }
    }else{
      formdata.append(image, files);
    }
    formdata.append("action", "chotu_update_captain_cp_and_pp");
    jQuery.ajax({
      type:"post",
      contentType: false,
      processData: false,
      url: flatsomeVars.ajaxurl,
      data:formdata,
      success: function(response) {
        alert(response.data.message);
        if(window.location.hash == ''){
          if(gl_flag){
            window.location.href=window.location.href+'#offers';
          }
          window.location.href=window.location.href;
        }
        window.location.reload(true);
      },
      error: function(XMLHttpRequest, textStatus, errorThrown) { 
        alert('something went wrong !')
      } 

    });
  }
  function RemoveImage(attachment_id){
    jQuery(this).prev().remove();
    jQuery(this).remove();
    console.log(attachment_id);
    jQuery.ajax({
      type:"post",
      url: flatsomeVars.ajaxurl,
      data:{'attachment_id':attachment_id,'action':'chotu_remove_gallery_image'},
      success: function(response) {
        alert(response.data.message);
        jQuery('#col-'+attachment_id).remove();
        window.location.reload(true);
      },
      error: function(XMLHttpRequest, textStatus, errorThrown) { 
        alert('something went wrong !')
      }
    });
  }
  </script>
  <?php
  if(!chotu_check_is_captain()){ ?>
    <script>
      jQuery('.offers_icons').on('click',function(){
        console.log(jQuery('.image_gallery .col-inner a:first').attr('href'));
        jQuery('.image_gallery .col-inner a:first').trigger('click');
      });
    </script>
  <?php }
  ?>