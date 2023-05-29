(function( $ ) {
	'use strict';

	/**
	 * All of the code for your admin-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */
	
	// remove the employee settings for Yoast under captain in wp-admin
	 $( window ).load(function() {
		jQuery('#your-profile .yoast-settings').eq(0).css('display','none');
	});
	
	//generic image uploader JS

	 jQuery(document).ready( function($){
		
	    var mediaUploader_woo;

	    $('#upload-button-woo').on('click',function(e) {
	        e.preventDefault();
	        if( mediaUploader_woo ){
	            mediaUploader_woo.open();
	            return;
	        }

	        mediaUploader_woo = wp.media.frames.file_frame = wp.media({
	            title: 'Choose an Image',
	            button: { text: 'Choose Image'},
	            multiple: false
	        });

	        mediaUploader_woo.on('select', function(){
	            attachment = mediaUploader_woo.state().get('selection').first().toJSON();
	            $('#category-meta-woo').val(attachment.url);
	            $('#category-header-preview').attr('src', ''+ attachment.url + '' );
	        });

	        mediaUploader_woo.open();
	    }); 
	
	

		$('#postYourAdd').click(function(){ 
			if(!$('#iframe').length) {
					$('#iframeHolder').html('<iframe id="iframe" src="//player.vimeo.com/video/90429499" width="700" height="450"></iframe>');
			}
		});   
	
	});
	 
})( jQuery );
