// $Header: /cvsroot/tikiwiki/tiki/lib/freetag/freetag_ajax.js,v 1.15 2006-01-28 21:04:13 amette Exp $

//var maxRecords gets defined in the template !
var objectType = '';
var currentTag = '';
var filter = '';
var offset = 0;
var count = 0;
var selectedElement = false;

function browseToTag(tag) {
	resetOffset();
	listObjects(tag);
}

function setObjectType(type, button) {
	resetOffset();
	objectType = type;
	if (!selectedElement) 
		selectedElement = document.getElementById('typeAll');
	selectedElement.className = 'linkbut';
	button = document.getElementById(button);
	button.className= 'linkbut highlight';
	selectedElement = button;
	listObjects(currentTag);
}

function setFilter(find) {
	resetOffset();
	filter = find;
	listObjects(currentTag);
}

function listObjects(tag) {
	currentTag = tag;
	var cp = new cpaint();
	cp.set_response_type('XML');
	//cp.set_debug(2);

	document.getElementById('ajaxLoading').style.display = 'block';

	cp.call('tiki-freetag_list_objects_ajax.php', 'list_objects', renderObjectList, tag, objectType, offset, filter);
}

function renderObjectList(result) {
	var objects = result.getElementsByTagName('object');

	if (!objects.length) {
		wipeList(0);
		document.getElementById('ajaxLoading').style.display = 'none';
		return;
	}

	var data = new Array();
	for (var i=0; i<objects.length; i++) {
		var obj = objects[i];
		var item = new Array();
		for (var j=0; j<obj.childNodes.length; j++) {
			item[obj.childNodes[j]['tagName']] = obj.childNodes[j]['textContent'];
		}
		data[i] = item;
	}

	for (var i=0; i<data.length; i++) {
		var item = data[i];
		document.getElementById('freetagObjectLink_' + (i) ).href = item['href'];
		document.getElementById('freetagObjectLink_' + (i) ).innerHTML = item['name'];
		document.getElementById('freetagObjectType_' + (i) ).innerHTML = item['type'];
		document.getElementById('freetagObjectDescription_' + (i) ).innerHTML = item['description'];
	}

	if (data.length < maxRecords) { // Need to wipe out the rest
		wipeList(data.length)
	}

	if (currentTag && document.getElementById('currentTag1')) {
		document.getElementById('currentTag1').innerHTML = currentTag;
	}
	if (currentTag && document.getElementById('currentTag2')) {    
		document.getElementById('currentTag2').innerHTML = currentTag;
	}

	updatePageCount(result);
	document.getElementById('ajaxLoading').style.display = 'none';
}

/*
	Functions for pagination of results
*/

function setOffset(deltaOffset) {
	if (((offset + deltaOffset) < count) && ((offset + deltaOffset) >= 0)) {
		offset = offset + deltaOffset;
		listObjects(currentTag);
	}
}

function resetOffset() {
	offset = 0;
	document.getElementById('actual_page').innerHTML = 1;
}


function wipeList(start) {
	for (var i=start; i<maxRecords; i++) {
		document.getElementById('freetagObjectLink_' + (i) ).href = '';
		document.getElementById('freetagObjectLink_' + (i) ).innerHTML = '';
		document.getElementById('freetagObjectType_' + (i) ).innerHTML = '';
		document.getElementById('freetagObjectDescription_' + (i) ).innerHTML = '';
	}
}
function updatePageCount(result) {
	count = result.getElementsByTagName('cant').item(0).firstChild.data;
	var cant_pages = Math.ceil(count / maxRecords);
	var pageLink = '';
	for (var i=0; i<cant_pages; i++) {
		pageLink += '<a href="javascript:setOffset('+( i * maxRecords - offset ) +')";>'+(i+1)+'&nbsp;'+'</a>';
	}
	document.getElementById('direct_pagination').innerHTML = pageLink;
	document.getElementById('cant_pages').innerHTML = cant_pages;
	curPage = 1 + (offset / maxRecords);
	document.getElementById('actual_page').innerHTML = curPage;
}
