var FileGallery = {
	dialogmode: true,
	form: function(id) {
		var counters = { };
		var form = $('form#'+id);
		var data = {};
		$('input[name]', form).each(function(){
			if (($(this).attr('type')=="radio" || $(this).attr('type')=="checkbox") && $(this).attr('checked') || $(this).attr('type')!="radio" && $(this).attr('type')!="checkbox") {
				if ($(this).val()) {
					var name = $(this).attr('name');
					if (name.substr(name.length-2)=="[]") {
						var subname = name.substr(0,name.length-2);
						if (typeof counters[subname] != "undefined")
							name = subname+"["+(++counters[subname])+"]";
						else {
							name = subname+"[0]";
							counters[subname] = 0;
						}
					}
					data[name] = $(this).val();
				}
			}
		});
		$('textarea[name]', form).each(function(){
			data[$(this).attr('name')] = $(this).val();
		});
		$('select[name]', form).each(function(){
			data[$(this).attr('name')] = $(this).val();
		});
		return data;
	},
	open: function(url, form, post) {
		if (!this.dialogmode) {
			if (form)
				return true;
			else
				window.location = url;
			return;
		}
		if (this.dialogmode) {
			$('#fg-jquery-dialog').dialog({
				autoOpen: false,
				width: 702,
				modal: false,
				resizable: false,
				draggable: true,
				stack: false 
			});
			$('.ui-draggable').draggable({handle:'h1'});
		}
		var data = post ? post : '';
		if (form) {
			data = this.form(form);
			if (document.forms[form].elements.xtarget && document.forms[form].elements.xtarget.value!='') {
				document.forms[form].target = document.forms[form].elements.xtarget.value;
				document.forms[form].onsubmit = function() { }
				document.forms[form].submit();
				document.forms[form].elements.xtarget.value = '';
				document.forms[form].onsubmit = function() { return FileGallery.open(document.forms[form].action, document.forms[form].id) };
				return;
			}
		}
		$('#fg-jquery-dialog').load(url, data, function() {
			if (FileGallery.dialogmode) {
				$('#fg-jquery-dialog').dialog('option','height','auto');
				$('#fg-jquery-dialog').dialog('open');
			}
			$('.fg-pager a').bind('click', function(e) { FileGallery.open(this.href); return false; });
		});
		return false;
	},
	close: function() {
		FileGallery.upload.close();
		$("#fg-jquery-dialog").dialog("close");
	},
	loadmid: function(url) {
		$("#fg-files-content").load(url);
	},
	tree: function() {
		var rowstep = 0;
		if ($(".fg-galleries").hasClass("fg-galleries-hidden")) {
			$(".fg-galleries").removeClass("fg-galleries-hidden");
			$(".fg-files").removeClass("fg-files-wide");
			$(".fg-galleries > .fg-toolbar > .fg-toolbar-right").append($(".fg-files > .fg-toolbar > .fg-toolbar-left > .fg-toolbar-icon"));
			$(".fg-galleries > .fg-toolbar > .fg-toolbar-right > .fg-toolbar-icon > img").attr("src", "images/file_gallery/icon-hidegalleries.gif");
			rowstep = 3;
		} else {
			$(".fg-galleries").addClass("fg-galleries-hidden");
			$(".fg-files").addClass("fg-files-wide");
			$(".fg-galleries > .fg-toolbar > .fg-toolbar-right > .fg-toolbar-icon > img").attr("src", "images/file_gallery/icon-showgalleries.gif");
			$(".fg-files > .fg-toolbar > .fg-toolbar-left").prepend($(".fg-galleries > .fg-toolbar > .fg-toolbar-right > .fg-toolbar-icon"));
			rowstep = 4;
		}

		var tds = [ ];
		$(".fg-gallery-view tr").each(function(a,b) {
			$(b).find("td").each(function(c,d) {
				tds.push(d);
			});
			$(b).addClass("fg-tmp-delete");
		});
		
		for (var i=0; i<tds.length; i+=rowstep) {
			var tr = $("<tr/>");
			for (var j=i; j<i+rowstep; j++)
				tr.append(tds[j]);
			tr.appendTo($(".fg-gallery-view"));
		}
		
		$(".fg-tmp-delete").remove();
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
	},
	tab: function(name) {
		$(".fg-tabheads > li").removeClass("fg-tabheads-active");
		$("#fg-tabheads-"+name).addClass("fg-tabheads-active");
		$(".fg-tab").hide();
		$("#fg-tab-"+name).show();
	},
	editGallery: function(url) {
		$("#fg-jquery-gallery-dialog").load(url, function() {
			$("#fg-jquery-gallery-dialog").dialog({
				autoOpen: false,
				width: 500,
				modal: true,
				resizable: false,
				draggable: true,
				stack: false,
				dialogClass: "smalltitlebar"
			});
//			$("#fg-jquery-gallery-dialog").dialog('option','height','auto');
//			$("#fg-jquery-gallery-dialog").dialog('option','width','504');
			$("#fg-jquery-gallery-dialog").dialog('open');
		});
	},
	saveGallery: function() {
		var params = $("#fg-folder-form").serialize();
		var url = $("#fg-folder-form").attr("action");
		url += (url.indexOf("?") ? "&" : "?")+params;
		$.post(url, null, function(data) {
			$("#fg-jquery-gallery-dialog").html(data);
			$("#fg-jquery-gallery-dialog").dialog("close");
		});
	},
	replacefile: function(form) {
		var tmpname = "hiddeniframe"+(Math.round(Math.random()*10000));
		$("<iframe/>")
			.width(1)
			.height(1)
			.css("opacity","0")
			.attr("name", tmpname)
			.attr("id", tmpname)
			.appendTo($("body"));
		$(form).attr("target", tmpname);
		form.submit();
		$("iframe[name="+tmpname+"]").bind('load',function() {
			FileGallery.open(form.action);
		});
	},
	closeGallery: function() {
		$("#fg-jquery-gallery-dialog").dialog("close");
	},
	showDialog: function(url) {
		var xdiv = $("<div/>")
			.appendTo($("body")).dialog({
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
		$("<div/>")
			.addClass("fg-custom-dialog")
			.appendTo(xdiv)
			.load(url, { dialog: 1 });
		xdiv.dialog("open");
	},
	closeDialog: function() {
		$(".fg-customdialog").remove();
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
			draggable: true,
			stack: false,
			modal: true,
			dialogClass: "smalltitlebar"
		});
	},
	show: function(gallery, fm) {
		this.dialog();
		$("#fg-jquery-upload-dialog").load("tiki-upload_file.php?galleryId="+gallery+"&filegals_manager="+fm+"&fgspecial=1", function() {
			$("#fg-jquery-upload-dialog").dialog("option", "height", "auto");
			$("#fg-jquery-upload-dialog").dialog("open")
		});
	},
	edit: function(url) {
		this.dialog();
		$("#fg-jquery-upload-dialog").load(url, function() {
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
//		document.getElementById('progress_'+id).innerHTML = msg;
		$(".fg-upload > .tip").show();
		$(".fg-upload > .tip > .rbox-data").html(msg);
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
		this.insertAt("editwiki", "{img fileId="+file+(defsize?"":" width="+width+" height="+height)+"}", false, false, true);
		$("#fg-jquery-dialog").dialog('close');
		$("#fg-jquery-upload-dialog").dialog('close');
	},
	insertLink: function(file, title) {
		this.insertAt('editwiki', '[tiki-download_file.php?fileId='+file+'|'+title+']', false, false, true);
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

//		if (!textarea.selectionStart && textarea.getAttribute("x-selection-start"))
//			textarea.selectionStart = parseInt(textarea.getAttribute("x-selection-start"));
//		if (!textarea.selectionEnd && textarea.getAttribute("x-selection-end"))
//			textarea.selectionEnd = parseInt(textarea.getAttribute("x-selection-end"));
		
		if (textarea.getAttribute("x-selection-start") && textarea.getAttribute("x-selection-end")) {
			var start = parseInt(textarea.getAttribute("x-selection-start"));
			var end = parseInt(textarea.getAttribute("x-selection-end"));
			textarea.value = textarea.value.substr(0, start)+text+textarea.value.substr(end);
		} else if (textarea.selectionStart<=textarea.selectionEnd && textarea.selectionEnd>0 && textarea.selectionStart>=0) {
//		alert(textarea.value.substr(0, textarea.selectionStart)+"\n------\n"+text+"\n------\n"+textarea.value.substr(textarea.selectionEnd));
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

window.onload = function() {
	var ctrl = document.getElementById("editwiki");
	if (ctrl) {
		ctrl.onblur = function() {
			if( document.selection && document.selection.createRange().text.length ) {
				var range = document.selection.createRange();
				var stored_range = range.duplicate();
				stored_range.moveToElementText( this );
				stored_range.setEndPoint( 'EndToEnd', range );
				this.selectionStart = stored_range.text.length - range.text.length;
				this.selectionEnd = this.selectionStart + range.text.length;
			}
			
			this.setAttribute("x-selection-start", this.selectionStart ? this.selectionStart : "");
			this.setAttribute("x-selection-end", this.selectionEnd ? this.selectionEnd : "");
		}
	}
}
