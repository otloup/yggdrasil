(function(window){

  var _window = window;
  
  var _card = function(sHtml){
    var that = this;
    
    this.iStackId = 0;
    this.sHtml = sHtml;
    this.oCard = $('<div>')
                  .html(this.sHtml)
                  .hide();
                  
    this.oProperties = null;
    
    this.getCardConfig = function(){
      var _oProperties = this.oCard.find('.card_properties');
      if(_oProperties instanceof jQuery){
        this.oProperties = {
          str:Number(_oProperties.find('.power').html())
          ,tgh:Number(_oProperties.find('.toughness').html())
          ,cst:Number(_oProperties.find('.cost').html())
          ,token:0
          ,type:'creature'
          ,speed:'instant'
          ,owner:1
          ,controller:1
        };
        
        _oProperties.remove();
      }
      else{
        console.log('false 2');
        return false;
      }
    },
    
    this.appendCardActions = function(){
      this.oCard.click(function(){
        this.oCard.play();
      });
    },
    
    this.modifyProperties = function(){
      
    }
    
    if(this.oCard instanceof jQuery){
      _window.$('body').append(this.oCard);
      this.getCardConfig();
      this.appendCardActions();
      
      this.sHtml = this.oCard.html();
    }
    else{
      console.log('false 1');
      return false;
    }
    
    
  };
  
  _card.prototype.toHtml = function(){
    return this.sHtml;
  };
  
  _card.prototype.toGrave = function(){
    window.game.modifyStack('remove', this.iStackId);
  };
  
  _card.prototype.toHand = function(oHand){
    var oCard = this.oCard.clone(true);
    this.oCard.remove();
    oCard.show();
    var oCardWrapper = $('<li>')
                        .append(oCard);
    oHand.append(oCardWrapper);
  };
  
  _card.prototype.toDeck = function(){
    window.game.modifyStack('remove', this.iStackId);
  };
  
  _card.prototype.toBanish = function(){
    window.game.modifyStack('remove', this.iStackId);
  };
  
  _card.prototype.toStack = function(){
    //card can be added to The stack only if is under current player control
    if(this.oProperties.owner == window.game.getCurrentPlayer()){
      this.iStackId = window.game.modifyStack('add', this);
    }
  };
  
  _card.prototype.tap = function(){  
    this.modifyProperties();
  };
  
  _card.prototype.sacrifice = function(){};
  _card.prototype.addToken = function(){};
  _card.prototype.removeToken = function(){};
  
  _card.prototype.play = function(){
    this.tap();
    this.toStack();    
    console.log('Strength: '+this.oProperties.str+' Cost: '+this.oPorperties.cst+' Toughness: '+this.oProperties.tgh)
  };
  
  window.Card = _card;

})(window);