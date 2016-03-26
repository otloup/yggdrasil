function print_r(arr,lvl,max){

	var p = function (arr,lvl,max){
	  var txt = '';
		var margin = '';
	
		max = typeof max=='undefined'?100:max;
	  lvl = typeof lvl=='undefined'?0:lvl;

		for(a in arr){
	    if(typeof arr[a]!='function'){
	      if(typeof arr[a]=='object' && arr[a]!=null){
					if(lvl>0){margin = new Array(lvl).join(" ");}
					if(lvl>max){
						txt += margin+a+" => [arr/obj]\n";
					}
					else{
		        txt += margin+a+" => [\n"+p(arr[a],lvl+2,max)+""+margin+"]\n";
					}
	      }
	      else{
	        if(lvl>0){margin = new Array(lvl).join(" ");}
	        if(arr[a]==null){txt += margin+a+' => NULL'+"\n";}
	        else{txt += margin+a+' => '+arr[a]+"\n";}
	      }
	    }
		}

	  return empty(txt) ? 'empty' : txt;
	}

	alert(p(arr,lvl,max));

}
