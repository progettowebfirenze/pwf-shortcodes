jQuery(document).ready(function($) {
    $('#contact_form').on("submit", function(e) {
        e.preventDefault();
        var form = $(this),
            action = 'pwf_sendmail',
            method = form.attr("method");
						
		var form_data = new FormData($(this)[0]);
		
		$('input[type=file]').each(function() {
            var file_data = $(this).prop('files')[0];
			form_data.append('file', file_data);
        });
		
		form_data.append('action', action);
		
        $.ajax({
            type: method,
            url: ajaxcontact,
            data: form_data,
			processData: false,
			contentType: false,
            dataType: "json",
            beforeSend: function() {
                $('#wait').show();
            },
            complete: function(data) {
                $('#wait').hide();
                var msg = $.parseJSON(data.responseText);
                if (msg.success == true) {
                    $('body').append('<div id="results"></div>');
                    $('#results').append("<div>" + msg.html + "</div>");
                    $('#results').show();
                    $.magnificPopup.open({
                        items: {
                            src: '#results',
                            type: 'inline'
                        },
                        callbacks: {
                            afterClose: function() {
                                if (refr == 'true') {
                                    location.reload(true);
                                };
                            }
                        }
                    });
                }
            },
            error: function() {
                alert("si è verificato un qualche errore");
            }
        });
    });
	
	$('#contact_form').formValidation({
        framework: 'bootstrap',
        icon: {
            valid: 'glyphicon glyphicon-ok',
            invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh'
        },
        fields: {
            _pwf_name: {
                row: '.col-sm-12',
                validators: {
                    notEmpty: {
                        message: function(validator, $field, validatorName) {
								return {							
								it_IT: 'Il nome è richiesto',
								en_GB: 'Name is required',
								};	
							}
                    }
                }
            },
            _pwf_email: {
                validators: {
                    notEmpty: {
                        message: function(validator, $field, validatorName) {
								return {							
								it_IT: 'L\'indirizzo mail è richiesto',
								en_GB: 'Email address is required',
								};	
							}
                    },
                    emailAddress: {
                        message: function(validator, $field, validatorName) {
								return {							
								it_IT: 'L\'indirizzo email non è valido',
								en_GB: 'Input is not a valid email address',
								};	
							}
                    }
                }
            },
            _pwf_message: {
                validators: {
                    notEmpty: {
                        message: function(validator, $field, validatorName) {
								return {							
								it_IT: 'Il messaggio è richiesto',
								en_GB: 'Message is required',
								};	
							}
                    },
                    stringLength: {
                        min: 20,
                        message: function(validator, $field, validatorName) {
								return {							
								it_IT: 'Scrivi un po\' di più',
								en_GB: 'Write jus a little bit more',
								};	
							}
                    }
                }
            },
        }
    })
	.on('success.form.fv', function(e) {
		e.preventDefault();
	});
});