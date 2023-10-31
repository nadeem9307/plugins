<?php global $chotu_current_captain;?>
<script>
    /**
     * change profile and cover pic
     */
    jQuery('.bs_profile_pic').on('click', function() {
        jQuery('input[name="bs_profile_pic"]').trigger('click');
    });

    jQuery('input[name="bs_profile_pic"]').change(function() {
        var reader = new FileReader();
        reader.onload = function(e) {
            jQuery('#bs_profile_pic').attr('src', e.target.result);
        };
        reader.readAsDataURL(this.files[0]);
        ChotuUploadImage(this.files[0], 'captain_display_pic');
    });

    jQuery('.bs_cover_pic').on('click', function() {
        jQuery('input[name="bs_cover_pic"]').trigger('click');
    });

    jQuery('input[name="bs_cover_pic"]').change(function() {
        var reader = new FileReader();
        reader.onload = function(e) {
            jQuery('#bs_cover_pic').attr('src', e.target.result);
        };
        reader.readAsDataURL(this.files[0]);
        ChotuUploadImage(this.files[0], 'captain_cover_pic');
    });

    /**
     * chotu status B footer icons onclick add border
     */

     jQuery('.footer_icon').on('click', function(){
        // jQuery(this).addClass('active');
        jQuery(this).addClass('footer_active').siblings('.footer_active').removeClass('footer_active');

     })
    /**
     * close the offers div
     */
    jQuery('.site-notice-dismiss').on('click',function(){
        jQuery(this).parent('.site-notice').remove();
    })

    /**
     * captain gallery
     */
    jQuery('#captain_gallery').change(function(e) {
        var file_array = [];
        var file = this.files[0];
        var current_length = jQuery('.image_gallery > div').length;
        var validImageTypes = ["image/gif", "image/png", "image/jpeg", "image/webp", "image/jpg"];
        var input = document.getElementById('captain_gallery');
        var total_length = parseInt(input.files.length) + parseInt(current_length);
        console.log(total_length);
        if (total_length > 20) {
            alert('☹️ Upload Max 5 Files allowed');
            input.value = '';
            return false;
        } else {
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
        }
        ChotuUploadImage(file_array, 'captain_gallery');
    });

    function ChotuUploadImage(files, image) {
        var gl_flag = false;
        let captain_gallery = ['gallery'];
        var formdata = new FormData();
        if (jQuery.isArray(files)) {
            for (var i = 0; i < files.length; i++) {
                gl_flag = true;
                formdata.append('captain_gallery[]', files[i]);
            }
        } else {
            formdata.append(image, files);
        }
        formdata.append("action", "chotu_update_captain_pics");
        jQuery.ajax({
            type: "post",
            contentType: false,
            processData: false,
            url: flatsomeVars.ajaxurl,
            data: formdata,
            success: function(response) {
                alert(response.data.message);
                if (window.location.hash == '') {
                    if (gl_flag) {
                        window.location.href = window.location.href + '#offers';
                    }
                    window.location.href = window.location.href;
                }
                window.location.reload(true);
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                alert('something went wrong !')
            }

        });
    }

    function ChotuRemoveImage(attachment_id) {
        jQuery(this).prev().remove();
        jQuery(this).remove();
        console.log(attachment_id);
        jQuery.ajax({
            type: "post",
            url: flatsomeVars.ajaxurl,
            data: {
                'attachment_id': attachment_id,
                'action': 'chotu_remove_gallery_image'
            },
            success: function(response) {
                alert(response.data.message);
                jQuery('#col-' + attachment_id).remove();
                window.location.reload(true);
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                alert('something went wrong !')
            }
        });
    }
    function ChotuHashRedirect(url) {
        //var current_url = window.location.href;
        console.log(url);
        if(jQuery('.tab.has-icon.active a').attr('href') !="#shop"){
        //     console.log(jQuery('.all_cat_link').attr('href'));
            window.location.href = url;
            window.location.reload();
        }
        //window.location.assign(current_url+"#allcat");
        //window.location.reload();
    }
    jQuery(document).ready(function() {
        // Check if there is a hash in the URL
        if (window.location.hash) {
            // Get the hash value (e.g., "#section-1")
            var hash = window.location.hash;

            // Scroll to the element with the matching ID
            jQuery('html, body').animate({
                scrollTop: jQuery(hash).offset().top
            }, 800); // You can adjust the animation duration (in milliseconds) as needed
        }
        
    });
    document.addEventListener("DOMContentLoaded", function () {
    // Handle tab clicks
    const tabLinks = document.querySelectorAll(".tab.has-icon a");
    tabLinks.forEach((tabLink) => {
        tabLink.addEventListener("click", function (e) {
        e.preventDefault();
        const targetId = this.getAttribute("href").substring(1);
        showTab(targetId);
        });
    });

    // Check for hash on page load
    const initialHash = window.location.hash.substring(1);
    if (initialHash) {
        showTab(initialHash);
    }
    });

    function showTab(tabId) {
    // Hide all tabs and show the selected tab
    const tabContents = document.querySelectorAll(".entry-content");
    tabContents.forEach((tabContent) => {
        tabContent.style.display = "none";
    });
    document.getElementById(tabId).style.display = "block";

    // Update the URL hash without triggering a page reload
    window.history.pushState(null, null, `#${tabId}`);
    }

</script>

<?php if (is_captain_logged_in()) { ?>
<script>
    // START - PWA (A2HS) Install my shop button (PWA for logged-in captains)
    if ('serviceWorker' in navigator) {
        navigator.serviceWorker
            .register('/captain-sw.js')
            .then(() => {
                console.log('oye Service Worker is Registered');
            });
    }

    let deferredPrompt;
    const addBtn = document.querySelector('.add-button');
    console.log('yes')
    addBtn.style.display = 'none';

    window.addEventListener('beforeinstallprompt', (e) => {
        console.log('coming')
        // Prevent Chrome 67 and earlier from automatically showing the prompt
        e.preventDefault();
        // Stash the event so it can be triggered later.
        deferredPrompt = e;
        // Update UI to notify the user they can add to home screen
        addBtn.style.display = 'block';

        addBtn.addEventListener('click', () => {
            console.log('cliked');
            // hide our user interface that shows our A2HS button
            addBtn.style.display = 'block';
            // Show the prompt
            deferredPrompt.prompt();
            // Wait for the user to respond to the prompt
            deferredPrompt.userChoice.then((choiceResult) => {
                if (choiceResult.outcome === 'accepted') {
                    console.log('User accepted the A2HS prompt');
                } else {
                    console.log('User dismissed the A2HS prompt');
                }
                deferredPrompt = null;
            });
        });
    });
    
    jQuery(document).ready(function() {
        // Hide the notice block when the "X" is clicked
        jQuery(".close").click(function() {
            jQuery("#dismissable-notice").hide();
        });
        console.log('ready')
    });
    // END - PWA (A2HS) Install my shop button (PWA for logged-in captains)

    var datetime = jQuery('input[name="next_payment_date"]').val();
    if(datetime){
        // Set the date we're counting down to
        var countDownDate = new Date(datetime).getTime();

        // Update the count down every 1 second
        var x = setInterval(function() {

        // Get today's date and time
        var now = new Date().getTime();

        // Find the distance between now and the count down date
        var distance = countDownDate - now;

        // Time calculations for days, hours, minutes and seconds
        var days = Math.floor(distance / (1000 * 60 * 60 * 24));
        var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        var seconds = Math.floor((distance % (1000 * 60)) / 1000);

        // Display the result in the element with id="demo"
        document.getElementById("countdown_timer").innerHTML = days + "d " + hours + "h "
        + minutes + "m " + seconds + "s ";

        // If the count down is finished, write some text
        if (distance < 0) {
            clearInterval(x);
            document.getElementById("countdown_timer").innerHTML = "EXPIRED";
        }
        }, 1000);
    }
    
</script>
<?php } ?>