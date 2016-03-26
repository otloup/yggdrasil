function registerUser(){
	bReturn = false
	$.ajax({
		async: false,
		dataType: 'json',
		type:'POST',
		url: 'json/register',
		data:{
			action:'register',
			TransactionID:$('input[name="TransactionID"]').val(),
			contact:$('input[name="contact"]').val(),
			fname:$('input[name="fname"]').val(),
			lname:$('input[name="lname"]').val(),
			email:$('input[name="display_email"]').val(),
			cb:$('input[name="cb"]').val(),
			pp:$('input[name="pp"]').val(),
			prr:$('input[name="prr"]').val(),
			jvz:$('input[name="jvz"]').val(),
			url:$('input[name="url"]').val(),
			urlsqueeze:$('input[name="urlsqueeze"]').val(),
			HostURL:$('input[name="HostURL"]').val(),
			HostUsername:$('input[name="HostUsername"]').val(),
			HostPassword:$('input[name="HostPassword"]').val(),
			Notes:$('textarea[name="Notes"]').val(),
			recaptcha_response_field:$('input[name="recaptcha_response_field"]').val(),
			recaptcha_challenge_field:$('input[name="recaptcha_challenge_field"]').val()
		},
		success: function(data) {
			if(!data.recaptcha_status){
				$('#recaptcha_error').show()
			}
			else if(!data.register){
				$('#register_error').show()
			}
			else if(data.recaptcha_status && data.register){
				bReturn = data.register
			}
		}
	})

	return bReturn
}
