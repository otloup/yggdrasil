$(function(){
    var out = function(a, p){
        p = p || false
        
        if($('#output').val().length >= 10000 && p){
            clean()
        }
        a = p ? '---------------------------------------------' : a
        $('#output').val($('#output').val()+a+"\r\n")
    }
    var countColor = function(a){
        out('', true)
        
        var generateRandomHex = function(a,b){
            
            a = a>=5 ? 5 : a-1
            
            /*b = b || 1
            b = b == 1 ? 255 : 16*/
            
            //out('generate random hex - power: '+a+' base: '+b)
            out('generate random hex - power: '+a)
            
            //var r = (Math.round(Math.pow(b,a)/**Math.random()*/)).toString(16)
            var r = (Math.round(Math.pow(16,a)/**Math.random()*/)).toString(16)
            out('random color: '+r)
            return r
        }
        
        var getHexFromChars = function(a){
            var l = a.length
            var hex = ''
            
            out('get hex code from string: '+a.join('|')+' with length: '+l)
            
            
            for(c in a){
                var th = ''
                out('loop string: '+a[c]+' string length: '+a[c].length)
                
                if(a[c].length>1){
                    out('string length '+a[c].length+' > 1')
                    out('string #0char ('+a[c].charAt(0)+'): '+Number(a[c].charCodeAt(0))+' | string #'+(a[c].length-1)+'char ('+a[c].charAt((a[c].length-1))+'): '+Number(a[c].charCodeAt((a[c].length-1))))
                    th = (Number(a[c].charCodeAt(0)+Number(a[c].charCodeAt((a[c].length-1))))).toString(16)
                    out('hax from chars ('+(Number(a[c].charCodeAt(0)+Number(a[c].charCodeAt((a[c].length-1)))))+'): '+hex)
                    hex += th
                }
                else{
                    out('string length '+a[c].length+' <= 1')
                    out('string #0char ('+a[c].charAt(0)+'): '+Number(a[c].charCodeAt(0)))
                    th = (a[c].charCodeAt(0)).toString(16)
                    out('hex from char ('+Number(a[c].charCodeAt(0))+'): '+th)
                    
                    hex += th
                }
            }
            
            out('hex length: '+hex.length)
            
            if(hex.length<6){
                out('hex length ('+hex.length+') is shorter than 6 - generate filler')
                out('generate random hex filler (length: '+Number(6-hex.length)+')')
                var hexf = generateRandomHex(6-hex.length)
                out('generated filler (l:'+Number(6-hex.length)+'): '+hexf)
                hex += hexf
            }
                            
            out('whole generated hex: '+hex)
             return hex            
        }
        
                            out('input string: '+a)
                            
        var l = Number(a.length)
        out('input string length: '+l)
        var h = '#'
        
        if(l<3){
            out(l+' < 3: generate + get from chars')
            var genh = ''
            
            
                if(l>0){
                    out(l+' > 0 : add hex from chars to generated '+genh)
                    var hfc = getHexFromChars(a.split(''))
                    out('hex from chars: '+hfc)
                }
                else{
                    out(l+' <= 0 - don\'t get hex from chars')
                }
           
                if(6-hfc.length>0){
                    genh = generateRandomHex(3-l)
                    out('generated hex: '+genh)
                }
            
                h +=genh+hfc
                out('combined generation ('+genh+') + chars hex ('+hfc+'): '+h)
        }
        else if(l==3){
            out(l+' == 3 - get hex from all chars')
                h += getHexFromChars(a.split(''))
                out('hex from chars: '+h)
        }
        else if(l>3){
            out(l+' > 3 - get hex from string segments')
                var d = Math.ceil(l/3)
                out('dividend = '+d)
                var r = new RegExp('.{1,'+d+'}','g')
                out('regExp: new RegExp(\'.{1,'+d+'}\',\'g\')')
                h += getHexFromChars(a.match(r))
                out('hex generated from segments: '+h)
        }
        
                    out('afterSwitch: generated hex: '+h)
                    
         var ch = a.charAt(0).toUpperCase()+a.slice(1,2)
         
         h = h.slice(0,7)
         
         out('first two characters: '+ch)
         
         out('returned combo: '+ch+','+h)
        return [ch,h]
    }
    
    var countGradient = function(from){
        var diff = isNaN(Number($('#diff').val())) ? 0 : Number($('#diff').val())
        
        var to = '#'+(Number(parseInt(from.slice(1),16)+diff).toString(16))
        addGradient(window.icoTxt, from, to)
    }
    
    var addGradient = function(text,from,to){
        $('#button').html(text)
        $('#button').css({background:'-webkit-linear-gradient('+from+','+to+')'})
        
        $('#buttonfrom').css({backgroundColor:from})
        $('#buttonto').css({backgroundColor:to})
        
        $('#buttonto .genhex').html(to)
        $('#buttonto .gensum').html(parseInt(to.slice(1),16))
        
        $('#diff_val').attr({max:Number(Math.pow(255,3)-parseInt(to.slice(1),16))})
        $('#diff_range').attr({max:Number(Math.pow(255,3)-parseInt(to.slice(1),16))})
        
        $('#buttonfrom .genhex').html(from)
        $('#buttonfrom .gensum').html(parseInt(from.slice(1),16))
    }
    
    var getText = function(){
        var t = $('#input').val()
        if(t==''){
            reset()
            return false
        }
        
        var d = countColor(t)
        
        window.icoTxt = d[0]
        return d[1]
    }
    
    var clean = function(){
        $('#output').val('')
    }
    
    var reset = function(){
        $('#button').html('&nbsp;')
        $('#button').css({background:'-webkit-linear-gradient(#fff,#fff)'})
        
        $('#buttonfrom').css({backgroundColor:'#fff'})
        $('#buttonto').css({backgroundColor:'#fff'})
        
        $('#buttonto .genhex').html('')
        $('#buttonto .gensum').html('')
        
        $('#buttonfrom .genhex').html('')
        $('#buttonfrom .gensum').html('')
        
        $('#input').val('')
        
        if(!($('#lock').is(':checked'))){
            $('#diff').val(0)
            $('#diff_range').val(0)
            $('#diff_val').val(0)
        }
        
        $('#diff_val').attr({max:Number(Math.pow(255,3))})
        $('#diff_range').attr({max:Number(Math.pow(255,3))})
    }
    
    $('#input').keyup(function(){
        var t = getText()
        
        if(t != false){
            countGradient(t)
        }
    })
    
    $('#diff_range').change(function(){
        var v = isNaN(Number($(this).val())) ? 1 : Number($(this).val())
        
        $('#diff_val').val(v)
        $('#diff').val(v)
        
        var t = getText()
        
        if(t != false){
            countGradient(t)
        }
    })
    
    $('#diff_val').change(function(){
        var v = isNaN(Number($(this).val())) ? 1 : Number($(this).val())
        
        $('#diff_range').val(v)
        $('#diff').val(v)
        
        var t = getText()
        
        if(t != false){
            countGradient(t)
        }
    })
    
    $('#count').click(function(){
        var t = getText()
        
        if(t != false){
            countGradient(t)
        }
        
        return false
    })
    
    $('#reset').click(function(){
        reset()
        return false
    })
    
    $('#clean').click(function(){
        clean()
        return false
    })
    
    $('#wipe').click(function(){
        reset()
        clean()
        return false
    })
})
