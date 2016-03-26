(function(){
 	var _navi = function(oConf){

		this.iPage = 1
		this.iLimit = 25
		this.iResults = 0
		this.iAllPages = 0
		this.iShowPages = 5
		this.sId = Math.ceil(Math.random()*1000)
		this.sCallerName = 'navi'
		this.sNavContainer = ''
		this.oNavConfig = {
			firstLabel:'&laquo;'
			,lastLabel:'&raquo;'
			,prevLabel:'<'
			,nextLabel:'>'
			,container:'<a href="[LINK]" data-selected="[SELECTED]" data-page="[PAGE]" data-owner="[OWNER]" data-controler_type="[TYPE]">[LABEL]</a>'
			,activeContainer:'<a href="[LINK]" data-selected="[SELECTED]" data-page="[PAGE]" data-owner="[OWNER]" data-controler_type="[TYPE]">[LABEL]</a>'
			,separator:' '
			,ofLabel:'none'
			,spaceFiller:' ... '
		}
		this.fMethod = null

		var self = this


		var countLimits = function(){
			self.iAllPages = Math.ceil(self.iResults/self.iLimit)
			console.log([
					self.iResults
					,self.iLimit
					,self.iAllPages
				])

		},

		getPage = function(iPage){
			if(iPage == 'reload'){
				iPage = self.iPage
			}
			else{
				self.iPage = iPage
			}
			
			oConf.generatorMethod(iPage)
				
			if(iResults != self.iResults){
				self.iResults = iResults
				countLimits()
			}
			
			generateNav()
		},

		next = function(){
			if(self.iPage+1<=self.iAllPages){
				self.iPage += 1
				getPage(self.iPage)
			}

			return false;
		},

		prev = function(){
			if(self.iPage-1>=1){
				self.iPage -= 1
				getPage(self.iPage)
			}

			return false;
		},

		generateNav = function(){
			var aHtml = []
			var _sContainer = self.oNavConfig.container;
			var _sActiveContainer = self.oNavConfig.activeContainer;
	
			if(self.oNavConfig.firstLabel != 'none' && self.iPage > 1){
				var sContainer = self.iPage == 1 ? _sActiveContainer : _sContainer

				aHtml.push(replace(sContainer, {
							'[LINK]'			:	'javascript:'+self.sCallerName+'.getPage(1);'
							,'[SELECTED]'	: (self.iPage == 1 ? true : false)
							,'[PAGE]'			: 1
							,'[OWNER]'		: self.sId
							,'[TYPE]'			:	'forward_first'
							,'[LABEL]'		: self.oNavConfig.firstLabel
						}))
			}

			if(self.oNavConfig.prevLabel != 'none' && self.iPage > 1){
				var sContainer = _sContainer			

				aHtml.push(replace(sContainer, {
							'[LINK]'			:	'javascript:'+self.sCallerName+'.prev();'
							,'[SELECTED]'	: false
							,'[PAGE]'			: 0
							,'[OWNER]'		: self.sId
							,'[TYPE]'			:	'prev'
							,'[LABEL]'		: self.oNavConfig.prevLabel
						}))
			}

			if(self.oNavConfig.iShowPages != 'none'){

				var iShowPages = self.iShowPages//+self.iPage < self.iAllPages ? self.iShowPages : (self.iAllPages - (self.iShowPages+self.iPage))
		
				if(self.iResults < 2){
					return ''
				}

				if(self.iPage > 1 && iShowPages < self.iAllPages){
					var sContainer = self.iPage == 1 ? _sActiveContainer : _sContainer			
					
					aHtml.push(replace(sContainer, {
							'[LINK]'			:	'javascript:'+self.sCallerName+'.getPage(1);'
							,'[SELECTED]'	: (self.iPage == 1 ? true : false)
							,'[PAGE]'			: iPage
							,'[OWNER]'		: self.sId
							,'[TYPE]'			:	'page_first'
							,'[LABEL]'		: 1
						})+self.oNavConfig.spaceFiller)
				}
				
				if(iShowPages > self.iAllPages){
					for(i = 0;i<self.iAllPages;i++){
						var iPage = self.iPage + i
						var sContainer = self.iPage == iPage ? _sActiveContainer : _sContainer					
						
						aHtml.push(replace(sContainer, {
								'[LINK]'			:	'javascript:'+self.sCallerName+'.getPage('+iPage+');'
								,'[SELECTED]'	: (self.iPage == iPage ? true : false)
								,'[PAGE]'			: iPage
								,'[OWNER]'		: self.sId
								,'[TYPE]'			:	'page'
								,'[LABEL]'		: iPage
							}))	
					}
				}
				else if(iShowPages+self.iPage >= self.iAllPages){
					for(i = self.iAllPages-iShowPages;i<=self.iAllPages;i++){
						var iPage = i
						var sContainer = self.iPage == iPage ? _sActiveContainer : _sContainer					
						
						aHtml.push(replace(sContainer, {
								'[LINK]'			:	'javascript:'+self.sCallerName+'.getPage('+iPage+');'
								,'[SELECTED]'	: (self.iPage == iPage ? true : false)
								,'[PAGE]'			: iPage
								,'[OWNER]'		: self.sId
								,'[TYPE]'			:	'page'
								,'[LABEL]'		: iPage
							}))	
					}
				}
				else{
					for(i = 0;i<iShowPages;i++){
						var iPage = self.iPage + i
						var sContainer = self.iPage == iPage ? _sActiveContainer : _sContainer					
							
						aHtml.push(replace(sContainer, {
								'[LINK]'			:	'javascript:'+self.sCallerName+'.getPage('+iPage+');'
								,'[SELECTED]'	: (self.iPage == iPage ? true : false)
								,'[PAGE]'			: iPage
								,'[OWNER]'		: self.sId
								,'[TYPE]'			:	'page'
								,'[LABEL]'		: iPage
							}))	
					}
				}

				if(iShowPages+self.iPage < self.iAllPages && self.oNavConfig.ofLabel == 'none'){
				var sContainer = self.iPage == self.iAllPages ? _sActiveContainer : _sContainer

					aHtml.push(self.oNavConfig.spaceFiller+replace(sContainer, {
							'[LINK]'			:	'javascript:'+self.sCallerName+'.getPage('+self.iAllPages+');'
							,'[SELECTED]'	: (self.iPage == self.iAllPages ? true : false)
							,'[PAGE]'			: self.iAllPages
							,'[OWNER]'		: self.sId
							,'[TYPE]'			:	'page_last'
							,'[LABEL]'		: self.iAllPages
						}))
				}
			}

			if(self.oNavConfig.ofLabel != 'none'){
				var sContainer = self.iPage == self.iAllPages ? _sActiveContainer : _sContainer

				aHtml.push(self.oNavConfig.ofLabel+replace(sContainer, {
							'[LINK]'			:	'javascript:'+self.sCallerName+'.getPage('+self.iAllPages+');'
							,'[SELECTED]'	: (self.iPage == self.iAllPages ? true : false)
							,'[PAGE]'			: self.iAllPages
							,'[OWNER]'		: self.sId
							,'[TYPE]'			:	'page_last'
							,'[LABEL]'		: self.iAllPages
						}))
			}

			if(self.oNavConfig.nextLabel != 'none' && self.iPage < self.iAllPages){
				var sContainer = _sContainer			
				
				aHtml.push(replace(sContainer, {
							'[LINK]'			:	'javascript:'+self.sCallerName+'.next();'
							,'[SELECTED]'	: false
							,'[PAGE]'			: 0
							,'[OWNER]'		: self.sId
							,'[TYPE]'			:	'next'
							,'[LABEL]'		: self.oNavConfig.nextLabel
						}))
			}

			if(self.oNavConfig.lastLabel != 'none' && self.iPage < self.iAllPages){
				var sContainer = self.iPage == self.iAllPages ? _sActiveContainer : _sContainer			

				aHtml.push(replace(sContainer, {
							'[LINK]'			:	'javascript:'+self.sCallerName+'.getPage('+self.iAllPages+');'
							,'[SELECTED]'	: (self.iPage == self.iAllPages ? true : false)
							,'[PAGE]'			: self.iAllPages
							,'[OWNER]'		: self.sId
							,'[TYPE]'			:	'forward_last'
							,'[LABEL]'		: self.oNavConfig.lastLabel
						}))
			}

			$(self.sNavContainer).html(aHtml.join(self.oNavConfig.separator))
		}

		if(typeof oConf != 'undefined'){
			this.fMethod = oConf.generatorMethod
			this.iPage = oConf.page
			this.iLimit = oConf.limit
			this.iResults = oConf.results
			this.iShowPages = oConf.showPages
			this.sNavContainer = oConf.navContainer

			if(
					typeof this.fMethod != 'function'
					|| this.iPage <= 0
					|| this.iResults <= 0
					|| this.sNavContainer == ''
				){
				console.error('invalid configuration')
				console.log([
						(typeof this.fMethod)
						,this.iPage
						,this.iResults
						,this.sNavContainer
				])
				return false;
			}

			if(typeof oConf.oNavConfig != 'undefined'){
				this.oNavConfig.firstLabel = oConf.oNavConfig.first 		||	this.oNavConfig.firstLabel
				this.oNavConfig.lastLabel  = oConf.oNavConfig.last			||	this.oNavConfig.lastLabel
				this.oNavConfig.prevLabel = oConf.oNavConfig.prev 			||	this.oNavConfig.prevLabel
				this.oNavConfig.nextLabel  = oConf.oNavConfig.next			||	this.oNavConfig.nextLabel
				this.oNavConfig.container	 = oConf.oNavConfig.container	||	this.oNavConfig.container
				this.oNavConfig.activeContainer	 = oConf.oNavConfig.active_container	||	this.oNavConfig.activeContainer
				this.oNavConfig.separator  = oConf.oNavConfig.separator	||	this.oNavConfig.separator
				this.oNavConfig.ofLabel  = oConf.oNavConfig.of_label		||	this.oNavConfig.ofLabel
			}

			self.sCallerName = self.sCallerName+self.sId

			countLimits()
		}


		if(this.iPage > 1){
			getPage(this.iPage)
		}

		window[this.sCallerName] = {
			next					:	function(){next()}
			,prev					:	function(){prev()}
			,getPage			:	function(iPage){getPage(iPage)}
			,getAllPages	:	function(){return this.iAllPages}
			,getNaviId		:	function(){return this.sId}
			,genHtml			:	function(){return generateNav()}
		} 

		return window[this.sCallerName]
	}

	window.navi = _navi
 })(window)

