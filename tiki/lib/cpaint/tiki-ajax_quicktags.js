var category = '';
var offset = 0;
var filter = '';
sort_mode = 'taglabel_asc';

function listObjects(tag) {
    load('quicktags_list_objects',category,offset,sort_mode,filter);

    document.getElementById('ajaxLoading').style.display = 'block';

    currentTag = tag;
}

function handle_quicktags_list_objects(result) {
	if (!result[0]) {
		wipeList(0);
		updatePageCount(result);
		document.getElementById('ajaxLoading').style.display = 'none';
		return;
	}

	for (i=0; i < result.length; i++) {
	    for (var j=0; j<ajax_cols.length; j++) {
		name = ajax_cols[j][0];
		id = ajax_cols[j][0] + '_' + i;
		
		if ( ajax_cols[j][1] == 'innerHTML' ) {
		    document.getElementById( id ).innerHTML = result[i][name];
		} else if ( ajax_cols[j][1] == 'a' ) {
		    document.getElementById( id ).innerHTML = result[i][name];
		    document.getElementById( id ).href = result[i][name];
		}
	    }
	}

	if (result.length < maxRecords) { // Need to wipe out the rest
	    wipeList(result.length);
	}

	updatePageCount(result);
	document.getElementById('ajaxLoading').style.display = 'none';
}

