var FileGallery = {
	dialogmode: true,
	open: function(url) {
		if (!this.dialogmode) {
			window.location = url;
			return;
		}
		if (this.dialogmode) {
			$('#fg-jquery-dialog').dialog({
				autoOpen: false,
				width: 702,
	//			modal: true,
				resizable: false,
				draggable: false
			});
		}
		$('#fg-jquery-dialog').load(url, function() {
			if (FileGallery.dialogmode) {
				$('#fg-jquery-dialog').dialog('option','height','auto');
				$('#fg-jquery-dialog').dialog('open');
			}
			$('.fg-pager a').bind('click', function(e) { FileGallery.open(this.href); return false; });
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
	},
	limit: function(event, count, view, galleryId, fm) {
		if (!event)
			event = window.event;
		if (event.keyCode==13) {
			count = parseInt(count);
			if (count>0)
				this.open("tiki-list_file_gallery.php?view="+view+"&filegals_manager="+fm+"&galleryId="+galleryId+"&maxRecords="+count);
		}
	},
	search: function(event, start, view, fm) {
		if (!start) {
			if (!event)
				event = window.event;
			if (event.keyCode!=13)
				return;
		}
		FileGallery.open('tiki-list_file_gallery.php?filegals_manager='+fm+'&view='+view+'&find='+$('.fg-toolbar-search-input').val());
	}
}

FileGallery.upload = {
	asimage: null,
	aslink: null,
	dimoriginal: null,
	dimthumb: null,
	dimwidth: null,
	dimheight: null,
	linktitle: null,
	dialog: function() {
		$("#fg-jquery-upload-dialog").dialog({
			autoOpen: false,
			width: 587,
			resizable: false,
			draggable: false
		});
	},
	show: function(gallery, fm) {
		this.dialog();
		$("#fg-jquery-upload-dialog").load("tiki-upload_file.php?galleryId="+gallery+"&filegals_manager="+fm, function() {
			$("#fg-jquery-upload-dialog").dialog("option", "height", "auto");
			$("#fg-jquery-upload-dialog").dialog("open")
		});
	},
	extra: function(gallery, fm) {
		this.dialog();
		$("#fg-jquery-upload-dialog").load("tiki-upload_file.php?extra=1&galleryId="+gallery+"&filegals_manager="+fm, function() {
			$("#fg-jquery-upload-dialog").dialog("option", "height", "auto");
			$("#fg-jquery-upload-dialog").dialog("open")
		});
	},
	close: function() {
		$("#fg-jquery-upload-dialog").dialog("close");
	},
	progress: function(id,msg) {
//			alert ('progress_'+id);
		document.getElementById('progress_'+id).innerHTML = msg;
	},
	do_submit: function(n) {
		this.asimage = $("#fg-insert-as-image").css("display")=="block";
		this.aslink = $("#fg-insert-as-link").css("display")=="block";
		this.dimoriginal = this.asimage && document.getElementById("fg-insert-link-x1").checked;
		this.dimthumb = this.asimage && document.getElementById("fg-insert-link-x2").checked;
		this.dimwidth = this.asimage && this.dimthumb ? $("#fg-insert-size-width").val() : null;
		this.dimheight = this.asimage && this.dimthumb ? $("#fg-insert-size-height").val() : null;
		this.linktitle = this.aslink ? $("#fg-insert-title").val() : null;
		if (document.forms['file_'+n].elements['userfile[]'].value != '') {
			this.progress(n,"<img src='img/spinner.gif'>Uploading file...");
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

