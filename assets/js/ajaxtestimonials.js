jQuery(document).ready(function($) {
	var registrationform = $('#testimonialsform');
	
   registrationform.on("submit", function(e) {
        e.preventDefault();
        var form = $(this),
            action = 'pwf_sendtestimonials_mail',
            method = form.attr("method");
						
		var form_data = new FormData($(this)[0]);
		
		$('input[type=file]').each(function() {
            var file_data = $(this).prop('files')[0];
			form_data.append('file', file_data);
        });
		
		form_data.append('action', action);
		
        $.ajax({
            type: method,
            url: ajaxtestimonials,
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
                if (msg.success === true) {
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
								registrationform.formValidation().destroy();
								registrationform.reset();
                            }
                        }
                    });
                }
                else if (msg.success === false) {
                    $('body').append('<div id="results" class="error"></div>');
                    $('#results').append("<div>" + msg.html + "</div>");
                    $('#results').show();
                    $.magnificPopup.open({
                        items: {
                            src: '#results',
                            type: 'inline'
                        },
                        callbacks: {
                            afterClose: function() {
								registrationform.data('formValidation').resetForm();
                            }
                        }
                    });
                }
            },
            error: function() {
                alert("si è verificato un qualche errore")
            }
        })
    });
	
	registrationform.formValidation({
        framework: 'bootstrap',
        icon: {
            valid: 'glyphicon glyphicon-ok',
            invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh'
        },
		addOns: {
			i18n: {}
		},
        fields: {
			_pwf_name: {
				row: '.col-sm-6',
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
			
			_pwf_title: {
				row: '.col-sm-6',
				validators: {
						notEmpty: {
							message: function(validator, $field, validatorName) {
								return {							
								it_IT: 'Il titolo è richiesto',
								en_GB: 'Title is required',
								};	
							}
						}
				}
			},
			
			_pwf_image: {
				row: '.col-sm-12',
				validators: {
						file: {
							extension: 'jpeg,jpg,png',
							type: 'image/jpeg,image/png',
							maxSize: 2097152,   // 2048 * 1024
							message: function(validator, $field, validatorName) {
								return {							
								it_IT: 'L\'immagine selezioneta non è valida',
								en_GB: 'The selected file is not valid',
								};	
						}
					}
				},
			},
			
			
			_pwf_message: {
				row: '.col-sm-12',
				validators: {
					notEmpty: {
						message: function(validator, $field, validatorName) {
							return {							
							it_IT: 'Una recensione è richiesta',
							en_GB: 'A review is required',
							};	
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
			}
			}
        }
    })
	.on('success.form.fv', function(e) {
		e.preventDefault();
	});
	registrationform.formValidation('setLocale', 'en_GB');
	
	///metti qui le tue funzioni
	
	var fileinput = $('#fileupload');
	fileinput.on('change', function() {
		var filename = $(this).val().split('\\').pop(),
			label = $('label[for=fileupload]');
			label.find('span').html(filename);
			
	});
	
	$( "#_pwf_date" ).dateDropper({
		dropPrimaryColor: '#4f3d36',
		dropTextColor: '#666',
		dropBorder: '1px solid #C64E61',
		dropBorderRadius: '0',
		dropShadow: '7px 7px 10px 0 rgba(0, 0, 0, 0.45)',
		dropWidth: '250'
		});
	
});