// $Header: /cvsroot/tikiwiki/tiki/lib/freetag/freetag_ajax.js,v 1.24 2006-02-11 17:04:45 amette Exp $

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

function setFilter(find) {
	resetOffset();
	filter = find;
	listObjects(currentTag);
}

function listObjects(tag) {
	currentTag = tag;
	var cp = new cpaint();
	cp.set_use_cpaint_api(true);
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
		for (var j=0; j<ajax_cols.length; j++) {
			name = ajax_cols[j][0];
			id = ajax_cols[j][0] + '_' + i;
			if ( ajax_cols[j][1] == 'innerHTML' ) {
				document.getElementById( id ).innerHTML = objects[i].find_item_by_id(name, id).data;
			} else if ( ajax_cols[j][1] == 'a' ) {
				document.getElementById( id ).innerHTML = objects[i].find_item_by_id(name, id).data;
				document.getElementById( id ).href = objects[i].find_item_by_id(name, id).data;
			}
		}
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
