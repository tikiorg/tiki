// $Id$

var auto_save_id = [];
var auto_save_data = [];
var submit = 0;
function register_id(id) {
    auto_save_id[id] = id;
    auto_save_data[id] = $jq('#' + id).val();
	$jq('#' + id).parents('form').submit(remove_save);
	$jq('#' + id).change(function(){ auto_save(); });
  }
    
function auto_save() {
	if (submit === 0) {
		if (typeof autoSaveId == 'undefined') { autoSaveId = ''; }
		for(var id in auto_save_id) {
			if (document.getElementById(id)) {
				var data = $jq('#' + id).val();
				if (auto_save_data[id] != data) {
					auto_save_data[id] = data;
					xajax_auto_save(id, encodeURIComponent(data), autoSaveId);
				}
			}
		}
		timer = setTimeout(auto_save, 60000);
	}
}

function remove_save() {
	submit = 1;
	if (typeof autoSaveId == 'undefined') { autoSaveId = ''; }
	for(var id in auto_save_id) {
		if (document.getElementById(id)) {	// not moo artifacts
			xajax_remove_save(id, autoSaveId);
		}
	}
}

