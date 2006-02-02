var category = '';
var offset = 0;
var sort_mode = 'taglabel_asc';
var filter = '';
function listObjects(tag) {
	currentTag = tag;
	var cp = new cpaint();
	//cp.set_debug(2);

	document.getElementById('ajaxLoading').style.display = 'block';

	cp.call('tiki-ajax_quicktags.php', 'list_objects', renderObjectList, category, offset, sort_mode, filter);
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
		document.getElementById('quicktagLabel_' + (i) ).innerHTML = objects[i].taglabel[0].data
		document.getElementById('quicktagInsert_' + (i) ).innerHTML = objects[i].taginsert[0].data;
		document.getElementById('quicktagIcon_' + (i) ).src = objects[i].tagicon[0].data;
		document.getElementById('quicktagCategory_' + (i) ).innerHTML = objects[i].tagcategory[0].data;
	}

	if (objects.length < maxRecords) { // Need to wipe out the rest
		wipeList(objects.length)
	}
	
/*
	if (currentTag && document.getElementById('currentTag1')) {
		document.getElementById('currentTag1').innerHTML = currentTag;
	}
	if (currentTag && document.getElementById('currentTag2')) {    
		document.getElementById('currentTag2').innerHTML = currentTag;
	}
		*/

	updatePageCount(result);
	document.getElementById('ajaxLoading').style.display = 'none';
}
