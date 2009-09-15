// $Id$

var auto_save_id = new Array();
var auto_save_data = new Array();
var submit = 0;
function register_id(id) {
    auto_save_id[id] = id;
    auto_save_data[id] = $jq('#' + id).val();
	$jq('#' + id).parents('form').submit(remove_save);
  }
    
function auto_save() {
	if (submit == 0) {
		if (typeof tikiPageName == 'undefined') { tikiPageName = ''; }
		for(var id in auto_save_id) {
			if (document.getElementById(id)) {
				var data = $jq('#' + id).val();
				if (auto_save_data[id] != data) {
					auto_save_data[id] = data;
					xajax_auto_save(id, encodeURIComponent(data), tikiPageName);
				}
			}
		}
		timer = setTimeout('auto_save()',60000);
	}
}

$jq('document').ready( function () {
	for (var id in auto_save_id) {
		$jq('#' + id).blur(function(){
			auto_save();
		});
	}
});

function remove_save() {
	submit = 1;
	if (typeof tikiPageName == 'undefined') { tikiPageName = ''; }
	for(var id in auto_save_id) {
		if (document.getElementById(id)) {	// not moo artifacts
			xajax_remove_save(id, tikiPageName);
		}
	}
}

