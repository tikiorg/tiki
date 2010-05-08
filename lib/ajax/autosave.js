// $Id$

var auto_save_id = [];
var auto_save_data = [];
var submit = 0;
function register_id(id) {
    auto_save_id[auto_save_id.length] = id;
    auto_save_data[id] = $jq('#' + id).val();
	$jq('#' + id).parents('form').submit(remove_save);
	$jq('#' + id).change(function () { auto_save(); });
  }
    
function auto_save() {
	if (submit === 0) {
		if (typeof autoSaveId === 'undefined') { autoSaveId = ''; }
		for (var id = 0; id < auto_save_id.length; id++) {
			if (document.getElementById(auto_save_id[id])) {
				var data = $jq('#' + auto_save_id[id]).val();
				if (auto_save_data[auto_save_id[id]] !== data) {
					auto_save_data[auto_save_id[id]] = data;
					xajax_auto_save(auto_save_id[id], encodeURIComponent(data), autoSaveId);
				}
			}
		}
		if (ajaxPreviewWindow && typeof ajaxPreviewWindow.get_new_preview === 'function') {  ajaxPreviewWindow.get_new_preview(); }
		timer = setTimeout(auto_save, 60000);
	}
}

function remove_save() {
	submit = 1;
	if (typeof autoSaveId === 'undefined') { autoSaveId = ''; }
	for (var id = 0; id < auto_save_id.length; id++) {
		xajax_remove_save(auto_save_id[id], autoSaveId);
	}
}

if (typeof ajaxPreviewWindow === 'undefined') { var ajaxPreviewWindow; }

function ajax_preview(id) {
	if (typeof id === 'undefined') {
		id = 0;
	}
	// wysiwyg does it differently :(
	
	if (typeof fckEditorInstances !== 'undefined' && fckEditorInstances.length > id) {
		auto_save_id[id] = fckEditorInstances.id.Config.autoSaveEditorId;
		autoSaveId = fckEditorInstances.id.Config.autoSaveSelf;
	}
	
	if (auto_save_id.length > id) {
		if (!ajaxPreviewWindow) {
			var features = 'menubar=no,toolbar=no,location=no,directories=no,fullscreen=no,titlebar=no,hotkeys=no,status=no,scrollbars=yes,resizable=yes,width=600';
			ajaxPreviewWindow = window.open('tiki-auto_save.php?editor_id=' + auto_save_id[id] + '&autoSaveId=' + escape(autoSaveId),'_blank',features);
		} else {
			if (typeof ajaxPreviewWindow.get_new_preview === 'function') {
				ajaxPreviewWindow.get_new_preview();
				ajaxPreviewWindow.focus();
			} else {
				ajaxPreviewWindow.open('tiki-auto_save.php?editor_id=' + auto_save_id[id] + '&autoSaveId=' + escape(autoSaveId));
			}
		}
	} else {
		alert("Auto save data not found");
	}
	
}

$jq(window).unload(function(){
	if (auto_save_id.length > 0 && ajaxPreviewWindow && typeof ajaxPreviewWindow.get_new_preview === 'function') {
		ajaxPreviewWindow.close();
	}
});

