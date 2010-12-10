var FileGallery = {
	dialogmode: true,
	form: function(id) {
		var counters = { };
		var form = jQuery('form#'+id);
		var data = {};
		jQuery('input[name]', form).each(function(){
			if ((jQuery(this).attr('type')=="radio" || jQuery(this).attr('type')=="checkbox") && jQuery(this).attr('checked') || jQuery(this).attr('type')!="radio" && jQuery(this).attr('type')!="checkbox") {
				if (jQuery(this).val()) {
					var name = jQuery(this).attr('name');
					if (name.substr(name.length-2)=="[]") {
						var subname = name.substr(0,name.length-2);
						if (typeof counters[subname] != "undefined")
							name = subname+"["+(++counters[subname])+"]";
						else {
							name = subname+"[0]";
							counters[subname] = 0;
						}
					}
					data[name] = jQuery(this).val();
				}
			}
		});
		jQuery('textarea[name]', form).each(function(){
			data[jQuery(this).attr('name')] = jQuery(this).val();
		});
		jQuery('select[name]', form).each(function(){
			data[jQuery(this).attr('name')] = jQuery(this).val();
		});
		return data;
	},
	open: function(url, area_id, dialogDiv) {
		this.dialogmode = true; /* FIXME */
		post = null;
		form = null;

		var data = post ? post : '';
		if (form)
			data = this.form(form);

		jQuery('#tbFilegalManager').load(url, data, function() {
			$('.fg-galleries-list a.fgalname, a.fgalgal, a.fgalaction').click( function(e) {
				e.preventDefault();
				FileGallery.open(this.href, area_id, dialogDiv);
				return false;
			});
			$('a.fgalfile').click( function(e) {
				e.preventDefault();
				dialogSharedClose( area_id, dialogDiv );
				return false;
			});
		});

		return false;
	},
	close: function() {
		FileGallery.upload.close();
		jQuery("#fg-jquery-dialog").dialog("close");
	},
	loadmid: function(url) {
		jQuery("#fg-files-content").load(url);
	},
	tree: function() {
		var rowstep = 0;
		if (jQuery(".fg-galleries").hasClass("fg-galleries-hidden")) {
			jQuery(".fg-galleries").removeClass("fg-galleries-hidden");
			jQuery(".fg-files").removeClass("fg-files-wide");
			jQuery(".fg-galleries > .fg-toolbar > .fg-toolbar-right").append(jQuery(".fg-files > .fg-toolbar > .fg-toolbar-left > .fg-toolbar-icon"));
			jQuery(".fg-galleries > .fg-toolbar > .fg-toolbar-right > .fg-toolbar-icon > img").attr("src", "images/file_gallery/icon-hidegalleries.gif");
			rowstep = 3;
		} else {
			jQuery(".fg-galleries").addClass("fg-galleries-hidden");
			jQuery(".fg-files").addClass("fg-files-wide");
			jQuery(".fg-galleries > .fg-toolbar > .fg-toolbar-right > .fg-toolbar-icon > img").attr("src", "images/file_gallery/icon-showgalleries.gif");
			jQuery(".fg-files > .fg-toolbar > .fg-toolbar-left").prepend(jQuery(".fg-galleries > .fg-toolbar > .fg-toolbar-right > .fg-toolbar-icon"));
			rowstep = 4;
		}

		var tds = [ ];
		jQuery(".fg-gallery-view tr").each(function(a,b) {
			jQuery(b).find("td").each(function(c,d) {
				tds.push(d);
			});
			jQuery(b).addClass("fg-tmp-delete");
		});
		
		for (var i=0; i<tds.length; i+=rowstep) {
			var tr = jQuery("<tr/>");
			for (var j=i; j<i+rowstep; j++)
				tr.append(tds[j]);
			tr.appendTo(jQuery(".fg-gallery-view"));
		}
		
		jQuery(".fg-tmp-delete").remove();
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
		FileGallery.open('tiki-list_file_gallery.php?filegals_manager='+fm+'&view='+view+'&find='+jQuery('.fg-toolbar-search-input').val());
	},
	tab: function(name) {
		jQuery(".fg-tabheads > li").removeClass("fg-tabheads-active");
		jQuery("#fg-tabheads-"+name).addClass("fg-tabheads-active");
		jQuery(".fg-tab").hide();
		jQuery("#fg-tab-"+name).show();
	},
	editGallery: function(url) {
		jQuery("#tbFilegalManagerSub").dialog('open');
		displayDialog('',2,'edit',url);
	},
	saveGallery: function() {
		var params = jQuery("#fg-folder-form").serialize();
		var url = jQuery("#fg-folder-form").attr("action");
		url += (url.indexOf("?") ? "&" : "?")+params;
		$.post(url, null, function(data) {
			jQuery("#fg-jquery-gallery-dialog").html(data);
		});
	},
	replacefile: function(form) {
		var tmpname = "hiddeniframe"+(Math.round(Math.random()*10000));
		jQuery("<iframe/>")
			.width(1)
			.height(1)
			.css("opacity","0")
			.attr("name", tmpname)
			.attr("id", tmpname)
			.appendTo(jQuery("body"));
		jQuery(form).attr("target", tmpname);
		form.submit();
		jQuery("iframe[name="+tmpname+"]").bind('load',function() {
			FileGallery.open(form.action);
		});
	},
	closeGallery: function() {
		jQuery("#fg-jquery-gallery-dialog").dialog("close");
	},
	showDialog: function(url) {
		var xdiv = jQuery("<div/>")
			.appendTo(jQuery("body")).dialog({
				autoOpen: false,
				width: 402,
				height: 142,
				modal: false,
				resizable: false,
				draggable: true,
				stack: false,
				dialogClass: "smalltitlebar fg-customdialog"
			})
			.draggable({handle:'h1'});
		jQuery("<div/>")
			.addClass("fg-custom-dialog")
			.appendTo(xdiv)
			.load(url, { dialog: 1 });
		xdiv.dialog("open");
	},
	closeDialog: function() {
		jQuery(".fg-customdialog").remove();
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
		jQuery("#fg-jquery-upload-dialog").dialog({
			autoOpen: false,
			width: 587,
			resizable: false,
			draggable: true,
			stack: false,
			modal: true,
			dialogClass: "smalltitlebar"
		});
	},
	show: function(gallery, fm) {
		//jQuery("#tbFilegalManagerSub").dialog().dialog('open');
		displayDialog('', 2, 'edit', 'tiki-upload_file.php?galleryId='+gallery+'&filegals_manager='+fm+'&fgspecial=1', 'Upload file');
/*
		this.dialog();
		jQuery("#fg-jquery-upload-dialog").load("tiki-upload_file.php?galleryId="+gallery+"&filegals_manager="+fm+"&fgspecial=1", function() {
			jQuery("#fg-jquery-upload-dialog").dialog("option", "height", "auto");
			jQuery("#fg-jquery-upload-dialog").dialog("open")
		});
*/
	},
	edit: function(url) {
		this.dialog();
		jQuery("#fg-jquery-upload-dialog").load(url, function() {
			jQuery("#fg-jquery-upload-dialog").dialog("option", "height", "auto");
			jQuery("#fg-jquery-upload-dialog").dialog("open")
		});
	},
	extra: function(gallery, fm) {
		this.dialog();
		jQuery("#fg-jquery-upload-dialog").load("tiki-upload_file.php?extra=1&galleryId="+gallery+"&filegals_manager="+fm, function() {
			jQuery("#fg-jquery-upload-dialog").dialog("option", "height", "auto");
			jQuery("#fg-jquery-upload-dialog").dialog("open")
		});
	},
	close: function() {
		jQuery("#fg-jquery-upload-dialog").dialog("close");
	},
	progress: function(id,msg) {
//			alert ('progress_'+id);
//		document.getElementById('progress_'+id).innerHTML = msg;
		jQuery(".fg-upload > .tip").show();
		jQuery(".fg-upload > .tip > .rbox-data").html(msg);
	},
	do_submit: function(n) {
		this.asimage = jQuery("#fg-insert-as-image").css("display")=="block";
		this.aslink = jQuery("#fg-insert-as-link").css("display")=="block";
		this.dimoriginal = this.asimage && document.getElementById("fg-insert-link-x1").checked;
		this.dimthumb = this.asimage && document.getElementById("fg-insert-link-x2").checked;
		this.dimwidth = this.asimage && this.dimthumb ? jQuery("#fg-insert-size-width").val() : null;
		this.dimheight = this.asimage && this.dimthumb ? jQuery("#fg-insert-size-height").val() : null;
		this.linktitle = this.aslink ? jQuery("#fg-insert-title").val() : null;
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
		jQuery("#fg-jquery-upload-dialog").dialog("close");
		jQuery("#fg-jquery-upload-dialog").load("tiki-insert_file.php?as=image&file="+file, function() {
			jQuery("#fg-jquery-upload-dialog").dialog("open");
		});
	},
	insertImage: function(file, defsize, width, height) {
		this.insertAt("editwiki", "{img fileId="+file+(defsize?"":" width="+width+" height="+height)+"}", false, false, true);
		jQuery("#fg-jquery-dialog").dialog('close');
		jQuery("#fg-jquery-upload-dialog").dialog('close');
	},
	insertLink: function(file, title) {
		this.insertAt('editwiki', '[tiki-download_file.php?fileId='+file+'|'+title+']', false, false, true);
		jQuery("#fg-jquery-dialog").dialog('close');
		jQuery("#fg-jquery-upload-dialog").dialog('close');
	},
	switchto: function(mode) {
		jQuery(".fg-insert-active").removeClass("fg-insert-active");
		jQuery("#fg-insert-mode-"+mode).addClass("fg-insert-active");
		if (mode=="image") {
			jQuery("#fg-insert-as-link").hide();
			jQuery("#fg-insert-as-image").show();
		} else {
			jQuery("#fg-insert-as-image").hide();
			jQuery("#fg-insert-as-link").show();
		}
	},
	insertAt: function(id, text) {
		var textarea = document.getElementById(id);
		
		if( document.selection && document.selection.createRange().text.length ) {
			var range = document.selection.createRange();
			var stored_range = range.duplicate();
			stored_range.moveToElementText( textarea );
			stored_range.setEndPoint( 'EndToEnd', range );
			textarea.selectionStart = stored_range.text.length - range.text.length;
			textarea.selectionEnd = textarea.selectionStart + range.text.length;
		}
		
		if (textarea.selectionStart<=textarea.selectionEnd && textarea.selectionEnd>0 && textarea.selectionStart>=0) {
//		alert(textarea.selectionStart);
			textarea.value = textarea.value.substr(0, textarea.selectionStart)+text+textarea.value.substr(textarea.selectionEnd);
			textarea.selectionStart = 0;
			textarea.selectionEnd = 0;
		} else {
			textarea.value = textarea.value + text;
		}
	}
}


function fastdel(url) {
	FileGallery.showDialog(url);
//	url += "&daconfirm=y";
//	FileGallery.open(url, '', { daconfirm: 'y' });
	return false;
}
