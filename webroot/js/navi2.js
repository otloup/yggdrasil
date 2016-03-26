(function(){
 	var _navi = function(oConf){

		this.iPage = 1
		this.iLimit = 25
		this.iResults = 0
		this.iAllPages = 0
		this.iShowPages = 5
                this.bShowOnePage = false
		this.sId = Math.ceil(Math.random()*1000)
		this.sCallerName = 'navi'
		this.sNavContainer = ''
		this.oNavConfig = {
			firstLabel:'<<'
			,lastLabel:'>>'
			,prevLabel:'<'
			,nextLabel:'>'
			,container:'<a href="[LINK]" data-selected="[SELECTED]" data-page="[PAGE]" data-owner="[OWNER]" data-controler_type="[TYPE]">[LABEL]</a>'
			,activeContainer:'<a href="[LINK]" data-selected="[SELECTED]" data-page="[PAGE]" data-owner="[OWNER]" data-controler_type="[TYPE]">[LABEL]</a>'
			,separator:' '
			,ofLabel:' of '
			,spaceFiller:' ... '
		}
		this.fMethod = null

		var self = this


		var countLimits = function(){
			self.iAllPages = Math.ceil(self.iResults/self.iLimit)
    },

		getPage = function(iPage){
			if(iPage == 'reload'){
				iPage = self.iPage
			}
			else{
				self.iPage = iPage
			}
					
			var iResults = oConf.generatorMethod(iPage)
                        
      if(iResults != self.iResults){
				self.iResults = iResults
				countLimits()

				if(self.iPage > self.iAllPages){
					getPage(self.iPage-1)
					return false
				}

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
			if(self.iShowPages != 'none'){
				var iShowPages = self.iShowPages//+self.iPage < self.iAllPages ? self.iShowPages : (self.iAllPages - (self.iShowPages+self.iPage))
					
				if(self.iAllPages <= 1 && !self.bShowOnePage){
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
				                            
				if(iShowPages > self.iAllPages && self.iPage == 1){
          for(i = 0;i<self.iAllPages;i++){
                                            
						var iPage = self.iPage + i

						var sType = iPage == 1 ? 'first_page' : (iPage == self.iAllPages ? 'last_page' : 'page')

						if(iPage<=self.iAllPages){
							var sContainer = self.iPage == iPage ? _sActiveContainer : _sContainer					
					
							aHtml.push(replace(sContainer, {
								'[LINK]'			:	'javascript:'+self.sCallerName+'.getPage('+iPage+');'
								,'[SELECTED]'	: (self.iPage == iPage ? true : false)
								,'[PAGE]'			: iPage
								,'[OWNER]'		: self.sId
								,'[TYPE]'			:	sType
								,'[LABEL]'		: iPage
							}))	
            }   
					}
        }
				else if(iShowPages+self.iPage >= self.iAllPages){
					
					for(i = self.iAllPages-iShowPages;i<=self.iAllPages;i++){
						var iPage = i
						var sContainer = self.iPage == iPage ? _sActiveContainer : _sContainer

						var sType = iPage == 1 ? 'first_page' : (iPage == self.iAllPages ? 'last_page' : 'page')
						
						if(iPage > 0 && iPage <= self.iAllPages){
							aHtml.push(replace(sContainer, {
								'[LINK]'			:	'javascript:'+self.sCallerName+'.getPage('+iPage+');'
								,'[SELECTED]'	: (self.iPage == iPage ? true : false)
								,'[PAGE]'			: iPage
								,'[OWNER]'		: self.sId
								,'[TYPE]'			:	sType
								,'[LABEL]'		: iPage
							}))	
						}
					}
				}
				else{
					for(i = 0;i<iShowPages;i++){
						var iPage = self.iPage + i
						var sContainer = self.iPage == iPage ? _sActiveContainer : _sContainer					

						var sType = iPage == 1 ? 'first_page' : (iPage == self.iAllPages ? 'last_page' : 'page')

						aHtml.push(replace(sContainer, {
								'[LINK]'			:	'javascript:'+self.sCallerName+'.getPage('+iPage+');'
								,'[SELECTED]'	: (self.iPage == iPage ? true : false)
								,'[PAGE]'			: iPage
								,'[OWNER]'		: self.sId
								,'[TYPE]'			:	sType
								,'[LABEL]'		: iPage
						}))	
					}
				}
                                
				if(iShowPages+self.iPage < self.iAllPages && self.ofLabel == 'none'){
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

			if(self.iAllPages > 1){
				$(self.sNavContainer).html(aHtml.join(self.oNavConfig.separator))
			}
		}

		if(typeof oConf != 'undefined'){
      var error = false
			this.fMethod = oConf.generatorMethod
			this.iPage = oConf.page
			this.iLimit = oConf.limit
			this.iResults = oConf.results
			this.iShowPages = oConf.show_pages
			this.sNavContainer = oConf.nav_container
			this.bShowOnePage = oConf.show_one_page || self.bShowOnePage

			if(typeof this.fMethod != 'function'){
					console.error('invalid pagination method')
					error = true
			}
			
			if(this.iPage <= 0){
					console.error('starting page must be >= 1')
					error = true
			}

			if(this.iResults <= 0){
//					error = true
			}
			
			if(this.sNavContainer == ''){
					console.error('Navigation container must be supplied')
					error = true
			}

			if(error){
					return false
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
