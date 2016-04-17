var last = 1;
var serverip = "54.208.32.189";
var full = [];

function drawState(state, r, g, b) {
	document.getElementById(state).style.fill = 'rgb(' + r + ',' + g + ',' + b +')';
}

function drawColors(sampleData) {
	// Find max
	var max = 0;
	for(var k in sampleData) {
		if(Math.abs(sampleData[k]) > max) max = Math.abs(sampleData[k]);
	}

	// Colorize the map
	var s,r,g,b;
	var timeout = 0;
	for(k in sampleData) {
		var scale = parseInt(200 - Math.abs(sampleData[k]) * 200.0/max);
		if(sampleData[k] > 0) {
			r = scale;
			g = scale;
			b = 255;
		} else if(sampleData[k] < 0) {
			r = 255;
			g = scale;
			b = scale;
		} else {
			r = 211;
			g = r;
			b = r;
		}
		s = k;
		var to = setTimeout(drawState(s, r, g, b),timeout);
		//timeout = timeout + 500;
	}


}

function loadFull() {
	var xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function() {
		if (xhttp.readyState == 4 && xhttp.status == 200) {
			full = JSON.parse(xhttp.responseText);
		}
	};
	var hostname = (window.location.hostname=="")?serverip:window.location.hostname;
 	xhttp.open('GET', 'http://' + hostname + '/query2.php', true);
	xhttp.send();
}

function loadDoc() {
	var xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function() {
		if (xhttp.readyState == 4 && xhttp.status == 200) {
			var test = JSON.parse(xhttp.responseText);
			if(full.length > 0) {
				//console.log('start addition');
				// Loop through
				for(var timepoint in full) {
					for(var state in full[timepoint]) {
						 // if(!(test[state] === null))
						 // 	full[timepoint][state] = parseInt(full[timepoint][state]) + parseInt(test[state]);
						if(test[state] > 0 || test[state] < 0)
							full[timepoint][state] = (parseInt(full[timepoint][state]) || 0) + (parseInt(test[state]) || 0);
					}
				}
				// Add new entry, pop old entry
				full.unshift(test);
				full.pop();
				//console.log('end addition');
				drawColors(full[last]);
			} else {
				// Show stuff while waiting for full to load
				drawColors(test);
			}
		}
	};
	var hostname = (window.location.hostname=="")?serverip:window.location.hostname;
 	xhttp.open('GET', 'http://' + hostname + '/query.php?last=1', true);
	xhttp.send();
}

function svgReady() {
	loadFull();
	window.setInterval(loadDoc, 1000);
	loadDoc();
}


function changedSlider(new_last) {
	last = new_last;
	if(full.length > 0) {
		drawColors(full[full.length - last]);
	}
	return last;
}

//synchronize 'last' across hash, variable and slider
var varchanged = false;
function updateLast(e) {
	//console.log(e);
	if (varchanged){
		varchanged = false;
		return;
	}
	if(e.type == "hashchange" || e.type == "load"){
		var res = /last[ ]?=[ ]?([0-9]*)/.exec(window.location.hash);
		if(res){
			if(res[1]!=""){
				last = parseInt(res[1]);
				document.querySelector('#count').value = last;
				document.querySelector('#slider').value = last;
			}else{
				varchanged = true;
				window.location.hash = "last="+last;
			}
		}
	}else if(e.type == "input"){
		last = parseInt(e.target.value);
		document.querySelector('#count').value = last;
		 varchanged = true;
		window.location.hash = "last="+last;
	}
	if(full.length > 0) {
		drawColors(full[full.length - last]);
	}
}

window.onhashchange = updateLast;
window.onload = function(e){
	updateLast(e);
	document.querySelector('#slider').oninput = updateLast;


	var slider = document.querySelector('#slider');
	var ith;

	function playSlider(){
		ith = setInterval(function(){
			if( (last + 50) > 10000) {
				clearInterval(ith);
				return;
			}
			window.location.hash = "last="+ (last+50);
			//console.log("slider.value = "+last);
		},300);
	}

	document.querySelector('h1').onclick = playSlider;
}
