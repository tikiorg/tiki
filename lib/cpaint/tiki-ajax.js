// $Header: /cvsroot/tikiwiki/tiki/lib/cpaint/tiki-ajax.js,v 1.7 2006-03-06 05:24:34 lfagundes Exp $

function load() {

    document.getElementById('ajaxLoading').style.display = 'block';

    var cp = new cpaint();
    cp.set_persistent_connection(false);

    var cmd = 'cp.call("tiki-ajax.php","handle",_handleLoadResult';
    for (var i=0; i<arguments.length; i++) {
	cmd += ',arguments['+i+']';
    }
    cmd += ');';

    eval(cmd);
}

function _handleLoadResult(result) {

    var metadata = _ajaxExtractItem(result, 'metaData');

    var func = metadata['function'];
    var type = metadata['type'];

    var res;
    if (metadata['type'] == 'list') {
	res = _ajaxExtractList(result, 'tikiResult');
    } else if (metadata['type'] == 'item') {
	res = _ajaxExtractItem(result, 'tikiResult');
    } else if (metadata['type'] == 'scalar') {
	res = _ajaxExtractScalar(result, 'tikiResult');
    } else {
	return false;
    }

    if (metadata['cant'] != null) {
	res.cant = metadata['cant'];
    }

    var cmd = 'handle_' + func + '(res);';
    eval(cmd);

    document.getElementById('ajaxLoading').style.display = 'none';
}

function loadContent() {

    document.getElementById('ajaxLoading').style.display = 'block';

    var cp = new cpaint();
    cp.set_persistent_connection(false);

    var cmd = 'cp.call("tiki-ajax.php","handleContent",_handleLoadContentResult';
    for (var i=0; i<arguments.length; i++) {
	cmd += ',arguments['+i+']';
    }
    cmd += ');';
    
    eval(cmd);
}

function _handleLoadContentResult(result) {
    var metadata = _ajaxExtractItem(result, 'metaData');

    var func = metadata['function'];

    var content = _ajaxExtractScalar(result, 'tikiResult');

    document.getElementById(metadata['containerId']).innerHTML = content;

    document.getElementById('ajaxLoading').style.display = 'none';
}

function _ajaxExtractList(result, name) {

    var objects = result.ajaxResponse[0][name];

    if (!objects) { return new Array(); }

    var jslist = new Array();

    for (var i=0; i < objects.length; i++) {

	jslist[i] = new Array();
	
	for (var key in objects[i]) {
	    if (Array.prototype.isPrototypeOf(objects[i][key])) {
		jslist[i][key] = objects[i][key][0]['data'];
	    }
	}

    }

    return jslist;
}

function _ajaxExtractItem(result, name) {
    var result = _ajaxExtractList(result, name);
    return result[0];
}

function _ajaxExtractScalar(result, name) {
    return result.ajaxResponse[0][name][0]['data'];
}

/* BELOW WILL GET OBSOLETE SOON */


/*
 * This function is intended to be used to extract an array of associative arrays
 * (as a result of sql select for listing objects) from the cpaint result object.
 * 
 * result: the cpaint object
 * name: the node name in cpaint where the list is contained
 * fields: an array containing the fields (keys) in associative array for each item
 */
function ajaxExtractArray(result, name, fields) {
    var objects = result.ajaxResponse[0][name];

    if (!objects) { return new Array(); }

    var jslist = new Array();

    for (var i=0; i < objects.length; i++) {

	jslist[i] = new Array();

	for (var j=0; j < fields.length; j++) {
	    var field = fields[j];

	    jslist[i][field] = objects[i][field][0].data;
	}
    }

    return jslist;
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
		for (var j=0; j<ajax_cols.length; j++) {
		document.getElementById( ajax_cols[j] + '_' + (i) ).innerHTML = '';
		}
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

function setSortMode(sortmode) {
	resetOffset();
	sort_mode = sortmode;
	var sort_stuff = sortmode.split("_");
	if ( sort_stuff[1] == 'desc') {
		sort_stuff[1] = 'asc'
	} else {
		sort_stuff[1] = 'desc'
	}
	document.getElementById('ajax_' + sort_stuff[0] + 'Header').href = "javascript:setSortMode('" + sort_stuff[0] + "_" + sort_stuff[1] + "')";
	listObjects(currentTag)
}
