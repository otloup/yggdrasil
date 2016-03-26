(function($){
	$.fn.halo = function(oConfig, sSpeed){
	
		/*
			oConfig:
			 - baseClass
			 - endClass
		*/
	
		var haloIdPrefix = 'halo_'
		var sSpeed = sSpeed || 'fast'

		var oSpeed = {
			slow:'5s'
			,medium:'1s'
			,fast:'.5s'
		}
		
		this.each(function(key, item){
			var $item = $(item)
			var id = Math.ceil(Math.random() * 100000)
			
			var halo = $('<div>')
			halo.hide()
			$('body').append(halo)
			
			halo.attr({
				id:haloIdPrefix+id
				,'data-start':oConfig.baseClass
				,'data-end':oConfig.endClass
			})
			
			halo.addClass(oConfig.baseClass)
			
			halo.css({
				position:'absolute'
				,'-webkit-transition':'all '+oSpeed[sSpeed]
			})
			
			halo.offset({
				top:Number(($item.offset().top+($item.outerHeight()/2))-(halo.outerHeight()/2))
				,left:Number(($item.offset().left+($item.outerWidth()/2))-(halo.outerWidth()/2))
			})
			
			halo.show()
			
			$item.attr({
				'data-halo':haloIdPrefix+id
			})
			
			$item.hover(function(){
				var sHaloId = $(this).data('halo')
				$('#'+sHaloId).toggleClass($('#'+sHaloId).data('end'))
			})
		})
	}
})(jQuery)