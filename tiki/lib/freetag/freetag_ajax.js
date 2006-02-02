// $Header: /cvsroot/tikiwiki/tiki/lib/freetag/freetag_ajax.js,v 1.21 2006-02-02 22:57:45 amette Exp $

//var maxRecords gets defined in the template !
var objectType = '';
var currentTag = '';
var filter = '';
var offset = 0;
var count = 0;
var selectedElement = false;
var sort_mode = 'type_asc';

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

function setSortMode(sortmode) {
	resetOffset();
	sort_mode = sortmode;
	var sort_stuff = sortmode.split("_");
	if ( sort_stuff[1] == 'desc') {
		sort_stuff[1] = 'asc'
	} else {
		sort_stuff[1] = 'desc'
	}
	document.getElementById('freetagObject' + sort_stuff[0] + 'Header').href = "javascript:setSortMode('" + sort_stuff[0] + "_" + sort_stuff[1] + "')";
	listObjects(currentTag)
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

	cp.call('tiki-freetag_list_objects_ajax.php', 'list_objects', renderObjectList, tag, objectType, offset, sort_mode, filter);
}

function renderObjectList(result) {
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
