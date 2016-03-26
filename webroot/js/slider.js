var _slider = function(mTarget){
	this._oSliderOptions = {
		iCurrentSlide: 								0
		,iAllSlides:									0
		,sListAddress: 								undefined
		,sSlideContentAddress:				undefined
		,bPreloadContent:							false
		,oSlider: 										undefined
		,sSliderWrapperSelector:			'.slider_wrapper'
		,sSliderSelector:							'.slider'
		,sSliderFocusSelector:				'.slider_focus'
		,sSliderFrameWrapperSelector:	'.slider_frames_wrapper'
		,sSliderFrameClass:						'slider_frame'
		,sSliderCurrentClass:					'current_frame'
		,sSliderPrevClass:						'prev_frame'
		,sSliderNextClass:						'next_frame'
		,sSliderNavWrapperSelector:		'.slider_nav'
		,sSliderNavButtonClass:				'slider_nav_button'
		,sSliderNavButtonActiveClass:	'active'
		,oNavPrev:										undefined
		,oNavNext:										undefined

		,callback:										undefined
	};

	this.oCurrent;
 	this.oPrev;
	this.oNext;

	this.getCurrentSlide = function(){
		self.oCurrent = self._oSliderOptions.oSlider.find(self._oSliderOptions.sSliderFrameWrapperSelector+'>.'+self._oSliderOptions.sSliderFrameClass+':eq('+self._oSliderOptions.iCurrentSlide+')')
	}
	this.getPrevSlide = function(){
		self.oPrev = self._oSliderOptions.oSlider.find(self._oSliderOptions.sSliderFrameWrapperSelector+'>.'+self._oSliderOptions.sSliderFrameClass+':eq('+Number(self._oSliderOptions.iCurrentSlide-1)+')')
	}
	this.getNextSlide = function(){
		self.oNext = self._oSliderOptions.oSlider.find(self._oSliderOptions.sSliderFrameWrapperSelector+'>.'+self._oSliderOptions.sSliderFrameClass+':eq('+Number(self._oSliderOptions.iCurrentSlide+1)+')')
	}

	this.generateSlider = function(){
		this.getSlidesList = function(){
			$.ajax({
				 dataType	: 'json'
				,type			:	'GET'
				,async		:	'false'
				,url			:	self._oSliderOptions.sListAddress
				,success	: function(result){
					self._oSliderOptions.oSlides = result
				}
			})
		}

		this.createSlides = function(){
			var aSlides = []

			$.each(self._oSliderOptions.oSlides, function(key, val){
				aSlides.push($('<div data-name="'+val.name+'" data-source="'+val.src+'">'))
			})

			$.each(aSlides, function(key, val){
				val.addClass(self._oSliderOptions.sSliderFrameClass)

				if(self._oSliderOptions.bPreloadContent){
					this.loadSlideContent(val)
				}
			})

			$(self._oSliderOptions.sSliderFramesWrapperSelector).append(aSlides)
		}

		this.createNavigation = function(){
			var aNavigation = []

			$.each(self._oSliderOptions.oSlides, function(key, val){
				aNavigation.push($('<div>'))
			})

			$.each(aNavigation, function(key, val){
				val.addClass(self._oSliderOptions.sSliderNavButtonClass)
			})

			$(self._oSliderOptions.sSliderNavWrapperSelector).append(aNavigation)	
		}

		this.getSlideContent = function(sContentSrc){
			var sContentHtml = ''

			$.ajax({
				 dataType	: 'html'
				,type			:	'GET'
				,async		:	'false'
				,url			:	sContentSrc
				,success	: function(result){
					sContentHtml = result
				}
			})

			return sContentHtml		
		}

		this.loadSlideContent = function(oSlide){
			if(!(oSlide instanceof jQuery)){
				return false
			}

			var sSrc = typeof self._oSliderOptions.sSlideContentAddress == 'undefined' ? oSlide.data('source') : self._oSliderOptions.sSlideContentAddress+oSlide.data('name')

			oSlide.html(this.getSlideContent(sSrc))
		}

		this.getSlidesList()
		this.createSlides()
		this.createNavigation()
	}

	this.activateSlider = function(){
		self._oSliderOptions.iAllSlides =	self._oSliderOptions.oSlider.find(self._oSliderOptions.sSliderFrameWrapperSelector+'>.'+self._oSliderOptions.sSliderFrameClass).length-1 

		this.appendSlidesActions = function(){
			self._oSliderOptions.oSlider.find(self._oSliderOptions.sSliderFrameWrapperSelector+'>.'+self._oSliderOptions.sSliderFrameClass).click(function(){
				var iIndex = Number($(this).index())

				if(self._oSliderOptions.iCurrentFrame != iIndex){
					self._oSliderOptions.oSlider.find(self._oSliderOptions.sSliderNavWrapperSelector+'>.'+self._oSliderOptions.sSliderNavButtonClass).removeClass(self._oSliderOptions.sSliderNavButtonActiveClass)
					self._oSliderOptions.oSlider.find(self._oSliderOptions.sSliderNavWrapperSelector+'>.'+self._oSliderOptions.sSliderNavButtonClass+':eq('+iIndex+')').addClass(self._oSliderOptions.sSliderNavButtonActiveClass)

					self.focusSlide(iIndex)
				}
			})
		}

		this.appendNavigationActions = function(){
			var iCurrentSlide = self._oSliderOptions.iCurrentSlide
			var bNavPrevAvailable = !!(typeof self._oSliderOptions.oNavPrev != 'undefined' && self._oSliderOptions.oNavPrev instanceof jQuery)
			var bNavNextAvailable = !!(typeof self._oSliderOptions.oNavNext != 'undefined' && self._oSliderOptions.oNavNext instanceof jQuery)

			self._oSliderOptions.oSlider.find(self._oSliderOptions.sSliderNavWrapperSelector+'>.'+self._oSliderOptions.sSliderNavButtonClass).click(function(){
				if(!$(this).hasClass(self._oSliderOptions.sSliderNavButtonActiveClass)){
					$(this).siblings().removeClass(self._oSliderOptions.sSliderNavButtonActiveClass)
					$(this).addClass(self._oSliderOptions.sSliderNavButtonActiveClass)

					var iIndex = Number($(this).index())
					self.focusSlide(iIndex)
				}
			})

			if(bNavPrevAvailable){
				self._oSliderOptions.oNavPrev.click(function(){
					iCurrentSlide = (iCurrentSlide > 0) ? iCurrentSlide-1 : self._oSliderOptions.iAllSlides
					self.focusSlide(iCurrentSlide)

					if(bNavNextAvailable && self._oSliderOptions.oNavNext.hasClass('inactive')){
						self._oSliderOptions.oNavNext.removeClass('inactive')
					}
				})
			}

			if(bNavNextAvailable){
				self._oSliderOptions.oNavNext.click(function(){
					iCurrentSlide = (iCurrentSlide < self._oSliderOptions.iAllSlides) ? iCurrentSlide+1 : 0
					self.focusSlide(iCurrentSlide)

					if(bNavPrevAvailable && self._oSliderOptions.oNavPrev.hasClass('inactive')){
						self._oSliderOptions.oNavPrev.removeClass('inactive')
					}
				})
			}

			if(bNavPrevAvailable && bNavNextAvailable){
				if(iCurrentSlide == 0){
					self._oSliderOptions.oNavPrev.addClass('inactive')
				}
				
				if(iCurrentSlide == self._oSliderOptions.iAllSlides){
					self._oSliderOptions.oNavNext.addClass('inactive')
				}
			}
		}

		this.appendSlidesActions()
		this.appendNavigationActions()
	}

	this.focusSlide = function(iSlideIndex){
		self._oSliderOptions.iCurrentSlide = iSlideIndex

		self.getCurrentSlide()
		self.getNextSlide()
		self.getPrevSlide()

		self._oSliderOptions.oSlider.find(self._oSliderOptions.sSliderFrameWrapperSelector+'>.'+self._oSliderOptions.sSliderFrameClass).removeClass(self._oSliderOptions.sSliderCurrentFrameClass)

		self.oCurrent.addClass(self._oSliderOptions.sSliderCurrentClass)

		self._oSliderOptions.oSlider.find(self._oSliderOptions.sSliderFrameWrapperSelector+'>.'+self._oSliderOptions.sSliderFrameClass).removeClass(self._oSliderOptions.sSliderNextClass+' '+self._oSliderOptions.sSliderPrevClass)

		self._oSliderOptions.oSlider.find(self._oSliderOptions.sSliderFrameWrapperSelector+'>.'+self._oSliderOptions.sSliderFrameClass+':gt('+iSlideIndex+')').addClass(self._oSliderOptions.sSliderNextClass)

	self._oSliderOptions.oSlider.find(self._oSliderOptions.sSliderFrameWrapperSelector+'>.'+self._oSliderOptions.sSliderFrameClass+':lt('+iSlideIndex+')').addClass(self._oSliderOptions.sSliderPrevClass)

		var iSlideValue = (iSlideIndex*-1) * self.oCurrent.width()
		self._oSliderOptions.oSlider.find(self._oSliderOptions.sSliderFrameWrapperSelector).css({transform:'translateX('+iSlideValue+'px)'})

		self._oSliderOptions.oSlider.find(self._oSliderOptions.sSliderNavWrapperSelector+'>.'+self._oSliderOptions.sSliderNavButtonClass).removeClass(self._oSliderOptions.sSliderNavButtonActiveClass)
		self._oSliderOptions.oSlider.find(self._oSliderOptions.sSliderNavWrapperSelector+'>.'+self._oSliderOptions.sSliderNavButtonClass+':eq('+self._oSliderOptions.iCurrentSlide+')').addClass(self._oSliderOptions.sSliderNavButtonActiveClass)


		if(typeof self._oSliderOptions.callback == 'function'){
			self._oSliderOptions.callback(iSlideIndex)
		}
	}

	var self = this;

	init = function(){
		if(typeof self._oSliderOptions.sListAddress != 'undefined') {
			self.generateSlider()
		}

		else if(self._oSliderOptions.oSlider != 'undefined' && self._oSliderOptions.oSlider instanceof jQuery){
			self.activateSlider()
		}
	}

	if(mTarget instanceof jQuery){
		self._oSliderOptions.oSlider = mTarget
	}
	else if(mTarget instanceof Object){
		$.extend(self._oSliderOptions, mTarget)
	}

	if(typeof self._oSliderOptions.sListAddress != 'undefined' || typeof self._oSliderOptions.oSlider != 'undefined'){
		init()
	}

	return {
		focusSlide: function(iSlideIndex){
										iSlideIndex = (typeof iSlideIndex == 'undefined' || iSlideIndex == 'first') ? 0 : Number(iSlideIndex)
										self.focusSlide(iSlideIndex)
								}
	}

}

window.slider = _slider
