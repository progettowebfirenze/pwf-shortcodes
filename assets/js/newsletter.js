jQuery(document).ready(function($) {
	var newsletterform = $('#newsletter-form');
   	newsletterform.on("submit", function(e) {
        e.preventDefault();
        var form 		= $(this),
            action 		= 'pwf_process_newsletter',
            method 		= form.attr("method"),
			response 	= form.find('.response');
						
		var form_data = new FormData($(this)[0]);
		
		form_data.append('action', action);
		
        $.ajax({
            type: 			method,
            url: 			newsletterajax,
            data: 			form_data,
			processData: 	false,
			contentType: 	false,
            dataType: 		"json",
            beforeSend: 	function() {
                				$('#wait').show();
            				},
            complete: 		function(data) {
								$('#wait').hide();
								var msg = $.parseJSON(data.responseText);
								if (msg.success === true) {
									response.addClass('success').removeClass('error');
									response.html(msg.html);
								}
								else if (msg.success === false) {
									response.addClass('error').removeClass('success');
									response.html(msg.html);
								}
							},
            error: 			function() {
								alert("si è verificato un qualche errore");
							}
        });
    });
});