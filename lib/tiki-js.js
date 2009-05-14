// $Id$
var feature_no_cookie = 'n';

function browser() {
    var b = navigator.appName
    if (b=="Netscape") this.b = "ns"
    else this.b = b
    this.version = navigator.appVersion
    this.v = parseInt(this.version)
    this.ns = (this.b=="ns" && this.v>=5)
    this.op = (navigator.userAgent.indexOf('Opera')>-1)
    this.safari = (navigator.userAgent.indexOf('Safari')>-1)
    this.op7 = (navigator.userAgent.indexOf('Opera')>-1 && this.v>=7)
    this.ie56 = (this.version.indexOf('MSIE 5')>-1||this.version.indexOf('MSIE 6')>-1)
/* ie567 added by Enmore */
	this.ie567 = (this.version.indexOf('MSIE 5')>-1||this.version.indexOf('MSIE 6')>-1||this.version.indexOf('MSIE 7')>-1)
    this.iewin = (this.ie56 && navigator.userAgent.indexOf('Windows')>-1)
/* iewin7 added by Enmore */	
	this.iewin7 = (this.ie567 && navigator.userAgent.indexOf('Windows')>-1)
    this.iemac = (this.ie56 && navigator.userAgent.indexOf('Mac')>-1)
    this.moz = (navigator.userAgent.indexOf('Mozilla')>-1)
    this.moz13 = (navigator.userAgent.indexOf('Mozilla')>-1 && navigator.userAgent.indexOf('1.3')>-1)
    this.oldmoz = (navigator.userAgent.indexOf('Mozilla')>-1 && navigator.userAgent.indexOf('1.4')>-1 || navigator.userAgent.indexOf('Mozilla')>-1 && navigator.userAgent.indexOf('1.5')>-1 || navigator.userAgent.indexOf('Mozilla')>-1 && navigator.userAgent.indexOf('1.6')>-1)
    this.ns6 = (navigator.userAgent.indexOf('Netscape6')>-1)
    this.docom = (this.ie56||this.ns||this.iewin||this.op||this.iemac||this.safari||this.moz||this.oldmoz||this.ns6)
}

function getElementById(id) {
    if (document.all) {
  return document.getElementById(id);
    }
    for (i=0;i<document.forms.length;i++) {
  if (document.forms[i].elements[id]) {return document.forms[i].elements[id]; }
    }
}

/* toggle CSS (tableless) layout columns */
function toggleCols(id,zeromargin,maincol) {
  var showit = 'show_' + escape(id);
  if (!zeromargin) var zeromargin = '';
  if (!id) var id = '';
  if (!maincol) var maincol = 'col1';
  if (document.getElementById(id).style.display == "none") {
    document.getElementById(id).style.display = "block";
    if (zeromargin == 'left') {
      document.getElementById(maincol).style.marginLeft = '';
      setSessionVar(showit,'y');
    } else {
      document.getElementById(maincol).style.marginRight = '';
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
        articleType = document.getElementById('articletype').value;
        typeProperties = articleTypes[articleType];

  propertyList = new Array('show_topline','y',
         'show_subtitle','y',
         'show_linkto','y',
         'show_lang','y',
         'show_author','y',
         'use_ratings','y',
         'heading_only','n',
         'show_image_caption','y',
         'show_pre_publ','y',
         'show_post_expire','y',
         'show_image','y'
         );

  var l = propertyList.length;
  for (var i=0; i<l; i++) {
      property = propertyList[i++];
      value = propertyList[i];

      if (typeProperties[property] == value) {
    display = "";
      } else {
    display = "none";
      }

      if (document.getElementById(property)) {
    document.getElementById(property).style.display = display;
      } else {
    j = 1;
    while (document.getElementById(property+'_'+j)) {
        document.getElementById(property+'_'+j).style.display=display;
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

    var linkHolder = document.getElementById('toctitle')
    if (!linkHolder) return;

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
    if (getCookie("hidetoc") == "1" ) toggleToc();
  }
}

function changeText(el, newText) {
  // Safari work around
  if (el.innerText)
    el.innerText = newText;
  else if (el.firstChild && el.firstChild.nodeValue)
    el.firstChild.nodeValue = newText;
}

function toggleToc() {
  var toc = document.getElementById('toc').getElementsByTagName('ul')[0];
  var toggleLink = document.getElementById('togglelink')

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
  document.getElementById('zStaticTextQuicktags').style.display = "none";

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
          document.getElementById('zStaticTextQuicktags').style.display = "";
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
  var toBeReplaced = /text|page|area_name/g; //substrings in replaceString to be replaced by the selection if a selection was done
  if (textarea.setSelectionRange) {
    //Mozilla UserAgent Gecko-1.4
    var selectionStart = textarea.selectionStart;
    var selectionEnd = textarea.selectionEnd;
    var scrollTop=textarea.scrollTop;
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
    textarea.scrollTop=scrollTop;
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

function setUserModuleFromCombo(id, textarea) {
  document.getElementById(textarea).value = document.getElementById(textarea).value
    + document.getElementById(id).options[document.getElementById(id).selectedIndex].value;
//document.getElementById('usermoduledata').value='das';
}


function show(foo,f,section) {
  document.getElementById(foo).style.display = "block";
  if (f) { setCookie(foo, "o", section); }
}

function hide(foo,f, section) {
  if (document.getElementById(foo)) {
    document.getElementById(foo).style.display = "none";
    if (f) {
      var wasnot = getCookie(foo, section, 'x') == 'x';
      setCookie(foo, "c", section);
      if (wasnot) {
        history.go(0);
      }
    }
  }
}

function flip_multi(foo,style) {
  showit = 'show_' + escape(foo);

  if (style == null) style = 'block';
  if (this.iewin && style == 'table-cell') {
    style = 'block';
  }

  //FIXME
  elements = document.getElementsByName(foo);
  for (i=0 ; i < elements.length; i++) {
    if (elements[i].style.display == "none") {
      elements[i].style.display = style;
      setSessionVar(showit,'y');
    } else {
      if (elements[i].style.display == style) {
        elements[i].style.display = "none";
        setSessionVar(showit, 'n');
      } else {
        elements[i].style.display = style;
        setSessionVar(showit, 'y');
      }
    }
  }
}

function flip(foo,style) {
  showit = 'show_' + escape(foo);

  if (style == null) style = 'block';
/* iewin changed to iewin7 by Enmore */	
	if (this.iewin7 && style == 'table-cell') {
    style = 'block';
  }

  if (document.getElementById(foo).style.display == "none") {
    document.getElementById(foo).style.display = style;
    setSessionVar(showit,'y');
  } else {
    if (document.getElementById(foo).style.display == style) {
      document.getElementById(foo).style.display = "none";
      setSessionVar(showit, 'n');
    } else {
      document.getElementById(foo).style.display = style;
      setSessionVar(showit, 'y');
    }
  }
}

function toggle(foo) {
  if (document.getElementById(foo).style.display == "none") {
    show(foo, true, "menu");
  } else {
    if (document.getElementById(foo).style.display == "block") {
      hide(foo, true, "menu");
    } else {
      show(foo, true, "menu");
    }
  }
}

function setopacity(obj,opac){
   if (document.all && !is.op){ //ie
       obj.filters.alpha.opacity = opac * 100;
   }else{
       obj.style.MozOpacity = opac;
       obj.style.opacity = opac;
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

function tikitabs(focus,max) {
  var didit = false, didone = false;
  for (var i = 1; i <= max; i++) {
    var tabname = 'tab' + i;
    var content = 'content' + i;
    if (document.getElementById(tabname) && typeof document.getElementById(tabname) != 'undefined') {
      if (i == focus) {
        //show(tabname);
        show(content);
        setCookie('tab',focus);
        document.getElementById(tabname).className = 'tabmark';
        document.getElementById(tabname).className += ' tabactive';
        didit = true;
      } else {
        //hide(tabname);
        hide(content);
        document.getElementById(tabname).className = 'tabmark';
        document.getElementById(tabname).className += ' tabinactive';
      }
      if (!didone) { didone = true; }
    }
  }
  if (didone && !didit) {
	  show('content1');
	  setCookie('tab',1);
	  document.getElementById('tab1').className = 'tabmark';
	  document.getElementById('tab1').className += ' tabactive';
  }
}

function setfoldericonstate(foo) {
  if (getCookie(foo, "menu", "o") == "o") {
    src = "ofolder.png";
  } else {
    src = "folder.png";
  }
  document.getElementsByName('icn' + foo)[0].src = document.getElementsByName('icn' + foo)[0].src.replace(/[^\\\/]*$/, src);
}
/* foo: name of the menu
 * def: menu type (e:extended, c:collapsed, f:fixed)
 * the menu is collapsed function of its cookie: if no cookie is set, the def is used
 */
function setfolderstate(foo, def, img) {
  var status = getCookie(foo, "menu", "o");
    if (!img) {
    if (document.getElementsByName('icn' + foo)[0].src.search(/[\\\/]/))
      img = document.getElementsByName('icn' + foo)[0].src.replace(/.*[\\\/]([^\\\/]*)$/, "$1");
    else
      img = 'folder.png';
  }
    var src = img; // default
  if (status == "o") {
    show(foo);
    src = "o" + img;
  } else if (status != "c"  && def != 'd') {
    show(foo);
    src = "o" + img;
  }
  else {
    hide(foo);
  }
  document.getElementsByName('icn' + foo)[0].src = document.getElementsByName('icn' + foo)[0].src.replace(/[^\\\/]*$/, src);
}

function setheadingstate(foo) {
  var status = getCookie(foo, "showhide_headings");
  if (status == "o") {
    show(foo);
    collapseSign("flipper" + foo);
  } else /*if (status == "c")*/ {
    if (!document.getElementById(foo).style.display == "none") {
      hide(foo);
      expandSign("flipper" + foo);
    }
  }
}

function setsectionstate(foo, def, img) {
  var status = getCookie(foo, "menu", "o");
  if (status == "o") {
    show(foo);
    if (img) src = "o" + img;
  } else if (status != "c" && def != 'd') {
    show(foo);
    if (img) src = "o" + img;
  } else /*if (status == "c")*/ {
    hide(foo);
    if (img) src = img;
  }
  if (img && document.getElementsByName('icn' + foo).length)
  	document.getElementsByName('icn' + foo)[0].src = document.getElementsByName('icn' + foo)[0].src.replace(/[^\\\/]*$/, src);
}

function icntoggle(foo, img) {
    if (!img) {
    if (document.getElementsByName('icn' + foo)[0].src.search(/[\\\/]/))
      img = document.getElementsByName('icn' + foo)[0].src.replace(/.*[\\\/]([^\\\/]*)$/, "$1");
    else
      img = 'folder.png';
  }
  if (document.getElementById(foo).style.display == "none") {
    show(foo, true, "menu");
    document.getElementsByName('icn' + foo)[0].src = document.getElementsByName('icn' + foo)[0].src.replace(/[^\\\/]*$/, 'o' + img);

  } else {
    hide(foo, true, "menu");
    img = img.replace(/(^|\/|\\)o(.*)$/, '$1$2');
    document.getElementsByName('icn' + foo)[0].src = document.getElementsByName('icn' + foo)[0].src.replace(/[^\\\/]*$/, img);
  }
}

// Initialize a cross-browser XMLHttpRequest object.
// The object return has to be sent using send(). More parameters can be
// given.
// callback - The function that will be called when the response arrives
//		First parameter will be the status
//		(HTTP Response Code [200,403, 404, ...])
// method - GET or POST
// url - The URL to open
function getHttpRequest( method, url, async )
{
  if( async == null )
    async = false;

  var request;

  if( window.XMLHttpRequest )
    request = new XMLHttpRequest();
  else if( window.ActiveXObject )
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
  else
    return false;

  if( !request )
    return false;

  request.open( method, url, async );

  return request;
}

// name - name of the cookie
// value - value of the cookie
// [expires] - expiration date of the cookie (defaults to end of current session)
// [path] - path for which the cookie is valid (defaults to path of calling document)
// [domain] - domain for which the cookie is valid (defaults to domain of calling document)
// [secure] - Boolean value indicating if the cookie transmission requires a secure transmission
// * an argument defaults when it is assigned null as a placeholder
// * a null placeholder is not required for trailing omitted arguments
function setSessionVar(name,value) {
  var request = getHttpRequest( "GET", "tiki-cookie-jar.php?" + name + "=" + escape(value));
  request.send('');
  tiki_cookie_jar[name] = value;
}

function setCookie(name, value, section, expires, path, domain, secure) {
    if (!expires) {
        expires = new Date();
        expires.setFullYear(expires.getFullYear() + 1);
    }
  if (feature_no_cookie == 'y') {
    var request = getHttpRequest( "GET", "tiki-cookie-jar.php?" + name + "=" + escape( value ) )
    try {
      request.send('');
      //alert("XMLHTTP/set"+request.readyState+request.responseText);
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
      if (new RegExp(name2).test(valSection))
        valSection  = valSection.replace(new RegExp(name2 + "[^@;]*"), name2 + value);
      else
        valSection = valSection + name2 + value;
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

// name - name of the desired cookie
// section - name of group of cookies or null
// * return string containing value of specified cookie or null if cookie does not exist
function getCookie(name, section, defval) {
  if( feature_no_cookie == 'y' && (window.XMLHttpRequest || window.ActiveXObject) && typeof tiki_cookie_jar != "undefined" && tiki_cookie_jar.length > 0) {
    if (typeof tiki_cookie_jar[name] == "undefined")
      return defval;
    return tiki_cookie_jar[name];
  }
  else {
    return getCookieBrowser(name, section, defval);
  }
}
function getCookieBrowser(name, section, defval) {
  if (section) {
    var valSection = getCookieBrowser(section);
    if (valSection) {
      var name2 = "@"+name+":";
      var val = valSection.match(new RegExp(name2 + "([^@;]*)"));
      if (val)
        return unescape(val[1]);
      else
        return null;
    } else {
      return defval;
    }
  } else {
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
}

// name - name of the cookie
// [path] - path of the cookie (must be same as path used to create cookie)
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

//	if( !setCookie( name, '', 0, path, domain ) ) {
//		if (getCookie(name)) {
      document.cookie = name + "="
        + ((path) ? "; path=" + path : "") + ((domain) ? "; domain=" + domain : "") + "; expires=Thu, 01-Jan-70 00:00:01 GMT";
//		}
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
    show(foo, true, "showhide_headings");
    collapseSign("flipper" + foo);
  } else {
    hide(foo, true, "showhide_headings");
    expandSign("flipper" + foo);
  }
}

// set the state of a flipped entry after page reload
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
  document.getElementById(foo).firstChild.nodeValue = "[+]";
}

function collapseSign(foo) {
  document.getElementById(foo).firstChild.nodeValue = "[-]";
} // flipWithSign()

//
// Check / Uncheck all Checkboxes
//
function switchCheckboxes(tform, elements_name, state) {
  // checkboxes need to have the same name elements_name
  // e.g. <input type="checkbox" name="my_ename[]">, will arrive as Array in php.
  for (var i = 0; i < tform.length; i++) {
    if (tform.elements[i].name == elements_name) {
      tform.elements[i].checked = state
    }
  }
  return true;
}

//
// Set client timezone
// Added 7/25/03 by Jeremy Jongsma (jjongsma@tickchat.com)
// Updated 11/04/07 by Nyloth to get timezone name instead of timezone offset
//

var expires = new Date();
var local_date = expires.toLocaleString();
var local_tz = local_date.substring(local_date.lastIndexOf(' ') + 1);
expires.setFullYear(expires.getFullYear() + 1);
setCookie("local_tz", local_tz, null, expires, "/");

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
  form1 = document.getElementById(formId);
  if (textarea && height != 0 && textarea.rows + height > 5) {
    textarea.rows += height;
    if (form1.rows)
      form1.rows.value = textarea.rows;
  }
  if (textarea && width != 0 && textarea.cols + width > 10) {
     textarea.cols += width;
    if (form1.cols)
      form1.cols.value = textarea.cols;
  }
}


/** \brief: insert img tag in textarea
 *
 */
function insertImgFile(elementId, fileId, oldfileId,type,page,attach_comment) {
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
    if (type == "file") {
        str = "{file name=\""+filename + "\"";
        if (desc = getElementById(attach_comment).value)
             str = str + " desc=\""+ desc + "\"";
        str = str + "}";
    }
    else
        str = "{img src=\"img/wiki_up/" + filename + "\" }\n";
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
    document.write('<a class="wiki" href="mailto:'+nom+'@'+domain+'">'+nom+sep+domain+'</a>');
}

browser();

// This was added to allow wiki3d to change url on tiki's window
window.name = 'tiki';

/* Function to add image from filegals in non wysiwyg editor */
/* must be here when ajax is activated                       */
function SetMyUrl(area,url) {
	var myurl = url.replace(/.*\/([^\/]*)$/, '$1'); /* make relative path from the absolute url */
  str = "{img src=\""+myurl.replace(/display$/, 'thumbnail')+"\" alt=\"\" link=\""+myurl+"\" rel=\"shadowbox[g];type=img\"} ";
  insertAt(area, str);
}
/* Count the number of words (spearated with space) */
function wordCount(maxSize, source, cpt, message) {
  var formcontent = source.value;
  str = formcontent.replace(/^\s+|\s+$/g, '') ;
  formcontent = str.split(/[^\S]+/);
  document.getElementById(cpt).value = formcontent.length;
  if (maxSize > 0 && formcontent.length > maxSize) {
        alert(message);
  }
}

function show_plugin_form( type, index, pageName, pluginArgs, bodyContent )
{
  var target = document.getElementById( type + index );
  var content = target.innerHTML;

  var form = build_plugin_form( type, index, pageName, pluginArgs, bodyContent );

  target.innerHTML = '';
  target.appendChild( form );
}

/* wikiplugin editor */
function popup_plugin_form( area_name, type, index, pageName, pluginArgs, bodyContent, edit_icon )
{
  var container = document.createElement( 'div' );
  container.className = 'plugin-form-float';

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
  if (!bodyContent) { bodyContent = ''; }

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

      if (matches == null) {
	// it's not a parameter, skip 
        continue;
      }
      var param = matches[1];

      var val = form.elements[i].value;

      if( val != '' )
        params.push( param + '="' + val + '"' ); 
    }

    var blob = '{' + type.toUpperCase() + '(' + params.join(',') + ')}' + (typeof form.content != 'undefined' ? form.content.value : '') + '{' + type.toUpperCase() + '}';

    if (edit) {
      return true;
    } else {
      insertAt( area_name, blob );
      document.body.removeChild( container );
    }
    return false;
  }

  minimize.onclick = function() {
    var edit = edit_icon;
    if (edit)
      edit.style.display = 'inline';
    document.body.removeChild( container );
  };

  document.body.appendChild( container );
  if (edit_icon)
    edit_icon.style.display = 'none';
  container.appendChild( form );
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
  form.appendChild( desc );

  var table = document.createElement( 'table' );
  table.className = 'normal';
  form.appendChild( table );

  var potentiallyExtraPluginArgs = pluginArgs;

  var rowNumber = 0;
  for( param in meta.params )
  {
    if( typeof(meta.params[param]) != 'object' || meta.params[param].name == 'array' )
      continue;

    var row = table.insertRow( rowNumber++ );
    build_plugin_form_row(row, param, meta.params[param].name, meta.params[param].required, pluginArgs[param], meta.params[param].description)	

    delete potentiallyExtraPluginArgs[param];
  }

  for( extraArg in potentiallyExtraPluginArgs) {
	if (extraArg == '') {
	   // TODO HACK: See bug 2499 http://dev.tikiwiki.org/tiki-view_tracker_item.php?itemId=2499
	   continue;
        }

        var row = table.insertRow( rowNumber++ );
	build_plugin_form_row(row, extraArg, extraArg, 'extra', pluginArgs[extraArg], extraArg)	
  }

  var bodyRow = table.insertRow(rowNumber++);
  var bodyCell = bodyRow.insertCell(0);
  var bodyField = document.createElement( 'textarea' );
	bodyField.cols = '70'
	bodyField.rows = '12';
  var bodyDesc = document.createElement( 'div' );

  if( meta.body )
    bodyDesc.innerHTML = meta.body;
  else
    bodyRow.style.display = 'none';

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


function build_plugin_form_row(row, name, label_name, requiredOrSpecial, value, description)
{

    var label = row.insertCell( 0 );
    var field = row.insertCell( 1 );
    row.className = 'formcolor';

    label.innerHTML = label_name;
    switch ( requiredOrSpecial ) {
	case (true):  // required flag
	      label.style.fontWeight = 'bold';
	case ('extra') :
	      label.style.fontStyle = 'italic';
    }

    var input = document.createElement( 'input' );
    input.type = 'text';
    input.name = 'params['+name+']'; 
    if( value )
      input.value = value;

    var desc = document.createElement( 'div' );
    desc.style.fontSize = 'x-small';
    desc.innerHTML = description; 

    field.appendChild( input );
    field.appendChild( desc );

}

// Password strength
// Based from code by:
// Matthew R. Miller - 2007
// www.codeandcoffee.com
// originally released as "free software license"

/*
  Password Strength Algorithm:

  Password Length:
    5 Points: Less than 4 characters
    10 Points: 5 to 7 characters
    25 Points: 8 or more

  Letters:
    0 Points: No letters
    10 Points: Letters are all lower case
    20 Points: Letters are upper case and lower case

  Numbers:
    0 Points: No numbers
    10 Points: 1 number
    20 Points: 3 or more numbers

  Characters:
    0 Points: No characters
    10 Points: 1 character
    25 Points: More than 1 character

  Bonus:
    2 Points: Letters and numbers
    3 Points: Letters, numbers, and characters
    5 Points: Mixed case letters, numbers, and characters

  Password Text Range:

    >= 90: Very Secure
    >= 80: Secure
    >= 70: Very Strong
    >= 60: Strong
    >= 50: Average
    >= 25: Weak
    >= 0: Very Weak

*/


// Settings
// -- Toggle to true or false, if you want to change what is checked in the password
var m_strUpperCase = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
var m_strLowerCase = "abcdefghijklmnopqrstuvwxyz";
var m_strNumber = "0123456789";
var m_strCharacters = "!@#$%^&*?_~"

// Check password
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
  if (nUpperCount == 0 && nLowerCount != 0)
  {
    nScore += 10;
  }
  // -- Letters are upper case and lower case
  else if (nUpperCount != 0 && nLowerCount != 0)
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
  if (nNumberCount != 0 && nLowerUpperCount != 0)
  {
    nScore += 2;
  }
  // -- Letters, numbers, and characters
  if (nNumberCount != 0 && nLowerUpperCount != 0 && nCharacterCount != 0)
  {
    nScore += 3;
  }
  // -- Mixed case letters, numbers, and characters
  if (nNumberCount != 0 && nUpperCount != 0 && nLowerCount != 0 && nCharacterCount != 0)
  {
    nScore += 5;
  }


  return nScore;
}

// Runs password through check and then updates GUI
function runPassword(strPassword, strFieldID)
{
  // Check password
  var nScore = checkPassword(strPassword);

   // Get controls
      var ctlBar = document.getElementById(strFieldID + "_bar");
      var ctlText = document.getElementById(strFieldID + "_text");
      if (!ctlBar || !ctlText)
        return;

      // Set new width
      ctlBar.style.width = nScore + "%";

   // Color and text
  // -- Very Secure
   if (nScore >= 90)
   {
    var strIcon = "<img src='pics/icons/accept.png' style='vertical-align:middle' alt='Very Secure' />";
    var strText = "Very Secure";
     var strColor = "#0ca908";
   }
   // -- Secure
   else if (nScore >= 80)
   {
    var strIcon = "<img src='pics/icons/accept.png' style='vertical-align:middle' alt='Secure' />";
    var strText = "Secure";
     vstrColor = "#0ca908";
  }
  // -- Very Strong
   else if (nScore >= 70)
   {
    var strIcon = "<img src='pics/icons/accept.png' style='vertical-align:middle' alt='Very Strong' />";
     var strText = "Very Strong";
     var strColor = "#0ca908";
  }
  // -- Strong
   else if (nScore >= 60)
   {
     var strIcon = "<img src='pics/icons/accept.png' style='vertical-align:middle' alt='Strong' />";
    var strText = "Strong";
     var strColor = "#0ca908";
  }
  // -- Average
   else if (nScore >= 40)
   {
     var strIcon = " ";
    var strText = "Average";
     var strColor = "#e3cb00";
  }
  // -- Weak
   else if (nScore >= 25)
   {
     var strIcon = "<img src='pics/icons/exclamation.png' style='vertical-align:middle' alt='Weak' />";
     var strText = "Weak";
     var strColor = "#ff0000";
  }
  // -- Very Weak
   else
   {
     var strIcon = "<img src='pics/icons/exclamation.png' style='vertical-align:middle' alt='Very weak' />";
    var strText = "Very Weak";
     var strColor = "#ff0000";
  }
  ctlBar.style.backgroundColor = strColor;
  ctlText.innerHTML = "<span>"  + strIcon + " Strength: " + strText + "</span>";
}

// Checks a string for a list of characters
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

/**
 * Adds an Option to the quickpoll section.
 */
function pollsAddOption()
{
  var newOptionDiv = new Element( 'div' );
  var newOption = new Element( 'input', { type: "text", name: "options[]" } );
  newOption.inject( newOptionDiv );
  newOptionDiv.inject( $( 'tikiPollsOptions' ) );
}

/**
 * toggles the quickpoll section
 */
function pollsToggleQuickOptions()
{
	var display = $( 'tikiPollsQuickOptions' ).getStyle( 'display' );
	if( display == 'none' ) $( 'tikiPollsQuickOptions' ).setStyle( 'display', 'block' );
	else $( 'tikiPollsQuickOptions' ).setStyle( 'display', 'none' );
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

