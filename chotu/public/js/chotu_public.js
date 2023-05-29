(function( $ ) {
	'use strict';

	/**
	 * All of the code for your public-facing JavaScript source
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
    $('.about_icons').on('click',function(){
        $('.fa-regular.fa-user').addClass('hide');
        $('.fa-solid.fa-user').removeClass('hide');
        
        $('.fa-regular.fa-face-smile').removeClass('hide');
        $('.fa-solid.fa-face-smile ').addClass('hide');
        
        $('.fa-regular.fa-circle-check').removeClass('hide');
        $('.fa-solid.fa-circle-check').addClass('hide');
        
    });
    $('.shop_icons').on('click',function(){
        $('.fa-solid.fa-user').addClass('hide');
        $('.fa-regular.fa-user').removeClass('hide');

        $('.fa-solid.fa-face-smile ').removeClass('hide');
        $('.fa-regular.fa-face-smile').addClass('hide');

        $('.fa-solid.fa-circle-check').addClass('hide');
        $('.fa-regular.fa-circle-check').removeClass('hide');
    });
    $('.offers_icons').on('click',function(){
        $('.fa-solid.fa-circle-check').removeClass('hide');
        $('.fa-regular.fa-circle-check').addClass('hide');

        $('.fa-solid.fa-user').addClass('hide');
        $('.fa-regular.fa-user').removeClass('hide');

        $('.fa-solid.fa-face-smile ').addClass('hide');
        $('.fa-regular.fa-face-smile').removeClass('hide');

        
    });
	jQuery( document ).ready(function( $ ) {
		$(window).on('popstate', function() {
			if(Cookies.get('googtrans') != '/en/en'){
				location.reload(true);
			}
		});
		
	 });
	 
	 window.onpageshow = function(event) {
		if (event.persisted) {
			if(Cookies.get('googtrans') !='/en/en'){
				location.reload(true);
			}
		}
	};
	jQuery('.add_to_cart_buttons').on('click',function(e){
		jQuery(this).parent().find('form').submit();
	})
	
 })( jQuery );
