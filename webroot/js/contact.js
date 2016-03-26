$(function(){
	function sendMail(sSerializedForm){
		$.ajax({
			dataType	: 'json'
			,data			:	{
				form:	sSerializedForm
			}
			,type			:	'POST'
			,async		:	'false'
			,url			:	'/json/email'
			,success	: function(result){
				$.each(result.flags, function(key, val){
					var oValidationObject = $('[data-validation="'+key+'"]')
					oValidationObject.removeClass('valid invalid')
					oValidationObject.find('.validationMessage').html('')

					switch(val){
						case false:
							oValidationObject.addClass('invalid')
						break

						case true:
							oValidationObject.addClass('valid')
						break
					}

					if(typeof result.messages[key] != 'undefined' && typeof result.messages[key].message != 'undefined'){
						oValidationObject.find('.validationMessage').html(result.messages[key].message)
						oValidationObject.find('.validationMessage').show()
					}

					if(typeof result.messages[key] == 'undefined' && val == true){
						oValidationObject.find('.validationMessage').hide()
					}

				})
			}
		})	
	}

	function submitMail(oForm){
		if(
			!empty(oForm)
			&& oForm instanceof jQuery 
			&& oForm.get()[0].nodeName.toLowerCase() == 'form'
		){
			var sFormValue = ''

			oForm.children().find('input, textarea').each(function(k, v){
				var sName = $(v).attr('name')

				sFormValue += sName+'='

				if(typeof sName != 'undefined'){
					var sPlaceholder = empty($(v).attr('data-placeholder')) ? $(v).attr('placeholder') : $(v).attr('data-placeholder')
					var sVal = $(v).val()

					if(!empty(sVal)){
						if($(v).val() != sPlaceholder && !empty(sVal)){
							sFormValue += sVal
						}
					}
				}

				sFormValue += '&'

			})

			sendMail(sFormValue)
		}
	}

	$('#contact_form').on( "submit", function(e) {
		e.preventDefault()
		submitMail($(this))
		Recaptcha.reload()
	});
})
