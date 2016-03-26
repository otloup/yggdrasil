(function(window){

  var _window = window;
  
  var _game = function(){
    var that = this;
                  
    this.oProperties = null;
    this.aPlayers = [];
    
    var loadPlayers = function(){
      _window.$('.field').each(function(k,v){
        that.aPlayers.push(new Player($(v).data('owner')))
      })  
    },
    
    initPlayers = function(){
      _window.$.each(this.aPlayers, function(k,v){
        v.init()
      })
    },
    
    init = function(){
      //get players
      // - set life points
      // - set meters
      // - get players properties (nick, avatar, etc)
      //get players' decks
      // - get cards number
      // - get deck colour
      // - preload first 2 hands
      // - append deck DOM actions
      //shuffle decks
      // - shuffle preloaded cards
      //get cards
      // - load one hand of cards for each player
      
      loadPlayers()
      initPlayers()
      
    }
    
  };
  
  _gamer.prototype.modifyStack = function(sMode, oObj){
    
  };
  
  window.Game = _game;

})(window);