// $Header: /cvsroot/tikiwiki/tiki/lib/cpaint/tiki-ajax.js,v 1.11 2006-03-14 22:59:02 lfagundes Exp $

function load() {

    document.getElementById('ajaxLoading').style.display = 'block';

    var cp = new cpaint();
    cp.set_persistent_connection(false);
    //cp.set_debug(2);

    var cmd = 'cp.call("tiki-ajax.php","handle",_handleLoadResult';
    for (var i=0; i<arguments.length; i++) {
	cmd += ',arguments['+i+']';
    }
    cmd += ');';

    eval(cmd);
}

function _handleLoadResult(result) {

    var metadata = _ajaxExtract(result, 'metaData');

    var func = metadata['function'];

    var res = _ajaxExtract(result, 'tikiResult');

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
    var metadata = _ajaxExtract(result, 'metaData');

    var func = metadata['function'];

    var content = _ajaxExtract(result, 'tikiResult');

    document.getElementById(metadata['containerId']).innerHTML = content;

    document.getElementById('ajaxLoading').style.display = 'none';
}

function _ajaxExtract(result, name) {

    var objects = result.ajaxResponse[0][name];

    if (!objects) { return false; }

    return _ajaxExtractItem(objects[0]);
}

function _ajaxExtractItem(obj) {
    var type = obj.get_attribute('type');

    if (type == 'scalar') {

	return obj['data'];

    } else if (type == 'array') {

	var res = new Array();
	var list = obj['item'];

	for (var i=0; i<list.length; i++) {
	    var key = list[i].get_attribute('key');
	    res[key] = _ajaxExtractItem(list[i]);
	}

	return res;
    } else {
	alert('bug');
    }
}

/* Translation */

var traTable = new Array();
var traIdTable = new Array();
var traId = 0;

function tra(str) {
    if (traTable[str]) {
	return traTable[str];
    }
    var id = traId++;
    traIdTable[str] = id;
    load('tra.php','tra',str);
    return '<span id="ajax-tra-'+id+'">'+str+'</span>';
}

function handle_tra(res) {
    traTable[res['from']] = res['to'];
    var id = traIdTable[res['from']];
    document.getElementById('ajax-tra-'+id).innerHTML = res['to'];
}

/* Translation end */

/*
	Functions for pagination of results
*/

var count;
var currentTag;
var sort_mode;


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
        count = result.cant;
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

/* End of pagination functions */
