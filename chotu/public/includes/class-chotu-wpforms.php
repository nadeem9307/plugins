<?php
class Chotu_WPForms
{
    public function __construct()
    {
        /**
         * 06-Sep-2023 | Not reviewed, TBD Later
         * chotu_wpforms_set_redirect_washare
         * on form submit, call the function chotu_form_place_wa_order
         * chotu_form_place_wa_order is in code snippet
         * @param  mixed $fields
         * @param  mixed $entry
         * @param  mixed $form_data
         * @param  mixed $entry_id
         * @return void
         */
        add_action('wpforms_process_complete', function ($fields, $entry, $form_data, $entry_id) {
            if (!empty($fields)) {
                // yravi-comment: should we not use the $chotu_status = B here?
                if (isset($_COOKIE['captain'])) {
                    $captain = get_user_by( 'login', $_COOKIE['captain'] );
                    if ($captain) {
                        $url = chotu_form_place_wa_order($captain->ID, $fields, $form_data, $entry_id);
                        $url = str_replace(PHP_EOL, ', ', $url);
                        //chotu_reset_captain();
                        //$form_data['settings']['confirmations'][1]['redirect'] = $url;
                        header("Location: $url");
                        exit;
                    }
                }
            }
        }, 10, 4);

        /**
         * to set the captain_mobile_number from hidden field into the entry
         *
         * @link   https://wpforms.com/developers/how-to-create-additional-formats-for-the-date-field/
         */

        add_action('wpforms_wp_footer_end', function () {
            // yravi-comment: should we not use the $chotu_status = B here?
            if (isset($_COOKIE['captain'])) {
                $phone_number = chotu_prepend_isd_code($_COOKIE['captain']);
                ?>
                <script type="text/javascript">
                    jQuery(function($){
                        jQuery('.captain_mobile_number input').val(<?php echo $phone_number; ?>);
                        // $('.wpforms-submit').on('click', function(){
                        //     setTimeout(function(){
                        //     location.href = "<?php //echo trim($start_url); ?>";
                        //     //window.location.reload();
                        //     }, 4000);
                        // });
                    });
                </script>
                <?php
            }
        }, 30);

        /**
         * Add additional formats for the Date field Date Picker.
         *
         * @link   https://wpforms.com/developers/how-to-create-additional-formats-for-the-date-field/
         */
        add_filter('wpforms_datetime_date_formats', function ($formats) {
            $formats['l, J F Y'] = 'l, jS F Y';
            // Adds new format Monday, 16/Dec/21
            $formats['d/M/y'] = 'd/M/y';
            return $formats;
        }, 10, 1);
    }
}
new Chotu_WPForms();