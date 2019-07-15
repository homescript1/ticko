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
    $(document).ready(function(){
		var ticko_add_btn = $('button#ticko_ticket_submit.button-primary');
		

		//add new ticket
        ticko_add_btn.on('click',function(e){
			e.preventDefault();
			var ticko_event_day = $('input#ticko_ticket_event_day').val();
			var ticko_phone_number = $('input#ticko_phone_number').val();
			var ticko_quantity = $('input#ticko_quantity').val();
			var ticko_ticket_price = $('input#ticko_ticket_price').val();
			var ticko_email = $('input#ticko_email').val();
			var ticko_ticket_id = $('input#post_ID').val();
			var ticko_ticket_name = $('input#title').val();
			var ticko_publish = $('input#publish');
			var data = {
				ticko_event_day : ticko_event_day,
				ticko_phone_number : ticko_phone_number,
				ticko_quantity : ticko_quantity , 
				ticko_ticket_price : ticko_ticket_price,
				ticko_email : ticko_email,
				ticko_id : ticko_ticket_id,
				ticko_name : ticko_ticket_name,
			};
			$.post(ticko_ajax_object.ticko_ajax_url,{
				action : "ticko_save_ticket_by_ajax",
				data : data,
				"security" : ticko_ajax_object.ticko_ajax_security
			},function(response){
				if (null != response ){
					ticko_add_btn.attr('disabled',true);
					ticko_ticket_div.append("Votre ticket a bien été crée, cette page sera rechargé dans quelques secondes.");
					ticko_publish.click();
				}else{
					ticko_add_btn.attr('disabled',false);
					ticko_ticket_div.append("Votre ticket n'a pas pu être crée, veuillez crée un nouveau, merci! ");
				}
			});            
		});
		
    });
})( jQuery );
