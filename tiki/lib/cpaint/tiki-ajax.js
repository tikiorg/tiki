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
