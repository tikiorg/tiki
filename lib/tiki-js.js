// $Header: /cvsroot/tikiwiki/tiki/lib/tiki-js.js,v 1.2 2003-01-01 18:31:17 lrargerich Exp $

function chgArtType() {
  if(document.getElementById('articletype').value=='Article') {
    document.getElementById('isreview').style.display="none";
  } else {
    document.getElementById('isreview').style.display="block";
  }
}

function setMenuCon(foo1,foo2) {
  document.getElementById('menu_url').value=foo1;
  document.getElementById('menu_name').value=foo2;
}

function genPass(w1,w2,w3){
  vo="aeiou";
  co="bcdfgjklmnprstvwxz";
  s=Math.round(Math.random());
  l=Math.round(Math.random()*3)+4;
  p='';
  for(i=0; i < l; i++){
    if (s){
      letra=vo.charAt(Math.round( 
      Math.random() * (vo.length - 1) ));
      s=0;
    }else{
      letra=co.charAt(Math.round( 
      Math.random() * (co.length - 1) ));
      s=1;
    }
    p=p + letra;
  }
  document.getElementById(w1).value=p;
  document.getElementById(w2).value=p;
  document.getElementById(w3).value=p;
}

function setUserModule(foo1) {
  document.getElementById('usermoduledata').value=foo1;
}

function setSomeElement(fooel,foo1) {
  document.getElementById(fooel).value=document.getElementById(fooel).value + foo1;
}

function replaceSome(fooel,what,repl) {
  document.getElementById(fooel).value = document.getElementById(fooel).value.replace(what,repl);
}

function replaceLimon(vec) {
  document.getElementById(vec[0]).value = document.getElementById(vec[0]).value.replace(vec[1],vec[2]);
}

function setUserModuleFromCombo(id) {
  document.getElementById('usermoduledata').value=document.getElementById('usermoduledata').value + document.getElementById(id).options[document.getElementById(id).selectedIndex].value;
  //document.getElementById('usermoduledata').value='das';
}

function flip(foo) {
  //document.getElementById(foo).style.visibility="hidden";
  if( document.getElementById(foo).style.display == "none") {
    document.getElementById(foo).style.display="block";
  } else {
    document.getElementById(foo).style.display="none";
  }
}

function show(foo) {
    document.getElementById(foo).style.display="block";
}

function hide(foo) {
    document.getElementById(foo).style.display="none";
}


// name - name of the cookie
// value - value of the cookie
// [expires] - expiration date of the cookie (defaults to end of current session)
// [path] - path for which the cookie is valid (defaults to path of calling document)
// [domain] - domain for which the cookie is valid (defaults to domain of calling document)
// [secure] - Boolean value indicating if the cookie transmission requires a secure transmission
// * an argument defaults when it is assigned null as a placeholder
// * a null placeholder is not required for trailing omitted arguments
function setCookie(name, value, expires, path, domain, secure) {
  var curCookie = name + "=" + escape(value) +
      ((expires) ? "; expires=" + expires.toGMTString() : "") +
      ((path) ? "; path=" + path : "") +
      ((domain) ? "; domain=" + domain : "") +
      ((secure) ? "; secure" : "");
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
    if (begin != 0) return null;
  } else
    begin += 2;
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
    document.cookie = name + "=" + 
    ((path) ? "; path=" + path : "") +
    ((domain) ? "; domain=" + domain : "") +
    "; expires=Thu, 01-Jan-70 00:00:01 GMT";
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



