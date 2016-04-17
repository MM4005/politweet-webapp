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
	//console.log(sampleData);
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
		timeout = timeout + 50;
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
	console.log('loaddoc')
	var xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function() {
		if (xhttp.readyState == 4 && xhttp.status == 200) {
			var test = JSON.parse(xhttp.responseText);
			if(full.length > 0) {
				//console.log('start addition');
				// Loop through
				for(var timepoint in full) {
					for(var state in full[timepoint]) {
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
		drawColors(full[last]);
	}
	return last;
}

function updateLast() {
	var res = /last[ ]?=[ ]?([0-9]*)/.exec(window.location.hash);
	if(res){
		last = parseInt(res[1]);
	}
	if(full.length > 0) {
		drawColors(full[full.length - last]);
	}
}

window.onhashchange = updateLast;
window.onload = updateLast;