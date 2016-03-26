iCurrentIndex = 0

function loadServiceDescription(iIndex){
	if(iIndex == iCurrentIndex){
		return false
	}

	$.ajax({
		dataType	: 'json'
		,data			:	{
			index:	Number(iIndex)
		}
		,type			:	'POST'
		,async		:	'true'
		,url			:	'/json/services'
		,success	: function(result){
			$('#service_title').html(result.header)
			$('#service_description_content').html(result.content)

			$('#service_title').data({name:'slider_services_'+result.name+'_header'})
			$('#service_description_content').data({name:'slider_services_'+result.name+'_content'})

			iCurrentIndex = iIndex
		}
	})	
}

$(function(){
	window.services_slider = new slider({
		oSlider: $('#services_slider'),
		oNavPrev: $('[data-trigger="work_switch"][data-option="prev"]'),
		oNavNext: $('[data-trigger="work_switch"][data-option="next"]'),
		callback: function(iIndex){
			loadServiceDescription(iIndex)
		}
	})

	services_slider.focusSlide('first')
})

