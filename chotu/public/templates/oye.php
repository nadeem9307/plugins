<?php
/* Template Name: Oye */
get_header();

?>
<style>
    * {
    box-sizing: border-box;
    }
    *:focus {
    outline: none;
    }
    body {
    font-family: 'Open Sans', sans-serif;
    font-size: 14px;
    }
    input {
    font-size: 20px;
    color: #d1d1d1;
    font-weight: 400;
    }

    input[type=text]{
        background-color: #f6f6f6!important;
        border: 0em !important;
        color: var(--primary)!important;
        font-size: 2rem!important;
        font-weight: 500;
        height: 3rem!important;
        text-align: center;
    }

    number-input {
    width: 100%;
    height: 55px;
    line-height: 55px;
    text-align: center;
    padding: 0;
    border-radius: 10px;
    border: 0;
    -webkit-box-shadow: 0px 0px 17px -1px rgba(132, 132, 132, 0.15);
    -moz-box-shadow: 0px 0px 17px -1px rgba(132, 132, 132, 0.15);
    box-shadow: 0px 0px 17px -1px rgba(132, 132, 132, 0.15);
    }
    .wrapper {
    width: 100%;
    }
    .wrapper .phone {
    width: 100%;
    margin: 40px auto 0 auto;
    position: relative;
    }
    .wrapper .phone title {
    font-weight: 700;
    num_digit-spacing: 2px;
    display: block;
    text-align: center;
    }
    .wrapper .phone .phone-container {
    width: 100%;
    margin-top: 30px;
    }
    .num_digit {
        background-color: #F6F6F6;
        color: #6B6B6B !important;
        margin: 1%;
        border-radius: 5px;
        height: 3rem !important;
    }
    .wrapper .phone .phone-container .keyboard {
    width: 100%;
    }
    .wrapper .phone .phone-container .keyboard .number {
    width: 100%;
    font-size: 0;
    text-align: center;
    }
    .wrapper .phone .phone-container .keyboard .number.aling-right {
    text-align: right;
    width: 100%;
    }
    .wrapper .phone .phone-container .keyboard .number span {
    font-size: 2rem;
    color: var(--primary);
    display: inline-block;
    width: 30%;
    text-align: center;
    }
    .wrapper .phone .phone-container .keyboard .number span.call-button {
    opacity: 0;
    transition: 250ms;
    }
    .wrapper .phone .phone-container .keyboard .number show {
    opacity: 1;
    }
    .wrapper .phone .phone-container .keyboard .number img {
    display: inline-block;
    vertical-align: middle;
    }
    .call-button{
        background-color: var(--primary);
        color: #fff;
        width: 100%;
        border: 0px!important;
        margin:0;
        font-size:1rem; 
        height: 3rem;
        border-radius:5px;
        margin-top: -10%;
        margin-left: -6%;
    }
    .wrapper .phone .phone-container .keyboard .number span i {
    display: inline-block;
    width: 33.33%;
    height: auto;
    line-height: auto;
    background: white;
    cursor: pointer;
    border-radius: 5px;
    transition: 250ms;
    -webkit-touch-callout: none;
    -webkit-user-select: none;
    -khtml-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;

    }
    .wrapper .phone .phone-container .keyboard .number span i.delete {
    background: transparent;
    box-shadow: 0 0 0 0;
    -webkit-box-shadow: 0 0 0 0;
    -moz-box-shadow: 0 0 0 0;
    -ms-box-shadow: 0 0 0 0;
    color: var(--lightprimary);
    }
    .wrapper .phone .phone-container .keyboard .number span i.delete img {
    display: inline-block;
    vertical-align: middle;
    }
    .wrapper .phone .phone-container .keyboard .number span:hover i {
    color: black;
    }
    .wrapper .phone .phone-container .keyboard .number span:active i {
    transform: translateY(1px);
    -webkit-box-shadow: 5px 5px 24px 0px rgba(132, 132, 132, 0.18);
    -moz-box-shadow: 5px 5px 16px 0px rgba(132, 132, 132, 0.18);
    box-shadow: 5px 5px 16px 0px rgba(132, 132, 132, 0.18);
    }
    .wrapper .phone .phone-container .keyboard .number span:active i.delete {
    box-shadow: 0 0 0 0;
    -webkit-box-shadow: 0 0 0 0;
    -moz-box-shadow: 0 0 0 0;
    -ms-box-shadow: 0 0 0 0;
    transform: translateY(0px);
    }
    .wrapper .phone .phone-container .keyboard .number span:active i.delete img {
    transition: 250ms;
    }
    .wrapper .phone .phone-container .keyboard .number span:active i.delete:active img {
    transform: translateY(2px);
    }
    .mfp-container {
        padding: 0 !important;
    }
    .mfp-content{
        /*position: absolute !important;*/
        /*bottom: 0 !important;*/
        position: absolute !important;
        bottom: 0 !important;
        left: 0 !important;
    }
    .delete{
        background-color: #FFF!important;
        color: var(--primary);
    }

</style>
<div class="row page-wrapper">
<div id="content" class="large-12 col" role="main">
    <div id="col-760104850" class="col medium-12 large-12">
        <div class="col-inner">
<!-- 
        <a href="#dial-a-chotu" target="_self" class="button primary oye_dial_button" >
            <i class="fa fa-plus" aria-hidden="true"></i>
        </a> -->

        <div class="row">
            <?php 
            $captains = chotu_get_visited_captain_shop_history();
            if(!empty($captains)){
                echo '<h1 style="color: var(--primary);">My <i class="fa-solid fa-clock-rotate-left"></i></h1>';
                foreach ($captains as $key => $captain) {
                    $user = get_user_by('login',$key);
                    if($user){
                        $captain_display_pic              = get_user_meta($user->ID,'captain_display_pic',true);
                        if(empty($captain_display_pic)){
                          $captain_display_pic    = get_option('captain_default_display_pic');
                        }
                      ?>
                      <div id="col-1291227841" class="col medium-3 large-3 small-6">
                            <div class="col-inner">
                            <div class="box has-hover has-hover box-overlay dark box-text-bottom">
                                <div class="box-image ">
                                <div class="box-image-inner image-cover" style="border-radius:100%;padding-top:100%;">
                                <a href="<?php echo $user->user_url;?>"><img width="600" height="600" src="<?php echo $captain_display_pic;?>" class="attachment- size- lazy-load-active" alt="" ></a>
                                    <div class="overlay" style="background-color:rgba(0,0,0,.2)"></div>
                                </div>
                                </div>
                            </div>
                            <div class="team-member-content pt-half text-center">
                                <a href="<?php echo $user->user_url;?>"><?php echo $user->display_name;?></a>
                            </div>
                            </div>
                        </div>
                    <?php }  
                    }
                }
                ?>
            </div>
            <!---captain shop gridClose --------------------->
           <?php
           the_content();
           $html = '<div class="wrapper">
                    <!-- ## phone area -->
                    <div class="phone">
                    
                    <!-- ##&nbsp;phone area -->
                    <div class="phone-container">
                        <input type="text" maxlength="10" class="number-input" id="numberInput" value="" placeholder="dial a chotu">
                        <!-- ## keyboard -->
                        <div class="keyboard">
                        <div class="number">
                            <span class="num_digit" data-number="1">1</span>
                            <span class="num_digit" data-number="2">2</span>
                            <span class="num_digit" data-number="3">3</span>
                            <span class="num_digit" data-number="4">4</span>
                            <span class="num_digit" data-number="5">5</span>
                            <span class="num_digit" data-number="6">6</span>
                            <span class="num_digit" data-number="7">7</span>
                            <span class="num_digit" data-number="8">8</span>
                            <span class="num_digit" data-number="9">9</span>
                            <span><button class="call-button hide">OYE!</button></span>
                            <span class="num_digit" data-number="0">0</span>
                            <span class="delete"><i class="fa-solid fa-delete-left"></i></span>
                        </div>
                    </div>
                    </div>
                </div>';
            echo do_shortcode('[lightbox id="dial-a-chotu" auto_open="true" auto_timer="0" auto_show="always" width="100%"]'.$html.'[/lightbox]');?>
            <!--echo do_shortcode('[lightbox auto_open="true" auto_timer="1000" auto_show="always" id="newsletter-signup-link" width="600px" padding="20px"]'.$html.'[/lightbox]');?>-->
        </div>
    </div>
</div>

<script>
    jQuery(".number-input").keyup(function(e){
    // if(jQuery(this).val().length >= 10)
    //     jQuery(".call-button").addClass("show");  
    if(e.which == 10)
        jQuery(".call-button").removeClass("show");
    })
    //called when key is pressed in textbox
    jQuery(".number-input").keypress(function (e) {
        //if the num_digit is not digit then display error and don't type anything
        if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
            //display error message
                return false;
        }
    });

    jQuery(".num_digit").on('click',function(){
        console.log('yes');
        if(jQuery(".number-input").val().length < 10){
            var phoneNumber = jQuery(".number-input").val() + jQuery(this).data("number");
            jQuery(".number-input").val(phoneNumber);
        }
        if(jQuery(".number-input").val().length === 10){
            jQuery(".call-button").addClass("show"); 
            jQuery(".call-button").removeClass("hide");
        }    
    });
    jQuery(document).on('click','.call-button',function(){
        console.log('coming')
        if(jQuery(".number-input").val().length === 10){
            jQuery.ajax({
                type: "POST",
                url: '/?wc-ajax=chotu_get_captain',
                data: {'captain':jQuery('#numberInput').val(),'action': 'chotu_get_captain'},
                success: function(response)
                {
                    window.location.href = response.data.url;
                }
            });
        }
    })

    jQuery(".delete").on('click',function(){
        var phoneNumber = jQuery(".number-input").val().slice(0,-1);
        jQuery(".number-input").val("");
        jQuery(".number-input").val(phoneNumber);
        jQuery(".call-button").removeClass("show");
    }); 
    jQuery(document).ready(function(){
        jQuery('.is-search-input').attr('placeholder',"<?php echo trim(do_shortcode('[block id="dnd-oye-search-text"]'));?>");
    })
</script>
<?php

get_footer();