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
		for (var j=0; j<ajax_cols.length; j++) {
		name = ajax_cols[j];
		id = ajax_cols[j] + '_' + i;
		document.getElementById( id ).innerHTML = objects[i].find_item_by_id(name, id).data;
		}
	}

	if (objects.length < maxRecords) { // Need to wipe out the rest
		wipeList(objects.length)
	}

	updatePageCount(result);
	document.getElementById('ajaxLoading').style.display = 'none';
}
