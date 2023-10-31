<?php
/* Template Name: View Shop QR */
// get_header();


?>
<div class="row page-wrapper">
    <div id="content" class="large-12 col" role="main">
        <div class="container" style="text-align:center">
            <?php while ( have_posts() ) : the_post(); ?>
                        <header class="entry-header text-center">
                            <h1 class="entry-title"><?php the_title(); ?></h1>
                            <div class="is-divider medium"></div>
                        </header>
            <?php endwhile; // end of the loop. 
             
            ?>
            <form name="captain_search" action="" id="captain_search" method="POST">
                <label for="search_my_shop"><?php esc_html_e( 'Shop Number', 'woocommerce' ); ?>&nbsp;</label>
                <input type="number" name="search_my_shop" id="search_my_shop" require maxlength="10">
                <span id="validationResult"></span>
                <button class="btn button-primary" type="button" id="find_my_shop" name="find_my_shop">Generate QR</button>
            </form>
            <div>
                
                <?php
                 if(isset($_POST['search_my_shop'])){
                    $user = get_user_by( 'login', $_POST['search_my_shop'] );
                    if($user){
                        $image = chotu_view_shop_qr_code($user->user_url);
                        if($image){ ?>
                            <input type="hidden" id="captain_shop_url" name="captain_shop_url" value="<?php echo $user->user_url;?>">
                            <input type="hidden" id="captain_shop_name" name="captain_shop_name" value="<?php echo $user->display_name;?>">
                            
                            <img id="sourceImage" src="<?php echo $image;?>" style="display:none">
                            <canvas id="canvas"  style="border: 1px solid #0000000d;"></canvas>
                            <!-- <canvas id="canvas_2" width="1600" height="900" style="border: 1px solid #0000000d;"></canvas> -->
                        <?php }
                   
                    }else{
                        echo __("no shop found.");
                    }?>

                    <script>
                        // jQuery(document).on('click','#find_my_shop', function(){
                        //     if(jQuery('#search_my_shop').val().length != 10) {
                        //         alert('please enter a valid 10 digit number');
                        //         jQuery('#search_my_shop').focus()
                        //         return false;
                        //     }
                        //     jQuery('#captain_search').submit();
                        // })
                        
                        
                            // Get a reference to the image and canvas
                            var image    = document.getElementById('sourceImage');
                            var captain_shop_url = document.getElementById("captain_shop_url").value;
                            var captain_shop_name = document.getElementById("captain_shop_name").value;

                            /**
                             * Canvas id is canvas start here 
                             */
                                var canvas   = document.getElementById('canvas');
                                var ctx      = canvas.getContext('2d');
                                // Set the canvas size to match the image
                                canvas.width  = image.width;
                                canvas.height = image.width;
                                ctx.fillStyle = '#b90f1a';
                                ctx.fillRect(0, 0, canvas.width, canvas.height);
                                // Draw the image onto the canvas
                                ctx.drawImage(image,
                                canvas.width / 2 - image.width / 2,
                                canvas.height / 2 - image.height / 2);

                                // You can perform additional operations on the canvas here, like adding text or shapes
                                ctx.fillStyle   = '#fff';
                                ctx.textAlign   = 'center';
                                ctx.font        = "36px Arial";
                                ctx.weight      = "600";
                                ctx.fillText(captain_shop_name, canvas.width / 2, 80);
                                ctx.lineTo(400, 100);
                                ctx.textAlign   = 'center';
                                ctx.font        = "20px Arial";
                                ctx.weight      = "600";
                                ctx.fillText(captain_shop_url, canvas.width / 2, canvas.height - 65);
                            /**
                             * Canvas id is canvas_2 end here 
                             */
                            /**
                             * Canvas id is canvas_2 start here 
                             */
                            /*
                             var canvasTwo   = document.getElementById('canvas_2');
                                var ctxTwo      = canvasTwo.getContext('2d');
                                // Set the canvas size to match the image
                                canvasTwo.width  = 900;
                                canvasTwo.height = 1600;
                                ctxTwo.fillStyle = '#ffff';
                                ctxTwo.fillRect(0, 0, canvasTwo.width, canvasTwo.height);
                                // Draw the image onto the canvas
                                ctxTwo.drawImage(image,
                                canvasTwo.width / 2 - image.width / 2,
                                canvasTwo.height / 2 - image.height / 2);

                                // You can perform additional operations on the canvas here, like adding text or shapes
                                ctxTwo.fillStyle   = '#0000';
                                ctxTwo.textAlign   = 'center';
                                ctxTwo.font        = "36px Arial";
                                ctxTwo.weight      = "600";
                                ctxTwo.fillText(captain_shop_name, canvasTwo.width / 2, 80);
                                ctxTwo.lineTo(400, 100);
                                ctxTwo.textAlign   = 'center';
                                ctxTwo.font        = "20px Arial";
                                ctxTwo.weight      = "600";
                                ctxTwo.fillText(captain_shop_url, canvasTwo.width / 2, canvasTwo.height - 65);
                            */
                                /**
                             * Canvas id is canvas end here 
                             */
                        </script>




                <?php
                }
                
                ?>
        </div>
        </div>
        
    </div>
    <style>
        #find_my_shop{
            margin-top: 30px;
        }
        .button-primary{
            color: #fff;
            background-color: #b90f1a;
            border-color: #b90f1a;
        }
        .validationResult{
            color: red;
            position: absolute;
            margin-bottom: 10%;
            display: flex;
        }
        .btn{
            display: inline-block;
            font-weight: 400;
            text-align: center;
            white-space: nowrap;
            vertical-align: middle;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
            border: 1px solid transparent;
            padding: 0.375rem 0.75rem;
            font-size: 1rem;
            line-height: 1.5;
            border-radius: 0.25rem;
            transition: color .15s ease-in-out,background-color .15s ease-in-out,border-color .15s ease-in-out,box-shadow .15s ease-in-out;
        }
        @import url(https://fonts.googleapis.com/css?family=Lily+Script+One);

            body {
                margin:0;
                font-family:arial,tahoma,sans-serif;
                font-size:12px;
                font-weight:normal;
                direction:ltr;
                background:white;
            }

            form {
                margin:0% auto 0 auto;
                padding:30px;
                /* width:400px; */
                height:auto;
                overflow:hidden;
                background:white;
                border-radius:10px;
            }

            form label {
                font-size:14px;
                /* color:darkgray; */
                cursor:pointer;
            }

            form label,
            form input {
                /* float:left; */
                clear:both;
            }

            form input {
                margin:15px 0;
                padding:15px 10px;
                width:100%;
                outline:none;
                border:1px solid #bbb;
                border-radius:20px;
                display:inline-block;
                -webkit-box-sizing:border-box;
                -moz-box-sizing:border-box;
                        box-sizing:border-box;
                -webkit-transition:0.2s ease all;
                -moz-transition:0.2s ease all;
                    -ms-transition:0.2s ease all;
                    -o-transition:0.2s ease all;
                        transition:0.2s ease all;
            }

            form input[type=text]:focus,
            form input[type="password"]:focus {
                border-color:cornflowerblue;
            }

            input[type=submit] {
                padding:15px 50px;
                width:auto;
                background:#1abc9c;
                border:none;
                color:white;
                cursor:pointer;
                display:inline-block;
                float:right;
                clear:right;
                -webkit-transition:0.2s ease all;
                -moz-transition:0.2s ease all;
                    -ms-transition:0.2s ease all;
                    -o-transition:0.2s ease all;
                        transition:0.2s ease all;
            }

            input[type=submit]:hover {
                opacity:0.8;
            }

            input[type="submit"]:active {
                opacity:0.4;
            }
    </style>
</div>
<?php //get_footer();?>
<script>
    document.getElementById("find_my_shop").addEventListener("click", function() {
        var search_my_shop = document.getElementById("search_my_shop").value;
        var validationResult = document.getElementById("validationResult");
        var captainSearch = document.getElementById("captain_search");
        if (validateNumber(search_my_shop)) {
            validationResult.textContent  = "Valid 10-digit number!";
            captainSearch.submit();
        } else {
            validationResult.textContent  = "Not a valid 10-digit number. Please enter exactly 10 digits.";
        }
    });
    var searchMyShop = document.getElementById("search_my_shop");
    // Add an event listener to the input field
    searchMyShop.addEventListener("input", function () {
        // Remove non-numeric characters using a regular expression
        var cleanedInput = searchMyShop.value.replace(/\D/g, "");

        // Limit the input to 10 digits
        if (cleanedInput.length > 10) {
            cleanedInput = cleanedInput.slice(0, 10);
        }

        // Update the input field with the cleaned value
        searchMyShop.value = cleanedInput;
    });
    function validateNumber(number) {
        // Create a regular expression to match a 10-digit number
        var regex = /^\d{10}$/;

        return regex.test(number);
    }
</script>
<?php
?>