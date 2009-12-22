var FileGallery = {
	open: function(url) {
		$('#fg-jquery-dialog').dialog({
			autoOpen: false,
			width: 702,
//			modal: true,
			resizable: false,
			draggable: false
		});
		$('#fg-jquery-dialog').load(url, function(){
//			$('.ui-dialog').removeClass();
//			$('.ui-widget-content').removeClass('ui-widget-content');
//			$('.ui-dialog-titlebar').remove();
			$('#fg-jquery-dialog').dialog('option','height','auto');
			$('#fg-jquery-dialog').dialog('open');
		})
	},
	loadmid: function(url) {
		$("#fg-files-content").load(url);
	},
	tree: function() {
		if ($(".fg-galleries").hasClass("fg-galleries-hidden")) {
			$(".fg-galleries").removeClass("fg-galleries-hidden");
			$(".fg-files").removeClass("fg-files-wide");
		} else {
			$(".fg-galleries").addClass("fg-galleries-hidden");
			$(".fg-files").addClass("fg-files-wide");
		}
	}
}
