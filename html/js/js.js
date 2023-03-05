function display(id){
  if(document.getElementById(id)){
  	var display = document.getElementById(id).style.display;
  	if(display=='block'){
  	  document.getElementById(id).style.display='none';
  	} else {
  	  document.getElementById(id).style.display='block';
  	}		
	}
}

function hide(id){
  if(document.getElementById(id)){
  	document.getElementById(id).style.display='none';
	}
}


function changeColor(id){
	if(id.style.backgroundColor == 'rgb(255, 255, 255)'){
	  id.style.background = 'rgb(247, 246, 244)';
	} else {
	  id.style.background = 'rgb(255, 255, 255)';
	}
}

function play(file){
  var path = getPath();
	if(path == '../'){
	  swfobject.embedSWF(path+'public/video_player_2.swf?video_source='+file+'', 'video', '236', '200', '9.0.0', path+'public/expressInstall.swf',{}, {allowfullscreen:'true'});
	} else {
	  swfobject.embedSWF('public/video_player.swf?video_source='+file+'', 'video', '236', '200', '9.0.0', path+'public/expressInstall.swf',{}, {allowfullscreen:'true'});
  }
}

function play2(file, id){
  var path = getPath();
	if(path == '../'){
	  swfobject.embedSWF(path+'public/video_player_big_2.swf?video_source='+file+'', id, '440', '380', '9.0.0', path+'public/expressInstall.swf',{}, {allowfullscreen:'true'});
	} else {
	  swfobject.embedSWF(path+'public/video_player_big.swf?video_source='+file+'', id, '440', '380', '9.0.0', 'public/expressInstall.swf',{}, {allowfullscreen:'true'});
 }
}



var http_request = false;

function createRequest(method, url, parameters, callback) // request 
{
	http_request = false;
	if (window.XMLHttpRequest) { // Mozilla, Safari,...
	   	http_request = new XMLHttpRequest();
	   	if (http_request.overrideMimeType) {
	       	http_request.overrideMimeType('text/html');
	   	}
	} else if (window.ActiveXObject) { // IE
	   	try {
	       	http_request = new ActiveXObject("Msxml2.XMLHTTP");
	   	} catch (e) {
	      	try {
		   		http_request = new ActiveXObject("Microsoft.XMLHTTP");
	       	} catch (e) {}
	   	}
	}
	if (!http_request) {
	   	return false;
	}
    				      	
	http_request.onreadystatechange = callback;
	http_request.open(method, url + parameters, true);      
}
			
function anketa(){   
	if (http_request.readyState == 4) {
    if (http_request.status == 200) {
    	document.getElementById('anketa').innerHTML = http_request.responseText;	   
    }
  }
}

function anketaHlasuj(idAnkety, hlas){
	createRequest('GET', getPath()+'./public/anketa.php', '?id_ankety='+idAnkety+'&hlas='+hlas, function(){anketa();});
	http_request.send(null);
}





