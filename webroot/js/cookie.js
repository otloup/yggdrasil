(function(window){
	var cookie = function (sName, mValue, bReplace, iValidityPeriod, sPeriodExpression, sPath){

		var timeTable = {
			ms:1,
			s:1000,
			mi:(60*1000),
			h:(60*60*1000),
			d:(24*60*60*1000),
			w:(7*24*60*60*1000),
			mo:(4*7*24*60*60*1000),
			y:(12*4*7*24*60*60*1000)
		}, 

		sCookieTpl = '{name}={value}; expires={expire_date}; path={path}', 

		set = function (sName, mValue, bReplace, iValidityPeriod, sPeriodExpression, sPath){
			bReplace = typeof bReplace == 'undefined' ? true : bReplace
			iValidityPeriod = iValidityPeriod || ''
			sPeriodExpression = sPeriodExpression || 'h'
			sPath = sPath || '/'
			date = new Date()
			
			var oCookieValues = {
				'{name}':sName,
				'{value}':String(mValue),
				'{expire_date}':'',
				'{path}':''
			}

			var sCookie = sCookieTpl

			if(iValidityPeriod != ''){
				if(typeof timeTable[sPeriodExpression] != 'undefined'){
					iValidityPeriod = Number(iValidityPeriod)*timeTable[sPeriodExpression]
					iValidityPeriod = date.setTime(date.getTime()+(iValidityPeriod));

					oCookieValues['{expire_date}'] = date.toGMTString(iValidityPeriod)
				}
				else{
					return false;
				}
			}

			if(!bReplace){
				if(!!(sPrevious = read(sName,'raw'))){
					oCookieValues['{value}'] = sPrevious+','+mValue
				}
			}

			$.each(oCookieValues, function(k,v){
						sCookie = sCookie.replace(k,v)
					})

			return !!(typeof(document.cookie = sCookie))
		},

		read = function(sName, sMode){
			sMode = sMode || 'raw'

			var aCookies = document.cookie.match(new RegExp(sName+'=([^;]*)','gi'))

			if(typeof aCookies != 'undefined' && aCookies != null && aCookies.length>0){
				var mValue = aCookies[0]
				mValue = mValue.substr(mValue.indexOf('=')+1)

				switch(sMode){
					case 'array':
						aReturn = []
						mValue = mValue.split(',')
						$.each(mValue, function(k,v){
							if(v != ''){
								aReturn.push(v)
							}

						})

						mValue = aReturn
					break;

					case 'object':
						oReturn = {}
						mValue = mValue.split(',')
						$.each(mValue, function(k,v){
							if(v != ''){
								v = v.split(':')
								oReturn[v[0]] = v[1]
							}
						})

						mValue = oReturn
					break;

					case 'string':
						mValue = String(mValue)
					break;

					case 'int':
						mValue = Number(mValue)
					break;

					case 'raw':
						mValue = mValue
					break;
				}

				return mValue
			}

			return false
		},

		edit = function(sName, mValue, sMode){
			if(typeof mValue != 'undefined'){
				mValue = String(mValue)
				aCookie = read(sName,'array')
				if(!!aCookie){
					switch(sMode){
						case 'add':
							if($.inArray(mValue,aCookie)==-1){
								aCookie.push(mValue)
								set(sName, aCookie.join(','))
							}
						break;
		
						case 'remove':
							if(-1 != (iPos = $.inArray(mValue,aCookie))){
								delete aCookie[iPos]
								set(sName, aCookie.join(','))
							}
						break;
					}
				}
				else if(sMode=='add'){
					new cookie(sName, mValue)
				}
			}
		}

		erase = function(sName){
			set(sName,'',true,-1)
			return !(!!read(sName))
		};

		if(cookie.arguments.length>=2 && typeof arguments[0] != 'undefined' && typeof arguments[1] != 'undefined'){
			bReplace = arguments[2] || ''
			iValidityPeriod = arguments[3] || '' 
			sPeriodExpression = arguments[4] || '' 
			sPath = arguments[5] || ''
			set(sName, mValue, bReplace, iValidityPeriod, sPeriodExpression, sPath)
		}

		return {
			set:function(sName, mValue, bReplace, iValidityPeriod, sPeriodExpression, sPath){return set(sName, mValue, bReplace, iValidityPeriod, sPeriodExpression, sPath)},
			edit:function(sName, mValue, sMode){return edit(sName, mValue, sMode)},
			read:function(sName, sMode){return read(sName, sMode)},
			erase:function(sName){return erase(sName)}
		}
	}
	window.cookie = new cookie()
})(window)

