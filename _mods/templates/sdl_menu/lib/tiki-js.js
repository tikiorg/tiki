// $Header: /cvsroot/tikiwiki/_mods/templates/sdl_menu/lib/tiki-js.js,v 1.1 2004-05-09 23:10:34 damosoft Exp $
// sdls revised tiki-js.js file for revisted menu modification
// TikiWiki 1.8 only

function getElementById(id) {
    if (document.all) {
	return document.getElementById(id);
    }
    for (i=0;i<document.forms.length;i++) {
	if (document.forms[i].elements[id]) {return document.forms[i].elements[id]; }
    }
}

function toggle_dynamic_var($name) {
	name1 = 'dyn_'+$name+'_display';
	name2 = 'dyn_'+$name+'_edit';
	if(document.getElementById(name1).style.display == "none") {
		document.getElementById(name2).style.display = "none";
		document.getElementById(name1).style.display = "inline";
	} else {
		document.getElementById(name1).style.display = "none";
		document.getElementById(name2).style.display = "inline";
	
	}
	
}

function chgArtType() {
	if (document.getElementById('articletype').value != 'Review') {
		document.getElementById('isreview').style.display = "none";
	} else {
		document.getElementById('isreview').style.display = "block";
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
	if (document.getElementById(id).style.display == "block") {
		document.getElementById(id).style.display = "none";
	} else {
		document.getElementById(id).style.display = "block";
	}
}

function chgTrkFld(f,o) {
	var opt = 0;
	document.getElementById('z').style.display = "none";
	for (var i = 0; i < f.length; i++) {
		if (document.getElementById(f[i])) { 
			if (f[i] == o) {
				document.getElementById(f[i]).style.display = "inline";
				document.getElementById('z').style.display = "inline";
			} else {
				document.getElementById(f[i]).style.display = "none";
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

function genPass(w1, w2, w3) {
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
	document.getElementById(w2).value = p;
	document.getElementById(w3).value = p;
}

function setUserModule(foo1) {
	document.getElementById('usermoduledata').value = foo1;
}

function setSomeElement(fooel, foo1) {
	document.getElementById(fooel).value = document.getElementById(fooel).value + foo1;
}

function replaceSome(fooel, what, repl) {
	document.getElementById(fooel).value = document.getElementById(fooel).value.replace(what, repl);
}

function replaceLimon(vec) {
	document.getElementById(vec[0]).value = document.getElementById(vec[0]).value.replace(vec[1], vec[2]);
}

function replaceImgSrc(imgName,replSrc) {
  document.getElementById(imgName).src = replSrc;
}

function setSelectionRange(textarea, selectionStart, selectionEnd) {
  if (textarea.setSelectionRange) {
    textarea.focus();
    textarea.setSelectionRange(selectionStart, selectionEnd);
  }
  else if (textarea.createTextRange) {
    var range = textarea.createTextRange();
    textarea.collapse(true);
    textarea.moveEnd('character', selectionEnd);
    textarea.moveStart('character', selectionStart);
    textarea.select();
  }
}
function setCaretToPos (textarea, pos) {
  setSelectionRange(textarea, pos, pos);
}
function insertAt(elementId, replaceString) {
  //inserts given text at selection or cursor position
  textarea = getElementById(elementId);
  var toBeReplaced = /text|page|area_name/;//substrings in replaceString to be replaced by the selection if a selection was done
  if (textarea.setSelectionRange) {
    //Mozilla UserAgent Gecko-1.4
    var selectionStart = textarea.selectionStart;
    var selectionEnd = textarea.selectionEnd;
    if (selectionStart != selectionEnd) { // has there been a selection
	var newString = replaceString.replace(toBeReplaced, textarea.value.substring(selectionStart, selectionEnd));
    	textarea.value = textarea.value.substring(0, selectionStart)
                  + newString
                  + textarea.value.substring(selectionEnd);
      setSelectionRange(textarea, selectionStart, selectionStart + newString.length);
    }
    else  {// set caret
       textarea.value = textarea.value.substring(0, selectionStart)
                  + replaceString
                  + textarea.value.substring(selectionEnd);
      setCaretToPos(textarea, selectionStart + replaceString.length);
    }
  }
  else if (document.selection) {
    //UserAgent IE-6.0
    textarea.focus();
    var range = document.selection.createRange();
    if (range.parentElement() == textarea) {
      var isCollapsed = range.text == '';
      if (! isCollapsed)  {
        range.text = replaceString.replace(toBeReplaced, range.text);
        range.moveStart('character', -range.text.length);
        range.select();
      }
	else {
		range.text = replaceString;
	}
    }
  }
  else { //UserAgent Gecko-1.0.1 (NN7.0)
    setSomeElement(elementId, replaceString)
    //alert("don't know yet how to handle insert" + document);
	}
}

function setUserModuleFromCombo(id) {
	document.getElementById('usermoduledata').value = document.getElementById('usermoduledata').value
		+ document.getElementById(id).options[document.getElementById(id).selectedIndex].value;
//document.getElementById('usermoduledata').value='das';
}

function show(foo,f) {
	document.getElementById(foo).style.display = "block";
	if (f) { setCookie(foo, "o"); }
}

function hide(foo,f) {
	document.getElementById(foo).style.display = "none";
	if (f) { setCookie(foo, "c"); }
}

function flip(foo) {
	if (document.getElementById(foo).style.display == "none") {
		show(foo);
	} else {
		if (document.getElementById(foo).style.display == "block") {
			hide(foo);
		} else {
			show(foo);
		}
	}
}

function toggle(foo) {
	if (document.getElementById(foo).style.display == "none") {
		show(foo);

		setCookie(foo, "o");
	} else {
		if (document.getElementById(foo).style.display == "block") {
			hide(foo,1);
		} else {
			show(foo,1);
		}
	}
}

function setfoldericonstate(foo) {
	pic = new Image();

	if (getCookie(foo) == "o") {
		pic.src = "img/icons/ofo.gif";
	} else {
		pic.src = "img/icons/fo.gif";
	}

	document.getElementsByName(foo + 'icn')[0].src = pic.src;
}

function setplusiconstate(foo) {
	pic = new Image();

	if (getCookie(foo) == "o") {
		pic.src = "img/icons/minus.gif";
	} else {
		pic.src = "img/icons/plus.gif";
	}

	document.getElementsByName(foo + 'icn')[0].src = pic.src;
}

/* foo: name of the menu
 * def: menu type (e:extended, c:collapsed, f:fixed)
 * the menu is collapsed function of its cookie: if no cookie is set, the def is used
 */
function setfolderstate(foo, def) {
	status = getCookie(foo);
	if (status == "o") {
		show(foo);
	} else if (status != "c" && def == 'e') {
		show(foo, "o");
	}
	else {
		hide(foo);
	}

	setfoldericonstate(foo);
}

function setplusstate(foo, def) {
	status = getCookie(foo);
	if (status == "o") {
		show(foo);
	} else if (status != "c" && def == 'e') {
		show(foo, "o");
	}
	else {
		hide(foo);
	}

	setplusiconstate(foo);
}

function pltoggle(foo) {
if (document.getElementById(foo).style.display == "none") {
		show(foo);

		setCookie(foo, "o");
	} else {
		hide(foo);

		setCookie(foo, "c");
	}
	setplusiconstate(foo);
	}
	
function icntoggle(foo) {
	if (document.getElementById(foo).style.display == "none") {
		show(foo);

		setCookie(foo, "o");
	} else {
		hide(foo);

		setCookie(foo, "c");
	}
	setfoldericonstate(foo);
}

//
// set folder icon state during page load
//
function setFolderIcons() {
	var elements = document.forms[the_form].elements[elements_name];

	var elements_cnt = ( typeof (elements.length) != 'undefined') ? elements.length : 0;

	if (elements_cnt) {
		for (var i = 0; i < elements_cnt; i++) {
			elements[i].checked = document.forms[the_form].elements[switcher_name].checked;
		}
	} else {
		elements.checked = document.forms[the_form].elements[switcher_name].checked;

		;
	} // end if... else

	return true;
}     // setFolderIcons()

//set plus icon state during load page 

function setPlusIcons() {
	var elements = document.forms[the_form].elements[elements_name];

	var elements_cnt = ( typeof (elements.length) != 'undefined') ? elements.length : 0;

	if (elements_cnt) {
		for (var i = 0; i < elements_cnt; i++) {
			elements[i].checked = document.forms[the_form].elements[switcher_name].checked;
		}
	} else {
		elements.checked = document.forms[the_form].elements[switcher_name].checked;

		;
	} // end if... else

	return true;
}     // setPlusIcons()

// name - name of the cookie
// value - value of the cookie
// [expires] - expiration date of the cookie (defaults to end of current session)
// [path] - path for which the cookie is valid (defaults to path of calling document)
// [domain] - domain for which the cookie is valid (defaults to domain of calling document)
// [secure] - Boolean value indicating if the cookie transmission requires a secure transmission
// * an argument defaults when it is assigned null as a placeholder
// * a null placeholder is not required for trailing omitted arguments
function setCookie(name, value, expires, path, domain, secure) {
	var curCookie = name + "=" + escape(value) + ((expires) ? "; expires=" + expires.toGMTString() : "")
		+ ((path) ? "; path=" + path : "") + ((domain) ? "; domain=" + domain : "") + ((secure) ? "; secure" : "");

	document.cookie = curCookie;
}

// name - name of the desired cookie
// * return string containing value of specified cookie or null if cookie does not exist
function getCookie(name) {
	var dc = document.cookie;

	var prefix = name + "=";
	var begin = dc.indexOf("; " + prefix);

	if (begin == -1) {
		begin = dc.indexOf(prefix);

		if (begin != 0)
			return null;
	} else begin += 2;

	var end = document.cookie.indexOf(";", begin);

	if (end == -1)
		end = dc.length;

	return unescape(dc.substring(begin + prefix.length, end));
}

// name - name of the cookie
// [path] - path of the cookie (must be same as path used to create cookie)
// [domain] - domain of the cookie (must be same as domain used to create cookie)
// * path and domain default if assigned null or omitted if no explicit argument proceeds
function deleteCookie(name, path, domain) {
	if (getCookie(name)) {
		document.cookie = name + "="
			+ ((path) ? "; path=" + path : "") + ((domain) ? "; domain=" + domain : "") + "; expires=Thu, 01-Jan-70 00:00:01 GMT";
	}
}

// date - any instance of the Date object
// * hand all instances of the Date object to this function for "repairs"
function fixDate(date) {
	var base = new Date(0);

	var skew = base.getTime();

	if (skew > 0)
		date.setTime(date.getTime() - skew);
}

//
// Expand/collapse lists
//
function flipWithSign(foo) {
	if (document.getElementById(foo).style.display == "none") {
		show(foo);

		collapseSign("flipper" + foo);
		setCookie(foo, "o");
	} else {
		hide(foo);

		expandSign("flipper" + foo);
		setCookie(foo, "c");
	}
}

// set the state of a flipped entry after page reload
function setFlipWithSign(foo) {
	if (getCookie(foo) == "o") {
		collapseSign("flipper" + foo);

		show(foo);
	} else {
		expandSign("flipper" + foo);

		hide(foo);
	}
}

function expandSign(foo) {
	document.getElementById(foo).firstChild.nodeValue = "[+]";
}

function collapseSign(foo) {
	document.getElementById(foo).firstChild.nodeValue = "[-]";
} // flipWithSign()

//
// Check / Uncheck all Checkboxes
//
function switchCheckboxes(the_form, elements_name, switcher_name) {
	// checkboxes need to have the same name elements_name
	// e.g. <input type="checkbox" name="my_ename[]">, will arrive as Array in php.
	var elements = document.forms[the_form].elements[elements_name];

	var elements_cnt = ( typeof (elements.length) != 'undefined') ? elements.length : 0;

	if (elements_cnt) {
		for (var i = 0; i < elements_cnt; i++) {
			elements[i].checked = document.forms[the_form].elements[switcher_name].checked;
		}
	} else {
		elements.checked = document.forms[the_form].elements[switcher_name].checked;

		;
	} // end if... else

	return true;
}     // switchCheckboxes()

//
// Set client offset (in minutes) to a cookie to avoid server-side DST issues
// Added 7/25/03 by Jeremy Jongsma (jjongsma@tickchat.com)
//
var expires = new Date();
var offset = -(expires.getTimezoneOffset() * 60);
expires.setFullYear(expires.getFullYear() + 1);
setCookie("tz_offset", offset, expires, "/");

// function added for use in navigation dropdown
// example :
// <select name="anything" onchange="go(this);">
// <option value="http://tikiwiki.org">tikiwiki.org</option>
// </select>
function go(o) {
	if (o.options[o.selectedIndex].value != "") {
		location = o.options[o.selectedIndex].value;

		o.options[o.selectedIndex] = 1;
	}

	return false;
}


// function:	targetBlank
// desc:	opens a new window, XHTML-compliant replacement of the "TARGET" tag
// added by: 	Ralf Lueders (lueders@lrconsult.com)
// date:	Sep 7, 2003
// params:	url: the url for the new window
//		mode='nw': new, full-featured browser window
//		mode='popup': new windows, no features & buttons

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

// function:	confirmTheLink
// desc:	pop up a dialog box to confirm the action
// added by: 	Franck Martin
// date:	Oct 12, 2003
// params:	theLink: The link where it is called from
// params: theMsg: The message to display
function confirmTheLink(theLink, theMsg)
{
    // Confirmation is not required if browser is Opera (crappy js implementation)
    if (typeof(window.opera) != 'undefined') {
        return true;
    }
                                                                                                                  
    var is_confirmed = confirm(theMsg);
    //if (is_confirmed) {
    //    theLink.href += '&amp;is_js_confirmed=1';
    //}
                                                                                                                  
    return is_confirmed;
} 

/** \brief: modif a textarea dimension
 * \elementId = textarea idea
 * \height = nb pixels to add to the height (the number can be negative)
 * \width = nb pixels to add to the width
 * \formid = form id (needs to have 2 input rows and cols
 **/
function textareasize(elementId, height, width, formId) {
	textarea = document.getElementById(elementId);
	form = document.getElementById(formId);
	if (textarea && height != 0 && textarea.rows + height > 5) {
		textarea.rows += height;
		if (form.rows)
			form.rows.value = textarea.rows;
	}
	if (textarea && width != 0 && textarea.cols + width > 10) {
		 textarea.cols += width;
		if (form.cols)
			form.cols.value = textarea.cols;
	}
}


/** \brief: insert img tag in textarea
 *	
 */	
function insertImg(elementId, fileId, oldfileId) {
    textarea = getElementById(elementId);
    fileup   = getElementById(fileId);    
    oldfile  = getElementById(oldfileId);    
    prefixEl = getElementById("prefix");    
    prefix   = "img/wiki_up/";

    if (!textarea || ! fileup) 
	return;

    if ( prefixEl) { prefix= prefixEl.value; }

    filename = fileup.value;
    oldfilename = oldfile.value;

    if (filename == oldfilename ||
	filename == "" ) { // insert only if name really changed
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
    //      replace with dyn. variable once in a while to respect the tikidomain 
    str = "{img src=\"img/wiki_up/" + filename + "\" }";
    insertAt(elementId, str);
}

