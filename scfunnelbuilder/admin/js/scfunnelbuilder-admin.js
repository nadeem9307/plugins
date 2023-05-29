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
	$(document).ready(function(){
		setTimeout(function() {
			$('.brand_logo').attr('href',ScFunnelVars.admin_url+'edit.php?post_type=sc_product')
		
			$('.back_btn.me-2').on('click',function(){
				console.log('dsa');
					window.location.href=  ScFunnelVars.admin_url+"admin.php?page=sc_funnels";
				});
		},2500)
		$('#add_new_funnel').on('click',function(){
			$('.scfunnel').css('display','none');
			$('.funnels_tab_filtering').css('display','block');
			$('.funnels_count').css('display','block');
		})
		$('#bact_btn_here').on('click',function(){
			$('.funnels_tab_filtering').css('display','none');
			$('.funnels_count').css('display','none');
			$('.scfunnel').css('display','block');
			$("#create_funnel_top").hide();
		})

		$("#top_level_create_your_own").click(function(){
			$("#create_funnel_top").css("display","block");
		});
		$("#top_level_create_your_own").click(function(){
			$("#create_funnel_top").css("display","block");
		});
	   $(".cancel").on('click',function(){
			$("#create_funnel_top").hide();
			$("#setting_modal").hide();
	   });
	   
	   

	   /*********create  funnel  */
	   $('.create_funnel_top_level').on('click',function(){
		var data = {};
		data.funnel_name = $('input[name="funnel-name"]').val();
		data.type = 'sc';
		data.nonce = ScFunnelVars.nonce;
		console.log(data);
		wpAjaxHelperRequest( "create-funnel", data )
			.success( function( response ) {
				//console.log( "done!" );
				//console.log( response );
				window.location.href = response.redirectUrl;
			})
			.error( function( response ) {
				//console.log( "Uh, oh!" );
				console.log( response.statusText );
			});
	   });

	   $(document).on('click','#delete_funnel',function(e){
		e.preventDefault();
		let funnel_id = $(this).attr('data-funnel-id');
		delete_funnel(funnel_id);
	   })
	   /*************change funnel status */
	   $(document).on('click','#change_funnel_status',function(e){
		e.preventDefault();
		let data = {};
		data.funnel_id = $(this).attr('data-funnel-id');
		data.funnel_status = $(this).attr('data-funnel-status');
		wpAjaxHelperRequest( "change-funnel-status", data )
		.success( function( response ) {
			console.log( response.redirect_url );
			window.location.href = response.redirect_url;
		})
		.error( function( response ) {
			console.log( response.statusText );
		});
	   })

	});
	 /***********delete funnel from list**********/
	 function delete_funnel(funnel_id){
		var data = {};
		data.funnel_id = funnel_id;
		data.type = 'sc';
		data.nonce = ScFunnelVars.nonce;
		wpAjaxHelperRequest( "delete-funnel", data )
		.success( function( response ) {
			console.log( "done!" );
			console.log( response );
			window.location.href = response.redirectUrl;
		})
		.error( function( response ) {
			console.log( "Uh, oh!" );
			console.log( response.statusText );
		});
	}

	/**********update funnel settings */
	$(document).on('click','.setting_modal_top_level',function(e){
		e.preventDefault();
		let data = {};
		data.builder 	= $('select[name="page-builder"]').val();
		data.builder_id = $('select[name="page-builder"]').find(':selected').attr('data-builder-id');
		data.funnel_type = 'sales';
		wpAjaxHelperRequest( "update-general-settings", data )
		.success( function( response ) {
			console.log( response.redirect_url );
			location.reload();
		})
		.error( function( response ) {
			console.log( response.statusText );
		});
	   })
	
})( jQuery );
