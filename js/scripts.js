var threshold = 500;
var apiDateFormat = 'YYYY-MM-DD';
var visualDateFormat = 'DD MMM';

function loadWordCount() {
	var xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function() {
	    if (this.readyState == 4 && this.status == 200) {
	       var json = JSON.parse(xhttp.response);
	       document.getElementById("numwords").innerHTML = json.num_words;
	       loadCurrentWeek();
	    }
	};
	xhttp.open("GET", "api/wordcount/", true);
	xhttp.send();
}

function loadPreviousWeek() {
	var from = moment().subtract(1, 'weeks').startOf('isoWeek').format(apiDateFormat);
	var to = moment().subtract(1, 'weeks').endOf('isoWeek').format(apiDateFormat);
	loadWeek(from, to, "previousWeekContainer", "previousWeekTitle", "La semana pasada");
}

function loadCurrentWeek() {
	var from = moment().startOf('isoWeek').format(apiDateFormat);
	var to = moment().endOf('isoWeek').format(apiDateFormat);
	loadWeek(from, to, "currentWeekContainer", "currentWeekTitle", "Esta semana");
}

function loadWeek(from, to, containerId, titleId, titlePrefix) {
	moment.locale("es");
	var xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function() {
	    if (this.readyState == 4 && this.status == 200) {
	    	document.getElementById(containerId).innerHTML = "";
	       var json = JSON.parse(xhttp.response);
	       json.entries.forEach(processEntry, containerId);
	    }
	};
	
	var url = "api/route/?from=" + from + "&to=" + to;
	xhttp.open("GET", url, true);
	xhttp.send();
	var titleDate = moment(from).format(visualDateFormat) + " - " + moment(to).format(visualDateFormat);
	document.getElementById(titleId).innerHTML = titlePrefix + " (" + titleDate + ")";
}

function processEntry(json) {
	var parentNode = document.createElement("div");
	var fail = json.date == moment().format(apiDateFormat) ? "ongoing" : "fail";
	parentNode.className = json.wordsWritten < threshold ? fail : "win";
	var dayNode = document.createElement("div");
	dayNode.className = "day";
	var wordsNode = document.createElement("div");
	wordsNode.className = "words";
	parentNode.appendChild(dayNode);
	parentNode.appendChild(wordsNode);

	var dayContent = document.createTextNode(moment(json.date).format('dddd'));
	dayNode.appendChild(dayContent);
	var wordsContent = document.createTextNode(json.wordsWritten);
	wordsNode.appendChild(wordsContent)

	document.getElementById(this).appendChild(parentNode);
}

function documentReady() {
	loadWordCount();
	loadPreviousWeek();
	loadCurrentWeek();
}