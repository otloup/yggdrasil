$(function(){
	function saveEmail(sEmailAddress){
		$.ajax({
			dataType	: 'json'
			,data			:	{
				email:	sEmailAddress
			}
			,type			:	'POST'
			,async		:	'false'
			,url			:	'/json/newsletter'
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

	function submitEmail(oForm){
		if(
			!empty(oForm)
			&& oForm instanceof jQuery 
			&& oForm.get()[0].nodeName.toLowerCase() == 'form'
		){

			var sFormValue = ''

			var oEmail = oForm.find('input[name="email"]')
			var sPlaceholder = empty(oEmail.attr('data-placeholder')) ? oEmail.attr('placeholder') : oEmail.attr('data-placeholder')
			var sVal = oEmail.val()

			if(!empty(sVal)){
				if(oEmail.val() != sPlaceholder && !empty(sVal)){
					sFormValue += sVal
				}
			}
			
			if(!empty(sFormValue)){
				saveEmail(sFormValue)
			}
		}
	}

	$('#newsletter_subscription').on("submit", function(e) {
		e.preventDefault()
		submitEmail($(this))
	});
})
