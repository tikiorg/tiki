// $Id$

var auto_save_id = [];
var auto_save_refs = [];
var auto_save_data = [];
var submit = 0;
var sending_auto_save = false;

function remove_save(editorId, autoSaveId) {
	if (typeof editorId !== 'string' || !editorId || !autoSaveId || submit === 1) {
		return;	// seems to get jQuery events arriving here or has been submitted before
	}
	if (sending_auto_save) {	// wait if autosaving
		setTimeout(function () { remove_save(editorId, autoSaveId); }, 100);
	}
	submit = 1;
	$.ajax({
		url: 'tiki-auto_save.php',
		data: 'command=auto_remove&editor_id=' + editorId + '&data=&referer=' + autoSaveId,
		type: "POST",
		async: false,	// called on form submit (save or cancel) so should wait 
		// good callback
		success: function(data) {
			// act casual?
		},
		// bad callback - no good info in the params :(
		error: function(req, status, error) {
			//alert(tr("Auto Save removal returned an error: ") + error);
		}
	});
}

function toggle_autosaved(editorId, autoSaveId) {
	if (typeof autoSaveId === 'undefined') { autoSaveId = ''; }
	var output = '', prefix = '';
	var $codeMirrorEditor = getCodeMirrorFromInput($('#' + editorId));
	var cked = typeof CKEDITOR !== 'undefined' ? CKEDITOR.instances[editorId] : null;
	if ($("#"+editorId+"_original").length === 0) {	// no save version already?
		if (cked) { prefix = 'cke_contents_'; }
		ajaxLoadingShow(prefix + editorId);
		$.ajax({
			url: 'tiki-auto_save.php',
			data: 'command=auto_get&editor_id=' + editorId + '&data=&referer=' + autoSaveId,
			async: true,
			type: "POST",
			// good callback
			success: function(data) {
				output = jQuery(data).find('data').text();
				// back up current
				$("#"+editorId).parents("form:first").
					append($("<input type='hidden' id='"+editorId+"_original' value='"+escape($("#"+editorId).val())+"' />"));
				if (cked) {
					cked.setData(output);
				} else if ($codeMirrorEditor) {
					$codeMirrorEditor.setCode(output);
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
		output = unescape($("#"+editorId+"_original").val());
		if (cked) {
			cked.setData(output);	// cked leaves the original content in the ta
		} else if ($codeMirrorEditor) {
			$codeMirrorEditor.setCode(output);
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
	if (submit === 0 && editorId && autoSaveId && !sending_auto_save) {
		var data = $('#' + editorId).val();
		var allowHtml = $('#allowhtml:checked').length;
		if (auto_save_data[editorId] !== data || $('#' + editorId).attr("old_allowhtml") != allowHtml) {
			auto_save_data[editorId] = data;
			sending_auto_save = true;
			$.ajax({
				url: 'tiki-auto_save.php',
				data: 'command=auto_save&editor_id=' + editorId + '&data=' + encodeURIComponent(data) + '&referer=' + autoSaveId
									+ "&allowHtml=" + allowHtml,
				type: "POST",
				// good callback
				success: function(data) {
					// update button when it's there (TODO)
					if (ajaxPreviewWindow && typeof ajaxPreviewWindow.get_new_preview === 'function') {
						 ajaxPreviewWindow.get_new_preview();
					} else {
						ajax_preview( editorId, autoSaveId, true );
					}
					$('#' + editorId).attr("old_allowhtml", allowHtml);
					setTimeout(auto_save, 60000);
					sending_auto_save = false;
				},
				// bad callback - no good info in the params :(
				error: function(req, status, error) {
					if (error) {
						alert(tr("Auto Save error: ") + error);
					}
					sending_auto_save = false;
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
		var allowHtml = $('#allowhtml:checked').length;
		if (!ajaxPreviewWindow) {
			if (inPage) {
				var $prvw = $("#autosave_preview:visible");
				if ($prvw.length) {
					ajaxLoadingShow($("#autosave_preview .wikitext"));
					var h = location.search.match(/&hdr=(\d?)/);
					h = h && h.length ? h[1] : "";
					$.get("tiki-auto_save.php", {
						editor_id: editorId,
						autoSaveId: autoSaveId,
						inPage: true,
						hdr: h,
						allowHtml: allowHtml
					}, function(data) {
						// remove JS and disarm links
						data = data.replace(/\shref/gi, " tiki_href").
								replace(/\sonclick/gi, " tiki_onclick").
								replace(/<script[.\s\S]*?<\/script>/mgi, "");
						$("#autosave_preview .wikitext").html(data);
						$("#preview_diff_style").val(function(){return getCookie("preview_diff_style","","");});
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
	}
	
}

$(window).unload(function () {
	if (auto_save_id.length > 0 && ajaxPreviewWindow && typeof ajaxPreviewWindow.get_new_preview === 'function') {
		ajaxPreviewWindow.close();
	}
});
