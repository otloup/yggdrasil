(function(window){
  
  var iCardLimit = 5
  
  var _generator = function(){
  
    var getCard = function(){
      var sCard = ''
      $.ajax({
        async:false,
        dataType:'html',
	      type:'POST',
	      url:'/game/index.php/card',
	      data:{},
	      success:function(data){
	        sCard = data
	      }
      })
      
      return sCard
    }
  
    var getCards = function(sPlayerId){
      var oCard = null
      var oHand = $('.field[data-owner="'+sPlayerId+'"] .hand').find('ul')
      
      var iCards = oHand.find('li').length
      for(i = iCards; i<iCardLimit; i++){
        oCard = new Card(getCard())
        if(oCard){
          oCard.toHand(oHand)
        }
      }
      colorize()
    }
  
    return {
      getCards:function(sPlayerId){getCards(sPlayerId)}
    }
  
  }

  window.generator = new _generator()

})(window)