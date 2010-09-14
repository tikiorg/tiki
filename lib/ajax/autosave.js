// $Id$

var auto_save_id = [];
var auto_save_refs = [];
var auto_save_data = [];
var submit = 0;
   
function remove_save(editorId, autoSaveId) {
	if (typeof editorId !== 'string' || !editorId || !autoSaveId) {
		return;	// seems to get jQuery events arriving here
	}
	submit = 1;
	$.ajax({
		url: 'tiki-auto_save.php',
		data: 'command=auto_remove&editor_id=' + editorId + '&data=&referer=' + autoSaveId,
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

function toggle_autosaved(editorId, autoSaveId) {
	if (typeof autoSaveId === 'undefined') { autoSaveId = ''; }
	var output = '';
	var cked = typeof CKEDITOR !== 'undefined' ? CKEDITOR.instances[editorId] : null;
	if ($("#"+editorId+"_original").length === 0) {	// no save version already?
		ajaxLoadingShow(editorId);
		$.ajax({
			url: 'tiki-auto_save.php',
			data: 'command=auto_get&editor_id=' + editorId + '&data=&referer=' + autoSaveId,
			async: false,
			type: "POST",
			// good callback
			success: function(data) {
				output = unescape(jQuery(data).find('data').text());
				// back up current
				$("#"+editorId).parents("form:first").
					append($("<input type='hidden' id='"+editorId+"_original' value='"+$("#"+editorId).val()+"' />"));
				if (cked) {
					cked.setData(output);
				} else if ($("#"+editorId).length) {	// wiki editor
					$("#"+editorId).val(output);
				}
				ajaxLoadingHide();
			},
			// bad callback - no good info in the params :(
			error: function(req, status, error) {
				alert(tr("Auto Save get returned an error: ") + error);
			}
		});
	} else {	// toggle back to original
		output = $("#"+editorId+"_original").val();
		if (cked) {
			cked.setData(output);	// cked leaves the original content in the ta
		} else if ($("#"+editorId).length) {	// wiki editor
			$("#"+editorId).val(output);
		}
		$("#"+editorId+"_original").remove();
	}
	// swap the messages around (fixed to first textarea only for now)
	var msg = $(".autosave_message_2:first").text();
	$(".autosave_message_2:first").text($(".autosave_message:first").text());
	$(".autosave_message:first").text(msg);

	return output;
}

function auto_save( editorId, autoSaveId ) {
	if (!autoSaveId) { autoSaveId = auto_save_refs[0]; }
	if (submit === 0 && editorId && autoSaveId) {
		var data = $('#' + editorId).val();
		if (auto_save_data[editorId] !== data) {
			auto_save_data[editorId] = data;
			$.ajax({
				url: 'tiki-auto_save.php',
				data: 'command=auto_save&editor_id=' + editorId + '&data=' + encodeURIComponent(data) + '&referer=' + autoSaveId,
				async: false,
				type: "POST",
				// good callback
				success: function(data) {
					// update button when it's there (TODO)
					//alert(tr("here! "));
					if (ajaxPreviewWindow && typeof ajaxPreviewWindow.get_new_preview === 'function') {
						 ajaxPreviewWindow.get_new_preview();
					} else {
						ajax_preview( editorId, autoSaveId, true );
					}
					setTimeout(auto_save, 60000);
				},
				// bad callback - no good info in the params :(
				error: function(req, status, error) {
					alert(tr("Auto Save an error: ") + error);
				}
			});
		}
	}
}

function register_id( editorId, autoSaveId ) {
	auto_save_id[auto_save_id.length] = editorId;
	auto_save_refs[editorId] = autoSaveId;
	auto_save_data[editorId] = $('#' + editorId).val();
	$('#' + editorId).parents('form').submit(function() { remove_save(editorId, autoSaveId); });
	$('#' + editorId).change(function () { auto_save( editorId, autoSaveId ); });
}

var ajaxPreviewWindow;

function ajax_preview(editorId, autoSaveId, inPage) {
	if (editorId) {
		if (!ajaxPreviewWindow) {
			if (inPage) {
				var $prvw = $(".wikipreview:visible");
				if ($prvw.length) {
					ajaxLoadingShow($(".wikipreview .wikitext"));
					$.get("tiki-auto_save.php", {
						editor_id: editorId,
						autoSaveId: escape(autoSaveId),
						inPage: true
					}, function(data) {
						$(".wikipreview .wikitext").html(data);
						ajaxLoadingHide();
					});
				}
			} else {
					var features = 'menubar=no,toolbar=no,location=no,directories=no,fullscreen=no,titlebar=no,hotkeys=no,status=no,scrollbars=yes,resizable=yes,width=600';
					ajaxPreviewWindow = window.open('tiki-auto_save.php?editor_id=' + editorId + '&autoSaveId=' + escape(autoSaveId), '_blank', features);
			}
		} else {
			if (typeof ajaxPreviewWindow.get_new_preview === 'function') {
				ajaxPreviewWindow.get_new_preview();
				ajaxPreviewWindow.focus();
			} else {
				ajaxPreviewWindow.open('tiki-auto_save.php?editor_id=' + editorId + '&autoSaveId=' + escape(autoSaveId));
			}
		}
	} else {
		alert("Auto save data not found");
		debugger;
	}
	
}

$(window).unload(function () {
	if (auto_save_id.length > 0 && ajaxPreviewWindow && typeof ajaxPreviewWindow.get_new_preview === 'function') {
		ajaxPreviewWindow.close();
	}
});
