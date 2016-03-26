var _placeholder = function(oConfig){

	this.oConfig = {
		mode:		'soft'
		,ommit:	''
	}

	this.placeholderSupport = function(){
		var oTestInput = document.createElement('input')
		var bSupport = !!('placeholder' in oTestInput)
		
		delete oTestInput
		return bSupport
	}

	this.mandatory = function(sName){
		return (oConfig.ommit.indexOf(sName) == '-1')
	}

	this.addPlaceholder = function(oMainObject){
		var getPlaceholderValue = function (oObject){
			oObject = typeof(oObject) == 'undefined' ? oMainObject : oObject

			return sPlaceholder = typeof oObject.attr('placeholder') == 'undefined' ? oObject.data('placeholder') : oObject.attr('placeholder')
		}

		var clear = function(sType, oObject){
			oObject = typeof(oObject) == 'undefined' ? oMainObject : oObject

			if(sType == 'clean'){
				if(oObject.val() == getPlaceholderValue(oObject)){
					oObject.val('')
				}
			}

			if(sType == 'placeholder'){
				if(oObject.val() == ''){
					oObject.val(getPlaceholderValue(oObject))
				}
			}
		}

		var clearOnFocus = function(oObject){
			oObject = typeof(oObject) == 'undefined' ? oMainObject : oObject

			oObject.focus(function(){
				clear('clean', $(this))
			})
		}

		var populateOnKeyUp = function(oObject){
			oObject = typeof(oObject) == 'undefined' ? oMainObject : oObject

			oObject.keyup = function(){
				clear('placeholder', $(this))
			}
		}
		
		var populateOnBlur = function(oObject){
			oObject = typeof(oObject) == 'undefined' ? oMainObject : oObject

			oObject.blur(function(){
				clear('placeholder', $(this))
			})
		}

		clear('placeholder')

		clearOnFocus()
		populateOnKeyUp()
		populateOnBlur()
	}

	self = this

	function init(oConfig){

		if(!empty(oConfig)){
			$.extend(self.oConfig, oConfig)
		}

		var bSubstitutePlaceholder = !self.placeholderSupport()

		if(typeof oConfig != 'undefined'){
			bSubstitutePlaceholder = (typeof oConfig.mode != 'undefined' && oConfig.mode == 'hard') ? true : bSubstitutePlaceholder
		}

		if(bSubstitutePlaceholder){
			$('input[placeholder]').each(function(key, val){
				val = $(val)

				if(self.mandatory(val.attr('name'))){
					self.addPlaceholder(val)
				}
			})
		}

		$('[data-placeholder]:not(input)').each(function(key, val){
			val = $(val)

			if(self.mandatory(val.attr('name'))){
				self.addPlaceholder(val)
			}
		})
	}

	init(oConfig)

	return {}

}

window.placeholder = _placeholder
