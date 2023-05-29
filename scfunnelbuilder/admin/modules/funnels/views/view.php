<style>
   #wpfooter {
    position: static
   }
   .filter-cat-results .funnel_list_item {
    opacity: 0;
    display: none;
    
    &.active {
        opacity: 1;
        display: block;
        -webkit-animation: fadeIn 0.65s ease forwards;
        animation: fadeIn 0.65s ease forwards;
    }
}
@-webkit-keyframes fadeIn {
    0% { opacity: 0; }
    100% { opacity: 1; }
}

@keyframes fadeIn {
    0% { opacity: 0; }
    100% { opacity: 1; }
}
.listed_funnel_data {
     justify-content: unset !important; 
}
</style>
<div class="sc_funnel_header">
   <div class="">
   <h3><?php echo __('Studiocart Funnel Builder','scfunnelbuilder');?></h3>
   </div>
   <!-- <div class="header_funnel_data">
            <div class="sc_funnel__header_left">
               <img src="<?php echo SC_FUNNEL_URL?>admin/images/icon-45x45-1.png" alt="Image">
            </div>
            <div class="sc_funnel__header_right">
               <div class="stng_btn_hdr">
                  <a href="#">Setting</a>
               </div>
               <div class="header_drp_down_cl">
                     <div class="dropdown_stng">
                        <img onclick="FunnelListDropDown()" class="dropbtn_stng"  src="<?php echo SC_FUNNEL_URL?>admin/images/dot.png"  alt="Image">
                        <div id="myDropdown" class="dropdown-content">
                           <a href="#home">Home</a>
                           <a href="#about">About</a>
                           <a href="#contact">Contact</a>
                        </div>
                     </div>
               </div>
            </div>
   </div>     -->
</div> 
   <div class="dashboard-nav__content">
   <?php
   use SCFunnelbuilder\ScFunnel_functions;
   $url = SC_FUNNEL_TEMPLATE_URL.'collection';
   $response = ScFunnel_functions::remote_get($url);
   $builder = ScFunnel_functions::get_builder_type_id();
   $posts_url = SC_FUNNEL_TEMPLATE_URL.'posts/?builder='.$builder;
   $templates = ScFunnel_functions::remote_get($posts_url);
   // dd($templates );
   $builder_active = ScFunnel_functions::is_any_plugin_missing();
   ?>
   <div id="templates-library">
      <!--------choose templates--------->
      <div class="funnels_tab_filtering" style="display:none">
      
         <div class="select_fiter">
            <div class="bact_btn_here">
               <a href="javascript:void(0)" id="bact_btn_here">
                  <img src="<?php echo SC_FUNNEL_URL?>admin/images//Vector-12.png" alt="Image">
               </a>
            </div>
            <div class="filter_dropdown_here filtering">
               <select name="filter_templates">
                  <option value="all">All</option>
                  <?php 
                   if(!empty($response)){
                     foreach ($response['data'] as $key => $cats) { ?>
                         <option value="<?php echo $cats['id']?>"><?php echo $cats['name']?></option>
                     <?php }
                   }
                  ?>
               </select>
            </div>
         </div>
         <div class="tab_sec_filter">
            <ul class="tab_wrap_data filtering">
               <li><button class="all active" data-type="all">All</button></li>
               <li><button class="free" data-type="free">Free</button></li>
               <li><button class="pro" data-type="pro"><img src="<?php echo SC_FUNNEL_URL?>admin/images/Vector-11.png" alt="Image">Premium</button></li>
            </ul>
         </div>
      </div>
      <div class="funnels_count" style="display:none">
      <?php 
      if($builder_active == 'yes' || $builder_active === ''){
         echo __('Oops! It looks like the page builder you selected is inactive.');
      }else{
      ?>
         <h3><?php echo count($templates['data']);?> Funnels</h3>
         </di>
         <div class="add_funnel_list">
            <div class="listed_funnel_data filter-cat-results">
               <div class="funnel_list_item use_your_own">
                  <button type="button" id="top_level_create_your_own" class="btn">Use Your Own</button>
               </div>
               <?php 
               foreach ($templates['data'] as $key => $template) {
                  $tmpl_type = $template['filters']['template_type'][0]['slug'] ?? '';
                  $type = 'free';
                  if($tmpl_type == "upsell"){
                     $type = 'pro';
                  }
                  $cat_name = $template['filters']['collection'][0]['name'] ?? '';
                  //dd($template);?>
                 <div class="funnel_list_item" data-cat="<?php echo $template['collection'][0] ?? '';?>" data-type="<?php echo $type;?>">
                  <div class="list_img_view">
                     <div class="list_img_view_area">
                        <img class="main_img_show" src="<?php echo $template['featured_image']?>" alt="Image">
                        <div class="view_on_loading" id="view_on_loading_<?php echo $template['id'];?>">
                              <p>Loading ...</p>
                        </div>
                        <div class="overlay_list_img_view" id="overlay_list_img_view_<?php echo $template['id'];?>">
                           <a href="javascript:void(0)" data-funnel_name="<?php echo $template['title']['rendered'] ?? '';?>" data-fdlink="<?php echo $template['acf']['download_link'] ?? '';?>" onclick="importMainTemplate(this,<?php echo $template['id']?>);">
                           <img src="<?php echo SC_FUNNEL_URL?>admin/images/Vector-10.png" alt="Image"> <span>Import</span>
                           </a>
                          
                        </div>

                     </div>
                     <div class="name_view_view">
                        <div class="name_data_show">
                           <h5><?php echo $cat_name;?></h5>
                        </div>
                        <div class="view_btn_click">
                           <a href="<?php echo $template['acf']['preview_link'];?>">
                           <img src="<?php echo SC_FUNNEL_URL?>admin/images/Frame-1.png" alt="Image">
                           </a>
                        </div>
                     </div>
                  </div>
               </div>
               <?php 
               }
               ?>
            </div>
         </div>
      </div>
      <?php } ?>
   </div>
   
   <div class="scfunnel">
      <!--Header-->
      <div class="scfunnel_search">
         <div class="search_area">

         <form class="funnel-search" method="get">
               <?php
                  $s = '';
                  if (isset($_GET['s'])) {
                     $s = sanitize_text_field( $_GET['s'] );
                  }
               ?>
               <div class="input-group">
               <input name="page" type="hidden" value="<?php echo SC_FUNNEL_MAIN_PAGE_SLUG; ?>">
                  <input type="text" class="form-control" name="s" id="exampleInputAmount" value="<?php echo $s; ?>" placeholder="<?php echo __('Search for funnel','scfunnelbuilder')?>">
                  <span class="input-group-btn">
                  <button type="button" class="btn btn-secondary">
                  <img src="<?php echo SC_FUNNEL_DIR_URL . 'admin\partials\icons\search_icon.png'; ?>"  alt="Image">
                  </button>
                  </span>
               </div>
            
         </form>
         </div>
         <div class="add_new">
            <div class="btn_and_stng">
               <div class="add_new_fnl">
                  <button type="button" id="add_new_funnel" class="">Add New Funnel</button>
               </div>
               <a href="javascript:void(0)" onclick="settingModal()">
               <div class="stng_btn_wrap">
                  <img src="<?php echo SC_FUNNEL_URL?>admin/images/Vector-7.png"  alt="Image">
               </div>
               </a>
            </div>
         </div>
      </div>
      <!--Listing-->  
      <div class="wrap_all_listing">
      <?php
      
      $builders = ScFunnel_functions::get_supported_builders();
         if (count($this->funnels)) {
            foreach ($this->funnels as $key =>  $funnel) {
                  $edit_link = add_query_arg(
                     [
                        'page' => SC_FUNNEL_EDIT_FUNNEL_SLUG,
                        'id' => $funnel->get_id(),
                        'step_id' => $funnel->get_first_step_id(),
                     ],
                     admin_url('admin.php')
                  );

                  $isAutomationEnable = get_post_meta( $funnel->get_id(), 'is_automation_enabled', true );
                  $isAutomationData 	= get_post_meta( $funnel->get_id(),'funnel_automation_data',true);
                  
                  $start_condition 	= get_post_meta( $funnel->get_id(), 'global_funnel_start_condition', true );
                  $builder 			= ScFunnel_functions::get_page_builder_by_step_id($funnel->get_id());
                    
                  $funnel_status      = 'publish' === get_post_status( $funnel->get_id() ) ? 'Draft': 'Enable';
                 
                  $_type = get_post_meta( $funnel->get_id(), '_scfunnel_funnel_type', true );

                  $funnel_type = __('sc', 'scfunnelbuilder');

                  $first_step_id = ScFunnel_functions::get_first_step( $funnel->get_id() );
                  if ($first_step_id) {
                     $view_link = apply_filters( 'scfunnels/modify_funnel_view_link', get_the_permalink( $first_step_id ), $first_step_id, $funnel->get_id() );          
                  } else {
                     $view_link = '#';
                  }
            ?>
            <div class="wrap_listing_content">
               <div>
                  <h3><?php echo ucfirst($funnel->get_funnel_name()) ?></h3>
               </div>
               <div>
                  <p><?php echo $funnel->get_total_steps(). ' '. ScFunnel_functions::get_formatted_data_with_phrase($funnel->get_total_steps(), 'step', 'steps'); ?></p>
               </div>
               <div>
                  <p>Published: <?php echo $funnel->get_published_date() ?></p>
               </div>
               <div>
                  <badge class="badge_cl <?php echo strtolower($funnel->get_status()) ?>"><?php echo $funnel->get_status() ?>  </badge>
               </div>
               <div>
                  <div class="action_btn_wrap">
                     <div>
                        <a href="<?php echo esc_url_raw($view_link); ?>">
                        <img src="<?php echo SC_FUNNEL_URL?>admin/images/Frame.png" alt="Image">
                        </a>
                     </div>
                     <div>
                        <a href="<?php echo esc_url_raw($edit_link); ?>">
                        <img src="<?php echo SC_FUNNEL_URL?>admin/images/Vector-8.png" alt="Image">
                        </a>
                     </div>
                     <div>
                        <input class="triger_menu" id="check<?php echo $key?>" type="checkbox" name="menu" />
                        <label for="check<?php echo $key?>" class="icon_cl">
                        <img src="<?php echo SC_FUNNEL_URL?>admin/images/Vector-9.png" alt="Image">
                        </label>
                        <ul class="submenu_veiw">
                           <li><a href="#" id="delete_funnel" data-funnel-id="<?php echo $funnel->get_id();?>">Delete</a></li>
                           <li><a href="#" id="change_funnel_status" data-funnel-id="<?php echo $funnel->get_id();?>" data-funnel-status="<?php echo strtolower($funnel_status); ?>"><?php echo $funnel_status ?></a></li>
                        </ul>
                     </div>
                  </div>
               </div>
            </div>
            <?php
            } //--end foreach--
         } else {
            if (isset($_GET['s'])) {
                  echo __('Sorry No Funnels Found', 'sc');
            } else {
                  $create_funnel_link = add_query_arg(
                     [
                        'page' => SC_FUNNEL_CREATE_FUNNEL_SLUG,
                     ],
                     admin_url('admin.php')
                  );
                  echo __('Sorry No Funnels Found', 'sc');
                  ?>

                  <!-- <div class="create-new-funnel">
                     <a href="#" class="btn-default add-new-funnel-btn"><?php echo __('Create Your First Funnel', 'scfunnelbuilder'); ?></a>
                  </div> -->
                  <?php
            }
         } ?>




      <?php if ($this->pagination) {
         $s = '';
         if (isset($_GET['s'])) {
               $s = '&s='. sanitize_text_field($_GET['s']);
         } ?>
         <ul class="pagination_custom">
            <li class="pre_btn">
               <a href="<?php if ($this->current_page <= 1) {
                     echo '#';
                  } else {
                        echo "?page=sc_funnels&pageno=".($this->current_page - 1).$s;
                  } ?>" class="nav-link prev <?php if ($this->current_page <= 1) {
                        echo 'disabled';
                  } ?>">    
               <img src="<?php echo SC_FUNNEL_URL?>admin/images/Vector-1.png" alt="Image">
               </a>
            </li> 
            <?php
               for ($i = 1; $i <= $this->total_page; $i ++) {
                     if ($i < 1) {
                        continue;
                     }
                     if ($i > $this->total_funnels) {
                        break;
                     }
                     if ($i == $this->current_page) {
                        $class = "active";
                     } else {
                        $class = "";
                     } ?>
                     <li>
                     <a href="?page=sc_funnels&pageno=<?php echo $i.$s; ?>" class="nav-link <?php echo $class; ?>"><?php echo $i; ?></a>
                     </li>
                     <?php
               } ?>
            <!-- <li><a href="#">1</a></li>
            <li class="disabled"><a href="#">2</a></li> -->
            <li class="nxt_btn">
            <a href="<?php if ($this->current_page == $this->total_page) {
                  echo '#';
            } else {
                  echo "?page=sc_funnels&pageno=".($this->current_page + 1);
            } ?>" class="nav-link next <?php if ($this->current_page >= $this->total_funnels) {
                  echo 'disabled';
            } ?>">
         
               <img src="<?php echo SC_FUNNEL_URL?>admin/images/Vector-2.png" alt="Image">
               </a>
            </li>
         </ul>
         <?php } ?>
      </div>
   </div>

</div>
<div class="modal create_funnel_top_level_modal" id="create_funnel_top">
  <div class="header">
  <a class="cancel">X</a>
   <h3 class="title">Enter Your Funnel Name</h3>
   
  </div>
  <div class="modal-body">
    <form >
      <input type="text" placeholder="" name="funnel-name">
    </form>
    
  </div>
  
  <div class="footer">
  <button type="submit" class="create_funnel_top_level">Create Funnel</button>
  </div>
</div>

<!---------- setting modal --------------->
<div class="modal setting_modal" id="setting_modal">
  <div class="header">
  <a class="cancel">X</a>
   <h3 class="title">General Settings</h3>
   
  </div>
  <div class="modal-body">
    <form >
      <div class="setting_label">
      <label><?php echo __('Page Builder','scfunnelbuilder')?></label>
      </div>
      <div class="setting_fields">
         <select name="page-builder" id="scfunnels-page-builder">
            <?php
            $settings = get_option('_scfunnels_general_settings');
            foreach ( $builders as $key => $value ) {
               if(empty($settings)){ ?>
                   <option value="<?php echo $value['slug']; ?>" data-builder-id="<?php echo $value['id']?>"><?php echo $value['name']; ?></option>
              <?php  }else{
               ?>
               <option value="<?php echo $value['slug']; ?>" data-builder-id="<?php echo $value['id']?>" <?php if($settings['builder'] ==$value['slug']){ echo 'selected';}?>><?php echo $value['name']; ?></option>

               <?php }
            }
            ?>
         </select>
      </div>
      <!-- <input type="text" placeholder="" name="funnel-name"> -->
    </form>
    
  </div>
  
  <div class="footer">
  <button type="submit" class="setting_modal_top_level">Save</button>
  </div>
</div>
<script>

/* When the user clicks on the button, 

toggle between hiding and showing the dropdown content */

function FunnelListDropDown() {

document.getElementById("myDropdown").classList.toggle("show_stng");

}

function importMainTemplate(e,template_id){
   var data = {};
   jQuery('#overlay_list_img_view_'+template_id).css('visibility','hidden');
   jQuery('#view_on_loading_'+template_id).css('display','block');
		data.funnel_name = jQuery(e).attr('data-funnel_name');
		data.type = 'sc';
		data.nonce = ScFunnelVars.nonce;
		data.download_link = jQuery(e).attr('data-fdlink');
		console.log(data);
		wpAjaxHelperRequest( "import-funnel", data )
			.success( function( response ) {
            jQuery('#view_on_loading_'+template_id).css('display','none');
				window.location.href = response.redirectUrl;
			})
			.error( function( response ) {
				//console.log( "Uh, oh!" );
				console.log( response.statusText );
			});

}

// Close the dropdown if the user clicks outside of it

window.onclick = function(event) {
   if (!event.target.matches('.dropbtn_stng')) {
      var dropdowns = document.getElementsByClassName("dropdown-content");
      var i;
      for (i = 0; i < dropdowns.length; i++) {
         var openDropdown = dropdowns[i];
         if (openDropdown.classList.contains('show_stng')) {
         openDropdown.classList.remove('show_stng');
         }
      }
   }
}


var filterActive;

function filterCategory(cat1, type) {
        
    // reset results list
    jQuery('.filter-cat-results .funnel_list_item').removeClass('active');
    
    // the filtering in action for all criteria
    var selector = ".filter-cat-results .funnel_list_item";
    if (cat1 !== 'all') {
         selector = '[data-cat=' + cat1 + "]";
    }
    if (type !== 'all') {
        selector = selector + '[data-type=' + type + "]";
    }
    
    // show all results
    jQuery(selector).addClass('active');

    // reset active filter
    filterActive = cat1;
}

// start by showing all items
jQuery('.filter-cat-results .funnel_list_item').addClass('active');

// call the filtering function when selects are changed
jQuery('.filtering select').on('change',function() {
    console.log(jQuery(this).val());
    filterCategory(jQuery(this).val(), jQuery('.filtering button.active').attr('data-type'));
    
});
jQuery('.filtering li button').on('click',function() {
    console.log(jQuery(this).attr('data-type'));
    filterCategory(jQuery('.filtering select').val(),jQuery(this).attr('data-type'));
    
});
jQuery('.use_your_own').addClass('active');
function settingModal(){
   jQuery("#setting_modal").css("display","block");
}
</script>