var auto_save_id = new Array();
var submit = 0;
function register_id(id) {
    auto_save_id[id] = id;
		parent = document.getElementById(id).parentNode;
		while (parent && parent.tagName != 'FORM') {
			parent = parent.parentNode;
		}
		if (parent) {
			addEventSimple(parent,'submit',remove_save);
		}
  }
    
function auto_save() {
	if (submit == 0) {
		for(var id in auto_save_id) {
			if (document.getElementById(id)) {
				xajax_auto_save(id,encodeURIComponent((document.getElementById(id).value)));
			}
		}
		timer = setTimeout('auto_save()',60000);
	}
}

function remove_save() {
	submit = 1;
	for(var id in auto_save_id) {
		if (document.getElementById(id)) {	// not moo artifacts
			xajax_remove_save(id);
		}
	}
}

	function addEventSimple(obj,evt,fn) {
		if (obj.addEventListener)
			obj.addEventListener(evt,fn,false);
		else if (obj.attachEvent)
			obj.attachEvent('on'+evt,fn);
	}
