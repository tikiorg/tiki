
//cp.set_persistent_connection('yes');

//var maxRecords gets defined in the template !
var objectType = '';
var currentTag = '';
var filter = '';
var offset = 0;
var count = 0;
var selectedElement = false;

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
    count = result.getElementsByTagName('cant').item(0).firstChild.data;
    var cant_pages = Math.ceil(count / maxRecords);

    if (!objects.length) {
	document.getElementById('objectList').innerHTML = '';
	document.getElementById('ajaxLoading').style.display = 'none';
	return;
    }

    var href = objects.item(0).getElementsByTagName('href');

    var data = new Array();
    for (var i=0; i<objects.length; i++) {
	var obj = objects[i];
	var item = new Array();
	for (var j=0; j<obj.childNodes.length; j++) {
	    item[obj.childNodes[j]['tagName']] = obj.childNodes[j]['textContent'];
	}
	data[i] = item;
    }

    var content = '';
    for (var i=0; i<data.length; i++) {
	var item = data[i];
	content += '<div class="freetagObject' + (i%2 ? 'Odd' : 'Even') + '">';
	content += '  <div class="freetagObjectName">';
	content += '    <a href="'+item['href']+'">'+item['name']+'</a>';
	content += '  </div>';
	content += '  <div class="freetagObjectType">'+item['type']+'</div>';
	content += '  <div class="freetagObjectDescription">'+item['description']+'</div>';
	content += '</div>';
    }

    var pageLink = '';
    for (var i=0; i<cant_pages; i++) {
    	pageLink += '<a href="javascript:setOffset(i*offset)";>'+(i+1)+'&nbsp;'+'</a>';
	}
	document.getElementById('direct_pagination').innerHTML = pageLink;


    if (currentTag && document.getElementById('currentTag1')) {
	document.getElementById('currentTag1').innerHTML = currentTag;
    }
    if (currentTag && document.getElementById('currentTag2')) {    
	document.getElementById('currentTag2').innerHTML = currentTag;
    }
    document.getElementById('objectList').innerHTML = content;    

    document.getElementById('cant_pages').innerHTML = cant_pages;

    document.getElementById('ajaxLoading').style.display = 'none';
}

function setObjectType(type, button) {
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
    filter = find;
    listObjects(currentTag);
}

function setOffset(newOffset) {
	if (((offset + newOffset) < count) && ((offset + newOffset) >= 0)) {
		offset = offset + newOffset;
		curPage = 1 + (offset / maxRecords);
		document.getElementById('actual_page').innerHTML = curPage;
		listObjects(currentTag);
	}
}
