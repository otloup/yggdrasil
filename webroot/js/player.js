(function(window){

  var _window = window;
  
  var _player = function(sPlayerRole){
    var that = this;
                  
    this.oProperties = null;
    this.sRole = null;
    this.oDeck = null;
    this.oGrave = null;
    
    var getPlayer = function(){
      $.ajax({
        async:false,
        dataType:'html',
        type:'POST',
	      url:'/game/index.php/json/game',
	      data:{
          'action'  : 'getPlayer' 
          ,'role'   : that.sRole
	      },
	      success:function(data){
	        that.oProperties = data
	      }
      })
      
      return !!this.oProperties
    },
    
    init = function(sPlayerRole){
      this.sRole = sPlayerRole
    
      this.getPlayer()
    
      this.oDeck = new Deck()
      this.oGrave = new Grave()
    }
    
    init(sPlayerRole)
    
  };
  
  _player.prototype.modifyMana = function(sMode){
    
  };
  
  window.Player = _player;

})(window);