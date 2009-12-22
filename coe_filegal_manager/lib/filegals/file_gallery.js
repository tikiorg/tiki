var FileGallery = {
	open: function(url) {
		$('#fg-jquery-dialog').dialog({
			autoOpen: false,
			width: 702,
//			modal: true,
			resizable: false,
			draggable: false
		});
		$('#fg-jquery-dialog').load(url, function() {
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

FileGallery.upload = {
	dialog: function() {
		$("#fg-jquery-upload-dialog").dialog({
			autoOpen: false,
			width: 587,
			resizable: false,
			draggable: false
		});
	},
	show: function(gallery) {
		this.dialog();
		$("#fg-jquery-upload-dialog").load("tiki-upload_file.php?galleryId=1&filegals_manager=edit", function() {
			$("#fg-jquery-upload-dialog").dialog("option", "height", "auto");
			$("#fg-jquery-upload-dialog").dialog("open")
		});
	},
	progress: function(id,msg) {
//			alert ('progress_'+id);
		document.getElementById('progress_'+id).innerHTML = msg;
	},
	do_submit: function(n) {
//				alert(document.getElementById('file_'+n).name);
		if (document.forms['file_'+n].elements['userfile[]'].value != '') {
			this.progress(n,"<img src='img/spinner.gif'>{tr}Uploading file...{/tr}");
			document.getElementById('file_'+n).submit();
			document.getElementById('file_'+n).reset();
		}
	},
	upload: function(form, loader){
		//only do this if the form exists
		n=0;
		while (document.forms['file_'+n]){
			this.do_submit(n);
			n++;
		}
		hide('form');
	},
	insert: function(file) {
		this.dialog();
		$("#fg-jquery-upload-dialog").dialog("close");
		$("#fg-jquery-upload-dialog").load("tiki-insert_file.php?as=image&file="+file, function() {
			$("#fg-jquery-upload-dialog").dialog("open");
		});
	},
	insertImage: function(file, defsize, width, height) {
		insertAt("edit", "{img fileId="+file+(defsize?"":" width="+width+" height="+height)+"}", false, false, true);
		$("#fg-jquery-dialog").dialog('close');
		$("#fg-jquery-upload-dialog").dialog('close');
	},
	insertLink: function(file, title) {
		insertAt('edit', '[tiki-download_file.php?fileId='+file+'|'+title+']', false, false, true);
		$("#fg-jquery-dialog").dialog('close');
		$("#fg-jquery-upload-dialog").dialog('close');
	},
	switchto: function(mode) {
		$(".fg-insert-active").removeClass("fg-insert-active");
		$("#fg-insert-mode-"+mode).addClass("fg-insert-active");
		if (mode=="image") {
			$("#fg-insert-as-link").hide();
			$("#fg-insert-as-image").show();
		} else {
			$("#fg-insert-as-image").hide();
			$("#fg-insert-as-link").show();
		}
	}
}

