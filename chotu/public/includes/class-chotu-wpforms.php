<?php
class Chotu_WPForms{
    public function __construct(){
         /**
         * chotu_wpforms_set_redirect_washare
         *
         * @param  mixed $fields
         * @param  mixed $entry
         * @param  mixed $form_data
         * @param  mixed $entry_id
         * @return void
         */
        add_action( 'wpforms_process_complete', function($fields, $entry, $form_data, $entry_id){
            if(!empty($fields)){
                if(isset($_COOKIE['captain'])){
                $captain_id = chotu_get_captain_id($_COOKIE['captain']);
                if($captain_id){
                    $url = chotu_form_place_wa_order($captain_id,$fields,$form_data,$entry_id);
                    chotu_reset_captain();
                    //$form_data['settings']['confirmations'][1]['redirect'] = $url;
                    header("Location: $url");
                    exit;
                }
                }
            }
        }, 10, 4 );

        /**
         * Add code to footer for wp-forms
         *
         * @link   https://wpforms.com/developers/how-to-create-additional-formats-for-the-date-field/
         */

        add_action( 'wpforms_wp_footer_end', function(){
            if(isset($_COOKIE['captain'])){
                $phone_number = chotu_append_isd_code($_COOKIE['captain']);
                $start_url = home_url('/oye/?cookie=true');
                ?>
                <script type="text/javascript">
                    jQuery(function($){
                        jQuery('.captain_mobile_number').val(<?php echo $phone_number;?>);
                        $('.wpforms-submit').on('click', function(){
                            setTimeout(function(){
                            location.href = "<?php echo trim($start_url);?>";
                            //window.location.reload();
                            }, 4000);
                        });
                    });
                
                    </script>
                
                <?php
                }
        }, 30 );

        /**
         * Add additional formats for the Date field Date Picker.
         *
         * @link   https://wpforms.com/developers/how-to-create-additional-formats-for-the-date-field/
         */
        add_filter( 'wpforms_datetime_date_formats', function($formats){
            $formats[ 'l, J F Y' ] = 'l, jS F Y';
            // Adds new format Monday, 16/Dec/21
            $formats[ 'd/M/y'] = 'd/M/y';
            return $formats;
        }, 10, 1 );
        add_action( 'wpforms_display_submit_before', function( $form_data ) {
            //$captain_mobile_number = isset($)
            echo '<input type="hidden" name="captain_mobile_number" class="captain_mobile_number" value="">';
            echo do_shortcode('[block id="dnd-opens-wa-message-form"]<br>');
        }, 10, 1 );
        
                
    }
}
new Chotu_WPForms();