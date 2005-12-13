
//cp.set_persistent_connection('yes');

var objectType = '';

function listObjects(tag) {
    var cp = new cpaint();
    cp.set_response_type('XML');
    //cp.set_debug(2);
    cp.call('tiki-freetag_list_objects_ajax.php', 'list_objects', renderObjectList, tag, objectType);
}

function renderObjectList(result) {
    var objects = result.getElementsByTagName('object');
    
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
	content += '<div class="freetagObject">';
	content += '  <div class="freetagObjectName">';
	content += '    <a href="'+item['href']+'">'+item['name']+'</a>';
	content += '  </div>';
	content += '  <div class="freetagObjectType">'+item['type']+'</div>';
	content += '  <div class="freetagObjectDescription">'+item['description']+'</div>';
	content += '</div>';
    }

    document.getElementById('objectList').innerHTML = content;    
}

function setObjectType(type) {
    objectType = type;
}

