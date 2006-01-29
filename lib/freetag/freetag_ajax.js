// $Header: /cvsroot/tikiwiki/tiki/lib/freetag/freetag_ajax.js,v 1.18 2006-01-29 20:54:54 amette Exp $

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
	//cp.set_debug(2);

	document.getElementById('ajaxLoading').style.display = 'block';

	cp.call('tiki-freetag_list_objects_ajax.php', 'list_objects', renderObjectList, tag, objectType, offset, filter);
}

function renderObjectList(result) {
	var disp = 'Test stuff:<br />';

	var objects = result.ajaxResponse[0].object;

	if (!objects) {
		wipeList(0);
		updatePageCount(result);
		document.getElementById('ajaxLoading').style.display = 'none';
		return;
	}

	for (i=0; i < objects.length; i++) {
		document.getElementById('freetagObjectLink_' + (i) ).href = objects[i].href[0].data
		document.getElementById('freetagObjectLink_' + (i) ).innerHTML = objects[i].name[0].data;
		document.getElementById('freetagObjectType_' + (i) ).innerHTML = objects[i].type[0].data;
		document.getElementById('freetagObjectDescription_' + (i) ).innerHTML = objects[i].description[0].data;
	}

	if (objects.length < maxRecords) { // Need to wipe out the rest
		wipeList(objects.length)
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
	count = result.ajaxResponse[0].cant[0].data;
	var cant_pages = Math.ceil(count / maxRecords);
	if (cant_pages == 0) cant_pages = '1';
	document.getElementById('cant_pages').innerHTML = cant_pages;
	curPage = 1 + (offset / maxRecords);
	document.getElementById('actual_page').innerHTML = curPage;
	if (directPagination == 'y') {
		var pageLink = '';
		for (var i=0; i<cant_pages; i++) {
			pageLink += '<a href="javascript:setOffset('+( i * maxRecords - offset ) +')";>'+(i+1)+'&nbsp;'+'</a>';
		}
		document.getElementById('direct_pagination').innerHTML = pageLink;
	}
}
