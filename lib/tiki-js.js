//$Id$

var feature_no_cookie = 'n';

// simple translation function for tiki 6
function tr(str) {
	if (typeof lang[str] == 'string') {
		return lang[str];
	} else {
		return str;
	}
}

var lang = {};	// object to hold JS string translations
				// default strings empty, override in lang/xx/language.js
				// which will be included automatically

// end translation

function browser() {
	var b = navigator.appName;
	if (b == "Netscape") { this.b = "ns"; }
	else { this.b = b; }
	this.version = navigator.appVersion;
	this.v = parseInt(this.version, 10);
	this.ns = (this.b=="ns" && this.v>=5);
	this.op = (navigator.userAgent.indexOf('Opera')>-1);
	this.safari = (navigator.userAgent.indexOf('Safari')>-1);
	this.op7 = (navigator.userAgent.indexOf('Opera')>-1 && this.v>=7);
	this.ie56 = (this.version.indexOf('MSIE 5')>-1||this.version.indexOf('MSIE 6')>-1);
	/* ie567 added by Enmore */
	this.ie567 = (this.version.indexOf('MSIE 5')>-1||this.version.indexOf('MSIE 6')>-1||this.version.indexOf('MSIE 7')>-1);
	this.iewin = (this.ie56 && navigator.userAgent.indexOf('Windows')>-1);
	/* iewin7 added by Enmore */	
	this.iewin7 = (this.ie567 && navigator.userAgent.indexOf('Windows')>-1);
	this.iemac = (this.ie56 && navigator.userAgent.indexOf('Mac')>-1);
	this.moz = (navigator.userAgent.indexOf('Mozilla')>-1);
	this.moz13 = (navigator.userAgent.indexOf('Mozilla')>-1 && navigator.userAgent.indexOf('1.3')>-1);
	this.oldmoz = (navigator.userAgent.indexOf('Mozilla')>-1 && navigator.userAgent.indexOf('1.4')>-1 || navigator.userAgent.indexOf('Mozilla')>-1 && navigator.userAgent.indexOf('1.5')>-1 || navigator.userAgent.indexOf('Mozilla')>-1 && navigator.userAgent.indexOf('1.6')>-1);
	this.ns6 = (navigator.userAgent.indexOf('Netscape6')>-1);
	this.docom = (this.ie56||this.ns||this.iewin||this.op||this.iemac||this.safari||this.moz||this.oldmoz||this.ns6);
}

/* toggle CSS (tableless) layout columns */
function toggleCols(id,zeromargin,maincol) {
	var showit = 'show_' + escape(id);
	if (!zeromargin) { zeromargin = ''; }
	if (!id) { id = ''; }
	if (!maincol) { maincol = 'col1'; }
	if (document.getElementById(id).style.display == "none") {
		document.getElementById(id).style.display = "block";
		if (zeromargin == 'left') {
			document.getElementById(maincol).style.marginLeft = '';
			if (!document.getElementById(maincol).style.marginLeft) {
				document.getElementById(maincol).style.marginLeft = $("#"+id).width() + "px";
			}
			setSessionVar(showit,'y');
		} else {
			document.getElementById(maincol).style.marginRight = '';
			if (!document.getElementById(maincol).style.marginRight) {
				document.getElementById(maincol).style.marginRight = $("#"+id).width() + "px";
			}
			setSessionVar(showit,'y');
		}
	} else {
		document.getElementById(id).style.display = "none";
		if (zeromargin == 'left') {
			document.getElementById(maincol).style.marginLeft = '0';
			setSessionVar(showit,'n');
		} else {
			document.getElementById(maincol).style.marginRight = '0';
			setSessionVar(showit,'n');
		}
	}
}

function toggle_dynamic_var(name) {
	name1 = 'dyn_'+name+'_display';
	name2 = 'dyn_'+name+'_edit';
	if(document.getElementById(name1).style.display == "none") {
		document.getElementById(name2).style.display = "none";
		document.getElementById(name1).style.display = "inline";
	} else {
		document.getElementById(name1).style.display = "none";
		document.getElementById(name2).style.display = "inline";

	}

}

function chgArtType() {
	var articleType = document.getElementById('articletype').value;
	var typeProperties = articleTypes[articleType];

	var propertyList = ['show_topline','y',
	                    'show_subtitle','y',
	                    'show_linkto','y',
	                    'show_lang','y',
	                    'show_author','y',
	                    'use_ratings','y',
	                    'heading_only','n',
	                    'show_image_caption','y',
	                    'show_pre_publ','y',
	                    'show_post_expire','y',
	                    'show_image','y',
	                    'show_expdate','y'
	                    ];
	if (typeof articleCustomAttributes != 'undefined') {
		propertyList = propertyList.concat(articleCustomAttributes);
	}
	var l = propertyList.length, property, value;
	for (var i=0; i<l; i++) {
		property = propertyList[i++];
		value = propertyList[i];

		if (typeProperties[property] == value || (!typeProperties[property] && value == "n")) {
			display = "";
		} else {
			display = "none";
		}

		if (document.getElementById(property)) {
			document.getElementById(property).style.display = display;
		} else {
			j = 1;
			while (document.getElementById(property+'_'+j)) {
				document.getElementById(property+'_'+j).style.display = display;
				j++;
			}
		}
	}
}

function chgMailinType() {
	if (document.getElementById('mailin_type').value != 'article-put') {
		document.getElementById('article_topic').style.display = "none";
		document.getElementById('article_type').style.display = "none";
	} else {
		document.getElementById('article_topic').style.display = "";
		document.getElementById('article_type').style.display = "";
	}
}

function toggleSpan(id) {
	if (document.getElementById(id).style.display == "inline") {
		document.getElementById(id).style.display = "none";
	} else {
		document.getElementById(id).style.display = "inline";
	}
}

function toggleBlock(id) {
	if (document.getElementById(id).style.display == "none") {
		document.getElementById(id).style.display = "block";
	} else {
		document.getElementById(id).style.display = "none";
	}
}

function toggleTrTd(id) {
	if (document.getElementById(id).style.display == "none") {
		document.getElementById(id).style.display = "";
	} else {
		document.getElementById(id).style.display = "none";
	}
}

function showTocToggle() {
	if (document.createTextNode) {
		// Uses DOM calls to avoid document.write + XHTML issues

		var linkHolder = document.getElementById('toctitle');
		if (!linkHolder) { return; }

		var outerSpan = document.createElement('span');
		outerSpan.className = 'toctoggle';

		var toggleLink = document.createElement('a');
		toggleLink.id = 'togglelink';
		toggleLink.className = 'internal';
		toggleLink.href = 'javascript:toggleToc()';
		toggleLink.appendChild(document.createTextNode(tocHideText));

		outerSpan.appendChild(document.createTextNode('['));
		outerSpan.appendChild(toggleLink);
		outerSpan.appendChild(document.createTextNode(']'));

		linkHolder.appendChild(document.createTextNode(' '));
		linkHolder.appendChild(outerSpan);
		if (getCookie("hidetoc") == "1" ) { toggleToc(); }
	}
}

function changeText(el, newText) {
	// Safari work around
	if (el.innerText) {
		el.innerText = newText;
	} else if (el.firstChild && el.firstChild.nodeValue) {
		el.firstChild.nodeValue = newText;
	}
}

function toggleToc() {
	var toc = document.getElementById('toc').getElementsByTagName('ul')[0];
	var toggleLink = document.getElementById('togglelink');

	if (toc && toggleLink && toc.style.display == 'none') {
		changeText(toggleLink, tocHideText);
		toc.style.display = 'block';
		setCookie("hidetoc","0");
	} else {
		changeText(toggleLink, tocShowText);
		toc.style.display = 'none';
		setCookie("hidetoc","1");
	}
}

function chgTrkFld(f,o) {
	var opt = 0;
	document.getElementById('z').style.display = "none";
	document.getElementById('zDescription').style.display = "";
	document.getElementById('zStaticText').style.display = "none";
	document.getElementById('zStaticTextToolbars').style.display = "none";

	for (var i = 0; i < f.length; i++) {
		var c = f.charAt(i);
		if (document.getElementById(c)) {
			var ichoiceParent = document.getElementById('itemChoicesRow');
			var ichoice = document.getElementById(c + 'itemChoices');
			if (c == o) {
				document.getElementById(c).style.display = "";
				document.getElementById('z').style.display = "block";
				if (c == 'S') {
					document.getElementById('zDescription').style.display = "none";
					document.getElementById('zStaticText').style.display = "";
					document.getElementById('zStaticTextToolbars').style.display = "";
				}
				if (ichoice) {
					ichoice.style.display = "";
					ichoiceParent.style.display = "";
				} else {
					ichoiceParent.style.display = "none";
				}
			} else {
				document.getElementById(c).style.display = "none";
				if (ichoice) {
					ichoice.style.display = "none";
				}
			}
		}
	}
}

function chgTrkLingual(item) {
	document.getElementById("multilabelRow").style.display = ( item == 't' || item == 'a' ) ? '' : 'none';
}

function multitoggle(f,o) {
	for (var i = 0; i < f.length; i++) {
		if (document.getElementById('fid'+f[i])) {
			if (f[i] == o) {
				document.getElementById('fid'+f[i]).style.display = "block";
			} else {
				document.getElementById('fid'+f[i]).style.display = "none";
			}
		}
	}
}

function setMenuCon(foo) {
	var it = foo.split(",");
	document.getElementById('menu_url').value = it[0];
	document.getElementById('menu_name').value = it[1];
	if (it[2]) {
		document.getElementById('menu_section').value = it[2];
	} else {
		document.getElementById('menu_section').value = '';
	}
	if (it[3]) {
		document.getElementById('menu_perm').value = it[3];
	} else {
		document.getElementById('menu_perm').value = '';
	}
}

function genPass(w1) {
	vo = "aeiouAEU";

	co = "bcdfgjklmnprstvwxzBCDFGHJKMNPQRSTVWXYZ0123456789_$%#";
	s = Math.round(Math.random());
	l = 8;
	p = '';

	for (i = 0; i < l; i++) {
		if (s) {
			letter = vo.charAt(Math.round(Math.random() * (vo.length - 1)));

			s = 0;
		} else {
			letter = co.charAt(Math.round(Math.random() * (co.length - 1)));

			s = 1;
		}

		p = p + letter;
	}

	document.getElementById(w1).value = p;
}

function setUserModule(foo1) {
	document.getElementById('usermoduledata').value = foo1;
}

function replaceLimon(vec) {
	document.getElementById(vec[0]).value = document.getElementById(vec[0]).value.replace(vec[1], vec[2]);
}

function setSelectionRange(textarea, selectionStart, selectionEnd) {
	$(textarea).selection(selectionStart, selectionEnd);
}

function getTASelection( textarea ) {
	var $textareaEditor = getCodeMirrorFromInput($(textarea));
	if ($textareaEditor) {
		return $textareaEditor.selection();
	}
	
	var ta_id = $(textarea).attr("id"), r, cked;
	if ($('#cke_contents_' + ta_id).length !== 0) {
		// get selection from ckeditor
		cked = typeof CKEDITOR !== 'undefined' ? CKEDITOR.instances[ta_id] : null;
		if (cked) {
			var sel = cked.getSelection();
			if (sel && sel.getType() === CKEDITOR.SELECTION_TEXT) {	// why so fiddly?
				if (CKEDITOR.env.ie) {
					output = sel.document.$.selection.createRange().text;
				} else {
					output = sel.getNative().toString();
				}
				return output;
			}
		}
	} else {
		if (typeof $(textarea).attr("selectionStartSaved") != 'undefined' && $(textarea).attr("selectionStartSaved")) { // forgetful firefox/IE now
			return textarea.value.substring($(textarea).attr("selectionStartSaved"), $(textarea).attr("selectionEndSaved"));
		} else if (typeof textarea.selectionStart != 'undefined') {
			return textarea.value.substring(textarea.selectionStart, textarea.selectionEnd);
		} else { // IE
			r = document.selection.createRange();
			return r.text;
		}
	}
}

function setCaretToPos (textarea, pos) {
	setSelectionRange(textarea, pos, pos);
}

function getCaretPos (textarea) {
	var $textareaEditor = getCodeMirrorFromInput($(textarea));
	if ($textareaEditor) {
		var endPoint = $textareaEditor.cursorCoords();
		return (endPoint.x ? endPoint.x : 0);
	}
	
	if (typeof textarea.selectionEnd != 'undefined') {
		return textarea.selectionEnd;
	} else if ( document.selection ) {

		textarea.focus();
		var range = document.selection.createRange();
		if (range === null) {
			return 0;
		}
		var re = textarea.createTextRange();
		var rc = re.duplicate();
		re.moveToBookmark(range.getBookmark());
		rc.setEndPoint('EndToStart', re);
		return rc.text.length ? rc.text.length : 0;

	} else {
		return 0;
	}
}

function insertAt(elementId, replaceString, blockLevel, perLine, replaceSelection) {
	
	// inserts given text at selection or cursor position
	var $textarea = $('#' + elementId);
	var $textareaEditor = getCodeMirrorFromInput($textarea);
	var toBeReplaced = /text|page|area_id/g; //substrings in replaceString to be replaced by the selection if a selection was done
	var hiddenParents = $textarea.parents('fieldset:hidden:last');
	if (hiddenParents.length) { hiddenParents.show(); }
	
	if ($textareaEditor) {
	 	var handle = $textareaEditor.cursorLine();
	 	var cursor = $textareaEditor.cursorPosition();
	 	
	 	if (blockLevel) {
	 		selection = $textareaEditor.lineContent(handle);
	 	}
	 	
	 	if (perLine) {
	 		lines = selection.split("\n");
	 		for (k = 0; lines.length > k; ++k) {
	 			if (lines[k].length !== 0) {
	 				newString += replaceString.replace(toBeReplaced, lines[k]);
	 			}
	 			if (k != lines.length - 1) {
	 				newString += "\n";
	 			}
	 		}
	 	} else {
	 		if (replaceSelection) {
	 			newString = replaceString;
	 		} else  if (replaceString.match(toBeReplaced)) {
	 			newString = replaceString.replace(toBeReplaced, selection);
	 		} else {
	 			newString = replaceString + '\n' + selection;
	 		}
	 	}
	 	
	 	if (blockLevel) {
	 		$textareaEditor.setLineContent(handle, newString);
	 	} else if (($textareaEditor.selection() + '').length) {
	 			$textareaEditor.replaceSelection(newString);
	 	} else {
	 		$textareaEditor.insertIntoLine($textareaEditor.lastLine(), 'end', newString);
	 	}
	 	
		return;
	 // get ckeditor handling out of the way - can only be simple text insert for now
	} else if ($('#cke_contents_' + elementId).length !== 0) {
		// get selection from ckeditor
		var cked = typeof CKEDITOR !== 'undefined' ? CKEDITOR.instances[elementId] : null;
		if (cked) {
			var isPlugin = replaceString.match(/^\s?\{/m);		// do match in two halves due to multiline problems
			if (isPlugin) {
				isPlugin = replaceString.match(/\}\s?$/m);		// not so simple {plugin} match
			}
			isPlugin = isPlugin && isPlugin.length > 0;

			var sel = cked.getSelection(), rng;
			if (sel) { // not from IE sometimes?
				rng = sel.getRanges();
				if (rng.length) {
					rng = rng[0];
				}
			}
			var plugin_el;
			if (isPlugin && rng && !rng.collapsed) {
				var com = cked.getSelection().getStartElement();
				if (typeof com !== 'undefined' && com && com.$) {
					while (com.$.nextSibling && com.$ !== rng.endContainer.$) {	// loop through selection if multiple elements
						com = new CKEDITOR.dom.element(com.$.nextSibling);
						if ($(com.$).hasClass("tiki_plugin") || $(com.$).find(".tiki_plugin").length === 0) {	// found it or parent (hmm)
							break;
						}
					}
					if (!$(com.$).hasClass("tiki_plugin")) { // not found it yet?
						plugin_el = $(com.$).find(".tiki_plugin"); // using jQuery
						if (plugin_el.length == 1) { // found descendant plugin
							com = new CKEDITOR.dom.element(plugin_el[0]);
						} else {
							plugin_el = $(com.$).parents(".tiki_plugin"); // try parents
							if (plugin_el.length == 1) { // found p plugin
								com = new CKEDITOR.dom.element(plugin_el[0]);
							} else { // still not found it? sometimes Fx seems to get the editor body as the selection...
								var plugin_type = replaceString.match(/^\s?\{([\w]+)/);
								if (plugin_type.length > 1) { plugin_type = plugin_type[1].toLowerCase(); }
								
								plugin_el = $(com.$).find("[plugin=" + plugin_type + "].tiki_plugin"); // find all of them
								if (plugin_el.length == 1) { // good guess!
									com = new CKEDITOR.dom.element(plugin_el[0]);
								} else {
									// Does not seem to be a problem at least with the image plugin, commenting out for release but keeping it here in case problem reappears
									//if (!confirm(tr("Development notice: Could not find plugin being edited, sorry. Choose cancel to debug."))) {
									//	debugger;
									//}
								}
							}
						}
					}
				}
				if ($(com.$).hasClass("tiki_plugin")) {
					$(com.$).replaceWith(document.createTextNode(replaceString));
					cked.reParse();
					return;
				}
			}
			//if (sel.getType() === CKEDITOR.SELECTION_TEXT) {
				// fall through to insertText as if all else failed
			//}
		}
		// catch all other issues and do the insert wherever ckeditor thinks best,
		// sadly as the first element sometimes FIXME
		cked.insertText( replaceString );
		if (isPlugin || replaceString.match(/^\s?\(\(.*?\)\)\s?$/)) {	// also ((wiki links))
			cked.reParse();
		}
		return;
	}
	
	if (!$textarea.length && elementId === "fgal_picker") {	// ckeditor file browser
		$(".cke_dialog_contents").find("input:first").val(replaceString);
		return;
	}

	($textareaEditor ? $textareaEditor : $textarea).focus();
	
	var val = $textarea.val();
	var selection = ( $textareaEditor ? $textareaEditor : $textarea ).selection();
	
	if (selection.start === 0 && selection.end === 0 &&
					typeof $textarea.attr("selectionStartSaved") != 'undefined') {	// get saved textarea selection
		if ($textarea.attr("selectionStartSaved")) {	// forgetful firefox/IE
			selection.start = $textarea.attr("selectionStartSaved");
			selection.end = $textarea.attr("selectionEndSaved");
		} else {
			selection.start = getCaretPos($textarea[0]);
			selection.end = selection.start;
		}
	}

	var lines;
	if ($textarea[0].createTextRange && $textarea[0].value !== val) {
		lines = val.substring(0, selection.start).match(/\n/g);
		if (lines) {
			selection.start -= lines.length;	// remove one char per line for IE
			selection.end   -= lines.length;
		}
		if (val.substring(selection.start, selection.start + 1) === "\n") {
			selection.start++;
		}
		lines = val.substring(selection.start, selection.end).match(/\n/g);
		if (lines) {
			selection.end   -= lines.length;	// if selection more than one line
		}
	}
	var selectionStart = selection.start;
	var selectionEnd = selection.end;
	var scrollTop=$textarea[0].scrollTop;

	if( blockLevel ) {
		// Block level operations apply to entire lines

		// +1 and -1 to handle end of line caret position correctly
		selectionStart = val.lastIndexOf( "\n", selectionStart - 1 ) + 1;
		selectionEnd = val.indexOf( "\n", selectionEnd );
		if (selectionEnd < 0) {
			selectionEnd = val.length;
		}
	}

	var newString = '';
	if ((selectionStart != selectionEnd) && !$textareaEditor) { // has there been a selection
		if( perLine ) {
			lines = val.substring(selectionStart, selectionEnd).split("\n");
			for( k = 0; lines.length > k; ++k ) {
				if( lines[k].length !== 0 ) {
					newString += replaceString.replace(toBeReplaced, lines[k]);
				}
				if( k != lines.length - 1 ) {
					newString += "\n";
				}
			}
		} else {
			if (replaceSelection) {
				newString = replaceString;
			} else if (replaceString.match(toBeReplaced)) {
				newString = replaceString.replace(toBeReplaced, val.substring(selectionStart, selectionEnd));
			} else {
				newString = replaceString + '\n' + val.substring(selectionStart, selectionEnd);
			}
		}
		
		$textarea.val(val.substring(0, selectionStart)
						+ newString
						+ val.substring(selectionEnd)
					);
		setSelectionRange($textarea[0], selectionStart, selectionStart + newString.length);
		
	} else { // insert at caret
		$textarea.val(val.substring(0, selectionStart)
						+ replaceString
						+ val.substring(selectionEnd)
					);
		setCaretToPos($textarea[0], selectionStart + replaceString.length);
	}
	$textarea[0].scrollTop=scrollTop;

	if (hiddenParents.length) { hiddenParents.hide(); }
	if (typeof auto_save_id != "undefined" && auto_save_id.length > 0 && typeof auto_save == 'function') {  auto_save(); }

}

function setUserModuleFromCombo(id, textarea) {
	document.getElementById(textarea).value = document.getElementById(textarea).value
				+ document.getElementById(id).options[document.getElementById(id).selectedIndex].value;
}


function toggle(foo) {
	var display = $("#"+foo).css('display');
	if (display == "none") {
		show(foo, true, "menu");
	} else {
		if (display == "block") {
			hide(foo, true, "menu");
		} else {
			show(foo, true, "menu");
		}
	}
}

function flip_thumbnail_status(id) {
	var elem = document.getElementById(id);
	if ( elem.className == 'thumbnailcontener' ) {
		elem.className += ' thumbnailcontenerchecked';
	} else {
		elem.className = 'thumbnailcontener';
	}
}

function flip_class(itemid, class1, class2) {
	var elem = document.getElementById(itemid);
	if (elem && typeof elem != 'undefined') {
		elem.className = elem.className == class1 ? class2 : class1;
		setCookie('flip_class_' + itemid, elem.className);
	}
}

function tikitabs( focus, tabElement) {
	var container;
	if (tabElement === "undefined") {
		container = $(".tabs:first").parent();
	} else {
		container = $(tabElement).parents(".tabs:first").parent();
	}

	if (focus > $("> .tabs .tabmark", container).length) {
		focus = 1;	// limit to number of tabs - somehow getting set to 222 sometimes
	}

	$("> .tabs .tabmark:not(.tab" + focus + ":first)", container).removeClass("tabactive");		// may need .addClass("tabinactive");
	$("> .tabs .tabmark.tab" + focus + ":first", container).addClass("tabactive");				// and .removeClass("tabinactive");
	$("> .tabcontent:not(.content" + focus + ":first)", container).hide();
	$("> .tabcontent.content" + focus + ":first", container).show();
	setCookie( $(".tabs:first", container).data("name"), focus, "tabs", "session");

}

/* foo: name of the menu
 * def: menu type (e:extended, c:collapsed, f:fixed)
 * the menu is collapsed function of its cookie: if no cookie is set, the def is used
 */
function setfolderstate(foo, def, img, status) {
	if (!status) {
		status = getCookie(foo, "menu", "o");
	}
	if (!img) {
		if (document.getElementsByName('icn' + foo)[0].src.search(/[\\\/]/)) {
			img = document.getElementsByName('icn' + foo)[0].src.replace(/.*[\\\/]([^\\\/]*)$/, "$1");
		} else {
			img = 'folder.png';
		}
	}
	var src = img; // default
	if (status == 'c') {
		hide(foo, false, "menu");
	} else {
		show(foo, false, "menu");
	}
	if (status == 'c' && def != 'd') { /* need to change the open icon to a close one*/
		src = src.replace(/^o/, '');
	} else if (status != 'c' && def == 'd' && src.indexOf('o') !== 0) { /* need to change the close icon to an open one */
		src = 'o' + img;
	}
	document.getElementsByName('icn' + foo)[0].src = document.getElementsByName('icn' + foo)[0].src.replace(/[^\\\/]*$/, src);
}

function setheadingstate(foo) {
	var status = getCookie(foo, "showhide_headings");
	if (status == "o") {
		show(foo);
		collapseSign("flipper" + foo);
	} else /* if (status == "c") */ {
		if (!document.getElementById(foo).style.display == "none") {
			hide(foo);
			expandSign("flipper" + foo);
		}
	}
}

function setsectionstate(foo, def, img, status) {
	if (!status) {
		status = getCookie(foo, "menu", "o");
	}
	if (status == "o") {
		show(foo);
		if (img) { src = "o" + img; }
	} else if (status != "c" && def != 'd') {
		show(foo);
		if (img) { src = "o" + img; }
	} else /* if (status == "c") */ {
		hide(foo);
		if (img) { src = img; }
	}
	if (img && document.getElementsByName('icn' + foo).length) {
		document.getElementsByName('icn' + foo)[0].src = document.getElementsByName('icn' + foo)[0].src.replace(/[^\\\/]*$/, src);
	}
}

function icntoggle(foo, img) {
	if (!img) {
		if ($("#icn" + foo).attr("src").search(/[\\\/]/)) {
			img = $("#icn" + foo).attr("src").replace(/.*[\\\/]([^\\\/]*)$/, "$1");
		} else {
			img = 'folder.png';
		}
	}
	if ($("#" + foo + ":hidden").length) {
		show(foo, true, "menu");
		$("#icn" + foo).attr("src", $("#icn" + foo).attr("src").replace(/[^\\\/]*$/, 'o' + img));

	} else {
		hide(foo, true, "menu");
		img = img.replace(/(^|\/|\\)o(.*)$/, '$1$2');
		$("#icn" + foo).attr("src", $("#icn" + foo).attr("src").replace(/[^\\\/]*$/, img));
	}
}

//Initialize a cross-browser XMLHttpRequest object.
//The object return has to be sent using send(). More parameters can be
//given.
//callback - The function that will be called when the response arrives
//First parameter will be the status
//(HTTP Response Code [200,403, 404, ...])
//method - GET or POST
//url - The URL to open
function getHttpRequest( method, url, async )
{
	if( async === undefined ) {
		async = false;
	}
	var request;

	if( window.XMLHttpRequest ) {
		request = new XMLHttpRequest();
	} else if( window.ActiveXObject )
	{
		try
		{
			request = new ActiveXObject( "Microsoft.XMLHTTP" );
		}
		catch( ex )
		{
			request = new ActiveXObject("MSXML2.XMLHTTP");
		}
	}
	else {
		return false;
	}
	if( !request ) {
		return false;
	}
	request.open( method, url, async );

	return request;
}

//name - name of the cookie
//value - value of the cookie
// [expires] - expiration date of the cookie (defaults to end of current session)
// [path] - path for which the cookie is valid (defaults to path of calling document)
// [domain] - domain for which the cookie is valid (defaults to domain of calling document)
// [secure] - Boolean value indicating if the cookie transmission requires a secure transmission
//* an argument defaults when it is assigned null as a placeholder
//* a null placeholder is not required for trailing omitted arguments
function setSessionVar(name,value) {
	var request = getHttpRequest( "GET", "tiki-cookie-jar.php?" + name + "=" + escape(value));
	request.send('');
	tiki_cookie_jar[name] = value;
}

function setCookie(name, value, section, expires, path, domain, secure) {
	if (getCookie(name, section) == value) {
		return true;
	}
	if (!expires) {
		expires = new Date();
		expires.setFullYear(expires.getFullYear() + 1);
	}
	if (expires === "session") {
		expires = "";
	}
	if (feature_no_cookie == 'y') {
		var request = getHttpRequest( "GET", "tiki-cookie-jar.php?" + name + "=" + escape( value ) );
		try {
			request.send('');
			// alert("XMLHTTP/set"+request.readyState+request.responseText);
			tiki_cookie_jar[name] = value;
			return true;
		}
		catch( ex )	{
			setCookieBrowser(name, value, section, expires, path, domain, secure);
			return false;
		}
	}
	else {
		setCookieBrowser(name, value, section, expires, path, domain, secure);
		return true;
	}
}
function setCookieBrowser(name, value, section, expires, path, domain, secure) {
	if (section) {
		valSection = getCookie(section);
		name2 = "@" + name + ":";
		if (valSection) {
			if (new RegExp(name2).test(valSection)) {
				valSection  = valSection.replace(new RegExp(name2 + "[^@;]*"), name2 + value);
			} else {
				valSection = valSection + name2 + value;
			}
			setCookieBrowser(section, valSection, null, expires, path, domain, secure);
		}
		else {
			valSection = name2+value;
			setCookieBrowser(section, valSection, null, expires, path, domain, secure);
		}

	}
	else {
		var curCookie = name + "=" + escape(value) + ((expires) ? "; expires=" + expires.toGMTString() : "")
		+ ((path) ? "; path=" + path : "") + ((domain) ? "; domain=" + domain : "") + ((secure) ? "; secure" : "");
		document.cookie = curCookie;
	}
}

//name - name of the desired cookie
//section - name of group of cookies or null
// * return string containing value of specified cookie or null if cookie does not exist
function getCookie(name, section, defval) {
	if( feature_no_cookie == 'y' && (window.XMLHttpRequest || window.ActiveXObject) && typeof tiki_cookie_jar != "undefined" && tiki_cookie_jar.length > 0) {
		if (typeof tiki_cookie_jar[name] == "undefined") {
			return defval;
		}
		return tiki_cookie_jar[name];
	}
	else {
		return getCookieBrowser(name, section, defval);
	}
}
function getCookieBrowser(name, section, defval) {
	if (typeof defval === "undefined") { defval = null; }
	if (section) {
		var valSection = getCookieBrowser(section);
		if (valSection) {
			var name2 = "@"+name+":";
			var val = valSection.match(new RegExp(name2 + "([^@;]*)"));
			if (val) {
				return unescape(val[1]);
			} else {
				return defval;
			}
		} else {
			return defval;
		}
	} else {
		var dc = document.cookie;

		var prefix = name + "=";
		var begin = dc.indexOf("; " + prefix);

		if (begin == -1) {
			begin = dc.indexOf(prefix);

			if (begin !== 0) {
				return defval;
			}
		} else { begin += 2; }

		var end = document.cookie.indexOf(";", begin);

		if (end == -1) {
			end = dc.length;
		}
		return unescape(dc.substring(begin + prefix.length, end));
	}
}

//name - name of the cookie
//[path] - path of the cookie (must be same as path used to create cookie)
// [domain] - domain of the cookie (must be same as domain used to create cookie)
// * path and domain default if assigned null or omitted if no explicit argument proceeds
function deleteCookie(name, section, expires, path, domain, secure) {
	if (section) {
		valSection = getCookieBrowser(section);
		name2 = "@" + name + ":";
		if (valSection) {
			if (new RegExp(name2).test(valSection)) {
				valSection  = valSection.replace(new RegExp(name2 + "[^@;]*"), "");
				setCookieBrowser(section, valSection, null, expires, path, domain, secure);
			}
		}
	}
	else {

//		if( !setCookie( name, '', 0, path, domain ) ) {
//		if (getCookie(name)) {
		document.cookie = name + "="
		+ ((path) ? "; path=" + path : "") + ((domain) ? "; domain=" + domain : "") + "; expires=Thu, 01-Jan-70 00:00:01 GMT";
//		}
	}
}

//date - any instance of the Date object
//* hand all instances of the Date object to this function for "repairs"
function fixDate(date) {
	var base = new Date(0);

	var skew = base.getTime();

	if (skew > 0) {
		date.setTime(date.getTime() - skew);
	}
}


//Expand/collapse lists

function flipWithSign(foo) {
	if (document.getElementById(foo).style.display == "none") {
		show(foo, true, "showhide_headings");
		collapseSign("flipper" + foo);
	} else {
		hide(foo, true, "showhide_headings");
		expandSign("flipper" + foo);
	}
}

//set the state of a flipped entry after page reload
function setFlipWithSign(foo) {
	if (getCookie(foo, "showhide_headings", "o") == "o") {
		collapseSign("flipper" + foo);

		show(foo);
	} else {
		expandSign("flipper" + foo);

		hide(foo);
	}
}

function expandSign(foo) {
	if (document.getElementById(foo)) {
		document.getElementById(foo).firstChild.nodeValue = "[+]";
	}
}

function collapseSign(foo) {
	if (document.getElementById(foo)) {
		document.getElementById(foo).firstChild.nodeValue = "[-]";
	}
} // flipWithSign()

// Set client timezone
// moved to js_detect.php

//function added for use in navigation dropdown
//example :
//<select name="anything" onchange="go(this);">
//<option value="http://tiki.org">tiki.org</option>
//</select>
function go(o) {
	if (o.options[o.selectedIndex].value !== "") {
		location = o.options[o.selectedIndex].value;

		o.options[o.selectedIndex] = 1;
	}

	return false;
}


//function: targetBlank
//desc: opens a new window, XHTML-compliant replacement of the "TARGET" tag
//added by: Ralf Lueders (lueders@lrconsult.com)
//date: Sep 7, 2003
//params: url: the url for the new window
//mode='nw': new, full-featured browser window
//mode='popup': new windows, no features & buttons

function targetBlank(url,mode) {
	var features = 'menubar=yes,toolbar=yes,location=yes,directories=yes,fullscreen=no,titlebar=yes,hotkeys=yes,status=yes,scrollbars=yes,resizable=yes';
	switch (mode) {
	// new full-equipped browser window
	case 'nw':
		break;
		// new popup-window
	case 'popup':
		features = 'menubar=no,toolbar=no,location=no,directories=no,fullscreen=no,titlebar=no,hotkeys=no,status=no,scrollbars=yes,resizable=yes';
		break;
	default:
		break;
	}
	blankWin = window.open(url,'_blank',features);
}

//function: confirmTheLink
//desc: pop up a dialog box to confirm the action
//added by: Franck Martin
//date: Oct 12, 2003
//params: theLink: The link where it is called from
//params: theMsg: The message to display
function confirmTheLink(theLink, theMsg)
{
    // Confirmation is not required if browser is Opera (crappy js implementation)
	if (typeof(window.opera) != 'undefined') {
		return true;
	}

	var is_confirmed = confirm(theMsg);
	// if (is_confirmed) {
	// theLink.href += '&amp;is_js_confirmed=1';
	// }

	return is_confirmed;
}

/** \brief: insert img tag in textarea
 * 
 */
function insertImgFile(elementId, fileId, oldfileId,type,page,attach_comment) {
	textarea = $('#' + elementId)[0];
	fileup   = $('input[name=' + fileId + ']')[0];
	oldfile  = $('input[name=' + oldfileId + ']')[0];
	prefixEl = $('input[name=prefix]')[0];
	prefix   = "img/wiki_up/";

	if (!textarea || ! fileup) {
		return;
	}
	if ( prefixEl) { prefix= prefixEl.value; }

	filename = fileup.value;
	oldfilename = oldfile.value;

    if (filename == oldfilename || filename === "" ) { // insert only if name really changed
		return;
	}
	oldfile.value = filename;

	if (filename.indexOf("/")>=0) { // unix
		dirs = filename.split("/");
		filename = dirs[dirs.length-1];
	}
	if (filename.indexOf("\\")>=0) { // dos
		dirs = filename.split("\\");
		filename = dirs[dirs.length-1];
	}
	if (filename.indexOf(":")>=0) { // mac
		dirs = filename.split(":");
		filename = dirs[dirs.length-1];
	}
	// @todo - here's a hack: we know its ending up in img/wiki_up.
	// replace with dyn. variable once in a while to respect the tikidomain
	if (type == "file") {
		str = "{file name=\""+filename + "\"";
		var desc = $('#' + attach_comment).val();
		if (desc) {
			str = str + " desc=\"" + desc + "\"";
		}
		str = str + "}";
	} else {
		str = "{img src=\"img/wiki_up/" + filename + "\" }\n";
	}
	insertAt(elementId, str);
}

/* add new upload image form in page edition */
var img_form_count = 2;
function addImgForm() {
	var new_text = document.createElement('span');
	new_text.setAttribute('id','picfile' + img_form_count);
	new_text.innerHTML = '<input name=\'picfile' + img_form_count + '\' type=\'file\' onchange=\'javascript:insertImgFile("editwiki","picfile' + img_form_count + '","hasAlreadyInserted","img")\'/><br />';
	document.getElementById('new_img_form').appendChild(new_text);
	needToConfirm = true;
	img_form_count ++;
}

/*
 * opens wiki 3d browser
 */
function wiki3d_open (page, width, height) {
	window.open('tiki-wiki3d.php?page='+page,'wiki3d','width='+width+',height='+height+',scrolling=no');
}

/* some little email protection */
function protectEmail(nom, domain, sep) {
	return '<a class="wiki" href="mailto:'+nom+'@'+domain+'">'+nom+sep+domain+'</a>';
}

browser();

//This was added to allow wiki3d to change url on tiki's window
window.name = 'tiki';

var fgals_window = null;

function openFgalsWindow(filegal_manager_url, reload) {
	if(fgals_window && typeof fgals_window.document != "undefined" && typeof fgals_window.document != "unknown" && !fgals_window.closed) {
		if (reload) {
			fgals_window.location.replace(filegal_manager_url);
		}
		fgals_window.focus();
	} else {
		fgals_window=window.open(filegal_manager_url,'_blank','menubar=1,scrollbars=1,resizable=1,height=500,width=800,left=50,top=50');
	}
	$(window).unload(function(){	// tidy
		fgals_window.close();
	});
}

/* Count the number of words (spearated with space) */
function wordCount(maxSize, source, cpt, message) {
	var formcontent = source.value;
	str = formcontent.replace(/^\s+|\s+$/g, '') ;
	formcontent = str.split(/[^\S]+/);
	if (maxSize > 0 && formcontent.length > maxSize) {
		alert(message);
		source.value = source.value.substr(0, source.value.length-1);
	} else {
		document.getElementById(cpt).value = formcontent.length;
	}
}
function charCount(maxSize, source, cpt, message) {
	var formcontent = source.value;
	if (maxSize > 0 && formcontent.length > maxSize) {
		alert(message);
		source.value = source.value.substr(0, maxSize);
	} else {
		document.getElementById(cpt).value = formcontent.length;
	}
}

// apparently this function is not used anymore, should we remove it? - sampaioprimo
function show_plugin_form( type, index, pageName, pluginArgs, bodyContent )
{
	var target = document.getElementById( type + index );
	var content = target.innerHTML;

	var form = build_plugin_form( type, index, pageName, pluginArgs, bodyContent );

	target.innerHTML = '';
	target.appendChild( form );
}

/* wikiplugin editor */
function popup_plugin_form(area_id, type, index, pageName, pluginArgs, bodyContent, edit_icon )
{
	if ($.ui) {
		return popupPluginForm( area_id, type, index, pageName, pluginArgs, bodyContent, edit_icon );
	}
	var container = document.createElement( 'div' );
	container.className = 'plugin-form-float';
	var textarea = $('#' + area_id)[0];

	var minimize = document.createElement( 'a' );
	var icon = document.createElement( 'img' );
	minimize.appendChild( icon );
	minimize.href = 'javascript:void(0)';
	container.appendChild( minimize );
	icon.src = 'pics/icons/cross.png';
	icon.style.position = 'absolute';
	icon.style.top = '5px';
	icon.style.right = '5px';
	icon.style.border = 'none';

	if (!index) { index = 0; }
	if (!pageName) { pageName = ''; }
	if (!pluginArgs) { pluginArgs = {}; }
	if (!bodyContent) { 
		if (document.getTASelection) {
			bodyContent = document.getTASelection(textarea);
		} else if (window.getTASelection) {
			bodyContent = window.getTASelection(textarea);
		} else if (document.selection) {
			bodyContent = document.selection.createRange().text;
		} else {
			bodyContent = '';
		}
	}

	var form = build_plugin_form(
			type,
			index,
			pageName,
			pluginArgs,
			bodyContent
	);

	form.onsubmit = function()
	{
		var meta = tiki_plugins[type];
		var params = [];
		var edit = edit_icon;

		for(i=0; i<form.elements.length; i++){
			element = form.elements[i].name;

			var matches = element.match(/params\[(.*)\]/);

			if (matches === null) {
				// it's not a parameter, skip
				continue;
			}
			var param = matches[1];

			var val = form.elements[i].value;

			if( val !== '' ) {
				params.push( param + '="' + val + '"' );
			}
		}

		var blob
		if (typeof form.content != 'undefined' && form.content.length > 0) {
			blob = '{' + type.toUpperCase() + '(' + params.join(' ') + ')}' + form.content.value + '{' + type.toUpperCase() + '}';
		} else {
			blob = '{' + type.toLowerCase() + ' ' + params.join(' ') + '}';
		}

		if (edit) {
			return true;
		} else {
			insertAt( area_id, blob );
			document.body.removeChild( container );
		}
		return false;
	};

	minimize.onclick = function() {
		var edit = edit_icon;
		if (edit) {
			edit.style.display = 'inline';
		}
		document.body.removeChild( container );
	};

	document.body.appendChild( container );
	if (edit_icon) {
		edit_icon.style.display = 'none';
	}
	container.appendChild( form );
	
	handlePluginFieldsHierarchy(type);
}

function build_plugin_form( type, index, pageName, pluginArgs, bodyContent )
{
	var form = document.createElement( 'form' );
	form.method = 'post';
	form.action = 'tiki-wikiplugin_edit.php';
	form.className  = 'wikiplugin_edit';

	var hiddenPage = document.createElement( 'input' );
	hiddenPage.type = 'hidden';
	hiddenPage.name = 'page';
	hiddenPage.value = pageName;
	form.appendChild( hiddenPage );

	var hiddenType = document.createElement( 'input' );
	hiddenType.type = 'hidden';
	hiddenType.name = 'type';
	hiddenType.value = type;
	form.appendChild( hiddenType );

	var hiddenIndex = document.createElement( 'input' );
	hiddenIndex.type = 'hidden';
	hiddenIndex.name = 'index';
	hiddenIndex.value = index;
	form.appendChild( hiddenIndex );

	var meta = tiki_plugins[type];

	var header = document.createElement( 'h3' );
	header.innerHTML = meta.name;
	form.appendChild( header );

	var desc = document.createElement( 'div' );
	desc.innerHTML = meta.description;
	if (meta.documentation) {
		desc.innerHTML += ' <a href="http://doc.tiki.org/' + meta.documentation + '" target="tikihelp" class="tikihelp" tabIndex="-1">' +
				'<img src="pics/icons/help.png" alt="Help" width="16" height="16" clss="icon" title="Help" class="icon">' +
			'</a>';

	}
	form.appendChild( desc );

	var table = document.createElement( 'table' );
	table.className = 'normal';
	table.id = 'plugin_params';
	form.appendChild( table );

	for (param in meta.params) {
		if (meta.params[param].advanced) {
			var br = document.createElement( 'br' );
			form.appendChild( br );

			var span_advanced_button = document.createElement( 'span' );
			span_advanced_button.className = 'button';
			form.appendChild( span_advanced_button );

			var advanced_button = document.createElement( 'a' );
			advanced_button.innerHTML = tr('Advanced options');
			advanced_button.onclick = function() { flip('plugin_params_advanced');};
			span_advanced_button.appendChild(advanced_button);

			var table_advanced = document.createElement( 'table' );
			table_advanced.className = 'normal';
			table_advanced.style.display = 'none';
			table_advanced.id = 'plugin_params_advanced';
			form.appendChild( table_advanced );

			break;
		}
	}

	var potentiallyExtraPluginArgs = pluginArgs;

	var rowNumber = 0;
	var rowNumberAdvanced = 0;
	for( param in meta.params )
	{
		if( typeof(meta.params[param]) != 'object' || meta.params[param].name == 'array' ) {
			continue;
		}

		var row;
		if (meta.params[param].advanced && !meta.params[param].required && typeof pluginArgs[param] === "undefined") {
			row = table_advanced.insertRow( rowNumberAdvanced++ );
		} else {
			row = table.insertRow( rowNumber++ );
		}

		build_plugin_form_row(row, param, meta.params[param].name, meta.params[param].required, pluginArgs[param], meta.params[param].description, meta.params[param]);

		delete potentiallyExtraPluginArgs[param];
	}

	for( extraArg in potentiallyExtraPluginArgs) {
		if (extraArg === '') {
			// TODO HACK: See bug 2499 http://dev.tiki.org/tiki-view_tracker_item.php?itemId=2499
			continue;
		}

		row = table.insertRow( rowNumber++ );
		build_plugin_form_row(row, extraArg, extraArg, 'extra', pluginArgs[extraArg], extraArg);
	}

	var bodyRow = table.insertRow(rowNumber++);
	var bodyCell = bodyRow.insertCell(0);
	var bodyField = document.createElement( 'textarea' );
	bodyField.cols = '70';
	bodyField.rows = '12';
	var bodyDesc = document.createElement( 'div' );

	if( meta.body ) {
		bodyDesc.innerHTML = meta.body;
	} else {
		bodyRow.style.display = 'none';
	}
	bodyField.name = 'content';
	bodyField.value = bodyContent;

	bodyRow.className = 'formcolor';

	bodyCell.appendChild( bodyDesc );
	bodyCell.appendChild( bodyField );
	bodyCell.colSpan = '2';

	var submitRow = table.insertRow(rowNumber++);
	var submitCell = submitRow.insertCell(0);
	var submit = document.createElement( 'input' );

	submit.type = 'submit';
	submitCell.colSpan = 2;
	submitCell.appendChild( submit );
	submitCell.className = 'submit';

	return form;
}


function build_plugin_form_row(row, name, label_name, requiredOrSpecial, value, description, paramDef)
{

	var label = row.insertCell( 0 );
	var field = row.insertCell( 1 );
	row.className = 'formcolor';
	row.id = 'param_' + name;

	label.innerHTML = label_name;
	label.style.width = '130px';
	switch ( requiredOrSpecial ) {
	case (true):  // required flag
		label.style.fontWeight = 'bold';
	break;
	case ('extra') :
		label.style.fontStyle = 'italic';
	}

	var input;
	if (paramDef && paramDef.options) {
		input = document.createElement('select');
		input.name = 'params[' + name + ']';
		for (var o = 0; o < paramDef.options.length; o++) {
			var opt = document.createElement('option');
			opt.value = paramDef.options[o].value;
			var opt_text = document.createTextNode(paramDef.options[o].text);
			opt.appendChild(opt_text);
			if (value && opt.value == value) {
				opt.selected = true;
			}
			input.appendChild(opt);
		}
	} else {
		input = document.createElement('input');
		input.type = 'text';
		input.name = 'params[' + name + ']';
		if (value) {
			input.value = value;
		}
	}

	field.appendChild( input );
	if (paramDef && paramDef.type == 'image') {
		icon = document.createElement( 'img' );
		icon.src = 'pics/icons/image.png';
		input.id = paramDef.area ? paramDef.area : 'fgal_picker';
		icon.onclick = function() {openFgalsWindowArea(paramDef.area ? paramDef.area :'fgal_picker');};
		field.appendChild( icon );
	} else if (paramDef && paramDef.type == 'fileId') {
		var help = document.createElement( 'span' );
		input.id = paramDef.area ? paramDef.area : 'fgal_picker';
		help.onclick = function() {openFgalsWindowArea(paramDef.area ? paramDef.area :'fgal_picker');};
		help.innerHTML = " <a href='#'>" + tr('Pick a file.') + "</a>";
		field.appendChild( help );
	}

	if (description) {
		var desc = document.createElement( 'div' );
		desc.style.fontSize = 'x-small';
		desc.innerHTML = description;
		field.appendChild( desc );
	}

	if (paramDef && paramDef.filter) {
		if (paramDef.filter == "pagename") {
			$(input).tiki("autocomplete", "pagename");
		} else if (paramDef.filter == "groupname") {
			$(input).tiki("autocomplete", "groupname", {multiple: true, multipleSeparator: "|"});
		} else if (paramDef.filter == "username") {
			$(input).tiki("autocomplete", "username", {multiple: true, multipleSeparator: "|"});
		} else if (paramDef.filter == "date") {
			$(input).tiki("datepicker");
		}
	}

}

function openFgalsWindowArea(area) {
	openFgalsWindow('tiki-list_file_gallery.php?filegals_manager='+area, true);	// reload
}


//Password strength
//Based from code by:
//Matthew R. Miller - 2007
//www.codeandcoffee.com
//originally released as "free software license"

/*
 * Password Strength Algorithm:
 * 
 * Password Length: 5 Points: Less than 4 characters 10 Points: 5 to 7
 * characters 25 Points: 8 or more
 * 
 * Letters: 0 Points: No letters 10 Points: Letters are all lower case 20
 * Points: Letters are upper case and lower case
 * 
 * Numbers: 0 Points: No numbers 10 Points: 1 number 20 Points: 3 or more
 * numbers
 * 
 * Characters: 0 Points: No characters 10 Points: 1 character 25 Points: More
 * than 1 character
 * 
 * Bonus: 2 Points: Letters and numbers 3 Points: Letters, numbers, and
 * characters 5 Points: Mixed case letters, numbers, and characters
 * 
 * Password Text Range: >= 90: Very Secure >= 80: Secure >= 70: Very Strong >=
 * 60: Strong >= 50: Average >= 25: Weak >= 0: Very Weak
 * 
 */


//Settings
// -- Toggle to true or false, if you want to change what is checked in the password
var m_strUpperCase = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
var m_strLowerCase = "abcdefghijklmnopqrstuvwxyz";
var m_strNumber = "0123456789";
var m_strCharacters = "!@#$%^&*?_~";

//Check password
function checkPassword(strPassword)
{
	// Reset combination count
	var nScore = 0;

	// Password length
	// -- Less than 4 characters
	if (strPassword.length < 5)
	{
		nScore += 5;
	}
	// -- 5 to 7 characters
	else if (strPassword.length > 4 && strPassword.length < 8)
	{
		nScore += 10;
	}
	// -- 8 or more
	else if (strPassword.length > 7)
	{
		nScore += 25;
	}

	// Letters
	var nUpperCount = countContain(strPassword, m_strUpperCase);
	var nLowerCount = countContain(strPassword, m_strLowerCase);
	var nLowerUpperCount = nUpperCount + nLowerCount;
	// -- Letters are all lower case
	if (nUpperCount === 0 && nLowerCount !== 0)
	{
		nScore += 10;
	}
	// -- Letters are upper case and lower case
	else if (nUpperCount !== 0 && nLowerCount !== 0)
	{
		nScore += 20;
	}

	// Numbers
	var nNumberCount = countContain(strPassword, m_strNumber);
	// -- 1 number
	if (nNumberCount == 1)
	{
		nScore += 10;
	}
	// -- 3 or more numbers
	if (nNumberCount >= 3)
	{
		nScore += 20;
	}

	// Characters
	var nCharacterCount = countContain(strPassword, m_strCharacters);
	// -- 1 character
	if (nCharacterCount == 1)
	{
		nScore += 10;
	}
	// -- More than 1 character
	if (nCharacterCount > 1)
	{
		nScore += 25;
	}

	// Bonus
	// -- Letters and numbers
	if (nNumberCount !== 0 && nLowerUpperCount !== 0)
	{
		nScore += 2;
	}
	// -- Letters, numbers, and characters
	if (nNumberCount !== 0 && nLowerUpperCount !== 0 && nCharacterCount !== 0)
	{
		nScore += 3;
	}
	// -- Mixed case letters, numbers, and characters
	if (nNumberCount !== 0 && nUpperCount !== 0 && nLowerCount !== 0 && nCharacterCount !== 0)
	{
		nScore += 5;
	}


	return nScore;
}

//Runs password through check and then updates GUI
function runPassword(strPassword, strFieldID)
{
	// Check password
	var nScore = checkPassword(strPassword);

	// Get controls
	var ctlBar = document.getElementById(strFieldID + "_bar");
	var ctlText = document.getElementById(strFieldID + "_text");
	if (!ctlBar || !ctlText) {
		return;
	}
	// Set new width
	ctlBar.style.width = nScore + "%";

	// Color and text
	// -- Very Secure
	if (nScore >= 90)
	{
		var strIcon = "<img src='pics/icons/accept.png' style='vertical-align:middle' alt='Very Secure' />";
		var strText = tr("Very Secure");
		var strColor = "#0ca908";
	}
	// -- Secure
	else if (nScore >= 80)
	{
		strIcon = "<img src='pics/icons/accept.png' style='vertical-align:middle' alt='Secure' />";
		strText = tr("Secure");
		vstrColor = "#0ca908";
	}
	// -- Very Strong
	else if (nScore >= 70)
	{
		strIcon = "<img src='pics/icons/accept.png' style='vertical-align:middle' alt='Very Strong' />";
		strText = tr("Very Strong");
		strColor = "#0ca908";
	}
	// -- Strong
	else if (nScore >= 60)
	{
		strIcon = "<img src='pics/icons/accept.png' style='vertical-align:middle' alt='Strong' />";
		strText = tr("Strong");
		strColor = "#0ca908";
	}
	// -- Average
	else if (nScore >= 40)
	{
		strIcon = " ";
		strText = tr("Average");
		strColor = "#e3cb00";
	}
	// -- Weak
	else if (nScore >= 25)
	{
		strIcon = "<img src='pics/icons/exclamation.png' style='vertical-align:middle' alt='Weak' />";
		strText = tr("Weak");
		strColor = "#ff0000";
	}
	// -- Very Weak
	else
	{
		strIcon = "<img src='pics/icons/exclamation.png' style='vertical-align:middle' alt='Very weak' />";
		strText = tr("Very Weak");
		strColor = "#ff0000";
	}
	ctlBar.style.backgroundColor = strColor;
	ctlText.innerHTML = "<span>"  + strIcon + " " + tr("Strength") + ": " + strText + "</span>";
}

//Checks a string for a list of characters
function countContain(strPassword, strCheck)
{
	// Declare variables
	var nCount = 0;

	for (i = 0; i < strPassword.length; i++)
	{
		if (strCheck.indexOf(strPassword.charAt(i)) > -1)
		{
			nCount++;
		}
	}

	return nCount;
}

function checkPasswordsMatch(in1, in2, el) {
	if ($(in1).val().length && $(in1).val() == $(in2).val()) {
		$(el).html("<img src='pics/icons/accept.png' style='vertical-align:middle' alt='Secure' /><em>" + tr("Passwords match") + "</em>");
		return true;
	} else {
		$(el).html("");
		return false;
	}
}

/**
 * Adds an Option to the quickpoll section.
 */
function pollsAddOption()
{
	var newOption = $( '<input />').attr('type', 'text').attr('name', 'options[]');
	$('#tikiPollsOptions').append($('<div></div>').append(newOption));
}

/**
 * toggles the quickpoll section
 */
function pollsToggleQuickOptions()
{
	$( '#tikiPollsQuickOptions' ).toggle();
}

/**
 * toggles div for droplist with Disabled option
 */

function hidedisabled(divid,value) {
	if(value=='disabled') {
		document.getElementById(divid).style.display = 'none';
	} else {
		document.getElementById(divid).style.display = 'block';
	}
}

/* for filegals */

function adjustThumbnails() {
	var i,j,h = 0;
	var t = document.getElementById("thumbnails").childNodes;
	for ( i = 0; i < t.length; i++ ) {
		if ( t[i].className == "thumbnailcontener" ) {
			var t2 = t[i].childNodes;
			for ( j = 0; j < t2.length; j++ ) {
				if ( t2[j].className == "thumbnail" ) {
					t2[j].style.height = "100%";
					t2[j].style.overflow = "visible";
				}
			}
			if ( t[i].offsetHeight >= h ) {
				h = t[i].offsetHeight;
				t[i].style.height = h+"px";
			} else if ( t[i].offsetHeight < h ) {
				t[i].style.height = h+"px";
			}
		}
	}
	for ( i = 0; i < t.length; i++ ) {
		if ( t[i].className == "thumbnailcontener" ) {
			if ( t[i].offsetHeight <= h ) {
				t[i].style.height = h+"px";
			} else {
				break;
			}
		}
	}
}

function open_webdav(url) {
	// Works only in IE
	if (typeof ActiveXObject != 'undefined') {
		EditDocumentButton = new ActiveXObject("SharePoint.OpenDocuments.1");
		EditDocumentButton.EditDocument(url); 
	} else {
		prompt(tr('URL to open this file with WebDAV'), url);
	}
}

function ccsValueToInteger(str) {
	var v = str.replace(/[^\d]*$/, "");
	if (v) {
		v = parseInt(v, 10);
	}
	if (isNaN(v)) {
		return 0;
	} else {
		return v;
	}
}

// function to allow multiselection in checkboxes
// must be called like this :
//
// <input type="checkbox" onclick="checkbox_list_check_all(form_name,[checkbox_name_1,checkbox_name2 ...],true|false);">
function checkbox_list_check_all(form,list,checking) {
  for (var checkbox in list) {
    document.forms[form].elements[list[checkbox]].checked=checking;
  }
}

//An effective way of interacting with a codemirror editor
function addCodeMirrorEditorRelation(editor, $input, fullscreen, skipResize) {
	window.codeMirrorEditor = (window.codeMirrorEditor ? window.codeMirrorEditor : []);
	var i = window.codeMirrorEditor.push(editor);
	
	if (fullscreen) {
		$input
			.attr('codeMirrorRelationshipFullscreen', i - 1)
			.addClass('codeMirrorFullscreen');
	} else {
		if ($.fn.resizable && !skipResize) {
			var codeWrapper = $('div.CodeMirror-wrapping:last');
			var codeMirrorIframe = codeWrapper.find('iframe');
			
			codeWrapper
				.resizable({
					start: function() {
						codeMirrorIframe.hide();
					},
					stop: function() {
						codeMirrorIframe.show();
					},
					minWidth: codeWrapper.width(),
					minHeight: codeWrapper.height()
				});
		}
		
		$input
			.attr('codeMirrorRelationship', i - 1)
			.addClass('codeMirror');
	}
}

function removeCodeMirrorEditorRelation($input) {
	var relationshipFullscreen = parseInt($input.attr('codeMirrorRelationshipFullscreen'));
	var relationship = parseInt($input.attr('codeMirrorRelationship'));
	if (isNaN(relationshipFullscreen)) {
		window.codeMirrorEditor[relationship] = null;
		$input.removeAttr('codeMirrorRelationship');
	} else {
		window.codeMirrorEditor[relationshipFullscreen] = null;
		$input.removeAttr('codeMirrorRelationshipFullscreen');
	}
}

function getCodeMirrorFromInput($input) {
	var relationshipFullscreen = parseInt($input.attr('codeMirrorRelationshipFullscreen'));
	var relationship = parseInt($input.attr('codeMirrorRelationship'));
	
	relationship = (isNaN(relationshipFullscreen) ? relationship : relationshipFullscreen);
	
	if (window.codeMirrorEditor) {
		if (window.codeMirrorEditor[relationship]) {
			return window.codeMirrorEditor[relationship];
		}
	}
	return false;
}