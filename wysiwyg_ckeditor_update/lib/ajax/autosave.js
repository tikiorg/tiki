// $Id$

var auto_save_id = [];
var auto_save_data = [];
var submit = 0;
   
function remove_save(editorId, autoSaveId) {
	submit = 1;
	if (typeof autoSaveId === 'undefined') { autoSaveId = ''; }
	for (var id = 0; id < auto_save_id.length; id++) {
		if (document.getElementById(auto_save_id[id]) && (!editorId || editorId === auto_save_id[id])) {
			$.ajax({
				url: 'tiki-auto_save.php',
				data: 'command=auto_remove&editor_id=' + auto_save_id[id] + '&data=&referer=' + autoSaveId,
				type: "POST",
				// good callback
				success: function(data) {
					// act casual?
				},
				// bad callback - no good info in the params :(
				error: function(req, status, error) {
					alert(tr("Auto Save removal returned an error: ") + error);
				}
			});
		}
	}
}

function auto_save( editorId, autoSaveId ) {
	if (submit === 0) {
		if (typeof autoSaveId === 'undefined') { autoSaveId = ''; }
		for (var id = 0; id < auto_save_id.length; id++) {
			if (document.getElementById(auto_save_id[id]) && (!editorId || editorId === auto_save_id[id]) ) {
				var data = $('#' + auto_save_id[id]).val();
				if (auto_save_data[auto_save_id[id]] !== data) {
					auto_save_data[auto_save_id[id]] = data;
					$.ajax({
						url: 'tiki-auto_save.php',
						data: 'command=auto_save&editor_id=' + auto_save_id[id] + '&data=' + encodeURIComponent(data) + '&referer=' + autoSaveId,
						type: "POST",
						// good callback
						success: function(data) {
							// update button when it's there (TODO)
							//alert(tr("here! "));
						},
						// bad callback - no good info in the params :(
						error: function(req, status, error) {
							alert(tr("Auto Save an error: ") + error);
						}
					});
				}
			}
		}
		if (ajaxPreviewWindow && typeof ajaxPreviewWindow.get_new_preview === 'function') {  ajaxPreviewWindow.get_new_preview(); }
		timer = setTimeout(auto_save, 60000);
	}
}

function register_id(id) {
	auto_save_id[auto_save_id.length] = id;
	auto_save_data[id] = $('#' + id).val();
	$('#' + id).parents('form').submit(remove_save);
	$('#' + id).change(function () { auto_save(); });
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
	} else if (typeof ckEditorInstances !== 'undefined' && ckEditorInstances.length > id) {
		auto_save_id[id] = ckEditorInstances[0].name;
		autoSaveId = ckEditorInstances[0].config.autoSaveSelf;
	}
	
	if (auto_save_id.length > id) {
		if (!ajaxPreviewWindow) {
			var features = 'menubar=no,toolbar=no,location=no,directories=no,fullscreen=no,titlebar=no,hotkeys=no,status=no,scrollbars=yes,resizable=yes,width=600';
			ajaxPreviewWindow = window.open('tiki-auto_save.php?editor_id=' + auto_save_id[id] + '&autoSaveId=' + escape(autoSaveId), '_blank', features);
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

$(window).unload(function () {
	if (auto_save_id.length > 0 && ajaxPreviewWindow && typeof ajaxPreviewWindow.get_new_preview === 'function') {
		ajaxPreviewWindow.close();
	}
});
