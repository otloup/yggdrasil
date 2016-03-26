function serializeForm(oForm){
	if(
		!empty(oForm)
		&& oForm instanceof jQuery
		&& oForm.get()[0].nodeName.toLowerCase() == 'form'
	){
		var sFormValue = ''
			oForm.find('input, textarea').each(function(k, v){
				
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

				if($(v).data('tmc') == true){
					sFormValue += encodeURIComponent(tinymce.get($(v).attr('id')).getContent());
				}
			}

			sFormValue += '&'
		})

		return sFormValue
	}

	return ''
}


function empty(variable) {
    return (typeof variable == 'undefined' || variable == '' || variable === null)
}

function loadTriggers(sTarget, oFrame, bReload){
	var sTarget = typeof sTarget == 'undefined' ? '[data-trigger]' : '[data-trigger="'+sTarget+'"]'
	var oReference = typeof oFrame == 'undefined' ? $('body') : oFrame

	if(bReload){
		oReference.find(sTarget).data({'trigger_loaded':undefined})
		oReference.find(sTarget).unbind('click')
	}

	if(typeof oReference.find(sTarget).data('trigger_loaded') == 'undefined'){
		oReference.find(sTarget).click(function(){
			var sTrigger = $(this).data('trigger')
			var sOption = $(this).data('option')
			var sCallback = $(this).data('callback')
			var mId = $(this).data('identifier')
			
			switch(sTrigger){
				case 'dialog':
					manageDialog(sOption, sCallback)
				break;

				case 'dropdown':
					manageDropdown(sOption, sCallback)
				break;

				case 'edition':
					editionAction(sOption)
				break;

				case 'edition_toggle':
					toggleEdition(sOption)
				break;

				case 'cookie_close':
					closeCookie()
				break;

				case 'news': 
					manageNews(sOption, mId);
				break;

				case 'language':
					switchLang(mId);
				break;
			}
		})

		oReference.find(sTarget).data({trigger_loaded:true})

		return oReference.find(sTarget).data('trigger_loaded')
	}
	else{
		return true
	}
}

function manageDropdown(sOption){
	var oObject = $('.dropdown_wrapper[data-option="'+sOption+'"]');
	
	if(oObject.hasClass('hidden')){
		oObject.removeClass('hidden').addClass('visible');
	}
	else{
		oObject.removeClass('visible').addClass('hidden');
	}
}

function readHash(){
    window.hash = {}

    if(location.hash != ''){
        var sHash = location.hash.substr(1)
        var aHash = sHash.split('&')
        delete sHash
        $.each(aHash, function(key, val){
            var aValue = val.split('=')
            window.hash[aValue[0]] = aValue[1]
        })
    }
}

function readSearch(){
    window.search = {}

    if(location.search != ''){
        var aSearch = location.search.substr(1).split('&')
        $.each(aSearch, function(key, val){
            var aValue = val.split('=')
            window.search[aValue[0]] = aValue[1]
        })
    }
}

function closeCookie(){
	$('#cookie_confirmation').hide()
	cookie.set('cookie_confirmation', true)
}

function replace(sString, oReplaceObject){
  $.each(oReplaceObject, function(k,v){
        sString = sString.replace(k,v)
      })
  return sString
}

function switchLang(sLang){
	sUrl = oUrlBase.base;

	if(location.href.indexOf('cms.') > -1){
		sUrl = 'cms.'+sUrl
	}

	switch(sLang){
		case 'PL':
			sUrl = 'gb.'+sUrl		
		break;

		case 'GB':
			sUrl = 'pl.'+sUrl
		break;
	}

	if(sUrl != ''){
		var sPathname = location.pathname.length > 1 ? location.pathname.substr(1) : ''
		location.href = 'http://'+sUrl+'/'+sPathname
	}
}
