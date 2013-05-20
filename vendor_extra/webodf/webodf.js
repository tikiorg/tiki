// Input 0
/*


 Copyright (C) 2012 KO GmbH <copyright@kogmbh.com>

 @licstart
 The JavaScript code in this page is free software: you can redistribute it
 and/or modify it under the terms of the GNU Affero General Public License
 (GNU AGPL) as published by the Free Software Foundation, either version 3 of
 the License, or (at your option) any later version.  The code is distributed
 WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 FITNESS FOR A PARTICULAR PURPOSE.  See the GNU AGPL for more details.

 As additional permission under GNU AGPL version 3 section 7, you
 may distribute non-source (e.g., minimized or compacted) forms of
 that code without the copy of the GNU GPL normally required by
 section 4, provided you include this license notice and a URL
 through which recipients can access the Corresponding Source.

 As a special exception to the AGPL, any HTML file which merely makes function
 calls to this code, and for that purpose includes it by reference shall be
 deemed a separate work for copyright law purposes. In addition, the copyright
 holders of this code give you permission to combine this code with free
 software libraries that are released under the GNU LGPL. You may copy and
 distribute such a system following the terms of the GNU AGPL for this code
 and the LGPL for the libraries. If you modify this code, you may extend this
 exception to your version of the code, but you are not obligated to do so.
 If you do not wish to do so, delete this exception statement from your
 version.

 This license applies to this entire compilation.
 @licend
 @source: http://www.webodf.org/
 @source: http://gitorious.org/webodf/webodf/
*/
var core={},gui={},xmldom={},odf={},ops={};
// Input 1
function Runtime(){}Runtime.ByteArray=function(h){};Runtime.prototype.getVariable=function(h){};Runtime.prototype.toJson=function(h){};Runtime.prototype.fromJson=function(h){};Runtime.ByteArray.prototype.slice=function(h,l){};Runtime.ByteArray.prototype.length=0;Runtime.prototype.byteArrayFromArray=function(h){};Runtime.prototype.byteArrayFromString=function(h,l){};Runtime.prototype.byteArrayToString=function(h,l){};Runtime.prototype.concatByteArrays=function(h,l){};
Runtime.prototype.read=function(h,l,g,e){};Runtime.prototype.readFile=function(h,l,g){};Runtime.prototype.readFileSync=function(h,l){};Runtime.prototype.loadXML=function(h,l){};Runtime.prototype.writeFile=function(h,l,g){};Runtime.prototype.isFile=function(h,l){};Runtime.prototype.getFileSize=function(h,l){};Runtime.prototype.deleteFile=function(h,l){};Runtime.prototype.log=function(h,l){};Runtime.prototype.setTimeout=function(h,l){};Runtime.prototype.libraryPaths=function(){};
Runtime.prototype.type=function(){};Runtime.prototype.getDOMImplementation=function(){};Runtime.prototype.parseXML=function(h){};Runtime.prototype.getWindow=function(){};Runtime.prototype.assert=function(h,l,g){};var IS_COMPILED_CODE=!0;
Runtime.byteArrayToString=function(h,l){function g(e){var b="",a,c=e.length;for(a=0;a<c;a+=1)b+=String.fromCharCode(e[a]&255);return b}function e(e){var b="",a,c=e.length,d,m,k;for(a=0;a<c;a+=1)d=e[a],128>d?b+=String.fromCharCode(d):(a+=1,m=e[a],224>d?b+=String.fromCharCode((d&31)<<6|m&63):(a+=1,k=e[a],b+=String.fromCharCode((d&15)<<12|(m&63)<<6|k&63)));return b}var f;"utf8"===l?f=e(h):("binary"!==l&&this.log("Unsupported encoding: "+l),f=g(h));return f};Runtime.getVariable=function(h){try{return eval(h)}catch(l){}};
Runtime.toJson=function(h){return JSON.stringify(h)};Runtime.fromJson=function(h){return JSON.parse(h)};Runtime.getFunctionName=function(h){return void 0===h.name?(h=/function\s+(\w+)/.exec(h))&&h[1]:h.name};
function BrowserRuntime(h){function l(e,b){var a,c,d;void 0!==b?d=e:b=e;h?(c=h.ownerDocument,d&&(a=c.createElement("span"),a.className=d,a.appendChild(c.createTextNode(d)),h.appendChild(a),h.appendChild(c.createTextNode(" "))),a=c.createElement("span"),0<b.length&&"<"===b[0]?a.innerHTML=b:a.appendChild(c.createTextNode(b)),h.appendChild(a),h.appendChild(c.createElement("br"))):console&&console.log(b);"alert"===d&&alert(b)}var g=this,e={},f=window.ArrayBuffer&&window.Uint8Array;this.ByteArray=f?function(e){Uint8Array.prototype.slice=
function(b,a){void 0===a&&(void 0===b&&(b=0),a=this.length);var c=this.subarray(b,a),d,m;a-=b;d=new Uint8Array(new ArrayBuffer(a));for(m=0;m<a;m+=1)d[m]=c[m];return d};return new Uint8Array(new ArrayBuffer(e))}:function(e){var b=[];b.length=e;return b};this.concatByteArrays=f?function(e,b){var a,c=e.length,d=b.length,m=new this.ByteArray(c+d);for(a=0;a<c;a+=1)m[a]=e[a];for(a=0;a<d;a+=1)m[a+c]=b[a];return m}:function(e,b){return e.concat(b)};this.byteArrayFromArray=function(e){return e.slice()};this.byteArrayFromString=
function(e,b){var a;if("utf8"===b){a=e.length;var c,d,m,k=0;for(d=0;d<a;d+=1)m=e.charCodeAt(d),k+=1+(128<m)+(2048<m);c=new g.ByteArray(k);for(d=k=0;d<a;d+=1)m=e.charCodeAt(d),128>m?(c[k]=m,k+=1):2048>m?(c[k]=192|m>>>6,c[k+1]=128|m&63,k+=2):(c[k]=224|m>>>12&15,c[k+1]=128|m>>>6&63,c[k+2]=128|m&63,k+=3)}else{"binary"!==b&&g.log("unknown encoding: "+b);a=e.length;c=new g.ByteArray(a);for(d=0;d<a;d+=1)c[d]=e.charCodeAt(d)&255}return a=c};this.byteArrayToString=Runtime.byteArrayToString;this.getVariable=
Runtime.getVariable;this.fromJson=Runtime.fromJson;this.toJson=Runtime.toJson;this.readFile=function(f,b,a){function c(){var c;4===d.readyState&&(0===d.status&&!d.responseText?a("File "+f+" is empty."):200===d.status||0===d.status?(c="binary"===b?"undefined"!==String(typeof VBArray)?(new VBArray(d.responseBody)).toArray():g.byteArrayFromString(d.responseText,"binary"):d.responseText,e[f]=c,a(null,c)):a(d.responseText||d.statusText))}if(e.hasOwnProperty(f))a(null,e[f]);else{var d=new XMLHttpRequest;
d.open("GET",f,!0);d.onreadystatechange=c;d.overrideMimeType&&("binary"!==b?d.overrideMimeType("text/plain; charset="+b):d.overrideMimeType("text/plain; charset=x-user-defined"));try{d.send(null)}catch(m){a(m.message)}}};this.read=function(f,b,a,c){function d(){var d;4===m.readyState&&(0===m.status&&!m.responseText?c("File "+f+" is empty."):200===m.status||0===m.status?(d="undefined"!==String(typeof VBArray)?(new VBArray(m.responseBody)).toArray():g.byteArrayFromString(m.responseText,"binary"),e[f]=
d,c(null,d.slice(b,b+a))):c(m.responseText||m.statusText))}if(e.hasOwnProperty(f))c(null,e[f].slice(b,b+a));else{var m=new XMLHttpRequest;m.open("GET",f,!0);m.onreadystatechange=d;m.overrideMimeType&&m.overrideMimeType("text/plain; charset=x-user-defined");try{m.send(null)}catch(k){c(k.message)}}};this.readFileSync=function(e,b){var a=new XMLHttpRequest,c;a.open("GET",e,!1);a.overrideMimeType&&("binary"!==b?a.overrideMimeType("text/plain; charset="+b):a.overrideMimeType("text/plain; charset=x-user-defined"));
try{if(a.send(null),200===a.status||0===a.status)c=a.responseText}catch(d){}return c};this.writeFile=function(f,b,a){e[f]=b;var c=new XMLHttpRequest;c.open("PUT",f,!0);c.onreadystatechange=function(){4===c.readyState&&(0===c.status&&!c.responseText?a("File "+f+" is empty."):200<=c.status&&300>c.status||0===c.status?a(null):a("Status "+String(c.status)+": "+c.responseText||c.statusText))};b=b.buffer&&!c.sendAsBinary?b.buffer:g.byteArrayToString(b,"binary");try{c.sendAsBinary?c.sendAsBinary(b):c.send(b)}catch(d){g.log("HUH? "+
d+" "+b),a(d.message)}};this.deleteFile=function(f,b){delete e[f];var a=new XMLHttpRequest;a.open("DELETE",f,!0);a.onreadystatechange=function(){4===a.readyState&&(200>a.status&&300<=a.status?b(a.responseText):b(null))};a.send(null)};this.loadXML=function(e,b){var a=new XMLHttpRequest;a.open("GET",e,!0);a.overrideMimeType&&a.overrideMimeType("text/xml");a.onreadystatechange=function(){4===a.readyState&&(0===a.status&&!a.responseText?b("File "+e+" is empty."):200===a.status||0===a.status?b(null,a.responseXML):
b(a.responseText))};try{a.send(null)}catch(c){b(c.message)}};this.isFile=function(e,b){g.getFileSize(e,function(a){b(-1!==a)})};this.getFileSize=function(e,b){var a=new XMLHttpRequest;a.open("HEAD",e,!0);a.onreadystatechange=function(){if(4===a.readyState){var c=a.getResponseHeader("Content-Length");c?b(parseInt(c,10)):b(-1)}};a.send(null)};this.log=l;this.assert=function(e,b,a){if(!e)throw l("alert","ASSERTION FAILED:\n"+b),a&&a(),b;};this.setTimeout=function(e,b){setTimeout(function(){e()},b)};
this.libraryPaths=function(){return["lib"]};this.setCurrentDirectory=function(e){};this.type=function(){return"BrowserRuntime"};this.getDOMImplementation=function(){return window.document.implementation};this.parseXML=function(e){return(new DOMParser).parseFromString(e,"text/xml")};this.exit=function(e){l("Calling exit with code "+String(e)+", but exit() is not implemented.")};this.getWindow=function(){return window};this.getNetwork=function(){var e=this.getVariable("now");return void 0===e?{networkStatus:"unavailable"}:
e}}
function NodeJSRuntime(){function h(a,c,d){a=e.resolve(f,a);"binary"!==c?g.readFile(a,c,d):g.readFile(a,null,d)}var l=this,g=require("fs"),e=require("path"),f="",n,b;this.ByteArray=function(a){return new Buffer(a)};this.byteArrayFromArray=function(a){var c=new Buffer(a.length),d,b=a.length;for(d=0;d<b;d+=1)c[d]=a[d];return c};this.concatByteArrays=function(a,c){var d=new Buffer(a.length+c.length);a.copy(d,0,0);c.copy(d,a.length,0);return d};this.byteArrayFromString=function(a,c){return new Buffer(a,c)};
this.byteArrayToString=function(a,c){return a.toString(c)};this.getVariable=Runtime.getVariable;this.fromJson=Runtime.fromJson;this.toJson=Runtime.toJson;this.readFile=h;this.loadXML=function(a,c){h(a,"utf-8",function(a,b){if(a)return c(a);c(null,l.parseXML(b))})};this.writeFile=function(a,c,d){a=e.resolve(f,a);g.writeFile(a,c,"binary",function(a){d(a||null)})};this.deleteFile=function(a,c){a=e.resolve(f,a);g.unlink(a,c)};this.read=function(a,c,d,b){a=e.resolve(f,a);g.open(a,"r+",666,function(a,e){if(a)b(a);
else{var f=new Buffer(d);g.read(e,f,0,d,c,function(a,c){g.close(e);b(a,f)})}})};this.readFileSync=function(a,c){return!c?"":"binary"===c?g.readFileSync(a,null):g.readFileSync(a,c)};this.isFile=function(a,c){a=e.resolve(f,a);g.stat(a,function(a,b){c(!a&&b.isFile())})};this.getFileSize=function(a,c){a=e.resolve(f,a);g.stat(a,function(a,b){a?c(-1):c(b.size)})};this.log=function(a,c){var d;void 0!==c?d=a:c=a;"alert"===d&&process.stderr.write("\n!!!!! ALERT !!!!!\n");process.stderr.write(c+"\n");"alert"===
d&&process.stderr.write("!!!!! ALERT !!!!!\n")};this.assert=function(a,c,d){a||(process.stderr.write("ASSERTION FAILED: "+c),d&&d())};this.setTimeout=function(a,c){setTimeout(function(){a()},c)};this.libraryPaths=function(){return[__dirname]};this.setCurrentDirectory=function(a){f=a};this.currentDirectory=function(){return f};this.type=function(){return"NodeJSRuntime"};this.getDOMImplementation=function(){return b};this.parseXML=function(a){return n.parseFromString(a,"text/xml")};this.exit=process.exit;
this.getWindow=function(){return null};this.getNetwork=function(){return{networkStatus:"unavailable"}};n=new (require("xmldom").DOMParser);b=l.parseXML("<a/>").implementation}
function RhinoRuntime(){function h(b,a){var c;void 0!==a?c=b:a=b;"alert"===c&&print("\n!!!!! ALERT !!!!!");print(a);"alert"===c&&print("!!!!! ALERT !!!!!")}var l=this,g=Packages.javax.xml.parsers.DocumentBuilderFactory.newInstance(),e,f,n="";g.setValidating(!1);g.setNamespaceAware(!0);g.setExpandEntityReferences(!1);g.setSchema(null);f=Packages.org.xml.sax.EntityResolver({resolveEntity:function(b,a){var c=new Packages.java.io.FileReader(a);return new Packages.org.xml.sax.InputSource(c)}});e=g.newDocumentBuilder();
e.setEntityResolver(f);this.ByteArray=function(b){return[b]};this.byteArrayFromArray=function(b){return b};this.byteArrayFromString=function(b,a){var c=[],d,e=b.length;for(d=0;d<e;d+=1)c[d]=b.charCodeAt(d)&255;return c};this.byteArrayToString=Runtime.byteArrayToString;this.getVariable=Runtime.getVariable;this.fromJson=Runtime.fromJson;this.toJson=Runtime.toJson;this.concatByteArrays=function(b,a){return b.concat(a)};this.loadXML=function(b,a){var c=new Packages.java.io.File(b),d;try{d=e.parse(c)}catch(f){print(f);
a(f);return}a(null,d)};this.readFile=function(b,a,c){n&&(b=n+"/"+b);var d=new Packages.java.io.File(b),e="binary"===a?"latin1":a;d.isFile()?(b=readFile(b,e),"binary"===a&&(b=l.byteArrayFromString(b,"binary")),c(null,b)):c(b+" is not a file.")};this.writeFile=function(b,a,c){n&&(b=n+"/"+b);b=new Packages.java.io.FileOutputStream(b);var d,e=a.length;for(d=0;d<e;d+=1)b.write(a[d]);b.close();c(null)};this.deleteFile=function(b,a){n&&(b=n+"/"+b);(new Packages.java.io.File(b))["delete"]()?a(null):a("Could not delete "+
b)};this.read=function(b,a,c,d){n&&(b=n+"/"+b);var e;e=b;var k="binary";(new Packages.java.io.File(e)).isFile()?("binary"===k&&(k="latin1"),e=readFile(e,k)):e=null;e?d(null,this.byteArrayFromString(e.substring(a,a+c),"binary")):d("Cannot read "+b)};this.readFileSync=function(b,a){return!a?"":readFile(b,a)};this.isFile=function(b,a){n&&(b=n+"/"+b);var c=new Packages.java.io.File(b);a(c.isFile())};this.getFileSize=function(b,a){n&&(b=n+"/"+b);var c=new Packages.java.io.File(b);a(c.length())};this.log=
h;this.assert=function(b,a,c){b||(h("alert","ASSERTION FAILED: "+a),c&&c())};this.setTimeout=function(b,a){b()};this.libraryPaths=function(){return["lib"]};this.setCurrentDirectory=function(b){n=b};this.currentDirectory=function(){return n};this.type=function(){return"RhinoRuntime"};this.getDOMImplementation=function(){return e.getDOMImplementation()};this.parseXML=function(b){return e.parse(b)};this.exit=quit;this.getWindow=function(){return null};this.getNetwork=function(){return{networkStatus:"unavailable"}}}
var runtime=function(){return"undefined"!==String(typeof window)?new BrowserRuntime(window.document.getElementById("logoutput")):"undefined"!==String(typeof require)?new NodeJSRuntime:new RhinoRuntime}();
(function(){function h(e){var f=e[0],g;g=eval("if (typeof "+f+" === 'undefined') {eval('"+f+" = {};');}"+f);for(f=1;f<e.length-1;f+=1)g=g.hasOwnProperty(e[f])?g[e[f]]:g[e[f]]={};return g[e[e.length-1]]}var l={},g={};runtime.loadClass=function(e){function f(a){a=a.replace(/\./g,"/")+".js";var d=runtime.libraryPaths(),b,k,e;runtime.currentDirectory&&d.push(runtime.currentDirectory());for(b=0;b<d.length;b+=1){k=d[b];if(!g.hasOwnProperty(k))if((e=runtime.readFileSync(d[b]+"/manifest.js","utf8"))&&e.length)try{g[k]=
eval(e)}catch(f){g[k]=null,runtime.log("Cannot load manifest for "+k+".")}else g[k]=null;if((k=g[k])&&k.indexOf&&-1!==k.indexOf(a))return d[b]+"/"+a}return null}function n(a){var d,b;b=f(a);if(!b)throw a+" is not listed in any manifest.js.";try{d=runtime.readFileSync(b,"utf8")}catch(k){throw runtime.log("Error loading "+a+" "+k),k;}if(void 0===d)throw"Cannot load class "+a;try{d=eval(a+" = eval(code);")}catch(e){throw runtime.log("Error loading "+a+" "+e),e;}return d}if(!IS_COMPILED_CODE&&!l.hasOwnProperty(e)){var b=
e.split("."),a;a=h(b);if(!a&&(a=n(e),!a||Runtime.getFunctionName(a)!==b[b.length-1]))throw runtime.log("Loaded code is not for "+b[b.length-1]),"Loaded code is not for "+b[b.length-1];l[e]=!0}}})();
(function(h){function l(g){if(g.length){var e=g[0];runtime.readFile(e,"utf8",function(f,h){function b(){var a;(a=eval(c))&&runtime.exit(a)}var a="";runtime.libraryPaths();var c=h;-1!==e.indexOf("/")&&(a=e.substring(0,e.indexOf("/")));runtime.setCurrentDirectory(a);f||null===c?(runtime.log(f),runtime.exit(1)):b.apply(null,g)})}}h=h?Array.prototype.slice.call(h):[];"NodeJSRuntime"===runtime.type()?l(process.argv.slice(2)):"RhinoRuntime"===runtime.type()?l(h):l(h.slice(1))})("undefined"!==String(typeof arguments)&&
arguments);
// Input 2
core.Base64=function(){function h(a){var c=[],d,b=a.length;for(d=0;d<b;d+=1)c[d]=a.charCodeAt(d)&255;return c}function l(a){var c,d="",b,k=a.length-2;for(b=0;b<k;b+=3)c=a[b]<<16|a[b+1]<<8|a[b+2],d+=q[c>>>18],d+=q[c>>>12&63],d+=q[c>>>6&63],d+=q[c&63];b===k+1?(c=a[b]<<4,d+=q[c>>>6],d+=q[c&63],d+="=="):b===k&&(c=a[b]<<10|a[b+1]<<2,d+=q[c>>>12],d+=q[c>>>6&63],d+=q[c&63],d+="=");return d}function g(a){a=a.replace(/[^A-Za-z0-9+\/]+/g,"");var d=[],c=a.length%4,b,k=a.length,e;for(b=0;b<k;b+=4)e=(s[a.charAt(b)]||
0)<<18|(s[a.charAt(b+1)]||0)<<12|(s[a.charAt(b+2)]||0)<<6|(s[a.charAt(b+3)]||0),d.push(e>>16,e>>8&255,e&255);d.length-=[0,0,2,1][c];return d}function e(a){var d=[],c,b=a.length,k;for(c=0;c<b;c+=1)k=a[c],128>k?d.push(k):2048>k?d.push(192|k>>>6,128|k&63):d.push(224|k>>>12&15,128|k>>>6&63,128|k&63);return d}function f(a){var d=[],c,b=a.length,k,e,p;for(c=0;c<b;c+=1)k=a[c],128>k?d.push(k):(c+=1,e=a[c],224>k?d.push((k&31)<<6|e&63):(c+=1,p=a[c],d.push((k&15)<<12|(e&63)<<6|p&63)));return d}function n(a){return l(h(a))}
function b(a){return String.fromCharCode.apply(String,g(a))}function a(a){return f(h(a))}function c(a){a=f(a);for(var c="",d=0;d<a.length;)c+=String.fromCharCode.apply(String,a.slice(d,d+45E3)),d+=45E3;return c}function d(a,d,c){var b="",k,e,p;for(p=d;p<c;p+=1)d=a.charCodeAt(p)&255,128>d?b+=String.fromCharCode(d):(p+=1,k=a.charCodeAt(p)&255,224>d?b+=String.fromCharCode((d&31)<<6|k&63):(p+=1,e=a.charCodeAt(p)&255,b+=String.fromCharCode((d&15)<<12|(k&63)<<6|e&63)));return b}function m(a,c){function b(){var f=
p+k;f>a.length&&(f=a.length);e+=d(a,p,f);p=f;f=p===a.length;c(e,f)&&!f&&runtime.setTimeout(b,0)}var k=1E5,e="",p=0;a.length<k?c(d(a,0,a.length),!0):("string"!==typeof a&&(a=a.slice()),b())}function k(a){return e(h(a))}function p(a){return String.fromCharCode.apply(String,e(a))}function r(a){return String.fromCharCode.apply(String,e(h(a)))}var q="ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/";(function(){var a=[],d;for(d=0;26>d;d+=1)a.push(65+d);for(d=0;26>d;d+=1)a.push(97+d);for(d=
0;10>d;d+=1)a.push(48+d);a.push(43);a.push(47);return a})();var s=function(a){var d={},c,b;c=0;for(b=a.length;c<b;c+=1)d[a.charAt(c)]=c;return d}(q),v,w,F=runtime.getWindow(),z,u;F&&F.btoa?(z=function(a){return F.btoa(a)},v=function(a){return z(r(a))}):(z=n,v=function(a){return l(k(a))});F&&F.atob?(u=function(a){return F.atob(a)},w=function(a){a=u(a);return d(a,0,a.length)}):(u=b,w=function(a){return c(g(a))});return function(){this.convertByteArrayToBase64=this.convertUTF8ArrayToBase64=l;this.convertBase64ToByteArray=
this.convertBase64ToUTF8Array=g;this.convertUTF16ArrayToByteArray=this.convertUTF16ArrayToUTF8Array=e;this.convertByteArrayToUTF16Array=this.convertUTF8ArrayToUTF16Array=f;this.convertUTF8StringToBase64=n;this.convertBase64ToUTF8String=b;this.convertUTF8StringToUTF16Array=a;this.convertByteArrayToUTF16String=this.convertUTF8ArrayToUTF16String=c;this.convertUTF8StringToUTF16String=m;this.convertUTF16StringToByteArray=this.convertUTF16StringToUTF8Array=k;this.convertUTF16ArrayToUTF8String=p;this.convertUTF16StringToUTF8String=
r;this.convertUTF16StringToBase64=v;this.convertBase64ToUTF16String=w;this.fromBase64=b;this.toBase64=n;this.atob=u;this.btoa=z;this.utob=r;this.btou=m;this.encode=v;this.encodeURI=function(a){return v(a).replace(/[+\/]/g,function(a){return"+"===a?"-":"_"}).replace(/\\=+$/,"")};this.decode=function(a){return w(a.replace(/[\-_]/g,function(a){return"-"===a?"+":"/"}))}}}();
// Input 3
core.RawDeflate=function(){function h(){this.dl=this.fc=0}function l(){this.extra_bits=this.static_tree=this.dyn_tree=null;this.max_code=this.max_length=this.elems=this.extra_base=0}function g(a,d,c,b){this.good_length=a;this.max_lazy=d;this.nice_length=c;this.max_chain=b}function e(){this.next=null;this.len=0;this.ptr=[];this.ptr.length=f;this.off=0}var f=8192,n,b,a,c,d=null,m,k,p,r,q,s,v,w,F,z,u,y,K,H,B,L,x,E,D,A,T,U,N,W,S,Q,I,J,M,C,t,G,R,O,Y,ca,X,da,ba,la,ga,ha,Z,ma,sa,ea,ia,$,fa,P,ta,ua=[0,0,
0,0,0,0,0,0,1,1,1,1,2,2,2,2,3,3,3,3,4,4,4,4,5,5,5,5,0],ja=[0,0,0,0,1,1,2,2,3,3,4,4,5,5,6,6,7,7,8,8,9,9,10,10,11,11,12,12,13,13],Ka=[0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,2,3,7],ya=[16,17,18,0,8,7,9,6,10,5,11,4,12,3,13,2,14,1,15],na;na=[new g(0,0,0,0),new g(4,4,8,4),new g(4,5,16,8),new g(4,6,32,32),new g(4,4,16,16),new g(8,16,32,32),new g(8,16,128,128),new g(8,32,128,256),new g(32,128,258,1024),new g(32,258,258,4096)];var oa=function(c){d[k+m++]=c;if(k+m===f){var p;if(0!==m){null!==n?(c=n,n=n.next):c=new e;
c.next=null;c.len=c.off=0;null===b?b=a=c:a=a.next=c;c.len=m-k;for(p=0;p<c.len;p++)c.ptr[p]=d[k+p];m=k=0}}},pa=function(a){a&=65535;k+m<f-2?(d[k+m++]=a&255,d[k+m++]=a>>>8):(oa(a&255),oa(a>>>8))},qa=function(){u=(u<<5^r[x+3-1]&255)&8191;y=v[32768+u];v[x&32767]=y;v[32768+u]=x},V=function(a,d){F>16-d?(w|=a<<F,pa(w),w=a>>16-F,F+=d-16):(w|=a<<F,F+=d)},aa=function(a,d){V(d[a].fc,d[a].dl)},za=function(a,d,c){return a[d].fc<a[c].fc||a[d].fc===a[c].fc&&X[d]<=X[c]},Aa=function(a,d,c){var b;for(b=0;b<c&&ta<P.length;b++)a[d+
b]=P.charCodeAt(ta++)&255;return b},va=function(){var a,d,c=65536-A-x;if(-1===c)c--;else if(65274<=x){for(a=0;32768>a;a++)r[a]=r[a+32768];E-=32768;x-=32768;z-=32768;for(a=0;8192>a;a++)d=v[32768+a],v[32768+a]=32768<=d?d-32768:0;for(a=0;32768>a;a++)d=v[a],v[a]=32768<=d?d-32768:0;c+=32768}D||(a=Aa(r,x+A,c),0>=a?D=!0:A+=a)},Ba=function(a){var d=T,c=x,b,k=L,e=32506<x?x-32506:0,p=x+258,f=r[c+k-1],g=r[c+k];L>=W&&(d>>=2);do if(b=a,!(r[b+k]!==g||r[b+k-1]!==f||r[b]!==r[c]||r[++b]!==r[c+1])){c+=2;b++;do++c;
while(r[c]===r[++b]&&r[++c]===r[++b]&&r[++c]===r[++b]&&r[++c]===r[++b]&&r[++c]===r[++b]&&r[++c]===r[++b]&&r[++c]===r[++b]&&r[++c]===r[++b]&&c<p);b=258-(p-c);c=p-258;if(b>k){E=a;k=b;if(258<=b)break;f=r[c+k-1];g=r[c+k]}}while((a=v[a&32767])>e&&0!==--d);return k},ka=function(a,c){s[Z++]=c;0===a?S[c].fc++:(a--,S[da[c]+256+1].fc++,Q[(256>a?ba[a]:ba[256+(a>>7)])&255].fc++,q[ma++]=a,ea|=ia);ia<<=1;0===(Z&7)&&(ha[sa++]=ea,ea=0,ia=1);if(2<N&&0===(Z&4095)){var d=8*Z,b=x-z,k;for(k=0;30>k;k++)d+=Q[k].fc*(5+ja[k]);
d>>=3;if(ma<parseInt(Z/2,10)&&d<parseInt(b/2,10))return!0}return 8191===Z||8192===ma},wa=function(a,c){for(var d=O[c],b=c<<1;b<=Y;){b<Y&&za(a,O[b+1],O[b])&&b++;if(za(a,d,O[b]))break;O[c]=O[b];c=b;b<<=1}O[c]=d},Ca=function(a,c){var d=0;do d|=a&1,a>>=1,d<<=1;while(0<--c);return d>>1},Da=function(a,c){var d=[];d.length=16;var b=0,k;for(k=1;15>=k;k++)b=b+R[k-1]<<1,d[k]=b;for(b=0;b<=c;b++)k=a[b].dl,0!==k&&(a[b].fc=Ca(d[k]++,k))},xa=function(a){var d=a.dyn_tree,c=a.static_tree,b=a.elems,k,e=-1,p=b;Y=0;
ca=573;for(k=0;k<b;k++)0!==d[k].fc?(O[++Y]=e=k,X[k]=0):d[k].dl=0;for(;2>Y;)k=O[++Y]=2>e?++e:0,d[k].fc=1,X[k]=0,$--,null!==c&&(fa-=c[k].dl);a.max_code=e;for(k=Y>>1;1<=k;k--)wa(d,k);do k=O[1],O[1]=O[Y--],wa(d,1),c=O[1],O[--ca]=k,O[--ca]=c,d[p].fc=d[k].fc+d[c].fc,X[p]=X[k]>X[c]+1?X[k]:X[c]+1,d[k].dl=d[c].dl=p,O[1]=p++,wa(d,1);while(2<=Y);O[--ca]=O[1];p=a.dyn_tree;k=a.extra_bits;var b=a.extra_base,c=a.max_code,f=a.max_length,r=a.static_tree,g,m,q,s,h=0;for(m=0;15>=m;m++)R[m]=0;p[O[ca]].dl=0;for(a=ca+
1;573>a;a++)g=O[a],m=p[p[g].dl].dl+1,m>f&&(m=f,h++),p[g].dl=m,g>c||(R[m]++,q=0,g>=b&&(q=k[g-b]),s=p[g].fc,$+=s*(m+q),null!==r&&(fa+=s*(r[g].dl+q)));if(0!==h){do{for(m=f-1;0===R[m];)m--;R[m]--;R[m+1]+=2;R[f]--;h-=2}while(0<h);for(m=f;0!==m;m--)for(g=R[m];0!==g;)k=O[--a],k>c||(p[k].dl!==m&&($+=(m-p[k].dl)*p[k].fc,p[k].fc=m),g--)}Da(d,e)},Ea=function(a,d){var c,b=-1,k,e=a[0].dl,p=0,f=7,m=4;0===e&&(f=138,m=3);a[d+1].dl=65535;for(c=0;c<=d;c++)k=e,e=a[c+1].dl,++p<f&&k===e||(p<m?M[k].fc+=p:0!==k?(k!==b&&
M[k].fc++,M[16].fc++):10>=p?M[17].fc++:M[18].fc++,p=0,b=k,0===e?(f=138,m=3):k===e?(f=6,m=3):(f=7,m=4))},Fa=function(){8<F?pa(w):0<F&&oa(w);F=w=0},Ga=function(a,c){var d,b=0,k=0,e=0,p=0,f,m;if(0!==Z){do 0===(b&7)&&(p=ha[e++]),d=s[b++]&255,0===(p&1)?aa(d,a):(f=da[d],aa(f+256+1,a),m=ua[f],0!==m&&(d-=la[f],V(d,m)),d=q[k++],f=(256>d?ba[d]:ba[256+(d>>7)])&255,aa(f,c),m=ja[f],0!==m&&(d-=ga[f],V(d,m))),p>>=1;while(b<Z)}aa(256,a)},Ha=function(a,d){var c,b=-1,k,e=a[0].dl,p=0,f=7,m=4;0===e&&(f=138,m=3);for(c=
0;c<=d;c++)if(k=e,e=a[c+1].dl,!(++p<f&&k===e)){if(p<m){do aa(k,M);while(0!==--p)}else 0!==k?(k!==b&&(aa(k,M),p--),aa(16,M),V(p-3,2)):10>=p?(aa(17,M),V(p-3,3)):(aa(18,M),V(p-11,7));p=0;b=k;0===e?(f=138,m=3):k===e?(f=6,m=3):(f=7,m=4)}},Ia=function(){var a;for(a=0;286>a;a++)S[a].fc=0;for(a=0;30>a;a++)Q[a].fc=0;for(a=0;19>a;a++)M[a].fc=0;S[256].fc=1;ea=Z=ma=sa=$=fa=0;ia=1},ra=function(a){var d,c,b,k;k=x-z;ha[sa]=ea;xa(C);xa(t);Ea(S,C.max_code);Ea(Q,t.max_code);xa(G);for(b=18;3<=b&&0===M[ya[b]].dl;b--);
$+=3*(b+1)+14;d=$+3+7>>3;c=fa+3+7>>3;c<=d&&(d=c);if(k+4<=d&&0<=z){V(0+a,3);Fa();pa(k);pa(~k);for(b=0;b<k;b++)oa(r[z+b])}else if(c===d)V(2+a,3),Ga(I,J);else{V(4+a,3);k=C.max_code+1;d=t.max_code+1;b+=1;V(k-257,5);V(d-1,5);V(b-4,4);for(c=0;c<b;c++)V(M[ya[c]].dl,3);Ha(S,k-1);Ha(Q,d-1);Ga(S,Q)}Ia();0!==a&&Fa()},Ja=function(a,c,e){var p,f,g;for(p=0;null!==b&&p<e;){f=e-p;f>b.len&&(f=b.len);for(g=0;g<f;g++)a[c+p+g]=b.ptr[b.off+g];b.off+=f;b.len-=f;p+=f;0===b.len&&(f=b,b=b.next,f.next=n,n=f)}if(p===e)return p;
if(k<m){f=e-p;f>m-k&&(f=m-k);for(g=0;g<f;g++)a[c+p+g]=d[k+g];k+=f;p+=f;m===k&&(m=k=0)}return p},La=function(a,d,e){var f;if(!c){if(!D){F=w=0;var g,q;if(0===J[0].dl){C.dyn_tree=S;C.static_tree=I;C.extra_bits=ua;C.extra_base=257;C.elems=286;C.max_length=15;C.max_code=0;t.dyn_tree=Q;t.static_tree=J;t.extra_bits=ja;t.extra_base=0;t.elems=30;t.max_length=15;t.max_code=0;G.dyn_tree=M;G.static_tree=null;G.extra_bits=Ka;G.extra_base=0;G.elems=19;G.max_length=7;for(q=g=G.max_code=0;28>q;q++){la[q]=g;for(f=
0;f<1<<ua[q];f++)da[g++]=q}da[g-1]=q;for(q=g=0;16>q;q++){ga[q]=g;for(f=0;f<1<<ja[q];f++)ba[g++]=q}for(g>>=7;30>q;q++){ga[q]=g<<7;for(f=0;f<1<<ja[q]-7;f++)ba[256+g++]=q}for(f=0;15>=f;f++)R[f]=0;for(f=0;143>=f;)I[f++].dl=8,R[8]++;for(;255>=f;)I[f++].dl=9,R[9]++;for(;279>=f;)I[f++].dl=7,R[7]++;for(;287>=f;)I[f++].dl=8,R[8]++;Da(I,287);for(f=0;30>f;f++)J[f].dl=5,J[f].fc=Ca(f,5);Ia()}for(f=0;8192>f;f++)v[32768+f]=0;U=na[N].max_lazy;W=na[N].good_length;T=na[N].max_chain;z=x=0;A=Aa(r,0,65536);if(0>=A)D=
!0,A=0;else{for(D=!1;262>A&&!D;)va();for(f=u=0;2>f;f++)u=(u<<5^r[f]&255)&8191}b=null;k=m=0;3>=N?(L=2,B=0):(B=2,H=0);p=!1}c=!0;if(0===A)return p=!0,0}if((f=Ja(a,d,e))===e)return e;if(p)return f;if(3>=N)for(;0!==A&&null===b;){qa();0!==y&&32506>=x-y&&(B=Ba(y),B>A&&(B=A));if(3<=B)if(q=ka(x-E,B-3),A-=B,B<=U){B--;do x++,qa();while(0!==--B);x++}else x+=B,B=0,u=r[x]&255,u=(u<<5^r[x+1]&255)&8191;else q=ka(0,r[x]&255),A--,x++;q&&(ra(0),z=x);for(;262>A&&!D;)va()}else for(;0!==A&&null===b;){qa();L=B;K=E;B=2;
0!==y&&(L<U&&32506>=x-y)&&(B=Ba(y),B>A&&(B=A),3===B&&4096<x-E&&B--);if(3<=L&&B<=L){q=ka(x-1-K,L-3);A-=L-1;L-=2;do x++,qa();while(0!==--L);H=0;B=2;x++;q&&(ra(0),z=x)}else 0!==H?ka(0,r[x-1]&255)&&(ra(0),z=x):H=1,x++,A--;for(;262>A&&!D;)va()}0===A&&(0!==H&&ka(0,r[x-1]&255),ra(1),p=!0);return f+Ja(a,f+d,e-f)};this.deflate=function(k,e){var p,g;P=k;ta=0;"undefined"===String(typeof e)&&(e=6);(p=e)?1>p?p=1:9<p&&(p=9):p=6;N=p;D=c=!1;if(null===d){n=b=a=null;d=[];d.length=f;r=[];r.length=65536;q=[];q.length=
8192;s=[];s.length=32832;v=[];v.length=65536;S=[];S.length=573;for(p=0;573>p;p++)S[p]=new h;Q=[];Q.length=61;for(p=0;61>p;p++)Q[p]=new h;I=[];I.length=288;for(p=0;288>p;p++)I[p]=new h;J=[];J.length=30;for(p=0;30>p;p++)J[p]=new h;M=[];M.length=39;for(p=0;39>p;p++)M[p]=new h;C=new l;t=new l;G=new l;R=[];R.length=16;O=[];O.length=573;X=[];X.length=573;da=[];da.length=256;ba=[];ba.length=512;la=[];la.length=29;ga=[];ga.length=30;ha=[];ha.length=1024}for(var m=Array(1024),y=[];0<(p=La(m,0,m.length));){var w=
[];w.length=p;for(g=0;g<p;g++)w[g]=String.fromCharCode(m[g]);y[y.length]=w.join("")}P=null;return y.join("")}};
// Input 4
core.ByteArray=function(h){this.pos=0;this.data=h;this.readUInt32LE=function(){var h=this.data,g=this.pos+=4;return h[--g]<<24|h[--g]<<16|h[--g]<<8|h[--g]};this.readUInt16LE=function(){var h=this.data,g=this.pos+=2;return h[--g]<<8|h[--g]}};
// Input 5
core.ByteArrayWriter=function(h){var l=this,g=new runtime.ByteArray(0);this.appendByteArrayWriter=function(e){g=runtime.concatByteArrays(g,e.getByteArray())};this.appendByteArray=function(e){g=runtime.concatByteArrays(g,e)};this.appendArray=function(e){g=runtime.concatByteArrays(g,runtime.byteArrayFromArray(e))};this.appendUInt16LE=function(e){l.appendArray([e&255,e>>8&255])};this.appendUInt32LE=function(e){l.appendArray([e&255,e>>8&255,e>>16&255,e>>24&255])};this.appendString=function(e){g=runtime.concatByteArrays(g,
runtime.byteArrayFromString(e,h))};this.getLength=function(){return g.length};this.getByteArray=function(){return g}};
// Input 6
core.RawInflate=function(){var h,l,g=null,e,f,n,b,a,c,d,m,k,p,r,q,s,v,w=[0,1,3,7,15,31,63,127,255,511,1023,2047,4095,8191,16383,32767,65535],F=[3,4,5,6,7,8,9,10,11,13,15,17,19,23,27,31,35,43,51,59,67,83,99,115,131,163,195,227,258,0,0],z=[0,0,0,0,0,0,0,0,1,1,1,1,2,2,2,2,3,3,3,3,4,4,4,4,5,5,5,5,0,99,99],u=[1,2,3,4,5,7,9,13,17,25,33,49,65,97,129,193,257,385,513,769,1025,1537,2049,3073,4097,6145,8193,12289,16385,24577],y=[0,0,0,0,1,1,2,2,3,3,4,4,5,5,6,6,7,7,8,8,9,9,10,10,11,11,12,12,13,13],K=[16,17,18,
0,8,7,9,6,10,5,11,4,12,3,13,2,14,1,15],H=function(){this.list=this.next=null},B=function(){this.n=this.b=this.e=0;this.t=null},L=function(a,d,c,b,k,p){this.BMAX=16;this.N_MAX=288;this.status=0;this.root=null;this.m=0;var e=Array(this.BMAX+1),f,g,m,q,r,h,s,v=Array(this.BMAX+1),y,n,l,w=new B,D=Array(this.BMAX);q=Array(this.N_MAX);var A,u=Array(this.BMAX+1),x,E,z;z=this.root=null;for(r=0;r<e.length;r++)e[r]=0;for(r=0;r<v.length;r++)v[r]=0;for(r=0;r<D.length;r++)D[r]=null;for(r=0;r<q.length;r++)q[r]=
0;for(r=0;r<u.length;r++)u[r]=0;f=256<d?a[256]:this.BMAX;y=a;n=0;r=d;do e[y[n]]++,n++;while(0<--r);if(e[0]==d)this.root=null,this.status=this.m=0;else{for(h=1;h<=this.BMAX&&0==e[h];h++);s=h;p<h&&(p=h);for(r=this.BMAX;0!=r&&0==e[r];r--);m=r;p>r&&(p=r);for(x=1<<h;h<r;h++,x<<=1)if(0>(x-=e[h])){this.status=2;this.m=p;return}if(0>(x-=e[r]))this.status=2,this.m=p;else{e[r]+=x;u[1]=h=0;y=e;n=1;for(l=2;0<--r;)u[l++]=h+=y[n++];y=a;r=n=0;do if(0!=(h=y[n++]))q[u[h]++]=r;while(++r<d);d=u[m];u[0]=r=0;y=q;n=0;
q=-1;A=v[0]=0;l=null;for(E=0;s<=m;s++)for(a=e[s];0<a--;){for(;s>A+v[1+q];){A+=v[1+q];q++;E=(E=m-A)>p?p:E;if((g=1<<(h=s-A))>a+1){g-=a+1;for(l=s;++h<E&&!((g<<=1)<=e[++l]);)g-=e[l]}A+h>f&&A<f&&(h=f-A);E=1<<h;v[1+q]=h;l=Array(E);for(g=0;g<E;g++)l[g]=new B;z=null==z?this.root=new H:z.next=new H;z.next=null;z.list=l;D[q]=l;0<q&&(u[q]=r,w.b=v[q],w.e=16+h,w.t=l,h=(r&(1<<A)-1)>>A-v[q],D[q-1][h].e=w.e,D[q-1][h].b=w.b,D[q-1][h].n=w.n,D[q-1][h].t=w.t)}w.b=s-A;n>=d?w.e=99:y[n]<c?(w.e=256>y[n]?16:15,w.n=y[n++]):
(w.e=k[y[n]-c],w.n=b[y[n++]-c]);g=1<<s-A;for(h=r>>A;h<E;h+=g)l[h].e=w.e,l[h].b=w.b,l[h].n=w.n,l[h].t=w.t;for(h=1<<s-1;0!=(r&h);h>>=1)r^=h;for(r^=h;(r&(1<<A)-1)!=u[q];)A-=v[q],q--}this.m=v[1];this.status=0!=x&&1!=m?1:0}}},x=function(a){for(;b<a;){var d=n,c;c=s.length==v?-1:s[v++];n=d|c<<b;b+=8}},E=function(a){return n&w[a]},D=function(a){n>>=a;b-=a},A=function(c,b,e){var f,g,s;if(0==e)return 0;for(s=0;;){x(r);g=k.list[E(r)];for(f=g.e;16<f;){if(99==f)return-1;D(g.b);f-=16;x(f);g=g.t[E(f)];f=g.e}D(g.b);
if(16==f)l&=32767,c[b+s++]=h[l++]=g.n;else{if(15==f)break;x(f);d=g.n+E(f);D(f);x(q);g=p.list[E(q)];for(f=g.e;16<f;){if(99==f)return-1;D(g.b);f-=16;x(f);g=g.t[E(f)];f=g.e}D(g.b);x(f);m=l-g.n-E(f);for(D(f);0<d&&s<e;)d--,m&=32767,l&=32767,c[b+s++]=h[l++]=h[m++]}if(s==e)return e}a=-1;return s},T,U=function(a,d,c){var b,e,f,g,m,h,s,v=Array(316);for(b=0;b<v.length;b++)v[b]=0;x(5);h=257+E(5);D(5);x(5);s=1+E(5);D(5);x(4);b=4+E(4);D(4);if(286<h||30<s)return-1;for(e=0;e<b;e++)x(3),v[K[e]]=E(3),D(3);for(;19>
e;e++)v[K[e]]=0;r=7;e=new L(v,19,19,null,null,r);if(0!=e.status)return-1;k=e.root;r=e.m;g=h+s;for(b=f=0;b<g;)if(x(r),m=k.list[E(r)],e=m.b,D(e),e=m.n,16>e)v[b++]=f=e;else if(16==e){x(2);e=3+E(2);D(2);if(b+e>g)return-1;for(;0<e--;)v[b++]=f}else{17==e?(x(3),e=3+E(3),D(3)):(x(7),e=11+E(7),D(7));if(b+e>g)return-1;for(;0<e--;)v[b++]=0;f=0}r=9;e=new L(v,h,257,F,z,r);0==r&&(e.status=1);if(0!=e.status)return-1;k=e.root;r=e.m;for(b=0;b<s;b++)v[b]=v[b+h];q=6;e=new L(v,s,0,u,y,q);p=e.root;q=e.m;return 0==q&&
257<h||0!=e.status?-1:A(a,d,c)};this.inflate=function(w,K){null==h&&(h=Array(65536));b=n=l=0;a=-1;c=!1;d=m=0;k=null;s=w;v=0;var H=new runtime.ByteArray(K);a:{var B,I;for(B=0;B<K&&!(c&&-1==a);){if(0<d){if(0!=a)for(;0<d&&B<K;)d--,m&=32767,l&=32767,H[0+B++]=h[l++]=h[m++];else{for(;0<d&&B<K;)d--,l&=32767,x(8),H[0+B++]=h[l++]=E(8),D(8);0==d&&(a=-1)}if(B==K)break}if(-1==a){if(c)break;x(1);0!=E(1)&&(c=!0);D(1);x(2);a=E(2);D(2);k=null;d=0}switch(a){case 0:I=H;var J=0+B,M=K-B,C=void 0,C=b&7;D(C);x(16);C=E(16);
D(16);x(16);if(C!=(~n&65535))I=-1;else{D(16);d=C;for(C=0;0<d&&C<M;)d--,l&=32767,x(8),I[J+C++]=h[l++]=E(8),D(8);0==d&&(a=-1);I=C}break;case 1:if(null!=k)I=A(H,0+B,K-B);else b:{I=H;J=0+B;M=K-B;if(null==g){for(var t=void 0,C=Array(288),t=void 0,t=0;144>t;t++)C[t]=8;for(;256>t;t++)C[t]=9;for(;280>t;t++)C[t]=7;for(;288>t;t++)C[t]=8;f=7;t=new L(C,288,257,F,z,f);if(0!=t.status){alert("HufBuild error: "+t.status);I=-1;break b}g=t.root;f=t.m;for(t=0;30>t;t++)C[t]=5;T=5;t=new L(C,30,0,u,y,T);if(1<t.status){g=
null;alert("HufBuild error: "+t.status);I=-1;break b}e=t.root;T=t.m}k=g;p=e;r=f;q=T;I=A(I,J,M)}break;case 2:I=null!=k?A(H,0+B,K-B):U(H,0+B,K-B);break;default:I=-1}if(-1==I)break a;B+=I}}s=null;return H}};
// Input 7
core.Selection=function(h){var l=this,g=[];this.getRangeAt=function(e){return g[e]};this.addRange=function(e){0===g.length&&(l.focusNode=e.startContainer,l.focusOffset=e.startOffset);g.push(e);l.rangeCount+=1};this.removeAllRanges=function(){g=[];l.rangeCount=0;l.focusNode=null;l.focusOffset=0};this.collapse=function(e,f){runtime.assert(0<=f,"invalid offset "+f+" in Selection.collapse");g.length=l.rangeCount=1;var n=g[0];n||(g[0]=n=h.createRange());n.setStart(e,f);n.collapse(!0);l.focusNode=e;l.focusOffset=
f};this.extend=function(e,f){};this.rangeCount=0;this.focusNode=null;this.focusOffset=0};
// Input 8
/*

 Copyright (C) 2012 KO GmbH <copyright@kogmbh.com>

 @licstart
 The JavaScript code in this page is free software: you can redistribute it
 and/or modify it under the terms of the GNU Affero General Public License
 (GNU AGPL) as published by the Free Software Foundation, either version 3 of
 the License, or (at your option) any later version.  The code is distributed
 WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 FITNESS FOR A PARTICULAR PURPOSE.  See the GNU AGPL for more details.

 As additional permission under GNU AGPL version 3 section 7, you
 may distribute non-source (e.g., minimized or compacted) forms of
 that code without the copy of the GNU GPL normally required by
 section 4, provided you include this license notice and a URL
 through which recipients can access the Corresponding Source.

 As a special exception to the AGPL, any HTML file which merely makes function
 calls to this code, and for that purpose includes it by reference shall be
 deemed a separate work for copyright law purposes. In addition, the copyright
 holders of this code give you permission to combine this code with free
 software libraries that are released under the GNU LGPL. You may copy and
 distribute such a system following the terms of the GNU AGPL for this code
 and the LGPL for the libraries. If you modify this code, you may extend this
 exception to your version of the code, but you are not obligated to do so.
 If you do not wish to do so, delete this exception statement from your
 version.

 This license applies to this entire compilation.
 @licend
 @source: http://www.webodf.org/
 @source: http://gitorious.org/webodf/webodf/
*/
core.LoopWatchDog=function(h,l){var g=Date.now(),e=0;this.check=function(){var f;if(h&&(f=Date.now(),f-g>h))throw runtime.log("alert","watchdog timeout"),"timeout!";if(0<l&&(e+=1,e>l))throw runtime.log("alert","watchdog loop overflow"),"loop overflow";}};
// Input 9
runtime.loadClass("core.Selection");
core.Cursor=function(h,l){function g(f){var g=e.nextSibling,b=e.previousSibling,a=0;runtime.assert(Boolean(e.parentNode),"cursorNode.parentNode is undefined");b&&3===b.nodeType&&(a=b.length,g&&3===g.nodeType&&(0<b.length&&g.insertData(0,b.data),b.parentNode.removeChild(b)));e.parentNode.removeChild(e);f(g,a)}var e;this.getNode=function(){return e};this.getSelection=function(){return h};this.updateToSelection=function(f,n){e.parentNode&&g(f);if(h.focusNode){var b=h.focusNode,a=h.focusOffset;if(3===
b.nodeType){runtime.assert(Boolean(b),"putCursorIntoTextNode: invalid container");var c=b.parentNode;runtime.assert(Boolean(c),"putCursorIntoTextNode: container without parent");runtime.assert(0<=a&&a<=b.length,"putCursorIntoTextNode: offset is out of bounds");0===a?c.insertBefore(e,b):(a!==b.length&&b.splitText(a),c.insertBefore(e,b.nextSibling));n(e.nextSibling,a)}else if(1===b.nodeType){runtime.assert(Boolean(b),"putCursorIntoContainer: invalid container");for(c=b.firstChild;null!==c&&0<a;)c=c.nextSibling,
a-=1;b.insertBefore(e,c);n(e.nextSibling,0)}}};this.remove=function(e){g(e)};this.getPositionInContainer=function(f){var g,b;e.previousSibling&&3===e.previousSibling.nodeType?(g=e.previousSibling,b=g.length):e.nextSibling&&3===e.nextSibling.nodeType&&(g=e.nextSibling,b=0);if(!g){g=e.parentNode;b=e;for(var a=0;null!==b.previousSibling;)b=b.previousSibling,1===f.acceptNode(b)&&(a+=1);b=a}return{container:g,offset:b}};e=l.createElementNS("urn:webodf:names:cursor","cursor")};
// Input 10
/*

 Copyright (C) 2013 KO GmbH <copyright@kogmbh.com>

 @licstart
 The JavaScript code in this page is free software: you can redistribute it
 and/or modify it under the terms of the GNU Affero General Public License
 (GNU AGPL) as published by the Free Software Foundation, either version 3 of
 the License, or (at your option) any later version.  The code is distributed
 WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 FITNESS FOR A PARTICULAR PURPOSE.  See the GNU AGPL for more details.

 As additional permission under GNU AGPL version 3 section 7, you
 may distribute non-source (e.g., minimized or compacted) forms of
 that code without the copy of the GNU GPL normally required by
 section 4, provided you include this license notice and a URL
 through which recipients can access the Corresponding Source.

 As a special exception to the AGPL, any HTML file which merely makes function
 calls to this code, and for that purpose includes it by reference shall be
 deemed a separate work for copyright law purposes. In addition, the copyright
 holders of this code give you permission to combine this code with free
 software libraries that are released under the GNU LGPL. You may copy and
 distribute such a system following the terms of the GNU AGPL for this code
 and the LGPL for the libraries. If you modify this code, you may extend this
 exception to your version of the code, but you are not obligated to do so.
 If you do not wish to do so, delete this exception statement from your
 version.

 This license applies to this entire compilation.
 @licend
 @source: http://www.webodf.org/
 @source: http://gitorious.org/webodf/webodf/
*/
core.EventNotifier=function(h){var l={};this.emit=function(g,e){var f,h;runtime.assert(l.hasOwnProperty(g),'unknown event fired "'+g+'"');h=l[g];for(f=0;f<h.length;f+=1)h[f](e)};this.subscribe=function(g,e){runtime.assert(l.hasOwnProperty(g),'tried to subscribe to unknown event "'+g+'"');l[g].push(e);runtime.log('event "'+g+'" subscribed.')};(function(){var g;for(g=0;g<h.length;g+=1)l[h[g]]=[]})()};
// Input 11
core.UnitTest=function(){};core.UnitTest.prototype.setUp=function(){};core.UnitTest.prototype.tearDown=function(){};core.UnitTest.prototype.description=function(){};core.UnitTest.prototype.tests=function(){};core.UnitTest.prototype.asyncTests=function(){};
core.UnitTest.provideTestAreaDiv=function(){var h=runtime.getWindow().document,l=h.getElementById("testarea");runtime.assert(!l,'Unclean test environment, found a div with id "testarea".');l=h.createElement("div");l.setAttribute("id","testarea");h.body.appendChild(l);return l};
core.UnitTest.cleanupTestAreaDiv=function(){var h=runtime.getWindow().document,l=h.getElementById("testarea");runtime.assert(!!l&&l.parentNode===h.body,'Test environment broken, found no div with id "testarea" below body.');h.body.removeChild(l)};
core.UnitTestRunner=function(){function h(f){e+=1;runtime.log("fail",f)}function l(e,g){var b;try{if(e.length!==g.length)return!1;for(b=0;b<e.length;b+=1)if(e[b]!==g[b])return!1}catch(a){return!1}return!0}function g(e,g,b){("string"!==typeof g||"string"!==typeof b)&&runtime.log("WARN: shouldBe() expects string arguments");var a,c;try{c=eval(g)}catch(d){a=d}e=eval(b);a?h(g+" should be "+e+". Threw exception "+a):(0===e?c===e&&1/c===1/e:c===e||("number"===typeof e&&isNaN(e)?"number"===typeof c&&isNaN(c):
Object.prototype.toString.call(e)===Object.prototype.toString.call([])&&l(c,e)))?runtime.log("pass",g+" is "+b):String(typeof c)===String(typeof e)?(b=0===c&&0>1/c?"-0":String(c),h(g+" should be "+e+". Was "+b+".")):h(g+" should be "+e+" (of type "+typeof e+"). Was "+c+" (of type "+typeof c+").")}var e=0;this.shouldBeNull=function(e,h){g(e,h,"null")};this.shouldBeNonNull=function(e,g){var b,a;try{a=eval(g)}catch(c){b=c}b?h(g+" should be non-null. Threw exception "+b):null!==a?runtime.log("pass",g+
" is non-null."):h(g+" should be non-null. Was "+a)};this.shouldBe=g;this.countFailedTests=function(){return e}};
core.UnitTester=function(){function h(e,f){return"<span style='color:blue;cursor:pointer' onclick='"+f+"'>"+e+"</span>"}var l=0,g={};this.runTests=function(e,f,n){function b(k){if(0===k.length)g[a]=m,l+=c.countFailedTests(),f();else{p=k[0];var e=Runtime.getFunctionName(p);runtime.log("Running "+e);q=c.countFailedTests();d.setUp();p(function(){d.tearDown();m[e]=q===c.countFailedTests();b(k.slice(1))})}}var a=Runtime.getFunctionName(e),c=new core.UnitTestRunner,d=new e(c),m={},k,p,r,q,s="BrowserRuntime"===
runtime.type();if(g.hasOwnProperty(a))runtime.log("Test "+a+" has already run.");else{s?runtime.log("<span>Running "+h(a,'runSuite("'+a+'");')+": "+d.description()+"</span>"):runtime.log("Running "+a+": "+d.description);r=d.tests();for(k=0;k<r.length;k+=1)p=r[k],e=Runtime.getFunctionName(p)||p.testName,n.length&&-1===n.indexOf(e)||(s?runtime.log("<span>Running "+h(e,'runTest("'+a+'","'+e+'")')+"</span>"):runtime.log("Running "+e),q=c.countFailedTests(),d.setUp(),p(),d.tearDown(),m[e]=q===c.countFailedTests());
b(d.asyncTests())}};this.countFailedTests=function(){return l};this.results=function(){return g}};
// Input 12
core.PositionIterator=function(h,l,g,e){function f(){this.acceptNode=function(a){return 3===a.nodeType&&0===a.length?2:1}}function n(a){this.acceptNode=function(d){return 3===d.nodeType&&0===d.length?2:a.acceptNode(d)}}function b(){var a=c.currentNode.nodeType;d=3===a?c.currentNode.length-1:1===a?1:0}var a=this,c,d,m;this.nextPosition=function(){if(c.currentNode===h)return!1;0===d&&1===c.currentNode.nodeType?null===c.firstChild()&&(d=1):3===c.currentNode.nodeType&&d+1<c.currentNode.length?d+=1:null!==
c.nextSibling()?d=0:(c.parentNode(),d=1);return!0};this.previousPosition=function(){var a=!0;if(0===d)if(null===c.previousSibling()){c.parentNode();if(c.currentNode===h)return c.firstChild(),!1;d=0}else b();else 3===c.currentNode.nodeType?d-=1:null!==c.lastChild()?b():c.currentNode===h?a=!1:d=0;return a};this.container=function(){var a=c.currentNode,b=a.nodeType;return 0===d&&3!==b?a.parentNode:a};this.rightNode=function(){var a=c.currentNode,b=a.nodeType;if(3===b&&d===a.length)for(a=a.nextSibling;a&&
1!==m(a);)a=a.nextSibling;else 1===b&&1===d&&(a=null);return a};this.leftNode=function(){var a=c.currentNode;if(0===d)for(a=a.previousSibling;a&&1!==m(a);)a=a.previousSibling;else if(1===a.nodeType)for(a=a.lastChild;a&&1!==m(a);)a=a.previousSibling;return a};this.getCurrentNode=function(){return c.currentNode};this.offset=function(){if(3===c.currentNode.nodeType)return d;var a=0,b=c.currentNode,e,f;for(e=1===d?c.lastChild():c.previousSibling();e;){if(3!==e.nodeType||e.nextSibling!==f||3!==f.nodeType)a+=
1;f=e;e=c.previousSibling()}c.currentNode=b;return a};this.domOffset=function(){if(3===c.currentNode.nodeType)return d;var a=0,b=c.currentNode,e;for(e=1===d?c.lastChild():c.previousSibling();e;)a+=1,e=c.previousSibling();c.currentNode=b;return a};this.unfilteredDomOffset=function(){if(3===c.currentNode.nodeType)return d;for(var a=0,b=c.currentNode,b=1===d?b.lastChild:b.previousSibling;b;)a+=1,b=b.previousSibling;return a};this.textOffset=function(){if(3!==c.currentNode.nodeType)return 0;for(var a=
d,b=c.currentNode;c.previousSibling()&&3===c.currentNode.nodeType;)a+=c.currentNode.length;c.currentNode=b;return a};this.getPreviousSibling=function(){var a=c.currentNode,d=c.previousSibling();c.currentNode=a;return d};this.getNextSibling=function(){var a=c.currentNode,d=c.nextSibling();c.currentNode=a;return d};this.text=function(){var d,c="",b=a.textNeighborhood();for(d=0;d<b.length;d+=1)c+=b[d].data;return c};this.textNeighborhood=function(){var a=c.currentNode,d=[];if(3!==a.nodeType)return d;
for(;c.previousSibling();)if(3!==c.currentNode.nodeType){c.nextSibling();break}do d.push(c.currentNode);while(c.nextSibling()&&3===c.currentNode.nodeType);c.currentNode=a;return d};this.substr=function(d,c){return a.text().substr(d,c)};this.setPosition=function(a,b){runtime.assert(null!==a&&void 0!==a,"PositionIterator.setPosition called without container");c.currentNode=a;if(3===a.nodeType)return d=b,runtime.assert(b<=a.length,"Error in setPosition: "+b+" > "+a.length),runtime.assert(0<=b,"Error in setPosition: "+
b+" < 0"),b===a.length&&(d=void 0,c.nextSibling()?d=0:c.parentNode()&&(d=1),runtime.assert(void 0!==d,"Error in setPosition: position not valid.")),!0;for(var e=b,f=c.firstChild(),g;0<b&&f;){b-=1;g=f;for(f=c.nextSibling();f&&3===f.nodeType&&3===g.nodeType&&f.previousSibling===g;)g=f,f=c.nextSibling()}runtime.assert(0===b,"Error in setPosition: offset "+e+" is out of range.");null===f?(c.currentNode=a,d=1):d=0;return!0};this.moveToEnd=function(){c.currentNode=h;d=1};this.moveToEndOfNode=function(b){3===
b.nodeType?a.setPosition(b,b.length):(c.currentNode=b,d=1)};this.getNodeFilter=function(){return m};m=(g?new n(g):new f).acceptNode;m.acceptNode=m;c=h.ownerDocument.createTreeWalker(h,l||4294967295,m,e);d=0;null===c.firstChild()&&(d=1)};
// Input 13
runtime.loadClass("core.PositionIterator");core.PositionFilter=function(){};core.PositionFilter.FilterResult={FILTER_ACCEPT:1,FILTER_REJECT:2,FILTER_SKIP:3};core.PositionFilter.prototype.acceptPosition=function(h){};(function(){return core.PositionFilter})();
// Input 14
core.Async=function(){this.forEach=function(h,l,g){function e(a){b!==n&&(a?(b=n,g(a)):(b+=1,b===n&&g(null)))}var f,n=h.length,b=0;for(f=0;f<n;f+=1)l(h[f],e)}};
// Input 15
runtime.loadClass("core.RawInflate");runtime.loadClass("core.ByteArray");runtime.loadClass("core.ByteArrayWriter");runtime.loadClass("core.Base64");
core.Zip=function(h,l){function g(a){var d=[0,1996959894,3993919788,2567524794,124634137,1886057615,3915621685,2657392035,249268274,2044508324,3772115230,2547177864,162941995,2125561021,3887607047,2428444049,498536548,1789927666,4089016648,2227061214,450548861,1843258603,4107580753,2211677639,325883990,1684777152,4251122042,2321926636,335633487,1661365465,4195302755,2366115317,997073096,1281953886,3579855332,2724688242,1006888145,1258607687,3524101629,2768942443,901097722,1119000684,3686517206,2898065728,
853044451,1172266101,3705015759,2882616665,651767980,1373503546,3369554304,3218104598,565507253,1454621731,3485111705,3099436303,671266974,1594198024,3322730930,2970347812,795835527,1483230225,3244367275,3060149565,1994146192,31158534,2563907772,4023717930,1907459465,112637215,2680153253,3904427059,2013776290,251722036,2517215374,3775830040,2137656763,141376813,2439277719,3865271297,1802195444,476864866,2238001368,4066508878,1812370925,453092731,2181625025,4111451223,1706088902,314042704,2344532202,
4240017532,1658658271,366619977,2362670323,4224994405,1303535960,984961486,2747007092,3569037538,1256170817,1037604311,2765210733,3554079995,1131014506,879679996,2909243462,3663771856,1141124467,855842277,2852801631,3708648649,1342533948,654459306,3188396048,3373015174,1466479909,544179635,3110523913,3462522015,1591671054,702138776,2966460450,3352799412,1504918807,783551873,3082640443,3233442989,3988292384,2596254646,62317068,1957810842,3939845945,2647816111,81470997,1943803523,3814918930,2489596804,
225274430,2053790376,3826175755,2466906013,167816743,2097651377,4027552580,2265490386,503444072,1762050814,4150417245,2154129355,426522225,1852507879,4275313526,2312317920,282753626,1742555852,4189708143,2394877945,397917763,1622183637,3604390888,2714866558,953729732,1340076626,3518719985,2797360999,1068828381,1219638859,3624741850,2936675148,906185462,1090812512,3747672003,2825379669,829329135,1181335161,3412177804,3160834842,628085408,1382605366,3423369109,3138078467,570562233,1426400815,3317316542,
2998733608,733239954,1555261956,3268935591,3050360625,752459403,1541320221,2607071920,3965973030,1969922972,40735498,2617837225,3943577151,1913087877,83908371,2512341634,3803740692,2075208622,213261112,2463272603,3855990285,2094854071,198958881,2262029012,4057260610,1759359992,534414190,2176718541,4139329115,1873836001,414664567,2282248934,4279200368,1711684554,285281116,2405801727,4167216745,1634467795,376229701,2685067896,3608007406,1308918612,956543938,2808555105,3495958263,1231636301,1047427035,
2932959818,3654703836,1088359270,936918E3,2847714899,3736837829,1202900863,817233897,3183342108,3401237130,1404277552,615818150,3134207493,3453421203,1423857449,601450431,3009837614,3294710456,1567103746,711928724,3020668471,3272380065,1510334235,755167117],c,b,e=a.length,k=0,k=0;c=-1;for(b=0;b<e;b+=1)k=(c^a[b])&255,k=d[k],c=c>>>8^k;return c^-1}function e(a){return new Date((a>>25&127)+1980,(a>>21&15)-1,a>>16&31,a>>11&15,a>>5&63,(a&31)<<1)}function f(a){var d=a.getFullYear();return 1980>d?0:d-1980<<
25|a.getMonth()+1<<21|a.getDate()<<16|a.getHours()<<11|a.getMinutes()<<5|a.getSeconds()>>1}function n(a,d){var c,b,k,f,p,g,h,m=this;this.load=function(d){if(void 0!==m.data)d(null,m.data);else{var c=p+34+b+k+256;c+h>q&&(c=q-h);runtime.read(a,h,c,function(c,b){if(c||null===b)d(c,b);else a:{var e=b,k=new core.ByteArray(e),h=k.readUInt32LE(),r;if(67324752!==h)d("File entry signature is wrong."+h.toString()+" "+e.length.toString(),null);else{k.pos+=22;h=k.readUInt16LE();r=k.readUInt16LE();k.pos+=h+r;
if(f){e=e.slice(k.pos,k.pos+p);if(p!==e.length){d("The amount of compressed bytes read was "+e.length.toString()+" instead of "+p.toString()+" for "+m.filename+" in "+a+".",null);break a}e=v(e,g)}else e=e.slice(k.pos,k.pos+g);g!==e.length?d("The amount of bytes read was "+e.length.toString()+" instead of "+g.toString()+" for "+m.filename+" in "+a+".",null):(m.data=e,d(null,e))}}})}};this.set=function(a,d,c,b){m.filename=a;m.data=d;m.compressed=c;m.date=b};this.error=null;d&&(c=d.readUInt32LE(),33639248!==
c?this.error="Central directory entry has wrong signature at position "+(d.pos-4).toString()+' for file "'+a+'": '+d.data.length.toString():(d.pos+=6,f=d.readUInt16LE(),this.date=e(d.readUInt32LE()),d.readUInt32LE(),p=d.readUInt32LE(),g=d.readUInt32LE(),b=d.readUInt16LE(),k=d.readUInt16LE(),c=d.readUInt16LE(),d.pos+=8,h=d.readUInt32LE(),this.filename=runtime.byteArrayToString(d.data.slice(d.pos,d.pos+b),"utf8"),d.pos+=b+k+c))}function b(a,d){if(22!==a.length)d("Central directory length should be 22.",
w);else{var c=new core.ByteArray(a),b;b=c.readUInt32LE();101010256!==b?d("Central directory signature is wrong: "+b.toString(),w):(b=c.readUInt16LE(),0!==b?d("Zip files with non-zero disk numbers are not supported.",w):(b=c.readUInt16LE(),0!==b?d("Zip files with non-zero disk numbers are not supported.",w):(b=c.readUInt16LE(),s=c.readUInt16LE(),b!==s?d("Number of entries is inconsistent.",w):(b=c.readUInt32LE(),c=c.readUInt16LE(),c=q-22-b,runtime.read(h,c,q-c,function(a,c){if(a||null===c)d(a,w);else a:{var b=
new core.ByteArray(c),e,k;r=[];for(e=0;e<s;e+=1){k=new n(h,b);if(k.error){d(k.error,w);break a}r[r.length]=k}d(null,w)}})))))}}function a(a,d){var c=null,b,e;for(e=0;e<r.length;e+=1)if(b=r[e],b.filename===a){c=b;break}c?c.data?d(null,c.data):c.load(d):d(a+" not found.",null)}function c(a){var d=new core.ByteArrayWriter("utf8"),c=0;d.appendArray([80,75,3,4,20,0,0,0,0,0]);a.data&&(c=a.data.length);d.appendUInt32LE(f(a.date));d.appendUInt32LE(g(a.data));d.appendUInt32LE(c);d.appendUInt32LE(c);d.appendUInt16LE(a.filename.length);
d.appendUInt16LE(0);d.appendString(a.filename);a.data&&d.appendByteArray(a.data);return d}function d(a,d){var c=new core.ByteArrayWriter("utf8"),b=0;c.appendArray([80,75,1,2,20,0,20,0,0,0,0,0]);a.data&&(b=a.data.length);c.appendUInt32LE(f(a.date));c.appendUInt32LE(g(a.data));c.appendUInt32LE(b);c.appendUInt32LE(b);c.appendUInt16LE(a.filename.length);c.appendArray([0,0,0,0,0,0,0,0,0,0,0,0]);c.appendUInt32LE(d);c.appendString(a.filename);return c}function m(a,d){if(a===r.length)d(null);else{var c=r[a];
void 0!==c.data?m(a+1,d):c.load(function(c){c?d(c):m(a+1,d)})}}function k(a,b){m(0,function(e){if(e)b(e);else{e=new core.ByteArrayWriter("utf8");var k,f,p,g=[0];for(k=0;k<r.length;k+=1)e.appendByteArrayWriter(c(r[k])),g.push(e.getLength());p=e.getLength();for(k=0;k<r.length;k+=1)f=r[k],e.appendByteArrayWriter(d(f,g[k]));k=e.getLength()-p;e.appendArray([80,75,5,6,0,0,0,0]);e.appendUInt16LE(r.length);e.appendUInt16LE(r.length);e.appendUInt32LE(k);e.appendUInt32LE(p);e.appendArray([0,0]);a(e.getByteArray())}})}
function p(a,d){k(function(c){runtime.writeFile(a,c,d)},d)}var r,q,s,v=(new core.RawInflate).inflate,w=this,F=new core.Base64;this.load=a;this.save=function(a,d,c,b){var e,k;for(e=0;e<r.length;e+=1)if(k=r[e],k.filename===a){k.set(a,d,c,b);return}k=new n(h);k.set(a,d,c,b);r.push(k)};this.write=function(a){p(h,a)};this.writeAs=p;this.createByteArray=k;this.loadContentXmlAsFragments=function(a,d){w.loadAsString(a,function(a,c){if(a)return d.rootElementReady(a);d.rootElementReady(null,c,!0)})};this.loadAsString=
function(d,c){a(d,function(a,d){if(a||null===d)return c(a,null);var b=runtime.byteArrayToString(d,"utf8");c(null,b)})};this.loadAsDOM=function(a,d){w.loadAsString(a,function(a,c){if(a||null===c)d(a,null);else{var b=(new DOMParser).parseFromString(c,"text/xml");d(null,b)}})};this.loadAsDataURL=function(d,c,b){a(d,function(a,d){if(a)return b(a,null);var e=0,k;c||(c=80===d[1]&&78===d[2]&&71===d[3]?"image/png":255===d[0]&&216===d[1]&&255===d[2]?"image/jpeg":71===d[0]&&73===d[1]&&70===d[2]?"image/gif":
"");for(k="data:"+c+";base64,";e<d.length;)k+=F.convertUTF8ArrayToBase64(d.slice(e,Math.min(e+45E3,d.length))),e+=45E3;b(null,k)})};this.getEntries=function(){return r.slice()};q=-1;null===l?r=[]:runtime.getFileSize(h,function(a){q=a;0>q?l("File '"+h+"' cannot be read.",w):runtime.read(h,q-22,22,function(a,d){a||null===l||null===d?l(a,w):b(d,l)})})};
// Input 16
core.CSSUnits=function(){var h={"in":1,cm:2.54,mm:25.4,pt:72,pc:12};this.convert=function(l,g,e){return l*h[e]/h[g]};this.convertMeasure=function(h,g){var e,f;h&&g?(e=parseFloat(h),f=h.replace(e.toString(),""),e=this.convert(e,f,g)):e="";return e.toString()}};
// Input 17
xmldom.LSSerializerFilter=function(){};
// Input 18
"function"!==typeof Object.create&&(Object.create=function(h){var l=function(){};l.prototype=h;return new l});
xmldom.LSSerializer=function(){function h(e){var f=e||{},g=function(a){var c={},b;for(b in a)a.hasOwnProperty(b)&&(c[a[b]]=b);return c}(e),b=[f],a=[g],c=0;this.push=function(){c+=1;f=b[c]=Object.create(f);g=a[c]=Object.create(g)};this.pop=function(){b[c]=void 0;a[c]=void 0;c-=1;f=b[c];g=a[c]};this.getLocalNamespaceDefinitions=function(){return g};this.getQName=function(a){var c=a.namespaceURI,b=0,e;if(!c)return a.localName;if(e=g[c])return e+":"+a.localName;do{e||!a.prefix?(e="ns"+b,b+=1):e=a.prefix;
if(f[e]===c)break;if(!f[e]){f[e]=c;g[c]=e;break}e=null}while(null===e);return e+":"+a.localName}}function l(e,f){var h="",b=g.filter?g.filter.acceptNode(f):1,a;if(1===b&&1===f.nodeType){e.push();a=e.getQName(f);var c,d=f.attributes,m,k,p,r="",q;c="<"+a;m=d.length;for(k=0;k<m;k+=1)p=d.item(k),"http://www.w3.org/2000/xmlns/"!==p.namespaceURI&&(q=g.filter?g.filter.acceptNode(p):1,1===q&&(q=e.getQName(p),r+=" "+(q+'="'+p.value+'"')));m=e.getLocalNamespaceDefinitions();for(k in m)m.hasOwnProperty(k)&&
((d=m[k])?"xmlns"!==d&&(c+=" xmlns:"+m[k]+'="'+k+'"'):c+=' xmlns="'+k+'"');h+=c+(r+">")}if(1===b||3===b){for(b=f.firstChild;b;)h+=l(e,b),b=b.nextSibling;f.nodeValue&&(h+=f.nodeValue)}a&&(h+="</"+a+">",e.pop());return h}var g=this;this.filter=null;this.writeToString=function(e,f){if(!e)return"";var g=new h(f);return l(g,e)}};
// Input 19
xmldom.RelaxNGParser=function(){function h(a,c){this.message=function(){c&&(a+=1===c.nodeType?" Element ":" Node ",a+=c.nodeName,c.nodeValue&&(a+=" with value '"+c.nodeValue+"'"),a+=".");return a}}function l(a){if(2>=a.e.length)return a;var c={name:a.name,e:a.e.slice(0,2)};return l({name:a.name,e:[c].concat(a.e.slice(2))})}function g(d){d=d.split(":",2);var c="",b;1===d.length?d=["",d[0]]:c=d[0];for(b in a)a[b]===c&&(d[0]=b);return d}function e(a,c){for(var b=0,f,h,q=a.name;a.e&&b<a.e.length;)if(f=
a.e[b],"ref"===f.name){h=c[f.a.name];if(!h)throw f.a.name+" was not defined.";f=a.e.slice(b+1);a.e=a.e.slice(0,b);a.e=a.e.concat(h.e);a.e=a.e.concat(f)}else b+=1,e(f,c);f=a.e;if("choice"===q&&(!f||!f[1]||"empty"===f[1].name))!f||!f[0]||"empty"===f[0].name?(delete a.e,a.name="empty"):(f[1]=f[0],f[0]={name:"empty"});if("group"===q||"interleave"===q)"empty"===f[0].name?"empty"===f[1].name?(delete a.e,a.name="empty"):(q=a.name=f[1].name,a.names=f[1].names,f=a.e=f[1].e):"empty"===f[1].name&&(q=a.name=
f[0].name,a.names=f[0].names,f=a.e=f[0].e);"oneOrMore"===q&&"empty"===f[0].name&&(delete a.e,a.name="empty");if("attribute"===q){h=a.names?a.names.length:0;for(var s,v=a.localnames=[h],l=a.namespaces=[h],b=0;b<h;b+=1)s=g(a.names[b]),l[b]=s[0],v[b]=s[1]}"interleave"===q&&("interleave"===f[0].name?a.e="interleave"===f[1].name?f[0].e.concat(f[1].e):[f[1]].concat(f[0].e):"interleave"===f[1].name&&(a.e=[f[0]].concat(f[1].e)))}function f(a,c){for(var b=0,e;a.e&&b<a.e.length;)e=a.e[b],"elementref"===e.name?
(e.id=e.id||0,a.e[b]=c[e.id]):"element"!==e.name&&f(e,c),b+=1}var n=this,b,a={"http://www.w3.org/XML/1998/namespace":"xml"},c;c=function(b,e,k){var f=[],h,q,s=b.localName,v=[];h=b.attributes;var n=s,F=v,z={},u,y;for(u=0;u<h.length;u+=1)if(y=h.item(u),y.namespaceURI)"http://www.w3.org/2000/xmlns/"===y.namespaceURI&&(a[y.value]=y.localName);else{"name"===y.localName&&("element"===n||"attribute"===n)&&F.push(y.value);if("name"===y.localName||"combine"===y.localName||"type"===y.localName){var K=y,H;H=
y.value;H=H.replace(/^\s\s*/,"");for(var B=/\s/,L=H.length-1;B.test(H.charAt(L));)L-=1;H=H.slice(0,L+1);K.value=H}z[y.localName]=y.value}h=z;h.combine=h.combine||void 0;b=b.firstChild;n=f;F=v;for(z="";b;){if(1===b.nodeType&&"http://relaxng.org/ns/structure/1.0"===b.namespaceURI){if(u=c(b,e,n))"name"===u.name?F.push(a[u.a.ns]+":"+u.text):"choice"===u.name&&(u.names&&u.names.length)&&(F=F.concat(u.names),delete u.names),n.push(u)}else 3===b.nodeType&&(z+=b.nodeValue);b=b.nextSibling}b=z;"value"!==s&&
"param"!==s&&(b=/^\s*([\s\S]*\S)?\s*$/.exec(b)[1]);"value"===s&&void 0===h.type&&(h.type="token",h.datatypeLibrary="");if(("attribute"===s||"element"===s)&&void 0!==h.name)q=g(h.name),f=[{name:"name",text:q[1],a:{ns:q[0]}}].concat(f),delete h.name;"name"===s||"nsName"===s||"value"===s?void 0===h.ns&&(h.ns=""):delete h.ns;"name"===s&&(q=g(b),h.ns=q[0],b=q[1]);if(1<f.length&&("define"===s||"oneOrMore"===s||"zeroOrMore"===s||"optional"===s||"list"===s||"mixed"===s))f=[{name:"group",e:l({name:"group",
e:f}).e}];2<f.length&&"element"===s&&(f=[f[0]].concat({name:"group",e:l({name:"group",e:f.slice(1)}).e}));1===f.length&&"attribute"===s&&f.push({name:"text",text:b});if(1===f.length&&("choice"===s||"group"===s||"interleave"===s))s=f[0].name,v=f[0].names,h=f[0].a,b=f[0].text,f=f[0].e;else if(2<f.length&&("choice"===s||"group"===s||"interleave"===s))f=l({name:s,e:f}).e;"mixed"===s&&(s="interleave",f=[f[0],{name:"text"}]);"optional"===s&&(s="choice",f=[f[0],{name:"empty"}]);"zeroOrMore"===s&&(s="choice",
f=[{name:"oneOrMore",e:[f[0]]},{name:"empty"}]);if("define"===s&&h.combine){a:{n=h.combine;F=h.name;z=f;for(u=0;k&&u<k.length;u+=1)if(y=k[u],"define"===y.name&&y.a&&y.a.name===F){y.e=[{name:n,e:y.e.concat(z)}];k=y;break a}k=null}if(k)return}k={name:s};f&&0<f.length&&(k.e=f);for(q in h)if(h.hasOwnProperty(q)){k.a=h;break}void 0!==b&&(k.text=b);v&&0<v.length&&(k.names=v);"element"===s&&(k.id=e.length,e.push(k),k={name:"elementref",id:k.id});return k};this.parseRelaxNGDOM=function(d,g){var k=[],p=c(d&&
d.documentElement,k,void 0),r,q,s={};for(r=0;r<p.e.length;r+=1)q=p.e[r],"define"===q.name?s[q.a.name]=q:"start"===q.name&&(b=q);if(!b)return[new h("No Relax NG start element was found.")];e(b,s);for(r in s)s.hasOwnProperty(r)&&e(s[r],s);for(r=0;r<k.length;r+=1)e(k[r],s);g&&(n.rootPattern=g(b.e[0],k));f(b,k);for(r=0;r<k.length;r+=1)f(k[r],k);n.start=b;n.elements=k;n.nsmap=a;return null}};
// Input 20
runtime.loadClass("xmldom.RelaxNGParser");
xmldom.RelaxNG=function(){function h(a){return function(){var c;return function(){void 0===c&&(c=a());return c}}()}function l(a,c){return function(){var b={},d=0;return function(e){var f=e.hash||e.toString(),k;k=b[f];if(void 0!==k)return k;b[f]=k=c(e);k.hash=a+d.toString();d+=1;return k}}()}function g(a){return function(){var c={};return function(b){var d,e;e=c[b.localName];if(void 0===e)c[b.localName]=e={};else if(d=e[b.namespaceURI],void 0!==d)return d;return e[b.namespaceURI]=d=a(b)}}()}function e(a,
c,b){return function(){var d={},e=0;return function(f,k){var g=c&&c(f,k),h,p;if(void 0!==g)return g;g=f.hash||f.toString();h=k.hash||k.toString();p=d[g];if(void 0===p)d[g]=p={};else if(g=p[h],void 0!==g)return g;p[h]=g=b(f,k);g.hash=a+e.toString();e+=1;return g}}()}function f(a,c){"choice"===c.p1.type?f(a,c.p1):a[c.p1.hash]=c.p1;"choice"===c.p2.type?f(a,c.p2):a[c.p2.hash]=c.p2}function n(a,c){return{type:"element",nc:a,nullable:!1,textDeriv:function(){return u},startTagOpenDeriv:function(b){return a.contains(b)?
r(c,y):u},attDeriv:function(a,c){return u},startTagCloseDeriv:function(){return this}}}function b(){return{type:"list",nullable:!1,hash:"list",textDeriv:function(a,c){return y}}}function a(c,b,e,f){if(b===u)return u;if(f>=e.length)return b;0===f&&(f=0);for(var k=e.item(f);k.namespaceURI===d;){f+=1;if(f>=e.length)return b;k=e.item(f)}return k=a(c,b.attDeriv(c,e.item(f)),e,f+1)}function c(a,b,d){d.e[0].a?(a.push(d.e[0].text),b.push(d.e[0].a.ns)):c(a,b,d.e[0]);d.e[1].a?(a.push(d.e[1].text),b.push(d.e[1].a.ns)):
c(a,b,d.e[1])}var d="http://www.w3.org/2000/xmlns/",m,k,p,r,q,s,v,w,F,z,u={type:"notAllowed",nullable:!1,hash:"notAllowed",textDeriv:function(){return u},startTagOpenDeriv:function(){return u},attDeriv:function(){return u},startTagCloseDeriv:function(){return u},endTagDeriv:function(){return u}},y={type:"empty",nullable:!0,hash:"empty",textDeriv:function(){return u},startTagOpenDeriv:function(){return u},attDeriv:function(a,c){return u},startTagCloseDeriv:function(){return y},endTagDeriv:function(){return u}},
K={type:"text",nullable:!0,hash:"text",textDeriv:function(){return K},startTagOpenDeriv:function(){return u},attDeriv:function(){return u},startTagCloseDeriv:function(){return K},endTagDeriv:function(){return u}},H,B,L;m=e("choice",function(a,c){if(a===u)return c;if(c===u||a===c)return a},function(a,c){var b={},d;f(b,{p1:a,p2:c});c=a=void 0;for(d in b)b.hasOwnProperty(d)&&(void 0===a?a=b[d]:c=void 0===c?b[d]:m(c,b[d]));return function(a,c){return{type:"choice",p1:a,p2:c,nullable:a.nullable||c.nullable,
textDeriv:function(b,d){return m(a.textDeriv(b,d),c.textDeriv(b,d))},startTagOpenDeriv:g(function(b){return m(a.startTagOpenDeriv(b),c.startTagOpenDeriv(b))}),attDeriv:function(b,d){return m(a.attDeriv(b,d),c.attDeriv(b,d))},startTagCloseDeriv:h(function(){return m(a.startTagCloseDeriv(),c.startTagCloseDeriv())}),endTagDeriv:h(function(){return m(a.endTagDeriv(),c.endTagDeriv())})}}(a,c)});k=function(a,c,b){return function(){var d={},e=0;return function(f,k){var g=c&&c(f,k),h,p;if(void 0!==g)return g;
g=f.hash||f.toString();h=k.hash||k.toString();g<h&&(p=g,g=h,h=p,p=f,f=k,k=p);p=d[g];if(void 0===p)d[g]=p={};else if(g=p[h],void 0!==g)return g;p[h]=g=b(f,k);g.hash=a+e.toString();e+=1;return g}}()}("interleave",function(a,c){if(a===u||c===u)return u;if(a===y)return c;if(c===y)return a},function(a,c){return{type:"interleave",p1:a,p2:c,nullable:a.nullable&&c.nullable,textDeriv:function(b,d){return m(k(a.textDeriv(b,d),c),k(a,c.textDeriv(b,d)))},startTagOpenDeriv:g(function(b){return m(H(function(a){return k(a,
c)},a.startTagOpenDeriv(b)),H(function(c){return k(a,c)},c.startTagOpenDeriv(b)))}),attDeriv:function(b,d){return m(k(a.attDeriv(b,d),c),k(a,c.attDeriv(b,d)))},startTagCloseDeriv:h(function(){return k(a.startTagCloseDeriv(),c.startTagCloseDeriv())})}});p=e("group",function(a,c){if(a===u||c===u)return u;if(a===y)return c;if(c===y)return a},function(a,c){return{type:"group",p1:a,p2:c,nullable:a.nullable&&c.nullable,textDeriv:function(b,d){var e=p(a.textDeriv(b,d),c);return a.nullable?m(e,c.textDeriv(b,
d)):e},startTagOpenDeriv:function(b){var d=H(function(a){return p(a,c)},a.startTagOpenDeriv(b));return a.nullable?m(d,c.startTagOpenDeriv(b)):d},attDeriv:function(b,d){return m(p(a.attDeriv(b,d),c),p(a,c.attDeriv(b,d)))},startTagCloseDeriv:h(function(){return p(a.startTagCloseDeriv(),c.startTagCloseDeriv())})}});r=e("after",function(a,c){if(a===u||c===u)return u},function(a,c){return{type:"after",p1:a,p2:c,nullable:!1,textDeriv:function(b,d){return r(a.textDeriv(b,d),c)},startTagOpenDeriv:g(function(b){return H(function(a){return r(a,
c)},a.startTagOpenDeriv(b))}),attDeriv:function(b,d){return r(a.attDeriv(b,d),c)},startTagCloseDeriv:h(function(){return r(a.startTagCloseDeriv(),c)}),endTagDeriv:h(function(){return a.nullable?c:u})}});q=l("oneormore",function(a){return a===u?u:{type:"oneOrMore",p:a,nullable:a.nullable,textDeriv:function(c,b){return p(a.textDeriv(c,b),m(this,y))},startTagOpenDeriv:function(c){var b=this;return H(function(a){return p(a,m(b,y))},a.startTagOpenDeriv(c))},attDeriv:function(c,b){return p(a.attDeriv(c,
b),m(this,y))},startTagCloseDeriv:h(function(){return q(a.startTagCloseDeriv())})}});v=e("attribute",void 0,function(a,c){return{type:"attribute",nullable:!1,nc:a,p:c,attDeriv:function(b,d){return a.contains(d)&&(c.nullable&&/^\s+$/.test(d.nodeValue)||c.textDeriv(b,d.nodeValue).nullable)?y:u},startTagCloseDeriv:function(){return u}}});s=l("value",function(a){return{type:"value",nullable:!1,value:a,textDeriv:function(c,b){return b===a?y:u},attDeriv:function(){return u},startTagCloseDeriv:function(){return this}}});
F=l("data",function(a){return{type:"data",nullable:!1,dataType:a,textDeriv:function(){return y},attDeriv:function(){return u},startTagCloseDeriv:function(){return this}}});H=function E(a,c){return"after"===c.type?r(c.p1,a(c.p2)):"choice"===c.type?m(E(a,c.p1),E(a,c.p2)):c};B=function(c,b,d){var e=d.currentNode;b=b.startTagOpenDeriv(e);b=a(c,b,e.attributes,0);var f=b=b.startTagCloseDeriv(),e=d.currentNode;b=d.firstChild();for(var k=[],g;b;)1===b.nodeType?k.push(b):3===b.nodeType&&!/^\s*$/.test(b.nodeValue)&&
k.push(b.nodeValue),b=d.nextSibling();0===k.length&&(k=[""]);g=f;for(f=0;g!==u&&f<k.length;f+=1)b=k[f],"string"===typeof b?g=/^\s*$/.test(b)?m(g,g.textDeriv(c,b)):g.textDeriv(c,b):(d.currentNode=b,g=B(c,g,d));d.currentNode=e;return b=g.endTagDeriv()};w=function(a){var b,d,e;if("name"===a.name)b=a.text,d=a.a.ns,a={name:b,ns:d,hash:"{"+d+"}"+b,contains:function(a){return a.namespaceURI===d&&a.localName===b}};else if("choice"===a.name){b=[];d=[];c(b,d,a);a="";for(e=0;e<b.length;e+=1)a+="{"+d[e]+"}"+
b[e]+",";a={hash:a,contains:function(a){var c;for(c=0;c<b.length;c+=1)if(b[c]===a.localName&&d[c]===a.namespaceURI)return!0;return!1}}}else a={hash:"anyName",contains:function(){return!0}};return a};z=function D(a,c){var d,e;if("elementref"===a.name){d=a.id||0;a=c[d];if(void 0!==a.name){var f=a;d=c[f.id]={hash:"element"+f.id.toString()};f=n(w(f.e[0]),z(f.e[1],c));for(e in f)f.hasOwnProperty(e)&&(d[e]=f[e]);return d}return a}switch(a.name){case "empty":return y;case "notAllowed":return u;case "text":return K;
case "choice":return m(D(a.e[0],c),D(a.e[1],c));case "interleave":d=D(a.e[0],c);for(e=1;e<a.e.length;e+=1)d=k(d,D(a.e[e],c));return d;case "group":return p(D(a.e[0],c),D(a.e[1],c));case "oneOrMore":return q(D(a.e[0],c));case "attribute":return v(w(a.e[0]),D(a.e[1],c));case "value":return s(a.text);case "data":return d=a.a&&a.a.type,void 0===d&&(d=""),F(d);case "list":return b()}throw"No support for "+a.name;};this.makePattern=function(a,c){var b={},d;for(d in c)c.hasOwnProperty(d)&&(b[d]=c[d]);return d=
z(a,b)};this.validate=function(a,c){var b;a.currentNode=a.root;b=B(null,L,a);b.nullable?c(null):(runtime.log("Error in Relax NG validation: "+b),c(["Error in Relax NG validation: "+b]))};this.init=function(a){L=a}};
// Input 21
runtime.loadClass("xmldom.RelaxNGParser");
xmldom.RelaxNG2=function(){function h(b,a){this.message=function(){a&&(b+=1===a.nodeType?" Element ":" Node ",b+=a.nodeName,a.nodeValue&&(b+=" with value '"+a.nodeValue+"'"),b+=".");return b}}function l(b,a,c,d){return"empty"===b.name?null:f(b,a,c,d)}function g(b,a,c){if(2!==b.e.length)throw"Element with wrong # of elements: "+b.e.length;for(var d=(c=a.currentNode)?c.nodeType:0,e=null;1<d;){if(8!==d&&(3!==d||!/^\s+$/.test(a.currentNode.nodeValue)))return[new h("Not allowed node of type "+d+".")];
d=(c=a.nextSibling())?c.nodeType:0}if(!c)return[new h("Missing element "+b.names)];if(b.names&&-1===b.names.indexOf(n[c.namespaceURI]+":"+c.localName))return[new h("Found "+c.nodeName+" instead of "+b.names+".",c)];if(a.firstChild()){for(e=l(b.e[1],a,c);a.nextSibling();)if(d=a.currentNode.nodeType,(!a.currentNode||!(3===a.currentNode.nodeType&&/^\s+$/.test(a.currentNode.nodeValue)))&&8!==d)return[new h("Spurious content.",a.currentNode)];if(a.parentNode()!==c)return[new h("Implementation error.")]}else e=
l(b.e[1],a,c);a.nextSibling();return e}var e,f,n;f=function(b,a,c,d){var e=b.name,k=null;if("text"===e)a:{for(var p=(b=a.currentNode)?b.nodeType:0;b!==c&&3!==p;){if(1===p){k=[new h("Element not allowed here.",b)];break a}p=(b=a.nextSibling())?b.nodeType:0}a.nextSibling();k=null}else if("data"===e)k=null;else if("value"===e)d!==b.text&&(k=[new h("Wrong value, should be '"+b.text+"', not '"+d+"'",c)]);else if("list"===e)k=null;else if("attribute"===e)a:{if(2!==b.e.length)throw"Attribute with wrong # of elements: "+
b.e.length;e=b.localnames.length;for(k=0;k<e;k+=1){d=c.getAttributeNS(b.namespaces[k],b.localnames[k]);""===d&&!c.hasAttributeNS(b.namespaces[k],b.localnames[k])&&(d=void 0);if(void 0!==p&&void 0!==d){k=[new h("Attribute defined too often.",c)];break a}p=d}k=void 0===p?[new h("Attribute not found: "+b.names,c)]:l(b.e[1],a,c,p)}else if("element"===e)k=g(b,a,c);else if("oneOrMore"===e){d=0;do p=a.currentNode,e=f(b.e[0],a,c),d+=1;while(!e&&p!==a.currentNode);1<d?(a.currentNode=p,k=null):k=e}else if("choice"===
e){if(2!==b.e.length)throw"Choice with wrong # of options: "+b.e.length;p=a.currentNode;if("empty"===b.e[0].name){if(e=f(b.e[1],a,c,d))a.currentNode=p;k=null}else{if(e=l(b.e[0],a,c,d))a.currentNode=p,e=f(b.e[1],a,c,d);k=e}}else if("group"===e){if(2!==b.e.length)throw"Group with wrong # of members: "+b.e.length;k=f(b.e[0],a,c)||f(b.e[1],a,c)}else if("interleave"===e)a:{p=b.e.length;d=[p];for(var r=p,q,s,v,n;0<r;){q=0;s=a.currentNode;for(k=0;k<p;k+=1)v=a.currentNode,!0!==d[k]&&d[k]!==v&&(n=b.e[k],(e=
f(n,a,c))?(a.currentNode=v,void 0===d[k]&&(d[k]=!1)):v===a.currentNode||"oneOrMore"===n.name||"choice"===n.name&&("oneOrMore"===n.e[0].name||"oneOrMore"===n.e[1].name)?(q+=1,d[k]=v):(q+=1,d[k]=!0));if(s===a.currentNode&&q===r){k=null;break a}if(0===q){for(k=0;k<p;k+=1)if(!1===d[k]){k=[new h("Interleave does not match.",c)];break a}k=null;break a}for(k=r=0;k<p;k+=1)!0!==d[k]&&(r+=1)}k=null}else throw e+" not allowed in nonEmptyPattern.";return k};this.validate=function(b,a){b.currentNode=b.root;var c=
l(e.e[0],b,b.root);a(c)};this.init=function(b,a){e=b;n=a}};
// Input 22
xmldom.OperationalTransformInterface=function(){};xmldom.OperationalTransformInterface.prototype.retain=function(h){};xmldom.OperationalTransformInterface.prototype.insertCharacters=function(h){};xmldom.OperationalTransformInterface.prototype.insertElementStart=function(h,l){};xmldom.OperationalTransformInterface.prototype.insertElementEnd=function(){};xmldom.OperationalTransformInterface.prototype.deleteCharacters=function(h){};xmldom.OperationalTransformInterface.prototype.deleteElementStart=function(){};
xmldom.OperationalTransformInterface.prototype.deleteElementEnd=function(){};xmldom.OperationalTransformInterface.prototype.replaceAttributes=function(h){};xmldom.OperationalTransformInterface.prototype.updateAttributes=function(h){};
// Input 23
xmldom.OperationalTransformDOM=function(h,l){this.retain=function(g){};this.insertCharacters=function(g){};this.insertElementStart=function(g,e){};this.insertElementEnd=function(){};this.deleteCharacters=function(g){};this.deleteElementStart=function(){};this.deleteElementEnd=function(){};this.replaceAttributes=function(g){};this.updateAttributes=function(g){};this.atEnd=function(){return!0}};
// Input 24
xmldom.XPathIterator=function(){};
xmldom.XPath=function(){function h(a,c,b){return-1!==a&&(a<c||-1===c)&&(a<b||-1===b)}function l(a){for(var c=[],b=0,d=a.length,e;b<d;){var f=a,g=d,l=c,n="",u=[],y=f.indexOf("[",b),K=f.indexOf("/",b),H=f.indexOf("=",b);h(K,y,H)?(n=f.substring(b,K),b=K+1):h(y,K,H)?(n=f.substring(b,y),b=m(f,y,u)):h(H,K,y)?(n=f.substring(b,H),b=H):(n=f.substring(b,g),b=g);l.push({location:n,predicates:u});if(b<d&&"="===a[b]){e=a.substring(b+1,d);if(2<e.length&&("'"===e[0]||'"'===e[0]))e=e.slice(1,e.length-1);else try{e=
parseInt(e,10)}catch(B){}b=d}}return{steps:c,value:e}}function g(){var a,c=!1;this.setNode=function(c){a=c};this.reset=function(){c=!1};this.next=function(){var b=c?null:a;c=!0;return b}}function e(a,c,b){this.reset=function(){a.reset()};this.next=function(){for(var d=a.next();d&&!(d=d.getAttributeNodeNS(c,b));)d=a.next();return d}}function f(a,c){var b=a.next(),d=null;this.reset=function(){a.reset();b=a.next();d=null};this.next=function(){for(;b;){if(d)if(c&&d.firstChild)d=d.firstChild;else{for(;!d.nextSibling&&
d!==b;)d=d.parentNode;d===b?b=a.next():d=d.nextSibling}else{do(d=b.firstChild)||(b=a.next());while(b&&!d)}if(d&&1===d.nodeType)return d}return null}}function n(a,c){this.reset=function(){a.reset()};this.next=function(){for(var b=a.next();b&&!c(b);)b=a.next();return b}}function b(a,c,b){c=c.split(":",2);var d=b(c[0]),e=c[1];return new n(a,function(a){return a.localName===e&&a.namespaceURI===d})}function a(a,c,b){var e=new g,f=d(e,c,b),h=c.value;return void 0===h?new n(a,function(a){e.setNode(a);f.reset();
return f.next()}):new n(a,function(a){e.setNode(a);f.reset();return(a=f.next())&&a.nodeValue===h})}function c(a,c,b){var e=a.ownerDocument,f=[],h=null;if(!e||!e.evaluate){f=new g;f.setNode(a);a=l(c);f=d(f,a,b);a=[];for(b=f.next();b;)a.push(b),b=f.next();f=a}else{b=e.evaluate(c,a,b,XPathResult.UNORDERED_NODE_ITERATOR_TYPE,null);for(h=b.iterateNext();null!==h;)1===h.nodeType&&f.push(h),h=b.iterateNext()}return f}var d,m;m=function(a,c,b){for(var d=c,e=a.length,f=0;d<e;)"]"===a[d]?(f-=1,0>=f&&b.push(l(a.substring(c,
d)))):"["===a[d]&&(0>=f&&(c=d+1),f+=1),d+=1;return d};xmldom.XPathIterator.prototype.next=function(){};xmldom.XPathIterator.prototype.reset=function(){};d=function(c,d,g){var h,m,v,l;for(h=0;h<d.steps.length;h+=1){v=d.steps[h];m=v.location;""===m?c=new f(c,!1):"@"===m[0]?(l=m.slice(1).split(":",2),c=new e(c,g(l[0]),l[1])):"."!==m&&(c=new f(c,!1),-1!==m.indexOf(":")&&(c=b(c,m,g)));for(m=0;m<v.predicates.length;m+=1)l=v.predicates[m],c=a(c,l,g)}return c};xmldom.XPath=function(){this.getODFElementsWithXPath=
c};return xmldom.XPath}();
// Input 25
/*

 Copyright (C) 2012-2013 KO GmbH <copyright@kogmbh.com>

 @licstart
 The JavaScript code in this page is free software: you can redistribute it
 and/or modify it under the terms of the GNU Affero General Public License
 (GNU AGPL) as published by the Free Software Foundation, either version 3 of
 the License, or (at your option) any later version.  The code is distributed
 WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 FITNESS FOR A PARTICULAR PURPOSE.  See the GNU AGPL for more details.

 As additional permission under GNU AGPL version 3 section 7, you
 may distribute non-source (e.g., minimized or compacted) forms of
 that code without the copy of the GNU GPL normally required by
 section 4, provided you include this license notice and a URL
 through which recipients can access the Corresponding Source.

 As a special exception to the AGPL, any HTML file which merely makes function
 calls to this code, and for that purpose includes it by reference shall be
 deemed a separate work for copyright law purposes. In addition, the copyright
 holders of this code give you permission to combine this code with free
 software libraries that are released under the GNU LGPL. You may copy and
 distribute such a system following the terms of the GNU AGPL for this code
 and the LGPL for the libraries. If you modify this code, you may extend this
 exception to your version of the code, but you are not obligated to do so.
 If you do not wish to do so, delete this exception statement from your
 version.

 This license applies to this entire compilation.
 @licend
 @source: http://www.webodf.org/
 @source: http://gitorious.org/webodf/webodf/
*/
odf.Namespaces=function(){function h(e){return l[e]||null}var l={draw:"urn:oasis:names:tc:opendocument:xmlns:drawing:1.0",fo:"urn:oasis:names:tc:opendocument:xmlns:xsl-fo-compatible:1.0",office:"urn:oasis:names:tc:opendocument:xmlns:office:1.0",presentation:"urn:oasis:names:tc:opendocument:xmlns:presentation:1.0",style:"urn:oasis:names:tc:opendocument:xmlns:style:1.0",svg:"urn:oasis:names:tc:opendocument:xmlns:svg-compatible:1.0",table:"urn:oasis:names:tc:opendocument:xmlns:table:1.0",text:"urn:oasis:names:tc:opendocument:xmlns:text:1.0",
xlink:"http://www.w3.org/1999/xlink",xml:"http://www.w3.org/XML/1998/namespace"},g;h.lookupNamespaceURI=h;g=function(){};g.forEachPrefix=function(e){for(var f in l)l.hasOwnProperty(f)&&e(f,l[f])};g.resolvePrefix=h;g.namespaceMap=l;g.drawns="urn:oasis:names:tc:opendocument:xmlns:drawing:1.0";g.fons="urn:oasis:names:tc:opendocument:xmlns:xsl-fo-compatible:1.0";g.officens="urn:oasis:names:tc:opendocument:xmlns:office:1.0";g.presentationns="urn:oasis:names:tc:opendocument:xmlns:presentation:1.0";g.stylens=
"urn:oasis:names:tc:opendocument:xmlns:style:1.0";g.svgns="urn:oasis:names:tc:opendocument:xmlns:svg-compatible:1.0";g.tablens="urn:oasis:names:tc:opendocument:xmlns:table:1.0";g.textns="urn:oasis:names:tc:opendocument:xmlns:text:1.0";g.xlinkns="http://www.w3.org/1999/xlink";g.xmlns="http://www.w3.org/XML/1998/namespace";return g}();
// Input 26
runtime.loadClass("xmldom.XPath");
odf.StyleInfo=function(){function h(a,c){for(var b=k[a.localName],e=b&&b[a.namespaceURI],f=e?e.length:0,g,b=0;b<f;b+=1)(g=a.getAttributeNS(e[b].ns,e[b].localname))&&a.setAttributeNS(e[b].ns,d[e[b].ns]+e[b].localname,c+g);for(b=a.firstChild;b;)1===b.nodeType&&(e=b,h(e,c)),b=b.nextSibling}function l(a,c){for(var b=k[a.localName],e=b&&b[a.namespaceURI],f=e?e.length:0,g,b=0;b<f;b+=1)if(g=a.getAttributeNS(e[b].ns,e[b].localname))g=g.replace(c,""),a.setAttributeNS(e[b].ns,d[e[b].ns]+e[b].localname,g);for(b=
a.firstChild;b;)1===b.nodeType&&(e=b,l(e,c)),b=b.nextSibling}function g(a,b){for(var c=k[a.localName],d=c&&c[a.namespaceURI],e=d?d.length:0,f,h,c=0;c<e;c+=1)if(f=a.getAttributeNS(d[c].ns,d[c].localname))h=d[c].keyname,h=b[h]=b[h]||{},h[f]=1;for(c=a.firstChild;c;)1===c.nodeType&&(d=c,g(d,b)),c=c.nextSibling}function e(a,b,c){this.key=a;this.name=b;this.family=c;this.requires={}}function f(a,b,c){var d=a+'"'+b,f=c[d];f||(f=c[d]=new e(d,a,b));return f}function n(a,b,d){var e=k[a.localName],g=(e=e&&e[a.namespaceURI])?
e.length:0,h=a.getAttributeNS(c,"name"),p=a.getAttributeNS(c,"family"),m;h&&p&&(b=f(h,p,d));if(b)for(h=0;h<g;h+=1)if(p=a.getAttributeNS(e[h].ns,e[h].localname))m=e[h].keyname,p=f(p,m,d),b.requires[p.key]=p;for(h=a.firstChild;h;)1===h.nodeType&&(a=h,n(a,b,d)),h=h.nextSibling;return d}function b(a,c){var d=c[a.family];d||(d=c[a.family]={});d[a.name]=1;Object.keys(a.requires).forEach(function(d){b(a.requires[d],c)})}function a(a,c){var d=n(a,null,{});Object.keys(d).forEach(function(a){a=d[a];var e=c[a.family];
e&&e.hasOwnProperty(a.name)&&b(a,c)})}var c="urn:oasis:names:tc:opendocument:xmlns:style:1.0",d={"urn:oasis:names:tc:opendocument:xmlns:chart:1.0":"chart:","urn:oasis:names:tc:opendocument:xmlns:database:1.0":"db:","urn:oasis:names:tc:opendocument:xmlns:dr3d:1.0":"dr3d:","urn:oasis:names:tc:opendocument:xmlns:drawing:1.0":"draw:","urn:oasis:names:tc:opendocument:xmlns:xsl-fo-compatible:1.0":"fo:","urn:oasis:names:tc:opendocument:xmlns:form:1.0":"form:","urn:oasis:names:tc:opendocument:xmlns:datastyle:1.0":"number:",
"urn:oasis:names:tc:opendocument:xmlns:office:1.0":"office:","urn:oasis:names:tc:opendocument:xmlns:presentation:1.0":"presentation:","urn:oasis:names:tc:opendocument:xmlns:style:1.0":"style:","urn:oasis:names:tc:opendocument:xmlns:svg-compatible:1.0":"svg:","urn:oasis:names:tc:opendocument:xmlns:table:1.0":"table:","urn:oasis:names:tc:opendocument:xmlns:text:1.0":"chart:","http://www.w3.org/XML/1998/namespace":"xml:"},m={text:[{ens:c,en:"tab-stop",ans:c,a:"leader-text-style"},{ens:c,en:"drop-cap",
ans:c,a:"style-name"},{ens:"urn:oasis:names:tc:opendocument:xmlns:text:1.0",en:"notes-configuration",ans:"urn:oasis:names:tc:opendocument:xmlns:text:1.0",a:"citation-body-style-name"},{ens:"urn:oasis:names:tc:opendocument:xmlns:text:1.0",en:"notes-configuration",ans:"urn:oasis:names:tc:opendocument:xmlns:text:1.0",a:"citation-style-name"},{ens:"urn:oasis:names:tc:opendocument:xmlns:text:1.0",en:"a",ans:"urn:oasis:names:tc:opendocument:xmlns:text:1.0",a:"style-name"},{ens:"urn:oasis:names:tc:opendocument:xmlns:text:1.0",
en:"alphabetical-index",ans:"urn:oasis:names:tc:opendocument:xmlns:text:1.0",a:"style-name"},{ens:"urn:oasis:names:tc:opendocument:xmlns:text:1.0",en:"linenumbering-configuration",ans:"urn:oasis:names:tc:opendocument:xmlns:text:1.0",a:"style-name"},{ens:"urn:oasis:names:tc:opendocument:xmlns:text:1.0",en:"list-level-style-number",ans:"urn:oasis:names:tc:opendocument:xmlns:text:1.0",a:"style-name"},{ens:"urn:oasis:names:tc:opendocument:xmlns:text:1.0",en:"ruby-text",ans:"urn:oasis:names:tc:opendocument:xmlns:text:1.0",
a:"style-name"},{ens:"urn:oasis:names:tc:opendocument:xmlns:text:1.0",en:"span",ans:"urn:oasis:names:tc:opendocument:xmlns:text:1.0",a:"style-name"},{ens:"urn:oasis:names:tc:opendocument:xmlns:text:1.0",en:"a",ans:"urn:oasis:names:tc:opendocument:xmlns:text:1.0",a:"visited-style-name"},{ens:c,en:"text-properties",ans:c,a:"text-line-through-text-style"},{ens:"urn:oasis:names:tc:opendocument:xmlns:text:1.0",en:"alphabetical-index-source",ans:"urn:oasis:names:tc:opendocument:xmlns:text:1.0",a:"main-entry-style-name"},
{ens:"urn:oasis:names:tc:opendocument:xmlns:text:1.0",en:"index-entry-bibliography",ans:"urn:oasis:names:tc:opendocument:xmlns:text:1.0",a:"style-name"},{ens:"urn:oasis:names:tc:opendocument:xmlns:text:1.0",en:"index-entry-chapter",ans:"urn:oasis:names:tc:opendocument:xmlns:text:1.0",a:"style-name"},{ens:"urn:oasis:names:tc:opendocument:xmlns:text:1.0",en:"index-entry-link-end",ans:"urn:oasis:names:tc:opendocument:xmlns:text:1.0",a:"style-name"},{ens:"urn:oasis:names:tc:opendocument:xmlns:text:1.0",
en:"index-entry-link-start",ans:"urn:oasis:names:tc:opendocument:xmlns:text:1.0",a:"style-name"},{ens:"urn:oasis:names:tc:opendocument:xmlns:text:1.0",en:"index-entry-page-number",ans:"urn:oasis:names:tc:opendocument:xmlns:text:1.0",a:"style-name"},{ens:"urn:oasis:names:tc:opendocument:xmlns:text:1.0",en:"index-entry-span",ans:"urn:oasis:names:tc:opendocument:xmlns:text:1.0",a:"style-name"},{ens:"urn:oasis:names:tc:opendocument:xmlns:text:1.0",en:"index-entry-tab-stop",ans:"urn:oasis:names:tc:opendocument:xmlns:text:1.0",
a:"style-name"},{ens:"urn:oasis:names:tc:opendocument:xmlns:text:1.0",en:"index-entry-text",ans:"urn:oasis:names:tc:opendocument:xmlns:text:1.0",a:"style-name"},{ens:"urn:oasis:names:tc:opendocument:xmlns:text:1.0",en:"index-title-template",ans:"urn:oasis:names:tc:opendocument:xmlns:text:1.0",a:"style-name"},{ens:"urn:oasis:names:tc:opendocument:xmlns:text:1.0",en:"list-level-style-bullet",ans:"urn:oasis:names:tc:opendocument:xmlns:text:1.0",a:"style-name"},{ens:"urn:oasis:names:tc:opendocument:xmlns:text:1.0",
en:"outline-level-style",ans:"urn:oasis:names:tc:opendocument:xmlns:text:1.0",a:"style-name"}],paragraph:[{ens:"urn:oasis:names:tc:opendocument:xmlns:drawing:1.0",en:"caption",ans:"urn:oasis:names:tc:opendocument:xmlns:drawing:1.0",a:"text-style-name"},{ens:"urn:oasis:names:tc:opendocument:xmlns:drawing:1.0",en:"circle",ans:"urn:oasis:names:tc:opendocument:xmlns:drawing:1.0",a:"text-style-name"},{ens:"urn:oasis:names:tc:opendocument:xmlns:drawing:1.0",en:"connector",ans:"urn:oasis:names:tc:opendocument:xmlns:drawing:1.0",
a:"text-style-name"},{ens:"urn:oasis:names:tc:opendocument:xmlns:drawing:1.0",en:"control",ans:"urn:oasis:names:tc:opendocument:xmlns:drawing:1.0",a:"text-style-name"},{ens:"urn:oasis:names:tc:opendocument:xmlns:drawing:1.0",en:"custom-shape",ans:"urn:oasis:names:tc:opendocument:xmlns:drawing:1.0",a:"text-style-name"},{ens:"urn:oasis:names:tc:opendocument:xmlns:drawing:1.0",en:"ellipse",ans:"urn:oasis:names:tc:opendocument:xmlns:drawing:1.0",a:"text-style-name"},{ens:"urn:oasis:names:tc:opendocument:xmlns:drawing:1.0",
en:"frame",ans:"urn:oasis:names:tc:opendocument:xmlns:drawing:1.0",a:"text-style-name"},{ens:"urn:oasis:names:tc:opendocument:xmlns:drawing:1.0",en:"line",ans:"urn:oasis:names:tc:opendocument:xmlns:drawing:1.0",a:"text-style-name"},{ens:"urn:oasis:names:tc:opendocument:xmlns:drawing:1.0",en:"measure",ans:"urn:oasis:names:tc:opendocument:xmlns:drawing:1.0",a:"text-style-name"},{ens:"urn:oasis:names:tc:opendocument:xmlns:drawing:1.0",en:"path",ans:"urn:oasis:names:tc:opendocument:xmlns:drawing:1.0",
a:"text-style-name"},{ens:"urn:oasis:names:tc:opendocument:xmlns:drawing:1.0",en:"polygon",ans:"urn:oasis:names:tc:opendocument:xmlns:drawing:1.0",a:"text-style-name"},{ens:"urn:oasis:names:tc:opendocument:xmlns:drawing:1.0",en:"polyline",ans:"urn:oasis:names:tc:opendocument:xmlns:drawing:1.0",a:"text-style-name"},{ens:"urn:oasis:names:tc:opendocument:xmlns:drawing:1.0",en:"rect",ans:"urn:oasis:names:tc:opendocument:xmlns:drawing:1.0",a:"text-style-name"},{ens:"urn:oasis:names:tc:opendocument:xmlns:drawing:1.0",
en:"regular-polygon",ans:"urn:oasis:names:tc:opendocument:xmlns:drawing:1.0",a:"text-style-name"},{ens:"urn:oasis:names:tc:opendocument:xmlns:office:1.0",en:"annotation",ans:"urn:oasis:names:tc:opendocument:xmlns:drawing:1.0",a:"text-style-name"},{ens:"urn:oasis:names:tc:opendocument:xmlns:form:1.0",en:"column",ans:"urn:oasis:names:tc:opendocument:xmlns:form:1.0",a:"text-style-name"},{ens:c,en:"style",ans:c,a:"next-style-name"},{ens:"urn:oasis:names:tc:opendocument:xmlns:table:1.0",en:"body",ans:"urn:oasis:names:tc:opendocument:xmlns:table:1.0",
a:"paragraph-style-name"},{ens:"urn:oasis:names:tc:opendocument:xmlns:table:1.0",en:"even-columns",ans:"urn:oasis:names:tc:opendocument:xmlns:table:1.0",a:"paragraph-style-name"},{ens:"urn:oasis:names:tc:opendocument:xmlns:table:1.0",en:"even-rows",ans:"urn:oasis:names:tc:opendocument:xmlns:table:1.0",a:"paragraph-style-name"},{ens:"urn:oasis:names:tc:opendocument:xmlns:table:1.0",en:"first-column",ans:"urn:oasis:names:tc:opendocument:xmlns:table:1.0",a:"paragraph-style-name"},{ens:"urn:oasis:names:tc:opendocument:xmlns:table:1.0",
en:"first-row",ans:"urn:oasis:names:tc:opendocument:xmlns:table:1.0",a:"paragraph-style-name"},{ens:"urn:oasis:names:tc:opendocument:xmlns:table:1.0",en:"last-column",ans:"urn:oasis:names:tc:opendocument:xmlns:table:1.0",a:"paragraph-style-name"},{ens:"urn:oasis:names:tc:opendocument:xmlns:table:1.0",en:"last-row",ans:"urn:oasis:names:tc:opendocument:xmlns:table:1.0",a:"paragraph-style-name"},{ens:"urn:oasis:names:tc:opendocument:xmlns:table:1.0",en:"odd-columns",ans:"urn:oasis:names:tc:opendocument:xmlns:table:1.0",
a:"paragraph-style-name"},{ens:"urn:oasis:names:tc:opendocument:xmlns:table:1.0",en:"odd-rows",ans:"urn:oasis:names:tc:opendocument:xmlns:table:1.0",a:"paragraph-style-name"},{ens:"urn:oasis:names:tc:opendocument:xmlns:text:1.0",en:"notes-configuration",ans:"urn:oasis:names:tc:opendocument:xmlns:text:1.0",a:"default-style-name"},{ens:"urn:oasis:names:tc:opendocument:xmlns:text:1.0",en:"alphabetical-index-entry-template",ans:"urn:oasis:names:tc:opendocument:xmlns:text:1.0",a:"style-name"},{ens:"urn:oasis:names:tc:opendocument:xmlns:text:1.0",
en:"bibliography-entry-template",ans:"urn:oasis:names:tc:opendocument:xmlns:text:1.0",a:"style-name"},{ens:"urn:oasis:names:tc:opendocument:xmlns:text:1.0",en:"h",ans:"urn:oasis:names:tc:opendocument:xmlns:text:1.0",a:"style-name"},{ens:"urn:oasis:names:tc:opendocument:xmlns:text:1.0",en:"illustration-index-entry-template",ans:"urn:oasis:names:tc:opendocument:xmlns:text:1.0",a:"style-name"},{ens:"urn:oasis:names:tc:opendocument:xmlns:text:1.0",en:"index-source-style",ans:"urn:oasis:names:tc:opendocument:xmlns:text:1.0",
a:"style-name"},{ens:"urn:oasis:names:tc:opendocument:xmlns:text:1.0",en:"object-index-entry-template",ans:"urn:oasis:names:tc:opendocument:xmlns:text:1.0",a:"style-name"},{ens:"urn:oasis:names:tc:opendocument:xmlns:text:1.0",en:"p",ans:"urn:oasis:names:tc:opendocument:xmlns:text:1.0",a:"style-name"},{ens:"urn:oasis:names:tc:opendocument:xmlns:text:1.0",en:"table-index-entry-template",ans:"urn:oasis:names:tc:opendocument:xmlns:text:1.0",a:"style-name"},{ens:"urn:oasis:names:tc:opendocument:xmlns:text:1.0",
en:"table-of-content-entry-template",ans:"urn:oasis:names:tc:opendocument:xmlns:text:1.0",a:"style-name"},{ens:"urn:oasis:names:tc:opendocument:xmlns:text:1.0",en:"table-index-entry-template",ans:"urn:oasis:names:tc:opendocument:xmlns:text:1.0",a:"style-name"},{ens:"urn:oasis:names:tc:opendocument:xmlns:text:1.0",en:"user-index-entry-template",ans:"urn:oasis:names:tc:opendocument:xmlns:text:1.0",a:"style-name"},{ens:c,en:"page-layout-properties",ans:c,a:"register-truth-ref-style-name"}],chart:[{ens:"urn:oasis:names:tc:opendocument:xmlns:chart:1.0",
en:"axis",ans:"urn:oasis:names:tc:opendocument:xmlns:chart:1.0",a:"style-name"},{ens:"urn:oasis:names:tc:opendocument:xmlns:chart:1.0",en:"chart",ans:"urn:oasis:names:tc:opendocument:xmlns:chart:1.0",a:"style-name"},{ens:"urn:oasis:names:tc:opendocument:xmlns:chart:1.0",en:"data-label",ans:"urn:oasis:names:tc:opendocument:xmlns:chart:1.0",a:"style-name"},{ens:"urn:oasis:names:tc:opendocument:xmlns:chart:1.0",en:"data-point",ans:"urn:oasis:names:tc:opendocument:xmlns:chart:1.0",a:"style-name"},{ens:"urn:oasis:names:tc:opendocument:xmlns:chart:1.0",
en:"equation",ans:"urn:oasis:names:tc:opendocument:xmlns:chart:1.0",a:"style-name"},{ens:"urn:oasis:names:tc:opendocument:xmlns:chart:1.0",en:"error-indicator",ans:"urn:oasis:names:tc:opendocument:xmlns:chart:1.0",a:"style-name"},{ens:"urn:oasis:names:tc:opendocument:xmlns:chart:1.0",en:"floor",ans:"urn:oasis:names:tc:opendocument:xmlns:chart:1.0",a:"style-name"},{ens:"urn:oasis:names:tc:opendocument:xmlns:chart:1.0",en:"footer",ans:"urn:oasis:names:tc:opendocument:xmlns:chart:1.0",a:"style-name"},
{ens:"urn:oasis:names:tc:opendocument:xmlns:chart:1.0",en:"grid",ans:"urn:oasis:names:tc:opendocument:xmlns:chart:1.0",a:"style-name"},{ens:"urn:oasis:names:tc:opendocument:xmlns:chart:1.0",en:"legend",ans:"urn:oasis:names:tc:opendocument:xmlns:chart:1.0",a:"style-name"},{ens:"urn:oasis:names:tc:opendocument:xmlns:chart:1.0",en:"mean-value",ans:"urn:oasis:names:tc:opendocument:xmlns:chart:1.0",a:"style-name"},{ens:"urn:oasis:names:tc:opendocument:xmlns:chart:1.0",en:"plot-area",ans:"urn:oasis:names:tc:opendocument:xmlns:chart:1.0",
a:"style-name"},{ens:"urn:oasis:names:tc:opendocument:xmlns:chart:1.0",en:"regression-curve",ans:"urn:oasis:names:tc:opendocument:xmlns:chart:1.0",a:"style-name"},{ens:"urn:oasis:names:tc:opendocument:xmlns:chart:1.0",en:"series",ans:"urn:oasis:names:tc:opendocument:xmlns:chart:1.0",a:"style-name"},{ens:"urn:oasis:names:tc:opendocument:xmlns:chart:1.0",en:"stock-gain-marker",ans:"urn:oasis:names:tc:opendocument:xmlns:chart:1.0",a:"style-name"},{ens:"urn:oasis:names:tc:opendocument:xmlns:chart:1.0",
en:"stock-loss-marker",ans:"urn:oasis:names:tc:opendocument:xmlns:chart:1.0",a:"style-name"},{ens:"urn:oasis:names:tc:opendocument:xmlns:chart:1.0",en:"stock-range-line",ans:"urn:oasis:names:tc:opendocument:xmlns:chart:1.0",a:"style-name"},{ens:"urn:oasis:names:tc:opendocument:xmlns:chart:1.0",en:"subtitle",ans:"urn:oasis:names:tc:opendocument:xmlns:chart:1.0",a:"style-name"},{ens:"urn:oasis:names:tc:opendocument:xmlns:chart:1.0",en:"title",ans:"urn:oasis:names:tc:opendocument:xmlns:chart:1.0",a:"style-name"},
{ens:"urn:oasis:names:tc:opendocument:xmlns:chart:1.0",en:"wall",ans:"urn:oasis:names:tc:opendocument:xmlns:chart:1.0",a:"style-name"}],section:[{ens:"urn:oasis:names:tc:opendocument:xmlns:text:1.0",en:"alphabetical-index",ans:"urn:oasis:names:tc:opendocument:xmlns:text:1.0",a:"style-name"},{ens:"urn:oasis:names:tc:opendocument:xmlns:text:1.0",en:"bibliography",ans:"urn:oasis:names:tc:opendocument:xmlns:text:1.0",a:"style-name"},{ens:"urn:oasis:names:tc:opendocument:xmlns:text:1.0",en:"illustration-index",
ans:"urn:oasis:names:tc:opendocument:xmlns:text:1.0",a:"style-name"},{ens:"urn:oasis:names:tc:opendocument:xmlns:text:1.0",en:"index-title",ans:"urn:oasis:names:tc:opendocument:xmlns:text:1.0",a:"style-name"},{ens:"urn:oasis:names:tc:opendocument:xmlns:text:1.0",en:"object-index",ans:"urn:oasis:names:tc:opendocument:xmlns:text:1.0",a:"style-name"},{ens:"urn:oasis:names:tc:opendocument:xmlns:text:1.0",en:"section",ans:"urn:oasis:names:tc:opendocument:xmlns:text:1.0",a:"style-name"},{ens:"urn:oasis:names:tc:opendocument:xmlns:text:1.0",
en:"table-of-content",ans:"urn:oasis:names:tc:opendocument:xmlns:text:1.0",a:"style-name"},{ens:"urn:oasis:names:tc:opendocument:xmlns:text:1.0",en:"table-index",ans:"urn:oasis:names:tc:opendocument:xmlns:text:1.0",a:"style-name"},{ens:"urn:oasis:names:tc:opendocument:xmlns:text:1.0",en:"user-index",ans:"urn:oasis:names:tc:opendocument:xmlns:text:1.0",a:"style-name"}],ruby:[{ens:"urn:oasis:names:tc:opendocument:xmlns:text:1.0",en:"ruby",ans:"urn:oasis:names:tc:opendocument:xmlns:text:1.0",a:"style-name"}],
table:[{ens:"urn:oasis:names:tc:opendocument:xmlns:database:1.0",en:"query",ans:"urn:oasis:names:tc:opendocument:xmlns:database:1.0",a:"style-name"},{ens:"urn:oasis:names:tc:opendocument:xmlns:database:1.0",en:"table-representation",ans:"urn:oasis:names:tc:opendocument:xmlns:database:1.0",a:"style-name"},{ens:"urn:oasis:names:tc:opendocument:xmlns:table:1.0",en:"background",ans:"urn:oasis:names:tc:opendocument:xmlns:table:1.0",a:"style-name"},{ens:"urn:oasis:names:tc:opendocument:xmlns:table:1.0",
en:"table",ans:"urn:oasis:names:tc:opendocument:xmlns:table:1.0",a:"style-name"}],"table-column":[{ens:"urn:oasis:names:tc:opendocument:xmlns:database:1.0",en:"column",ans:"urn:oasis:names:tc:opendocument:xmlns:database:1.0",a:"style-name"},{ens:"urn:oasis:names:tc:opendocument:xmlns:table:1.0",en:"table-column",ans:"urn:oasis:names:tc:opendocument:xmlns:table:1.0",a:"style-name"}],"table-row":[{ens:"urn:oasis:names:tc:opendocument:xmlns:database:1.0",en:"query",ans:"urn:oasis:names:tc:opendocument:xmlns:database:1.0",
a:"default-row-style-name"},{ens:"urn:oasis:names:tc:opendocument:xmlns:database:1.0",en:"table-representation",ans:"urn:oasis:names:tc:opendocument:xmlns:database:1.0",a:"default-row-style-name"},{ens:"urn:oasis:names:tc:opendocument:xmlns:table:1.0",en:"table-row",ans:"urn:oasis:names:tc:opendocument:xmlns:table:1.0",a:"style-name"}],"table-cell":[{ens:"urn:oasis:names:tc:opendocument:xmlns:database:1.0",en:"column",ans:"urn:oasis:names:tc:opendocument:xmlns:database:1.0",a:"default-cell-style-name"},
{ens:"urn:oasis:names:tc:opendocument:xmlns:table:1.0",en:"table-column",ans:"urn:oasis:names:tc:opendocument:xmlns:table:1.0",a:"default-cell-style-name"},{ens:"urn:oasis:names:tc:opendocument:xmlns:table:1.0",en:"table-row",ans:"urn:oasis:names:tc:opendocument:xmlns:table:1.0",a:"default-cell-style-name"},{ens:"urn:oasis:names:tc:opendocument:xmlns:table:1.0",en:"body",ans:"urn:oasis:names:tc:opendocument:xmlns:table:1.0",a:"style-name"},{ens:"urn:oasis:names:tc:opendocument:xmlns:table:1.0",en:"covered-table-cell",
ans:"urn:oasis:names:tc:opendocument:xmlns:table:1.0",a:"style-name"},{ens:"urn:oasis:names:tc:opendocument:xmlns:table:1.0",en:"even-columns",ans:"urn:oasis:names:tc:opendocument:xmlns:table:1.0",a:"style-name"},{ens:"urn:oasis:names:tc:opendocument:xmlns:table:1.0",en:"covered-table-cell",ans:"urn:oasis:names:tc:opendocument:xmlns:table:1.0",a:"style-name"},{ens:"urn:oasis:names:tc:opendocument:xmlns:table:1.0",en:"even-columns",ans:"urn:oasis:names:tc:opendocument:xmlns:table:1.0",a:"style-name"},
{ens:"urn:oasis:names:tc:opendocument:xmlns:table:1.0",en:"even-rows",ans:"urn:oasis:names:tc:opendocument:xmlns:table:1.0",a:"style-name"},{ens:"urn:oasis:names:tc:opendocument:xmlns:table:1.0",en:"first-column",ans:"urn:oasis:names:tc:opendocument:xmlns:table:1.0",a:"style-name"},{ens:"urn:oasis:names:tc:opendocument:xmlns:table:1.0",en:"first-row",ans:"urn:oasis:names:tc:opendocument:xmlns:table:1.0",a:"style-name"},{ens:"urn:oasis:names:tc:opendocument:xmlns:table:1.0",en:"last-column",ans:"urn:oasis:names:tc:opendocument:xmlns:table:1.0",
a:"style-name"},{ens:"urn:oasis:names:tc:opendocument:xmlns:table:1.0",en:"last-row",ans:"urn:oasis:names:tc:opendocument:xmlns:table:1.0",a:"style-name"},{ens:"urn:oasis:names:tc:opendocument:xmlns:table:1.0",en:"odd-columns",ans:"urn:oasis:names:tc:opendocument:xmlns:table:1.0",a:"style-name"},{ens:"urn:oasis:names:tc:opendocument:xmlns:table:1.0",en:"odd-rows",ans:"urn:oasis:names:tc:opendocument:xmlns:table:1.0",a:"style-name"},{ens:"urn:oasis:names:tc:opendocument:xmlns:table:1.0",en:"table-cell",
ans:"urn:oasis:names:tc:opendocument:xmlns:table:1.0",a:"style-name"}],graphic:[{ens:"urn:oasis:names:tc:opendocument:xmlns:dr3d:1.0",en:"cube",ans:"urn:oasis:names:tc:opendocument:xmlns:drawing:1.0",a:"style-name"},{ens:"urn:oasis:names:tc:opendocument:xmlns:dr3d:1.0",en:"extrude",ans:"urn:oasis:names:tc:opendocument:xmlns:drawing:1.0",a:"style-name"},{ens:"urn:oasis:names:tc:opendocument:xmlns:dr3d:1.0",en:"rotate",ans:"urn:oasis:names:tc:opendocument:xmlns:drawing:1.0",a:"style-name"},{ens:"urn:oasis:names:tc:opendocument:xmlns:dr3d:1.0",
en:"scene",ans:"urn:oasis:names:tc:opendocument:xmlns:drawing:1.0",a:"style-name"},{ens:"urn:oasis:names:tc:opendocument:xmlns:dr3d:1.0",en:"sphere",ans:"urn:oasis:names:tc:opendocument:xmlns:drawing:1.0",a:"style-name"},{ens:"urn:oasis:names:tc:opendocument:xmlns:drawing:1.0",en:"caption",ans:"urn:oasis:names:tc:opendocument:xmlns:drawing:1.0",a:"style-name"},{ens:"urn:oasis:names:tc:opendocument:xmlns:drawing:1.0",en:"circle",ans:"urn:oasis:names:tc:opendocument:xmlns:drawing:1.0",a:"style-name"},
{ens:"urn:oasis:names:tc:opendocument:xmlns:drawing:1.0",en:"connector",ans:"urn:oasis:names:tc:opendocument:xmlns:drawing:1.0",a:"style-name"},{ens:"urn:oasis:names:tc:opendocument:xmlns:drawing:1.0",en:"control",ans:"urn:oasis:names:tc:opendocument:xmlns:drawing:1.0",a:"style-name"},{ens:"urn:oasis:names:tc:opendocument:xmlns:drawing:1.0",en:"custom-shape",ans:"urn:oasis:names:tc:opendocument:xmlns:drawing:1.0",a:"style-name"},{ens:"urn:oasis:names:tc:opendocument:xmlns:drawing:1.0",en:"ellipse",
ans:"urn:oasis:names:tc:opendocument:xmlns:drawing:1.0",a:"style-name"},{ens:"urn:oasis:names:tc:opendocument:xmlns:drawing:1.0",en:"frame",ans:"urn:oasis:names:tc:opendocument:xmlns:drawing:1.0",a:"style-name"},{ens:"urn:oasis:names:tc:opendocument:xmlns:drawing:1.0",en:"g",ans:"urn:oasis:names:tc:opendocument:xmlns:drawing:1.0",a:"style-name"},{ens:"urn:oasis:names:tc:opendocument:xmlns:drawing:1.0",en:"line",ans:"urn:oasis:names:tc:opendocument:xmlns:drawing:1.0",a:"style-name"},{ens:"urn:oasis:names:tc:opendocument:xmlns:drawing:1.0",
en:"measure",ans:"urn:oasis:names:tc:opendocument:xmlns:drawing:1.0",a:"style-name"},{ens:"urn:oasis:names:tc:opendocument:xmlns:drawing:1.0",en:"page-thumbnail",ans:"urn:oasis:names:tc:opendocument:xmlns:drawing:1.0",a:"style-name"},{ens:"urn:oasis:names:tc:opendocument:xmlns:drawing:1.0",en:"path",ans:"urn:oasis:names:tc:opendocument:xmlns:drawing:1.0",a:"style-name"},{ens:"urn:oasis:names:tc:opendocument:xmlns:drawing:1.0",en:"polygon",ans:"urn:oasis:names:tc:opendocument:xmlns:drawing:1.0",a:"style-name"},
{ens:"urn:oasis:names:tc:opendocument:xmlns:drawing:1.0",en:"polyline",ans:"urn:oasis:names:tc:opendocument:xmlns:drawing:1.0",a:"style-name"},{ens:"urn:oasis:names:tc:opendocument:xmlns:drawing:1.0",en:"rect",ans:"urn:oasis:names:tc:opendocument:xmlns:drawing:1.0",a:"style-name"},{ens:"urn:oasis:names:tc:opendocument:xmlns:drawing:1.0",en:"regular-polygon",ans:"urn:oasis:names:tc:opendocument:xmlns:drawing:1.0",a:"style-name"},{ens:"urn:oasis:names:tc:opendocument:xmlns:office:1.0",en:"annotation",
ans:"urn:oasis:names:tc:opendocument:xmlns:drawing:1.0",a:"style-name"}],presentation:[{ens:"urn:oasis:names:tc:opendocument:xmlns:dr3d:1.0",en:"cube",ans:"urn:oasis:names:tc:opendocument:xmlns:presentation:1.0",a:"style-name"},{ens:"urn:oasis:names:tc:opendocument:xmlns:dr3d:1.0",en:"extrude",ans:"urn:oasis:names:tc:opendocument:xmlns:presentation:1.0",a:"style-name"},{ens:"urn:oasis:names:tc:opendocument:xmlns:dr3d:1.0",en:"rotate",ans:"urn:oasis:names:tc:opendocument:xmlns:presentation:1.0",a:"style-name"},
{ens:"urn:oasis:names:tc:opendocument:xmlns:dr3d:1.0",en:"scene",ans:"urn:oasis:names:tc:opendocument:xmlns:presentation:1.0",a:"style-name"},{ens:"urn:oasis:names:tc:opendocument:xmlns:dr3d:1.0",en:"sphere",ans:"urn:oasis:names:tc:opendocument:xmlns:presentation:1.0",a:"style-name"},{ens:"urn:oasis:names:tc:opendocument:xmlns:drawing:1.0",en:"caption",ans:"urn:oasis:names:tc:opendocument:xmlns:presentation:1.0",a:"style-name"},{ens:"urn:oasis:names:tc:opendocument:xmlns:drawing:1.0",en:"circle",
ans:"urn:oasis:names:tc:opendocument:xmlns:presentation:1.0",a:"style-name"},{ens:"urn:oasis:names:tc:opendocument:xmlns:drawing:1.0",en:"connector",ans:"urn:oasis:names:tc:opendocument:xmlns:presentation:1.0",a:"style-name"},{ens:"urn:oasis:names:tc:opendocument:xmlns:drawing:1.0",en:"control",ans:"urn:oasis:names:tc:opendocument:xmlns:presentation:1.0",a:"style-name"},{ens:"urn:oasis:names:tc:opendocument:xmlns:drawing:1.0",en:"custom-shape",ans:"urn:oasis:names:tc:opendocument:xmlns:presentation:1.0",
a:"style-name"},{ens:"urn:oasis:names:tc:opendocument:xmlns:drawing:1.0",en:"ellipse",ans:"urn:oasis:names:tc:opendocument:xmlns:presentation:1.0",a:"style-name"},{ens:"urn:oasis:names:tc:opendocument:xmlns:drawing:1.0",en:"frame",ans:"urn:oasis:names:tc:opendocument:xmlns:presentation:1.0",a:"style-name"},{ens:"urn:oasis:names:tc:opendocument:xmlns:drawing:1.0",en:"g",ans:"urn:oasis:names:tc:opendocument:xmlns:presentation:1.0",a:"style-name"},{ens:"urn:oasis:names:tc:opendocument:xmlns:drawing:1.0",
en:"line",ans:"urn:oasis:names:tc:opendocument:xmlns:presentation:1.0",a:"style-name"},{ens:"urn:oasis:names:tc:opendocument:xmlns:drawing:1.0",en:"measure",ans:"urn:oasis:names:tc:opendocument:xmlns:presentation:1.0",a:"style-name"},{ens:"urn:oasis:names:tc:opendocument:xmlns:drawing:1.0",en:"page-thumbnail",ans:"urn:oasis:names:tc:opendocument:xmlns:presentation:1.0",a:"style-name"},{ens:"urn:oasis:names:tc:opendocument:xmlns:drawing:1.0",en:"path",ans:"urn:oasis:names:tc:opendocument:xmlns:presentation:1.0",
a:"style-name"},{ens:"urn:oasis:names:tc:opendocument:xmlns:drawing:1.0",en:"polygon",ans:"urn:oasis:names:tc:opendocument:xmlns:presentation:1.0",a:"style-name"},{ens:"urn:oasis:names:tc:opendocument:xmlns:drawing:1.0",en:"polyline",ans:"urn:oasis:names:tc:opendocument:xmlns:presentation:1.0",a:"style-name"},{ens:"urn:oasis:names:tc:opendocument:xmlns:drawing:1.0",en:"rect",ans:"urn:oasis:names:tc:opendocument:xmlns:presentation:1.0",a:"style-name"},{ens:"urn:oasis:names:tc:opendocument:xmlns:drawing:1.0",
en:"regular-polygon",ans:"urn:oasis:names:tc:opendocument:xmlns:presentation:1.0",a:"style-name"},{ens:"urn:oasis:names:tc:opendocument:xmlns:office:1.0",en:"annotation",ans:"urn:oasis:names:tc:opendocument:xmlns:presentation:1.0",a:"style-name"}],"drawing-page":[{ens:"urn:oasis:names:tc:opendocument:xmlns:drawing:1.0",en:"page",ans:"urn:oasis:names:tc:opendocument:xmlns:drawing:1.0",a:"style-name"},{ens:"urn:oasis:names:tc:opendocument:xmlns:presentation:1.0",en:"notes",ans:"urn:oasis:names:tc:opendocument:xmlns:drawing:1.0",
a:"style-name"},{ens:c,en:"handout-master",ans:"urn:oasis:names:tc:opendocument:xmlns:drawing:1.0",a:"style-name"},{ens:c,en:"master-page",ans:"urn:oasis:names:tc:opendocument:xmlns:drawing:1.0",a:"style-name"}],"list-style":[{ens:"urn:oasis:names:tc:opendocument:xmlns:text:1.0",en:"list",ans:"urn:oasis:names:tc:opendocument:xmlns:text:1.0",a:"style-name"},{ens:"urn:oasis:names:tc:opendocument:xmlns:text:1.0",en:"numbered-paragraph",ans:"urn:oasis:names:tc:opendocument:xmlns:text:1.0",a:"style-name"},
{ens:"urn:oasis:names:tc:opendocument:xmlns:text:1.0",en:"list-item",ans:"urn:oasis:names:tc:opendocument:xmlns:text:1.0",a:"style-override"},{ens:c,en:"style",ans:c,a:"list-style-name"},{ens:c,en:"style",ans:c,a:"data-style-name"},{ens:c,en:"style",ans:c,a:"percentage-data-style-name"},{ens:"urn:oasis:names:tc:opendocument:xmlns:presentation:1.0",en:"date-time-decl",ans:c,a:"data-style-name"},{ens:"urn:oasis:names:tc:opendocument:xmlns:text:1.0",en:"creation-date",ans:c,a:"data-style-name"},{ens:"urn:oasis:names:tc:opendocument:xmlns:text:1.0",
en:"creation-time",ans:c,a:"data-style-name"},{ens:"urn:oasis:names:tc:opendocument:xmlns:text:1.0",en:"database-display",ans:c,a:"data-style-name"},{ens:"urn:oasis:names:tc:opendocument:xmlns:text:1.0",en:"date",ans:c,a:"data-style-name"},{ens:"urn:oasis:names:tc:opendocument:xmlns:text:1.0",en:"editing-duration",ans:c,a:"data-style-name"},{ens:"urn:oasis:names:tc:opendocument:xmlns:text:1.0",en:"expression",ans:c,a:"data-style-name"},{ens:"urn:oasis:names:tc:opendocument:xmlns:text:1.0",en:"meta-field",
ans:c,a:"data-style-name"},{ens:"urn:oasis:names:tc:opendocument:xmlns:text:1.0",en:"modification-date",ans:c,a:"data-style-name"},{ens:"urn:oasis:names:tc:opendocument:xmlns:text:1.0",en:"modification-time",ans:c,a:"data-style-name"},{ens:"urn:oasis:names:tc:opendocument:xmlns:text:1.0",en:"print-date",ans:c,a:"data-style-name"},{ens:"urn:oasis:names:tc:opendocument:xmlns:text:1.0",en:"print-time",ans:c,a:"data-style-name"},{ens:"urn:oasis:names:tc:opendocument:xmlns:text:1.0",en:"table-formula",
ans:c,a:"data-style-name"},{ens:"urn:oasis:names:tc:opendocument:xmlns:text:1.0",en:"time",ans:c,a:"data-style-name"},{ens:"urn:oasis:names:tc:opendocument:xmlns:text:1.0",en:"user-defined",ans:c,a:"data-style-name"},{ens:"urn:oasis:names:tc:opendocument:xmlns:text:1.0",en:"user-field-get",ans:c,a:"data-style-name"},{ens:"urn:oasis:names:tc:opendocument:xmlns:text:1.0",en:"user-field-input",ans:c,a:"data-style-name"},{ens:"urn:oasis:names:tc:opendocument:xmlns:text:1.0",en:"variable-get",ans:c,a:"data-style-name"},
{ens:"urn:oasis:names:tc:opendocument:xmlns:text:1.0",en:"variable-input",ans:c,a:"data-style-name"},{ens:"urn:oasis:names:tc:opendocument:xmlns:text:1.0",en:"variable-set",ans:c,a:"data-style-name"}],data:[{ens:c,en:"style",ans:c,a:"data-style-name"},{ens:c,en:"style",ans:c,a:"percentage-data-style-name"},{ens:"urn:oasis:names:tc:opendocument:xmlns:presentation:1.0",en:"date-time-decl",ans:c,a:"data-style-name"},{ens:"urn:oasis:names:tc:opendocument:xmlns:text:1.0",en:"creation-date",ans:c,a:"data-style-name"},
{ens:"urn:oasis:names:tc:opendocument:xmlns:text:1.0",en:"creation-time",ans:c,a:"data-style-name"},{ens:"urn:oasis:names:tc:opendocument:xmlns:text:1.0",en:"database-display",ans:c,a:"data-style-name"},{ens:"urn:oasis:names:tc:opendocument:xmlns:text:1.0",en:"date",ans:c,a:"data-style-name"},{ens:"urn:oasis:names:tc:opendocument:xmlns:text:1.0",en:"editing-duration",ans:c,a:"data-style-name"},{ens:"urn:oasis:names:tc:opendocument:xmlns:text:1.0",en:"expression",ans:c,a:"data-style-name"},{ens:"urn:oasis:names:tc:opendocument:xmlns:text:1.0",
en:"meta-field",ans:c,a:"data-style-name"},{ens:"urn:oasis:names:tc:opendocument:xmlns:text:1.0",en:"modification-date",ans:c,a:"data-style-name"},{ens:"urn:oasis:names:tc:opendocument:xmlns:text:1.0",en:"modification-time",ans:c,a:"data-style-name"},{ens:"urn:oasis:names:tc:opendocument:xmlns:text:1.0",en:"print-date",ans:c,a:"data-style-name"},{ens:"urn:oasis:names:tc:opendocument:xmlns:text:1.0",en:"print-time",ans:c,a:"data-style-name"},{ens:"urn:oasis:names:tc:opendocument:xmlns:text:1.0",en:"table-formula",
ans:c,a:"data-style-name"},{ens:"urn:oasis:names:tc:opendocument:xmlns:text:1.0",en:"time",ans:c,a:"data-style-name"},{ens:"urn:oasis:names:tc:opendocument:xmlns:text:1.0",en:"user-defined",ans:c,a:"data-style-name"},{ens:"urn:oasis:names:tc:opendocument:xmlns:text:1.0",en:"user-field-get",ans:c,a:"data-style-name"},{ens:"urn:oasis:names:tc:opendocument:xmlns:text:1.0",en:"user-field-input",ans:c,a:"data-style-name"},{ens:"urn:oasis:names:tc:opendocument:xmlns:text:1.0",en:"variable-get",ans:c,a:"data-style-name"},
{ens:"urn:oasis:names:tc:opendocument:xmlns:text:1.0",en:"variable-input",ans:c,a:"data-style-name"},{ens:"urn:oasis:names:tc:opendocument:xmlns:text:1.0",en:"variable-set",ans:c,a:"data-style-name"}],"page-layout":[{ens:"urn:oasis:names:tc:opendocument:xmlns:presentation:1.0",en:"notes",ans:c,a:"page-layout-name"},{ens:c,en:"handout-master",ans:c,a:"page-layout-name"},{ens:c,en:"master-page",ans:c,a:"page-layout-name"}]},k,p=new xmldom.XPath;this.UsedStyleList=function(b,d){var e={};this.uses=function(a){var b=
a.localName,d=a.getAttributeNS("urn:oasis:names:tc:opendocument:xmlns:drawing:1.0","name")||a.getAttributeNS(c,"name");a="style"===b?a.getAttributeNS(c,"family"):"urn:oasis:names:tc:opendocument:xmlns:datastyle:1.0"===a.namespaceURI?"data":b;return(a=e[a])?0<a[d]:!1};g(b,e);d&&a(d,e)};this.canElementHaveStyle=function(a,b){var c=k[b.localName],c=c&&c[b.namespaceURI];return 0<(c?c.length:0)};this.hasDerivedStyles=function(a,b,c){var d=b("style"),e=c.getAttributeNS(d,"name");c=c.getAttributeNS(d,"family");
return p.getODFElementsWithXPath(a,"//style:*[@style:parent-style-name='"+e+"'][@style:family='"+c+"']",b).length?!0:!1};this.prefixStyleNames=function(a,b,e){var f;if(a){for(f=a.firstChild;f;){if(1===f.nodeType){var g=f,k=b,p=g.getAttributeNS("urn:oasis:names:tc:opendocument:xmlns:drawing:1.0","name"),m=void 0;p?m="urn:oasis:names:tc:opendocument:xmlns:drawing:1.0":(p=g.getAttributeNS(c,"name"))&&(m=c);m&&g.setAttributeNS(m,d[m]+"name",k+p)}f=f.nextSibling}h(a,b);e&&h(e,b)}};this.removePrefixFromStyleNames=
function(a,b,e){var f=RegExp("^"+b);if(a){for(b=a.firstChild;b;){if(1===b.nodeType){var g=b,k=f,h=g.getAttributeNS("urn:oasis:names:tc:opendocument:xmlns:drawing:1.0","name"),p=void 0;h?p="urn:oasis:names:tc:opendocument:xmlns:drawing:1.0":(h=g.getAttributeNS(c,"name"))&&(p=c);p&&(h=h.replace(k,""),g.setAttributeNS(p,d[p]+"name",h))}b=b.nextSibling}l(a,f);e&&l(e,f)}};k=function(a){var b,c,d,e,f,g={},k;for(b in a)if(a.hasOwnProperty(b)){e=a[b];d=e.length;for(c=0;c<d;c+=1)f=e[c],k=g[f.en]=g[f.en]||
{},k=k[f.ens]=k[f.ens]||[],k.push({ns:f.ans,localname:f.a,keyname:b})}return g}(m)};
// Input 27
/*

 Copyright (C) 2012-2013 KO GmbH <copyright@kogmbh.com>

 @licstart
 The JavaScript code in this page is free software: you can redistribute it
 and/or modify it under the terms of the GNU Affero General Public License
 (GNU AGPL) as published by the Free Software Foundation, either version 3 of
 the License, or (at your option) any later version.  The code is distributed
 WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 FITNESS FOR A PARTICULAR PURPOSE.  See the GNU AGPL for more details.

 As additional permission under GNU AGPL version 3 section 7, you
 may distribute non-source (e.g., minimized or compacted) forms of
 that code without the copy of the GNU GPL normally required by
 section 4, provided you include this license notice and a URL
 through which recipients can access the Corresponding Source.

 As a special exception to the AGPL, any HTML file which merely makes function
 calls to this code, and for that purpose includes it by reference shall be
 deemed a separate work for copyright law purposes. In addition, the copyright
 holders of this code give you permission to combine this code with free
 software libraries that are released under the GNU LGPL. You may copy and
 distribute such a system following the terms of the GNU AGPL for this code
 and the LGPL for the libraries. If you modify this code, you may extend this
 exception to your version of the code, but you are not obligated to do so.
 If you do not wish to do so, delete this exception statement from your
 version.

 This license applies to this entire compilation.
 @licend
 @source: http://www.webodf.org/
 @source: http://gitorious.org/webodf/webodf/
*/
runtime.loadClass("odf.Namespaces");
odf.Style2CSS=function(){function h(a,b){var c={},d,e,f;if(!b)return c;for(d=b.firstChild;d;){if(f=d.namespaceURI===p&&("style"===d.localName||"default-style"===d.localName)?d.getAttributeNS(p,"family"):d.namespaceURI===r&&"list-style"===d.localName?"list":void 0)(e=d.getAttributeNS&&d.getAttributeNS(p,"name"))||(e=""),f=c[f]=c[f]||{},f[e]=d;d=d.nextSibling}return c}function l(a,b){if(!b||!a)return null;if(a[b])return a[b];var c,d;for(c in a)if(a.hasOwnProperty(c)&&(d=l(a[c].derivedStyles,b)))return d;
return null}function g(a,b,c){var d=b[a],e,f;d&&(e=d.getAttributeNS(p,"parent-style-name"),f=null,e&&(f=l(c,e),!f&&b[e]&&(g(e,b,c),f=b[e],b[e]=null)),f?(f.derivedStyles||(f.derivedStyles={}),f.derivedStyles[a]=d):c[a]=d)}function e(a,b){for(var c in a)a.hasOwnProperty(c)&&(g(c,a,b),a[c]=null)}function f(a,b){var c=s[a],d;d="";if(null===c)return null;d=b?"["+c+'|style-name="'+b+'"]':"["+c+"|style-name]";"presentation"===c&&(c="draw",d=b?'[presentation|style-name="'+b+'"]':"[presentation|style-name]");
return d=c+"|"+v[a].join(d+","+c+"|")+d}function n(a,b,c){var d=[],e,g;d.push(f(a,b));for(e in c.derivedStyles)if(c.derivedStyles.hasOwnProperty(e))for(g in b=n(a,e,c.derivedStyles[e]),b)b.hasOwnProperty(g)&&d.push(b[g]);return d}function b(a,b,c){if(!a)return null;for(a=a.firstChild;a;){if(a.namespaceURI===b&&a.localName===c)return b=a;a=a.nextSibling}return null}function a(a,b){var c="",d,e;for(d in b)b.hasOwnProperty(d)&&(d=b[d],(e=a.getAttributeNS(d[0],d[1]))&&(c+=d[2]+":"+e+";"));return c}function c(a,
b,c,d){b='text|list[text|style-name="'+b+'"]';var e=c.getAttributeNS(r,"level"),f;c=c.firstChild.firstChild;var g,k="";c&&(f=c.attributes,g=f["fo:text-indent"]?f["fo:text-indent"].value:void 0,f=f["fo:margin-left"]?f["fo:margin-left"].value:void 0);g||(g="-0.6cm");c="-"===g.charAt(0)?g.substring(1):"-"+g;for(e=e&&parseInt(e,10);1<e;)b+=" > text|list-item > text|list",e-=1;e=b+" > text|list-item > *:not(text|list):first-child";void 0!==f&&(f=e+"{margin-left:"+f+";}",a.insertRule(f,a.cssRules.length));
k=b+" > text|list-item > *:not(text|list):first-child:before{"+d+";";k+="counter-increment:list;";k+="margin-left:"+g+";";k+="width:"+c+";";k+="display:inline-block}";try{a.insertRule(k,a.cssRules.length)}catch(h){throw h;}}function d(e,f,g,k){if("list"===f)for(var h=k.firstChild,m,l;h;){if(h.namespaceURI===r)if(m=h,"list-level-style-number"===h.localName){l=m;var s=l.getAttributeNS(p,"num-format"),v=l.getAttributeNS(p,"num-suffix"),Q="",Q={1:"decimal",a:"lower-latin",A:"upper-latin",i:"lower-roman",
I:"upper-roman"},I="",I=l.getAttributeNS(p,"num-prefix")||"",I=Q.hasOwnProperty(s)?I+(" counter(list, "+Q[s]+")"):s?I+("'"+s+"';"):I+" ''";v&&(I+=" '"+v+"'");l=Q="content: "+I+";";c(e,g,m,l)}else"list-level-style-image"===h.localName?(l="content: none;",c(e,g,m,l)):"list-level-style-bullet"===h.localName&&(l="content: '"+m.getAttributeNS(r,"bullet-char")+"';",c(e,g,m,l));h=h.nextSibling}else{g=n(f,g,k).join(",");m="";if(h=b(k,p,"text-properties")){s=h;v="";h=""+a(s,w);l=s.getAttributeNS(p,"text-underline-style");
"solid"===l&&(v+=" underline");l=s.getAttributeNS(p,"text-line-through-style");"solid"===l&&(v+=" line-through");v.length&&(h+="text-decoration:"+v+";");if(s=s.getAttributeNS(p,"font-name"))l=L[s],h+="font-family: "+(l||s)+";";m+=h}if(h=b(k,p,"paragraph-properties")){l=h;h=""+a(l,z);l=l.getElementsByTagNameNS(p,"background-image");if(0<l.length&&(s=l.item(0).getAttributeNS(q,"href")))h+="background-image: url('odfkit:"+s+"');",l=l.item(0),h+=a(l,F);m+=h}if(h=b(k,p,"graphic-properties"))h=""+a(h,u),
m+=h;if(h=b(k,p,"table-cell-properties"))h=""+a(h,y),m+=h;if(h=b(k,p,"table-row-properties"))h=""+a(h,H),m+=h;if(h=b(k,p,"table-column-properties"))h=""+a(h,K),m+=h;if(h=b(k,p,"table-properties"))h=""+a(h,B),m+=h;"table"===f&&runtime.log(m);if(0!==m.length)try{e.insertRule(g+"{"+m+"}",e.cssRules.length)}catch(J){throw J;}}for(var M in k.derivedStyles)k.derivedStyles.hasOwnProperty(M)&&d(e,f,M,k.derivedStyles[M])}var m=odf.Namespaces.drawns,k=odf.Namespaces.fons,p=odf.Namespaces.stylens,r=odf.Namespaces.textns,
q=odf.Namespaces.xlinkns,s={graphic:"draw",paragraph:"text",presentation:"presentation",ruby:"text",section:"text",table:"table","table-cell":"table","table-column":"table","table-row":"table",text:"text",list:"text"},v={graphic:"circle connected control custom-shape ellipse frame g line measure page page-thumbnail path polygon polyline rect regular-polygon".split(" "),paragraph:"alphabetical-index-entry-template h illustration-index-entry-template index-source-style object-index-entry-template p table-index-entry-template table-of-content-entry-template user-index-entry-template".split(" "),
presentation:"caption circle connector control custom-shape ellipse frame g line measure page-thumbnail path polygon polyline rect regular-polygon".split(" "),ruby:["ruby","ruby-text"],section:"alphabetical-index bibliography illustration-index index-title object-index section table-of-content table-index user-index".split(" "),table:["background","table"],"table-cell":"body covered-table-cell even-columns even-rows first-column first-row last-column last-row odd-columns odd-rows table-cell".split(" "),
"table-column":["table-column"],"table-row":["table-row"],text:"a index-entry-chapter index-entry-link-end index-entry-link-start index-entry-page-number index-entry-span index-entry-tab-stop index-entry-text index-title-template linenumbering-configuration list-level-style-number list-level-style-bullet outline-level-style span".split(" "),list:["list-item"]},w=[[k,"color","color"],[k,"background-color","background-color"],[k,"font-weight","font-weight"],[k,"font-style","font-style"],[k,"font-size",
"font-size"]],F=[[p,"repeat","background-repeat"]],z=[[k,"background-color","background-color"],[k,"text-align","text-align"],[k,"text-indent","text-indent"],[k,"padding-left","padding-left"],[k,"padding-right","padding-right"],[k,"padding-top","padding-top"],[k,"padding-bottom","padding-bottom"],[k,"border-left","border-left"],[k,"border-right","border-right"],[k,"border-top","border-top"],[k,"border-bottom","border-bottom"],[k,"margin-left","margin-left"],[k,"margin-right","margin-right"],[k,"margin-top",
"margin-top"],[k,"margin-bottom","margin-bottom"],[k,"border","border"]],u=[[m,"fill-color","background-color"],[m,"fill","background"],[k,"min-height","min-height"],[m,"stroke","border"],[odf.Namespaces.svgns,"stroke-color","border-color"]],y=[[k,"background-color","background-color"],[k,"border-left","border-left"],[k,"border-right","border-right"],[k,"border-top","border-top"],[k,"border-bottom","border-bottom"],[k,"border","border"]],K=[[p,"column-width","width"]],H=[[p,"row-height","height"],
[k,"keep-together",null]],B=[[p,"width","width"],[k,"margin-left","margin-left"],[k,"margin-right","margin-right"],[k,"margin-top","margin-top"],[k,"margin-bottom","margin-bottom"]],L={};this.style2css=function(a,b,c,f){for(var g,k,m,p;a.cssRules.length;)a.deleteRule(a.cssRules.length-1);g=null;c&&(g=c.ownerDocument);f&&(g=f.ownerDocument);if(g)for(p in odf.Namespaces.forEachPrefix(function(b,c){m="@namespace "+b+" url("+c+");";try{a.insertRule(m,a.cssRules.length)}catch(d){}}),L=b,b=h(g,c),c=h(g,
f),f={},s)if(s.hasOwnProperty(p))for(k in g=f[p]={},e(b[p],g),e(c[p],g),g)g.hasOwnProperty(k)&&d(a,p,k,g[k])}};
// Input 28
runtime.loadClass("core.Base64");runtime.loadClass("core.Zip");runtime.loadClass("xmldom.LSSerializer");runtime.loadClass("odf.StyleInfo");runtime.loadClass("odf.Namespaces");
odf.OdfContainer=function(){function h(a,b,c){for(a=a?a.firstChild:null;a;){if(a.localName===c&&a.namespaceURI===b)return a;a=a.nextSibling}return null}function l(a){var b,d=k.length;for(b=0;b<d;b+=1)if(a.namespaceURI===c&&a.localName===k[b])return b;return-1}function g(b,c){var d;b&&(d=new a.UsedStyleList(b,c));this.acceptNode=function(a){return"http://www.w3.org/1999/xhtml"===a.namespaceURI?3:a.namespaceURI&&a.namespaceURI.match(/^urn:webodf:/)?2:d&&a.parentNode===c&&1===a.nodeType?d.uses(a)?1:
2:1}}function e(a,b){if(b){var c=l(b),d,e=a.firstChild;if(-1!==c){for(;e;){d=l(e);if(-1!==d&&d>c)break;e=e.nextSibling}a.insertBefore(b,e)}}}function f(a){this.OdfContainer=a}function n(a,b,c,d){var e=this;this.size=0;this.type=null;this.name=a;this.container=c;this.onchange=this.onreadystatechange=this.document=this.mimetype=this.url=null;this.EMPTY=0;this.LOADING=1;this.DONE=2;this.state=this.EMPTY;this.load=function(){null!==d&&(this.mimetype=b,d.loadAsDataURL(a,b,function(a,b){a&&runtime.log(a);
e.url=b;if(e.onchange)e.onchange(e);if(e.onstatereadychange)e.onstatereadychange(e)}))};this.abort=function(){}}function b(a){this.length=0;this.item=function(a){}}var a=new odf.StyleInfo,c="urn:oasis:names:tc:opendocument:xmlns:office:1.0",d="urn:oasis:names:tc:opendocument:xmlns:manifest:1.0",m="urn:webodf:names:scope",k="meta settings scripts font-face-decls styles automatic-styles master-styles body".split(" "),p=(new Date).getTime()+"_webodf_",r=new core.Base64;f.prototype=new function(){};f.prototype.constructor=
f;f.namespaceURI=c;f.localName="document";n.prototype.load=function(){};n.prototype.getUrl=function(){return this.data?"data:;base64,"+r.toBase64(this.data):null};odf.OdfContainer=function s(k,l){function r(a){for(var b=a.firstChild,c;b;)c=b.nextSibling,1===b.nodeType?r(b):7===b.nodeType&&a.removeChild(b),b=c}function z(a,b){for(var c=a&&a.firstChild;c;)1===c.nodeType&&c.setAttributeNS(m,"scope",b),c=c.nextSibling}function u(a,b){var c=null,d,e,f;if(a){c=a.cloneNode(!0);for(d=c.firstChild;d;)e=d.nextSibling,
1===d.nodeType&&(f=d.getAttributeNS(m,"scope"))&&f!==b&&c.removeChild(d),d=e}return c}function y(a){var b=t.rootElement.ownerDocument,c;if(a){r(a.documentElement);try{c=b.importNode(a.documentElement,!0)}catch(d){}}return c}function K(a){t.state=a;if(t.onchange)t.onchange(t);if(t.onstatereadychange)t.onstatereadychange(t)}function H(b){b=y(b);var d=t.rootElement;!b||"document-styles"!==b.localName||b.namespaceURI!==c?K(s.INVALID):(d.fontFaceDecls=h(b,c,"font-face-decls"),e(d,d.fontFaceDecls),d.styles=
h(b,c,"styles"),e(d,d.styles),d.automaticStyles=h(b,c,"automatic-styles"),z(d.automaticStyles,"document-styles"),e(d,d.automaticStyles),d.masterStyles=h(b,c,"master-styles"),e(d,d.masterStyles),a.prefixStyleNames(d.automaticStyles,p,d.masterStyles))}function B(a){a=y(a);var b,d,f;if(!a||"document-content"!==a.localName||a.namespaceURI!==c)K(s.INVALID);else{b=t.rootElement;d=h(a,c,"font-face-decls");if(b.fontFaceDecls&&d)for(f=d.firstChild;f;)b.fontFaceDecls.appendChild(f),f=d.firstChild;else d&&(b.fontFaceDecls=
d,e(b,d));d=h(a,c,"automatic-styles");z(d,"document-content");if(b.automaticStyles&&d)for(f=d.firstChild;f;)b.automaticStyles.appendChild(f),f=d.firstChild;else d&&(b.automaticStyles=d,e(b,d));b.body=h(a,c,"body");e(b,b.body)}}function L(a){a=y(a);var b;if(a&&!("document-meta"!==a.localName||a.namespaceURI!==c))b=t.rootElement,b.meta=h(a,c,"meta"),e(b,b.meta)}function x(a){a=y(a);var b;if(a&&!("document-settings"!==a.localName||a.namespaceURI!==c))b=t.rootElement,b.settings=h(a,c,"settings"),e(b,
b.settings)}function E(a,b){G.loadAsDOM(a,b)}function D(){E("styles.xml",function(a,b){H(b);t.state!==s.INVALID&&E("content.xml",function(a,b){B(b);t.state!==s.INVALID&&E("meta.xml",function(a,b){L(b);t.state!==s.INVALID&&E("settings.xml",function(a,b){b&&x(b);E("META-INF/manifest.xml",function(a,b){if(b){var c=y(b),e;if(c&&!("manifest"!==c.localName||c.namespaceURI!==d)){e=t.rootElement;e.manifest=c;for(c=e.manifest.firstChild;c;)1===c.nodeType&&("file-entry"===c.localName&&c.namespaceURI===d)&&
(R[c.getAttributeNS(d,"full-path")]=c.getAttributeNS(d,"media-type")),c=c.nextSibling}}t.state!==s.INVALID&&K(s.DONE)})})})})})}function A(a){var b="";odf.Namespaces.forEachPrefix(function(a,c){b+=" xmlns:"+a+'="'+c+'"'});return'<?xml version="1.0" encoding="UTF-8"?><office:'+a+" "+b+' office:version="1.2">'}function T(){var a=new xmldom.LSSerializer,b=A("document-meta");a.filter=new g;b+=a.writeToString(t.rootElement.meta,odf.Namespaces.namespaceMap);return b+"</office:document-meta>"}function U(a,
b){var c=document.createElementNS(d,"manifest:file-entry");c.setAttributeNS(d,"manifest:full-path",a);c.setAttributeNS(d,"manifest:media-type",b);return c}function N(){var a=runtime.parseXML('<manifest:manifest xmlns:manifest="'+d+'"></manifest:manifest>'),b=h(a,d,"manifest"),c=new xmldom.LSSerializer,e;for(e in R)R.hasOwnProperty(e)&&b.appendChild(U(e,R[e]));c.filter=new g;return'<?xml version="1.0" encoding="UTF-8" standalone="yes"?>\n'+c.writeToString(a,odf.Namespaces.namespaceMap)}function W(){var a=
new xmldom.LSSerializer,b=A("document-settings");a.filter=new g;b+=a.writeToString(t.rootElement.settings,odf.Namespaces.namespaceMap);return b+"</office:document-settings>"}function S(){var b=odf.Namespaces.namespaceMap,c=new xmldom.LSSerializer,d=u(t.rootElement.automaticStyles,"document-styles"),e=t.rootElement.masterStyles&&t.rootElement.masterStyles.cloneNode(!0),f=A("document-styles");a.removePrefixFromStyleNames(d,p,e);c.filter=new g(e,d);f+=c.writeToString(t.rootElement.fontFaceDecls,b);f+=
c.writeToString(t.rootElement.styles,b);f+=c.writeToString(d,b);f+=c.writeToString(e,b);return f+"</office:document-styles>"}function Q(){var a=odf.Namespaces.namespaceMap,b=new xmldom.LSSerializer,c=u(t.rootElement.automaticStyles,"document-content"),d=A("document-content");b.filter=new g(t.rootElement.body,c);d+=b.writeToString(c,a);d+=b.writeToString(t.rootElement.body,a);return d+"</office:document-content>"}function I(a,b){runtime.loadXML(a,function(a,d){if(a)b(a);else{var e=y(d);!e||"document"!==
e.localName||e.namespaceURI!==c?K(s.INVALID):(t.rootElement=e,e.fontFaceDecls=h(e,c,"font-face-decls"),e.styles=h(e,c,"styles"),e.automaticStyles=h(e,c,"automatic-styles"),e.masterStyles=h(e,c,"master-styles"),e.body=h(e,c,"body"),e.meta=h(e,c,"meta"),K(s.DONE))}})}function J(){var a=new core.Zip("",null),b=runtime.byteArrayFromString("application/vnd.oasis.opendocument.text","utf8"),d=t.rootElement,e=document.createElementNS(c,"text");a.save("mimetype",b,!1,new Date);d.body=document.createElementNS(c,
"body");d.body.appendChild(e);d.appendChild(d.body);K(s.DONE);return a}function M(){var a,b=new Date;a=runtime.byteArrayFromString(W(),"utf8");G.save("settings.xml",a,!0,b);a=runtime.byteArrayFromString(T(),"utf8");G.save("meta.xml",a,!0,b);a=runtime.byteArrayFromString(S(),"utf8");G.save("styles.xml",a,!0,b);a=runtime.byteArrayFromString(Q(),"utf8");G.save("content.xml",a,!0,b);a=runtime.byteArrayFromString(N(),"utf8");G.save("META-INF/manifest.xml",a,!0,b)}function C(a,b){M();G.writeAs(a,function(a){b(a)})}
var t=this,G,R={};this.onstatereadychange=l;this.parts=this.rootElement=this.state=this.onchange=null;this.getPart=function(a){return new n(a,R[a],t,G)};this.getPartData=function(a,b){G.load(a,b)};this.createByteArray=function(a,b){M();G.createByteArray(a,b)};this.saveAs=C;this.save=function(a){C(k,a)};this.getUrl=function(){return k};this.state=s.LOADING;this.rootElement=function(a){var b=document.createElementNS(a.namespaceURI,a.localName),c;a=new a;for(c in a)a.hasOwnProperty(c)&&(b[c]=a[c]);return b}(f);
this.parts=new b(this);G=k?new core.Zip(k,function(a,b){G=b;a?I(k,function(b){a&&(G.error=a+"\n"+b,K(s.INVALID))}):D()}):J()};odf.OdfContainer.EMPTY=0;odf.OdfContainer.LOADING=1;odf.OdfContainer.DONE=2;odf.OdfContainer.INVALID=3;odf.OdfContainer.SAVING=4;odf.OdfContainer.MODIFIED=5;odf.OdfContainer.getContainer=function(a){return new odf.OdfContainer(a,null)};return odf.OdfContainer}();
// Input 29
/*

 Copyright (C) 2012-2013 KO GmbH <copyright@kogmbh.com>

 @licstart
 The JavaScript code in this page is free software: you can redistribute it
 and/or modify it under the terms of the GNU Affero General Public License
 (GNU AGPL) as published by the Free Software Foundation, either version 3 of
 the License, or (at your option) any later version.  The code is distributed
 WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 FITNESS FOR A PARTICULAR PURPOSE.  See the GNU AGPL for more details.

 As additional permission under GNU AGPL version 3 section 7, you
 may distribute non-source (e.g., minimized or compacted) forms of
 that code without the copy of the GNU GPL normally required by
 section 4, provided you include this license notice and a URL
 through which recipients can access the Corresponding Source.

 As a special exception to the AGPL, any HTML file which merely makes function
 calls to this code, and for that purpose includes it by reference shall be
 deemed a separate work for copyright law purposes. In addition, the copyright
 holders of this code give you permission to combine this code with free
 software libraries that are released under the GNU LGPL. You may copy and
 distribute such a system following the terms of the GNU AGPL for this code
 and the LGPL for the libraries. If you modify this code, you may extend this
 exception to your version of the code, but you are not obligated to do so.
 If you do not wish to do so, delete this exception statement from your
 version.

 This license applies to this entire compilation.
 @licend
 @source: http://www.webodf.org/
 @source: http://gitorious.org/webodf/webodf/
*/
runtime.loadClass("core.Base64");runtime.loadClass("xmldom.XPath");runtime.loadClass("odf.OdfContainer");
odf.FontLoader=function(){function h(f,g,b,a,c){var d,m=0,k;for(k in f)if(f.hasOwnProperty(k)){if(m===b){d=k;break}m+=1}if(!d)return c();g.getPartData(f[d].href,function(k,m){if(k)runtime.log(k);else{var l="@font-face { font-family: '"+(f[d].family||d)+"'; src: url(data:application/x-font-ttf;charset=binary;base64,"+e.convertUTF8ArrayToBase64(m)+') format("truetype"); }';try{a.insertRule(l,a.cssRules.length)}catch(s){runtime.log("Problem inserting rule in CSS: "+runtime.toJson(s)+"\nRule: "+l)}}h(f,
g,b+1,a,c)})}function l(e,g,b){h(e,g,0,b,function(){})}var g=new xmldom.XPath,e=new core.Base64;odf.FontLoader=function(){this.loadFonts=function(e,h){for(var b=e.rootElement.fontFaceDecls;h.cssRules.length;)h.deleteRule(h.cssRules.length-1);if(b){var a={},c,d,m,k;if(b){b=g.getODFElementsWithXPath(b,"style:font-face[svg:font-face-src]",odf.Namespaces.resolvePrefix);for(c=0;c<b.length;c+=1)d=b[c],m=d.getAttributeNS(odf.Namespaces.stylens,"name"),k=d.getAttributeNS(odf.Namespaces.svgns,"font-family"),
d=g.getODFElementsWithXPath(d,"svg:font-face-src/svg:font-face-uri",odf.Namespaces.resolvePrefix),0<d.length&&(d=d[0].getAttributeNS(odf.Namespaces.xlinkns,"href"),a[m]={href:d,family:k})}l(a,e,h)}}};return odf.FontLoader}();
// Input 30
/*

 Copyright (C) 2012-2013 KO GmbH <copyright@kogmbh.com>

 @licstart
 The JavaScript code in this page is free software: you can redistribute it
 and/or modify it under the terms of the GNU Affero General Public License
 (GNU AGPL) as published by the Free Software Foundation, either version 3 of
 the License, or (at your option) any later version.  The code is distributed
 WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 FITNESS FOR A PARTICULAR PURPOSE.  See the GNU AGPL for more details.

 As additional permission under GNU AGPL version 3 section 7, you
 may distribute non-source (e.g., minimized or compacted) forms of
 that code without the copy of the GNU GPL normally required by
 section 4, provided you include this license notice and a URL
 through which recipients can access the Corresponding Source.

 As a special exception to the AGPL, any HTML file which merely makes function
 calls to this code, and for that purpose includes it by reference shall be
 deemed a separate work for copyright law purposes. In addition, the copyright
 holders of this code give you permission to combine this code with free
 software libraries that are released under the GNU LGPL. You may copy and
 distribute such a system following the terms of the GNU AGPL for this code
 and the LGPL for the libraries. If you modify this code, you may extend this
 exception to your version of the code, but you are not obligated to do so.
 If you do not wish to do so, delete this exception statement from your
 version.

 This license applies to this entire compilation.
 @licend
 @source: http://www.webodf.org/
 @source: http://gitorious.org/webodf/webodf/
*/
runtime.loadClass("odf.OdfContainer");
odf.Formatting=function(){function h(a,b){for(var d in b)if(b.hasOwnProperty(d))try{a[d]=b[d].constructor===Object?h(a[d],b[d]):b[d]}catch(e){a[d]=b[d]}return a}function l(a,c,d){for(a=a.firstChild;a;){if(1===a.nodeType&&a.namespaceURI===b&&"style"===a.localName&&a.getAttributeNS(b,"family")===d&&a.getAttributeNS(b,"name")===c)return a;a=a.nextSibling}return null}function g(a){for(var c={},d=a.firstChild;d;){if(1===d.nodeType&&d.namespaceURI===b){c[d.nodeName]={};for(a=0;a<d.attributes.length;a+=
1)c[d.nodeName][d.attributes[a].name]=d.attributes[a].value}d=d.nextSibling}return c}var e,f=new odf.StyleInfo,n=odf.Namespaces.svgns,b=odf.Namespaces.stylens;this.setOdfContainer=function(a){e=a};this.getFontMap=function(){for(var a=e.rootElement.fontFaceDecls,c={},d,f,a=a&&a.firstChild;a;){if(1===a.nodeType&&(d=a.getAttributeNS(b,"name")))if((f=a.getAttributeNS(n,"font-family"))||a.getElementsByTagNameNS(n,"font-face-uri")[0])c[d]=f;a=a.nextSibling}return c};this.getAvailableParagraphStyles=function(){for(var a=
e.rootElement.styles&&e.rootElement.styles.firstChild,c,d,f=[];a;)1===a.nodeType&&("style"===a.localName&&a.namespaceURI===b)&&(d=a,c=d.getAttributeNS(b,"family"),"paragraph"===c&&(c=d.getAttributeNS(b,"name"),d=d.getAttributeNS(b,"display-name")||c,c&&d&&f.push({name:c,displayName:d}))),a=a.nextSibling;return f};this.isStyleUsed=function(a){var b;b=f.hasDerivedStyles(e.rootElement,odf.Namespaces.resolvePrefix,a);a=(new f.UsedStyleList(e.rootElement.styles)).uses(a)||(new f.UsedStyleList(e.rootElement.automaticStyles)).uses(a)||
(new f.UsedStyleList(e.rootElement.body)).uses(a);return b||a};this.getStyleElement=l;this.getInheritedStyleAttributes=function(a,c){var d;d={};for(var e={},f=c;f;)d=g(f),e=h(d,e),f=(d=f.getAttributeNS(b,"parent-style-name"))?l(a,d,c.getAttributeNS(b,"family")):null;a:{d=c.getAttributeNS(b,"family");for(f=a.firstChild;f;){if(1===f.nodeType&&f.namespaceURI===b&&"default-style"===f.localName&&f.getAttributeNS(b,"family")===d){d=f;break a}f=f.nextSibling}d=null}d=g(d);return e=h(d,e)};this.getFirstNamedParentStyleNameOrSelf=
function(a){for(var c=e.rootElement.automaticStyles,d=e.rootElement.styles,f;null!==(f=l(c,a,"paragraph"));)a=f.getAttributeNS(b,"parent-style-name");f=l(d,a,"paragraph");return!f?null:a};this.hasParagraphStyle=function(a){return l(e.rootElement.automaticStyles,a,"paragraph")||l(e.rootElement.styles,a,"paragraph")};this.getParagraphStyleAttribute=function(a,c,d){for(var f=e.rootElement.automaticStyles,g=e.rootElement.styles,h;null!==(h=l(f,a,"paragraph"));){if(a=h.getAttributeNS(c,d))return a;a=h.getAttributeNS(b,
"parent-style-name")}for(;null!==(h=l(g,a,"paragraph"));){if(a=h.getAttributeNS(c,d))return a;a=h.getAttributeNS(b,"parent-style-name")}return null}};
// Input 31
/*

 Copyright (C) 2012-2013 KO GmbH <copyright@kogmbh.com>

 @licstart
 The JavaScript code in this page is free software: you can redistribute it
 and/or modify it under the terms of the GNU Affero General Public License
 (GNU AGPL) as published by the Free Software Foundation, either version 3 of
 the License, or (at your option) any later version.  The code is distributed
 WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 FITNESS FOR A PARTICULAR PURPOSE.  See the GNU AGPL for more details.

 As additional permission under GNU AGPL version 3 section 7, you
 may distribute non-source (e.g., minimized or compacted) forms of
 that code without the copy of the GNU GPL normally required by
 section 4, provided you include this license notice and a URL
 through which recipients can access the Corresponding Source.

 As a special exception to the AGPL, any HTML file which merely makes function
 calls to this code, and for that purpose includes it by reference shall be
 deemed a separate work for copyright law purposes. In addition, the copyright
 holders of this code give you permission to combine this code with free
 software libraries that are released under the GNU LGPL. You may copy and
 distribute such a system following the terms of the GNU AGPL for this code
 and the LGPL for the libraries. If you modify this code, you may extend this
 exception to your version of the code, but you are not obligated to do so.
 If you do not wish to do so, delete this exception statement from your
 version.

 This license applies to this entire compilation.
 @licend
 @source: http://www.webodf.org/
 @source: http://gitorious.org/webodf/webodf/
*/
runtime.loadClass("odf.OdfContainer");runtime.loadClass("odf.Formatting");runtime.loadClass("xmldom.XPath");runtime.loadClass("odf.FontLoader");runtime.loadClass("odf.Style2CSS");
odf.OdfCanvas=function(){function h(){function a(d){c=!0;runtime.setTimeout(function(){try{d()}catch(e){runtime.log(e)}c=!1;0<b.length&&a(b.pop())},10)}var b=[],c=!1;this.clearQueue=function(){b.length=0};this.addToQueue=function(d){if(0===b.length&&!c)return a(d);b.push(d)}}function l(a){function b(){for(;0<c.cssRules.length;)c.deleteRule(0);c.insertRule("office|presentation draw|page {display:none;}",0);c.insertRule("office|presentation draw|page:nth-child("+d+") {display:block;}",1)}var c=a.sheet,
d=1;this.showFirstPage=function(){d=1;b()};this.showNextPage=function(){d+=1;b()};this.showPreviousPage=function(){1<d&&(d-=1,b())};this.showPage=function(a){0<a&&(d=a,b())};this.css=a}function g(a,b,c){a.addEventListener?a.addEventListener(b,c,!1):a.attachEvent?a.attachEvent("on"+b,c):a["on"+b]=c}function e(a){function b(a,c){for(;c;){if(c===a)return!0;c=c.parentNode}return!1}function c(){var f=[],g=runtime.getWindow().getSelection(),h,k;for(h=0;h<g.rangeCount;h+=1)k=g.getRangeAt(h),null!==k&&(b(a,
k.startContainer)&&b(a,k.endContainer))&&f.push(k);if(f.length===d.length){for(g=0;g<f.length&&!(h=f[g],k=d[g],h=h===k?!1:null===h||null===k?!0:h.startContainer!==k.startContainer||h.startOffset!==k.startOffset||h.endContainer!==k.endContainer||h.endOffset!==k.endOffset,h);g+=1);if(g===f.length)return}d=f;var g=[f.length],m,p=a.ownerDocument;for(h=0;h<f.length;h+=1)k=f[h],m=p.createRange(),m.setStart(k.startContainer,k.startOffset),m.setEnd(k.endContainer,k.endOffset),g[h]=m;d=g;g=e.length;for(f=
0;f<g;f+=1)e[f](a,d)}var d=[],e=[];this.addListener=function(a,b){var c,d=e.length;for(c=0;c<d;c+=1)if(e[c]===b)return;e.push(b)};g(a,"mouseup",c);g(a,"keyup",c);g(a,"keydown",c)}function f(a){for(a=a.firstChild;a;){if(a.namespaceURI===p&&"binary-data"===a.localName)return"data:image/png;base64,"+a.textContent.replace(/[\r\n\s]/g,"");a=a.nextSibling}return""}function n(a,b,c,d){function e(b){b&&(b='draw|image[styleid="'+a+'"] {'+("background-image: url("+b+");")+"}",d.insertRule(b,d.cssRules.length))}
c.setAttribute("styleid",a);var g=c.getAttributeNS(w,"href"),h;if(g)try{h=b.getPart(g),h.onchange=function(a){e(a.url)},h.load()}catch(k){runtime.log("slight problem: "+k)}else g=f(c),e(g)}function b(a,b,c){function d(a,b,c){b.hasAttributeNS(w,"href")&&(b.onclick=function(){z.open(b.getAttributeNS(w,"href"))})}var e,f;e=b.getElementsByTagNameNS(v,"a");for(b=0;b<e.length;b+=1)f=e.item(b),d(a,f,c)}function a(a,b,c,d){function e(a,b){var d=k.documentElement.namespaceURI;"video/"===b.substr(0,6)?(g=k.createElementNS(d,
"video"),g.setAttribute("controls","controls"),h=k.createElementNS(d,"source"),h.setAttribute("src",a),h.setAttribute("type",b),g.appendChild(h),c.parentNode.appendChild(g)):c.innerHtml="Unrecognised Plugin"}var g,h,k=c.ownerDocument,m;if(a=c.getAttributeNS(w,"href"))try{m=b.getPart(a),m.onchange=function(a){e(a.url,a.mimetype)},m.load()}catch(p){runtime.log("slight problem: "+p)}else runtime.log("using MP4 data fallback"),a=f(c),e(a,"video/mp4")}function c(a){var b=a.getElementsByTagName("head")[0],
c;"undefined"!==String(typeof webodf_css)?(c=a.createElementNS(b.namespaceURI,"style"),c.setAttribute("media","screen, print, handheld, projection"),c.appendChild(a.createTextNode(webodf_css))):(c=a.createElementNS(b.namespaceURI,"link"),a="webodf.css",runtime.currentDirectory&&(a=runtime.currentDirectory()+"/../"+a),c.setAttribute("href",a),c.setAttribute("rel","stylesheet"));c.setAttribute("type","text/css");b.appendChild(c);return c}function d(a){var b=a.getElementsByTagName("head")[0],c=a.createElementNS(b.namespaceURI,
"style"),d="";c.setAttribute("type","text/css");c.setAttribute("media","screen, print, handheld, projection");odf.Namespaces.forEachPrefix(function(a,b){d+="@namespace "+a+" url("+b+");\n"});c.appendChild(a.createTextNode(d));b.appendChild(c);return c}var m=odf.Namespaces.drawns,k=odf.Namespaces.fons,p=odf.Namespaces.officens,r=odf.Namespaces.stylens,q=odf.Namespaces.svgns,s=odf.Namespaces.tablens,v=odf.Namespaces.textns,w=odf.Namespaces.xlinkns,F=odf.Namespaces.xmlns,z=runtime.getWindow(),u=new xmldom.XPath;
odf.OdfCanvas=function(f){function p(a,b,c){function d(a,b,c,e){t.addToQueue(function(){n(a,b,c,e)})}var e,f;e=b.getElementsByTagNameNS(m,"image");for(b=0;b<e.length;b+=1)f=e.item(b),d("image"+String(b),a,f,c)}function w(b,c,d){function e(b,c,d,f){t.addToQueue(function(){a(b,c,d,f)})}var f,g;f=c.getElementsByTagNameNS(m,"plugin");for(c=0;c<f.length;c+=1)g=f.item(c),e("video"+String(c),b,g,d)}function B(){var a=f.firstChild;a.firstChild&&(1<J?(a.style.MozTransformOrigin="center top",a.style.WebkitTransformOrigin=
"center top",a.style.OTransformOrigin="center top",a.style.msTransformOrigin="center top"):(a.style.MozTransformOrigin="left top",a.style.WebkitTransformOrigin="left top",a.style.OTransformOrigin="left top",a.style.msTransformOrigin="left top"),a.style.WebkitTransform="scale("+J+")",a.style.MozTransform="scale("+J+")",a.style.OTransform="scale("+J+")",a.style.msTransform="scale("+J+")",f.style.width=Math.round(J*a.offsetWidth)+"px",f.style.height=Math.round(J*a.offsetHeight)+"px")}function L(){function a(){for(var c=
f;c.firstChild;)c.removeChild(c.firstChild);f.style.display="inline-block";var d=A.rootElement;f.ownerDocument.importNode(d,!0);T.setOdfContainer(A);var c=A,e=W;(new odf.FontLoader).loadFonts(c,e.sheet);c=T;e=S;(new odf.Style2CSS).style2css(e.sheet,c.getFontMap(),d.styles,d.automaticStyles);var e=A,c=Q.sheet,g;g=d.body;var h,l,n;l=[];for(h=g.firstChild;h&&h!==g;)if(h.namespaceURI===m&&(l[l.length]=h),h.firstChild)h=h.firstChild;else{for(;h&&h!==g&&!h.nextSibling;)h=h.parentNode;h&&h.nextSibling&&
(h=h.nextSibling)}for(n=0;n<l.length;n+=1){h=l[n];var x="frame"+String(n),t=c;h.setAttribute("styleid",x);var G=void 0,E=h.getAttributeNS(v,"anchor-type"),C=h.getAttributeNS(q,"x"),L=h.getAttributeNS(q,"y"),I=h.getAttributeNS(q,"width"),J=h.getAttributeNS(q,"height"),N=h.getAttributeNS(k,"min-height"),U=h.getAttributeNS(k,"min-width");if("as-char"===E)G="display: inline-block;";else if(E||C||L)G="position: absolute;";else if(I||J||N||U)G="display: block;";C&&(G+="left: "+C+";");L&&(G+="top: "+L+";");
I&&(G+="width: "+I+";");J&&(G+="height: "+J+";");N&&(G+="min-height: "+N+";");U&&(G+="min-width: "+U+";");G&&(G="draw|"+h.localName+'[styleid="'+x+'"] {'+G+"}",t.insertRule(G,t.cssRules.length))}n=u.getODFElementsWithXPath(g,".//*[*[@text:anchor-type='paragraph']]",odf.Namespaces.resolvePrefix);for(l=0;l<n.length;l+=1)g=n[l],g.setAttributeNS&&g.setAttributeNS("urn:webodf","containsparagraphanchor",!0);c.insertRule("draw|page { background-color:#fff; }",c.cssRules.length);for(g=f;g.firstChild;)g.removeChild(g.firstChild);
g=D.createElementNS(f.namespaceURI,"div");g.style.display="inline-block";g.style.background="white";g.appendChild(d);f.appendChild(g);l=d.body.getElementsByTagNameNS(s,"table-cell");for(g=0;g<l.length;g+=1)n=l.item(g),n.hasAttributeNS(s,"number-columns-spanned")&&n.setAttribute("colspan",n.getAttributeNS(s,"number-columns-spanned")),n.hasAttributeNS(s,"number-rows-spanned")&&n.setAttribute("rowspan",n.getAttributeNS(s,"number-rows-spanned"));b(e,d.body,c);l=d.body;g=l.ownerDocument;n=l.getElementsByTagNameNS(v,
"s");for(l=0;l<n.length;l+=1){for(t=x=n.item(l);t.firstChild;)t.removeChild(t.firstChild);t.appendChild(g.createTextNode(" "));h=parseInt(t.getAttributeNS(v,"c"),10);if(1<h){t.removeAttributeNS(v,"c");for(x=1;x<h;x+=1)t.parentNode.insertBefore(t.cloneNode(!0),t)}}p(e,d.body,c);w(e,d.body,c);l=d.body;e={};g={};var P;n=z.document.getElementsByTagNameNS(v,"list-style");for(d=0;d<n.length;d+=1)t=n.item(d),(G=t.getAttributeNS(r,"name"))&&(g[G]=t);l=l.getElementsByTagNameNS(v,"list");for(d=0;d<l.length;d+=
1)if(t=l.item(d),n=t.getAttributeNS(F,"id")){x=t.getAttributeNS(v,"continue-list");t.setAttribute("id",n);h="text|list#"+n+" > text|list-item > *:first-child:before {";if(G=t.getAttributeNS(v,"style-name"))t=g[G],P=t.firstChild,t=void 0,"list-level-style-number"===P.localName?(t=P.getAttributeNS(r,"num-format"),G=P.getAttributeNS(r,"num-suffix"),E="",E={1:"decimal",a:"lower-latin",A:"upper-latin",i:"lower-roman",I:"upper-roman"},C=void 0,C=P.getAttributeNS(r,"num-prefix")||"",C=E.hasOwnProperty(t)?
C+(" counter(list, "+E[t]+")"):t?C+("'"+t+"';"):C+" ''",G&&(C+=" '"+G+"'"),t=E="content: "+C+";"):"list-level-style-image"===P.localName?t="content: none;":"list-level-style-bullet"===P.localName&&(t="content: '"+P.getAttributeNS(v,"bullet-char")+"';"),P=t;if(x){for(t=e[x];t;)x=t,t=e[x];h+="counter-increment:"+x+";";P?(P=P.replace("list",x),h+=P):h+="content:counter("+x+");"}else x="",P?(P=P.replace("list",n),h+=P):h+="content: counter("+n+");",h+="counter-increment:"+n+";",c.insertRule("text|list#"+
n+" {counter-reset:"+n+"}",c.cssRules.length);h+="}";e[n]=x;h&&c.insertRule(h,c.cssRules.length)}B();c=[A];if(M.hasOwnProperty("statereadychange")){P=M.statereadychange;for(d=0;d<P.length;d+=1)P[d].apply(null,c)}}A.state===odf.OdfContainer.DONE?a():(runtime.log("WARNING: refreshOdf called but ODF was not DONE."),runtime.setTimeout(function O(){A.state===odf.OdfContainer.DONE?a():(runtime.log("will be back later..."),runtime.setTimeout(O,500))},100))}function x(){if(C){for(var a=C.ownerDocument.createDocumentFragment();C.firstChild;)a.insertBefore(C.firstChild,
null);C.parentNode.replaceChild(a,C)}}function E(a){a=a||z.event;for(var b=a.target,c=z.getSelection(),d=0<c.rangeCount?c.getRangeAt(0):null,e=d&&d.startContainer,f=d&&d.startOffset,h=d&&d.endContainer,g=d&&d.endOffset,k,m;b&&!(("p"===b.localName||"h"===b.localName)&&b.namespaceURI===v);)b=b.parentNode;I&&(b&&b.parentNode!==C)&&(k=b.ownerDocument,m=k.documentElement.namespaceURI,C?C.parentNode&&x():(C=k.createElementNS(m,"p"),C.style.margin="0px",C.style.padding="0px",C.style.border="0px",C.setAttribute("contenteditable",
!0)),b.parentNode.replaceChild(C,b),C.appendChild(b),C.focus(),d&&(c.removeAllRanges(),d=b.ownerDocument.createRange(),d.setStart(e,f),d.setEnd(h,g),c.addRange(d)),a.preventDefault?(a.preventDefault(),a.stopPropagation()):(a.returnValue=!1,a.cancelBubble=!0))}var D=f.ownerDocument,A,T=new odf.Formatting,U=new e(f),N,W,S,Q,I=!1,J=1,M={},C,t=new h;c(D);N=new l(d(D));W=d(D);S=d(D);Q=d(D);this.refreshCSS=function(){var a=A.rootElement,b=T,c=S;(new odf.Style2CSS).style2css(c.sheet,b.getFontMap(),a.styles,
a.automaticStyles);B()};this.refreshSize=function(){B()};this.odfContainer=function(){return A};this.slidevisibilitycss=function(){return N.css};this.setOdfContainer=function(a){A=a;L()};this.load=this.load=function(a){t.clearQueue();f.innerHTML="loading "+a;f.removeAttribute("style");A=new odf.OdfContainer(a,function(a){A=a;L()})};this.save=function(a){x();A.save(a)};this.setEditable=function(a){g(f,"click",E);(I=a)||x()};this.addListener=function(a,b){switch(a){case "selectionchange":U.addListener(a,
b);break;case "click":g(f,a,b);break;default:var c=M[a];void 0===c&&(c=M[a]=[]);b&&-1===c.indexOf(b)&&c.push(b)}};this.getFormatting=function(){return T};this.setZoomLevel=function(a){J=a;B()};this.getZoomLevel=function(){return J};this.fitToContainingElement=function(a,b){var c=f.offsetHeight/J;J=a/(f.offsetWidth/J);b/c<J&&(J=b/c);B()};this.fitToWidth=function(a){J=a/(f.offsetWidth/J);B()};this.fitSmart=function(a,b){var c,d;c=f.offsetWidth/J;d=f.offsetHeight/J;c=a/c;void 0!==b&&b/d<c&&(c=b/d);J=
Math.min(1,c);B()};this.fitToHeight=function(a){J=a/(f.offsetHeight/J);B()};this.showFirstPage=function(){N.showFirstPage()};this.showNextPage=function(){N.showNextPage()};this.showPreviousPage=function(){N.showPreviousPage()};this.showPage=function(a){N.showPage(a)};this.showAllPages=function(){};this.getElement=function(){return f}};return odf.OdfCanvas}();
// Input 32
runtime.loadClass("odf.OdfCanvas");
odf.CommandLineTools=function(){this.roundTrip=function(h,l,g){new odf.OdfContainer(h,function(e){if(e.state===odf.OdfContainer.INVALID)return g("Document "+h+" is invalid.");e.state===odf.OdfContainer.DONE?e.saveAs(l,function(e){g(e)}):g("Document was not completely loaded.")})};this.render=function(h,l,g){for(l=l.getElementsByTagName("body")[0];l.firstChild;)l.removeChild(l.firstChild);l=new odf.OdfCanvas(l);l.addListener("statereadychange",function(e){g(e)});l.load(h)}};
// Input 33
/*

 Copyright (C) 2012-2013 KO GmbH <copyright@kogmbh.com>

 @licstart
 The JavaScript code in this page is free software: you can redistribute it
 and/or modify it under the terms of the GNU Affero General Public License
 (GNU AGPL) as published by the Free Software Foundation, either version 3 of
 the License, or (at your option) any later version.  The code is distributed
 WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 FITNESS FOR A PARTICULAR PURPOSE.  See the GNU AGPL for more details.

 As additional permission under GNU AGPL version 3 section 7, you
 may distribute non-source (e.g., minimized or compacted) forms of
 that code without the copy of the GNU GPL normally required by
 section 4, provided you include this license notice and a URL
 through which recipients can access the Corresponding Source.

 As a special exception to the AGPL, any HTML file which merely makes function
 calls to this code, and for that purpose includes it by reference shall be
 deemed a separate work for copyright law purposes. In addition, the copyright
 holders of this code give you permission to combine this code with free
 software libraries that are released under the GNU LGPL. You may copy and
 distribute such a system following the terms of the GNU AGPL for this code
 and the LGPL for the libraries. If you modify this code, you may extend this
 exception to your version of the code, but you are not obligated to do so.
 If you do not wish to do so, delete this exception statement from your
 version.

 This license applies to this entire compilation.
 @licend
 @source: http://www.webodf.org/
 @source: http://gitorious.org/webodf/webodf/
*/
ops.Operation=function(){};ops.Operation.prototype.init=function(h){};ops.Operation.prototype.execute=function(h){};ops.Operation.prototype.spec=function(){};
// Input 34
/*

 Copyright (C) 2012-2013 KO GmbH <copyright@kogmbh.com>

 @licstart
 The JavaScript code in this page is free software: you can redistribute it
 and/or modify it under the terms of the GNU Affero General Public License
 (GNU AGPL) as published by the Free Software Foundation, either version 3 of
 the License, or (at your option) any later version.  The code is distributed
 WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 FITNESS FOR A PARTICULAR PURPOSE.  See the GNU AGPL for more details.

 As additional permission under GNU AGPL version 3 section 7, you
 may distribute non-source (e.g., minimized or compacted) forms of
 that code without the copy of the GNU GPL normally required by
 section 4, provided you include this license notice and a URL
 through which recipients can access the Corresponding Source.

 As a special exception to the AGPL, any HTML file which merely makes function
 calls to this code, and for that purpose includes it by reference shall be
 deemed a separate work for copyright law purposes. In addition, the copyright
 holders of this code give you permission to combine this code with free
 software libraries that are released under the GNU LGPL. You may copy and
 distribute such a system following the terms of the GNU AGPL for this code
 and the LGPL for the libraries. If you modify this code, you may extend this
 exception to your version of the code, but you are not obligated to do so.
 If you do not wish to do so, delete this exception statement from your
 version.

 This license applies to this entire compilation.
 @licend
 @source: http://www.webodf.org/
 @source: http://gitorious.org/webodf/webodf/
*/
ops.OpAddCursor=function(){var h,l;this.init=function(g){h=g.memberid;l=g.timestamp};this.execute=function(g){var e=g.getCursor(h);if(e)return!1;e=new ops.OdtCursor(h,g);g.addCursor(e);g.emit(ops.OdtDocument.signalCursorAdded,e);return!0};this.spec=function(){return{optype:"AddCursor",memberid:h,timestamp:l}}};
// Input 35
/*

 Copyright (C) 2012-2013 KO GmbH <copyright@kogmbh.com>

 @licstart
 The JavaScript code in this page is free software: you can redistribute it
 and/or modify it under the terms of the GNU Affero General Public License
 (GNU AGPL) as published by the Free Software Foundation, either version 3 of
 the License, or (at your option) any later version.  The code is distributed
 WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 FITNESS FOR A PARTICULAR PURPOSE.  See the GNU AGPL for more details.

 As additional permission under GNU AGPL version 3 section 7, you
 may distribute non-source (e.g., minimized or compacted) forms of
 that code without the copy of the GNU GPL normally required by
 section 4, provided you include this license notice and a URL
 through which recipients can access the Corresponding Source.

 As a special exception to the AGPL, any HTML file which merely makes function
 calls to this code, and for that purpose includes it by reference shall be
 deemed a separate work for copyright law purposes. In addition, the copyright
 holders of this code give you permission to combine this code with free
 software libraries that are released under the GNU LGPL. You may copy and
 distribute such a system following the terms of the GNU AGPL for this code
 and the LGPL for the libraries. If you modify this code, you may extend this
 exception to your version of the code, but you are not obligated to do so.
 If you do not wish to do so, delete this exception statement from your
 version.

 This license applies to this entire compilation.
 @licend
 @source: http://www.webodf.org/
 @source: http://gitorious.org/webodf/webodf/
*/
ops.OpRemoveCursor=function(){var h,l;this.init=function(g){h=g.memberid;l=g.timestamp};this.execute=function(g){if(!g.removeCursor(h))return!1;g.emit(ops.OdtDocument.signalCursorRemoved,h);return!0};this.spec=function(){return{optype:"RemoveCursor",memberid:h,timestamp:l}}};
// Input 36
/*

 Copyright (C) 2012-2013 KO GmbH <copyright@kogmbh.com>

 @licstart
 The JavaScript code in this page is free software: you can redistribute it
 and/or modify it under the terms of the GNU Affero General Public License
 (GNU AGPL) as published by the Free Software Foundation, either version 3 of
 the License, or (at your option) any later version.  The code is distributed
 WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 FITNESS FOR A PARTICULAR PURPOSE.  See the GNU AGPL for more details.

 As additional permission under GNU AGPL version 3 section 7, you
 may distribute non-source (e.g., minimized or compacted) forms of
 that code without the copy of the GNU GPL normally required by
 section 4, provided you include this license notice and a URL
 through which recipients can access the Corresponding Source.

 As a special exception to the AGPL, any HTML file which merely makes function
 calls to this code, and for that purpose includes it by reference shall be
 deemed a separate work for copyright law purposes. In addition, the copyright
 holders of this code give you permission to combine this code with free
 software libraries that are released under the GNU LGPL. You may copy and
 distribute such a system following the terms of the GNU AGPL for this code
 and the LGPL for the libraries. If you modify this code, you may extend this
 exception to your version of the code, but you are not obligated to do so.
 If you do not wish to do so, delete this exception statement from your
 version.

 This license applies to this entire compilation.
 @licend
 @source: http://www.webodf.org/
 @source: http://gitorious.org/webodf/webodf/
*/
ops.OpMoveCursor=function(){var h,l,g;this.init=function(e){h=e.memberid;l=e.timestamp;g=e.number};this.execute=function(e){var f=e.getCursor(h),l=e.getPositionFilter(),b;if(!f)return!1;b=f.getStepCounter();if(0<g)l=b.countForwardSteps(g,l);else if(0>g)l=-b.countBackwardSteps(-g,l);else return!0;f.move(l);e.emit(ops.OdtDocument.signalCursorMoved,f);return!0};this.spec=function(){return{optype:"MoveCursor",memberid:h,timestamp:l,number:g}}};
// Input 37
ops.OpInsertTable=function(){function h(a,b){var c;if(1===d.length)c=d[0];else if(3===d.length)switch(a){case 0:c=d[0];break;case e-1:c=d[2];break;default:c=d[1]}else c=d[a];if(1===c.length)return c[0];if(3===c.length)switch(b){case 0:return c[0];case f-1:return c[2];default:return c[1]}return c[b]}var l,g,e,f,n,b,a,c,d;this.init=function(h){l=h.memberid;g=h.timestamp;n=h.position;e=h.initialRows;f=h.initialColumns;b=h.tableName;a=h.tableStyleName;c=h.tableColumnStyleName;d=h.tableCellStyleMatrix};
this.execute=function(d){var k=d.getPositionInTextNode(n),p=d.getRootNode();if(k){var r=d.getDOM(),q=r.createElementNS("urn:oasis:names:tc:opendocument:xmlns:table:1.0","table:table"),s=r.createElementNS("urn:oasis:names:tc:opendocument:xmlns:table:1.0","table:table-column"),v,w,F,z;a&&q.setAttributeNS("urn:oasis:names:tc:opendocument:xmlns:table:1.0","table:style-name",a);b&&q.setAttributeNS("urn:oasis:names:tc:opendocument:xmlns:table:1.0","table:name",b);s.setAttributeNS("urn:oasis:names:tc:opendocument:xmlns:table:1.0",
"table:number-columns-repeated",f);c&&s.setAttributeNS("urn:oasis:names:tc:opendocument:xmlns:table:1.0","table:style-name",c);q.appendChild(s);for(F=0;F<e;F+=1){s=r.createElementNS("urn:oasis:names:tc:opendocument:xmlns:table:1.0","table:table-row");for(z=0;z<f;z+=1)v=r.createElementNS("urn:oasis:names:tc:opendocument:xmlns:table:1.0","table:table-cell"),(w=h(F,z))&&v.setAttributeNS("urn:oasis:names:tc:opendocument:xmlns:table:1.0","table:style-name",w),w=r.createElementNS("urn:oasis:names:tc:opendocument:xmlns:text:1.0",
"text:p"),v.appendChild(w),s.appendChild(v);q.appendChild(s)}k=d.getParagraphElement(k.textNode);p.insertBefore(q,k?k.nextSibling:void 0);d.getOdfCanvas().refreshSize();d.emit(ops.OdtDocument.signalTableAdded,{tableElement:q,memberId:l,timeStamp:g});return!0}return!1};this.spec=function(){return{optype:"InsertTable",memberid:l,timestamp:g,position:n,initialRows:e,initialColumns:f,tableName:b,tableStyleName:a,tableColumnStyleName:c,tableCellStyleMatrix:d}}};
// Input 38
/*

 Copyright (C) 2012-2013 KO GmbH <copyright@kogmbh.com>

 @licstart
 The JavaScript code in this page is free software: you can redistribute it
 and/or modify it under the terms of the GNU Affero General Public License
 (GNU AGPL) as published by the Free Software Foundation, either version 3 of
 the License, or (at your option) any later version.  The code is distributed
 WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 FITNESS FOR A PARTICULAR PURPOSE.  See the GNU AGPL for more details.

 As additional permission under GNU AGPL version 3 section 7, you
 may distribute non-source (e.g., minimized or compacted) forms of
 that code without the copy of the GNU GPL normally required by
 section 4, provided you include this license notice and a URL
 through which recipients can access the Corresponding Source.

 As a special exception to the AGPL, any HTML file which merely makes function
 calls to this code, and for that purpose includes it by reference shall be
 deemed a separate work for copyright law purposes. In addition, the copyright
 holders of this code give you permission to combine this code with free
 software libraries that are released under the GNU LGPL. You may copy and
 distribute such a system following the terms of the GNU AGPL for this code
 and the LGPL for the libraries. If you modify this code, you may extend this
 exception to your version of the code, but you are not obligated to do so.
 If you do not wish to do so, delete this exception statement from your
 version.

 This license applies to this entire compilation.
 @licend
 @source: http://www.webodf.org/
 @source: http://gitorious.org/webodf/webodf/
*/
ops.OpInsertText=function(){var h,l,g,e;this.init=function(f){h=f.memberid;l=f.timestamp;g=f.position;e=f.text};this.execute=function(f){var n,b=e.split(" "),a,c,d,m,k=f.getRootNode().ownerDocument,p;if(n=f.getPositionInTextNode(g)){c=n.textNode;d=c.parentNode;m=c.nextSibling;a=n.offset;n=f.getParagraphElement(c);a!==c.length&&(m=c.splitText(a));0<b[0].length&&c.appendData(b[0]);for(p=1;p<b.length;p+=1)a=k.createElementNS("urn:oasis:names:tc:opendocument:xmlns:text:1.0","text:s"),a.appendChild(k.createTextNode(" ")),
d.insertBefore(a,m),0<b[p].length&&d.insertBefore(k.createTextNode(b[p]),m);b=c.parentNode;d=c.nextSibling;b.removeChild(c);b.insertBefore(c,d);0===c.length&&c.parentNode.removeChild(c);f.getOdfCanvas().refreshSize();f.emit(ops.OdtDocument.signalParagraphChanged,{paragraphElement:n,memberId:h,timeStamp:l});return!0}return!1};this.spec=function(){return{optype:"InsertText",memberid:h,timestamp:l,position:g,text:e}}};
// Input 39
/*

 Copyright (C) 2012-2013 KO GmbH <copyright@kogmbh.com>

 @licstart
 The JavaScript code in this page is free software: you can redistribute it
 and/or modify it under the terms of the GNU Affero General Public License
 (GNU AGPL) as published by the Free Software Foundation, either version 3 of
 the License, or (at your option) any later version.  The code is distributed
 WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 FITNESS FOR A PARTICULAR PURPOSE.  See the GNU AGPL for more details.

 As additional permission under GNU AGPL version 3 section 7, you
 may distribute non-source (e.g., minimized or compacted) forms of
 that code without the copy of the GNU GPL normally required by
 section 4, provided you include this license notice and a URL
 through which recipients can access the Corresponding Source.

 As a special exception to the AGPL, any HTML file which merely makes function
 calls to this code, and for that purpose includes it by reference shall be
 deemed a separate work for copyright law purposes. In addition, the copyright
 holders of this code give you permission to combine this code with free
 software libraries that are released under the GNU LGPL. You may copy and
 distribute such a system following the terms of the GNU AGPL for this code
 and the LGPL for the libraries. If you modify this code, you may extend this
 exception to your version of the code, but you are not obligated to do so.
 If you do not wish to do so, delete this exception statement from your
 version.

 This license applies to this entire compilation.
 @licend
 @source: http://www.webodf.org/
 @source: http://gitorious.org/webodf/webodf/
*/
ops.OpRemoveText=function(){function h(a){var b,d,e,f;b=a.getCursors();e=a.getPositionFilter();for(f in b)b.hasOwnProperty(f)&&(d=b[f].getStepCounter(),d.isPositionWalkable(e)||(d=-d.countBackwardSteps(1,e),b[f].move(d),f===g&&a.emit(ops.OdtDocument.signalCursorMoved,b[f])))}function l(a,b){for(var d=b.firstChild;d;)b.removeChild(d),"editinfo"!==d.localName&&a.appendChild(d),d=b.firstChild;b.parentNode.removeChild(b)}var g,e,f,n,b;this.init=function(a){g=a.memberid;e=a.timestamp;f=a.position;n=a.length;
b=a.text};this.execute=function(a){n=parseInt(n,10);f=parseInt(f,10);var b=[],d,m,k,p=0>n?-1:1,r=0>n?"backspace":"delete",q=null,s=null,v;m=f;var w=n;d=a.getPositionInTextNode(m);var b=d.textNode,F=d.offset,z=b.parentNode;d=a.getParagraphElement(z);k=Math.abs(w);var u=0>w?-1:1,y=0>w?"backspace":"delete";""===b.data?(z.removeChild(b),m=a.getTextNeighborhood(m,w)):0!==F?("delete"===y?(z=k<b.length-F?k:b.length-F,b.deleteData(F,z)):(z=k<F?k:F,b.deleteData(F-z,z)),m=a.getTextNeighborhood(m,w+z*u),k-=
z,z&&m[0]===b&&m.splice(0,1)):m=a.getTextNeighborhood(m,w);b=m;if(null===a.getNeighboringParagraph(d,p)&&0===b.length)return!1;for(;k;)if(b[0]&&(q=b[0],s=q.parentNode,v=q.length),m=a.getParagraphElement(q),d!==m){if(m=a.getNeighboringParagraph(d,p))"delete"===r?l(d,m):(l(m,d),d=m);k-=1}else v<=k?(s.removeChild(q),h(a),("s"===s.localName||"span"===s.localName)&&0===s.textContent.length&&s.parentNode.removeChild(s),k-=v,b.splice(0,1)):("delete"===r?q.deleteData(0,k):q.deleteData(v-k,k),k=0);h(a);a.getOdfCanvas().refreshSize();
a.emit(ops.OdtDocument.signalParagraphChanged,{paragraphElement:d,memberId:g,timeStamp:e});a.emit(ops.OdtDocument.signalCursorMoved,a.getCursor(g));return!0};this.spec=function(){return{optype:"RemoveText",memberid:g,timestamp:e,position:f,length:n,text:b}}};
// Input 40
/*

 Copyright (C) 2012-2013 KO GmbH <copyright@kogmbh.com>

 @licstart
 The JavaScript code in this page is free software: you can redistribute it
 and/or modify it under the terms of the GNU Affero General Public License
 (GNU AGPL) as published by the Free Software Foundation, either version 3 of
 the License, or (at your option) any later version.  The code is distributed
 WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 FITNESS FOR A PARTICULAR PURPOSE.  See the GNU AGPL for more details.

 As additional permission under GNU AGPL version 3 section 7, you
 may distribute non-source (e.g., minimized or compacted) forms of
 that code without the copy of the GNU GPL normally required by
 section 4, provided you include this license notice and a URL
 through which recipients can access the Corresponding Source.

 As a special exception to the AGPL, any HTML file which merely makes function
 calls to this code, and for that purpose includes it by reference shall be
 deemed a separate work for copyright law purposes. In addition, the copyright
 holders of this code give you permission to combine this code with free
 software libraries that are released under the GNU LGPL. You may copy and
 distribute such a system following the terms of the GNU AGPL for this code
 and the LGPL for the libraries. If you modify this code, you may extend this
 exception to your version of the code, but you are not obligated to do so.
 If you do not wish to do so, delete this exception statement from your
 version.

 This license applies to this entire compilation.
 @licend
 @source: http://www.webodf.org/
 @source: http://gitorious.org/webodf/webodf/
*/
ops.OpSplitParagraph=function(){var h,l,g;this.init=function(e){h=e.memberid;l=e.timestamp;g=e.position};this.execute=function(e){var f,n,b,a,c;if(f=e.getPositionInTextNode(g))if(n=e.getParagraphElement(f.textNode)){0===f.offset?(c=f.textNode.previousSibling,a=null):(c=f.textNode,a=f.offset>=f.textNode.length?null:f.textNode.splitText(f.offset));for(f=f.textNode;f!==n;)if(f=f.parentNode,b=f.cloneNode(!1),c){for(a&&b.appendChild(a);c.nextSibling;)b.appendChild(c.nextSibling);f.parentNode.insertBefore(b,
f.nextSibling);c=f;a=b}else f.parentNode.insertBefore(b,f),c=b,a=f;e.getOdfCanvas().refreshSize();e.emit(ops.OdtDocument.signalParagraphChanged,{paragraphElement:n,memberId:h,timeStamp:l});e.emit(ops.OdtDocument.signalParagraphChanged,{paragraphElement:a,memberId:h,timeStamp:l});return!0}return!1};this.spec=function(){return{optype:"SplitParagraph",memberid:h,timestamp:l,position:g}}};
// Input 41
/*

 Copyright (C) 2012-2013 KO GmbH <copyright@kogmbh.com>

 @licstart
 The JavaScript code in this page is free software: you can redistribute it
 and/or modify it under the terms of the GNU Affero General Public License
 (GNU AGPL) as published by the Free Software Foundation, either version 3 of
 the License, or (at your option) any later version.  The code is distributed
 WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 FITNESS FOR A PARTICULAR PURPOSE.  See the GNU AGPL for more details.

 As additional permission under GNU AGPL version 3 section 7, you
 may distribute non-source (e.g., minimized or compacted) forms of
 that code without the copy of the GNU GPL normally required by
 section 4, provided you include this license notice and a URL
 through which recipients can access the Corresponding Source.

 As a special exception to the AGPL, any HTML file which merely makes function
 calls to this code, and for that purpose includes it by reference shall be
 deemed a separate work for copyright law purposes. In addition, the copyright
 holders of this code give you permission to combine this code with free
 software libraries that are released under the GNU LGPL. You may copy and
 distribute such a system following the terms of the GNU AGPL for this code
 and the LGPL for the libraries. If you modify this code, you may extend this
 exception to your version of the code, but you are not obligated to do so.
 If you do not wish to do so, delete this exception statement from your
 version.

 This license applies to this entire compilation.
 @licend
 @source: http://www.webodf.org/
 @source: http://gitorious.org/webodf/webodf/
*/
ops.OpSetParagraphStyle=function(){var h,l,g,e,f;this.init=function(n){h=n.memberid;l=n.timestamp;g=n.position;e=n.styleNameBefore;f=n.styleNameAfter};this.execute=function(e){var b;if(b=e.getPositionInTextNode(g))if(b=e.getParagraphElement(b.textNode))return b.setAttributeNS("urn:oasis:names:tc:opendocument:xmlns:text:1.0","text:style-name",f),e.getOdfCanvas().refreshSize(),e.emit(ops.OdtDocument.signalParagraphChanged,{paragraphElement:b,timeStamp:l,memberId:h}),!0;return!1};this.spec=function(){return{optype:"SetParagraphStyle",
memberid:h,timestamp:l,position:g,styleNameBefore:e,styleNameAfter:f}}};
// Input 42
/*

 Copyright (C) 2012-2013 KO GmbH <copyright@kogmbh.com>

 @licstart
 The JavaScript code in this page is free software: you can redistribute it
 and/or modify it under the terms of the GNU Affero General Public License
 (GNU AGPL) as published by the Free Software Foundation, either version 3 of
 the License, or (at your option) any later version.  The code is distributed
 WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 FITNESS FOR A PARTICULAR PURPOSE.  See the GNU AGPL for more details.

 As additional permission under GNU AGPL version 3 section 7, you
 may distribute non-source (e.g., minimized or compacted) forms of
 that code without the copy of the GNU GPL normally required by
 section 4, provided you include this license notice and a URL
 through which recipients can access the Corresponding Source.

 As a special exception to the AGPL, any HTML file which merely makes function
 calls to this code, and for that purpose includes it by reference shall be
 deemed a separate work for copyright law purposes. In addition, the copyright
 holders of this code give you permission to combine this code with free
 software libraries that are released under the GNU LGPL. You may copy and
 distribute such a system following the terms of the GNU AGPL for this code
 and the LGPL for the libraries. If you modify this code, you may extend this
 exception to your version of the code, but you are not obligated to do so.
 If you do not wish to do so, delete this exception statement from your
 version.

 This license applies to this entire compilation.
 @licend
 @source: http://www.webodf.org/
 @source: http://gitorious.org/webodf/webodf/
*/
ops.OpUpdateParagraphStyle=function(){function h(e,b,a,c,d){void 0!==c&&e.setAttributeNS(b,a,void 0!==d?c+d:c)}var l,g,e,f;this.init=function(h){l=h.memberid;g=h.timestamp;e=h.styleName;f=h.info};this.execute=function(g){var b,a,c;return(b=g.getParagraphStyleElement(e))?(a=b.getElementsByTagNameNS("urn:oasis:names:tc:opendocument:xmlns:style:1.0","paragraph-properties")[0],c=b.getElementsByTagNameNS("urn:oasis:names:tc:opendocument:xmlns:style:1.0","text-properties")[0],void 0===a&&f.paragraphProperties&&
(a=g.getDOM().createElementNS("urn:oasis:names:tc:opendocument:xmlns:style:1.0","style:paragraph-properties"),b.appendChild(a)),void 0===c&&f.textProperties&&(c=g.getDOM().createElementNS("urn:oasis:names:tc:opendocument:xmlns:style:1.0","style:text-properties"),b.appendChild(c)),f.paragraphProperties&&(h(a,"urn:oasis:names:tc:opendocument:xmlns:xsl-fo-compatible:1.0","fo:margin-top",f.paragraphProperties.topMargin,"mm"),h(a,"urn:oasis:names:tc:opendocument:xmlns:xsl-fo-compatible:1.0","fo:margin-bottom",
f.paragraphProperties.bottomMargin,"mm"),h(a,"urn:oasis:names:tc:opendocument:xmlns:xsl-fo-compatible:1.0","fo:margin-left",f.paragraphProperties.leftMargin,"mm"),h(a,"urn:oasis:names:tc:opendocument:xmlns:xsl-fo-compatible:1.0","fo:margin-right",f.paragraphProperties.rightMargin,"mm"),h(a,"urn:oasis:names:tc:opendocument:xmlns:xsl-fo-compatible:1.0","fo:text-align",f.paragraphProperties.textAlign)),f.textProperties&&(h(c,"urn:oasis:names:tc:opendocument:xmlns:xsl-fo-compatible:1.0","fo:font-size",
f.textProperties.fontSize,"pt"),f.textProperties.fontName&&!g.getOdfCanvas().getFormatting().getFontMap().hasOwnProperty(f.textProperties.fontName)&&(b=g.getDOM().createElementNS("urn:oasis:names:tc:opendocument:xmlns:style:1.0","style:font-face"),b.setAttributeNS("urn:oasis:names:tc:opendocument:xmlns:style:1.0","style:name",f.textProperties.fontName),b.setAttributeNS("urn:oasis:names:tc:opendocument:xmlns:svg-compatible:1.0","svg:font-family",f.textProperties.fontName),g.getOdfCanvas().odfContainer().rootElement.fontFaceDecls.appendChild(b)),
h(c,"urn:oasis:names:tc:opendocument:xmlns:style:1.0","style:font-name",f.textProperties.fontName),h(c,"urn:oasis:names:tc:opendocument:xmlns:xsl-fo-compatible:1.0","fo:color",f.textProperties.color),h(c,"urn:oasis:names:tc:opendocument:xmlns:xsl-fo-compatible:1.0","fo:background-color",f.textProperties.backgroundColor),h(c,"urn:oasis:names:tc:opendocument:xmlns:xsl-fo-compatible:1.0","fo:font-weight",f.textProperties.fontWeight),h(c,"urn:oasis:names:tc:opendocument:xmlns:xsl-fo-compatible:1.0","fo:font-style",
f.textProperties.fontStyle),h(c,"urn:oasis:names:tc:opendocument:xmlns:style:1.0","style:text-underline-style",f.textProperties.underline),h(c,"urn:oasis:names:tc:opendocument:xmlns:style:1.0","style:text-line-through-style",f.textProperties.strikethrough)),g.getOdfCanvas().refreshCSS(),g.emit(ops.OdtDocument.signalParagraphStyleModified,e),!0):!1};this.spec=function(){return{optype:"UpdateParagraphStyle",memberid:l,timestamp:g,styleName:e,info:f}}};
// Input 43
/*

 Copyright (C) 2012-2013 KO GmbH <copyright@kogmbh.com>

 @licstart
 The JavaScript code in this page is free software: you can redistribute it
 and/or modify it under the terms of the GNU Affero General Public License
 (GNU AGPL) as published by the Free Software Foundation, either version 3 of
 the License, or (at your option) any later version.  The code is distributed
 WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 FITNESS FOR A PARTICULAR PURPOSE.  See the GNU AGPL for more details.

 As additional permission under GNU AGPL version 3 section 7, you
 may distribute non-source (e.g., minimized or compacted) forms of
 that code without the copy of the GNU GPL normally required by
 section 4, provided you include this license notice and a URL
 through which recipients can access the Corresponding Source.

 As a special exception to the AGPL, any HTML file which merely makes function
 calls to this code, and for that purpose includes it by reference shall be
 deemed a separate work for copyright law purposes. In addition, the copyright
 holders of this code give you permission to combine this code with free
 software libraries that are released under the GNU LGPL. You may copy and
 distribute such a system following the terms of the GNU AGPL for this code
 and the LGPL for the libraries. If you modify this code, you may extend this
 exception to your version of the code, but you are not obligated to do so.
 If you do not wish to do so, delete this exception statement from your
 version.

 This license applies to this entire compilation.
 @licend
 @source: http://www.webodf.org/
 @source: http://gitorious.org/webodf/webodf/
*/
ops.OpCloneParagraphStyle=function(){var h,l,g,e,f;this.init=function(n){h=n.memberid;l=n.timestamp;g=n.styleName;e=n.newStyleName;f=n.newStyleDisplayName};this.execute=function(h){var b=h.getParagraphStyleElement(g),a;if(!b)return!1;a=b.cloneNode(!0);a.setAttributeNS("urn:oasis:names:tc:opendocument:xmlns:style:1.0","style:name",e);a.setAttributeNS("urn:oasis:names:tc:opendocument:xmlns:style:1.0","style:display-name",f);b.parentNode.appendChild(a);h.getOdfCanvas().refreshCSS();h.emit(ops.OdtDocument.signalStyleCreated,
e);return!0};this.spec=function(){return{optype:"CloneParagraphStyle",memberid:h,timestamp:l,styleName:g,newStyleName:e,newStyleDisplayName:f}}};
// Input 44
/*

 Copyright (C) 2012-2013 KO GmbH <copyright@kogmbh.com>

 @licstart
 The JavaScript code in this page is free software: you can redistribute it
 and/or modify it under the terms of the GNU Affero General Public License
 (GNU AGPL) as published by the Free Software Foundation, either version 3 of
 the License, or (at your option) any later version.  The code is distributed
 WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 FITNESS FOR A PARTICULAR PURPOSE.  See the GNU AGPL for more details.

 As additional permission under GNU AGPL version 3 section 7, you
 may distribute non-source (e.g., minimized or compacted) forms of
 that code without the copy of the GNU GPL normally required by
 section 4, provided you include this license notice and a URL
 through which recipients can access the Corresponding Source.

 As a special exception to the AGPL, any HTML file which merely makes function
 calls to this code, and for that purpose includes it by reference shall be
 deemed a separate work for copyright law purposes. In addition, the copyright
 holders of this code give you permission to combine this code with free
 software libraries that are released under the GNU LGPL. You may copy and
 distribute such a system following the terms of the GNU AGPL for this code
 and the LGPL for the libraries. If you modify this code, you may extend this
 exception to your version of the code, but you are not obligated to do so.
 If you do not wish to do so, delete this exception statement from your
 version.

 This license applies to this entire compilation.
 @licend
 @source: http://www.webodf.org/
 @source: http://gitorious.org/webodf/webodf/
*/
ops.OpDeleteParagraphStyle=function(){var h,l,g;this.init=function(e){h=e.memberid;l=e.timestamp;g=e.styleName};this.execute=function(e){var f=e.getParagraphStyleElement(g);if(!f)return!1;f.parentNode.removeChild(f);e.getOdfCanvas().refreshCSS();e.emit(ops.OdtDocument.signalStyleDeleted,g);return!0};this.spec=function(){return{optype:"DeleteParagraphStyle",memberid:h,timestamp:l,styleName:g}}};
// Input 45
/*

 Copyright (C) 2012-2013 KO GmbH <copyright@kogmbh.com>

 @licstart
 The JavaScript code in this page is free software: you can redistribute it
 and/or modify it under the terms of the GNU Affero General Public License
 (GNU AGPL) as published by the Free Software Foundation, either version 3 of
 the License, or (at your option) any later version.  The code is distributed
 WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 FITNESS FOR A PARTICULAR PURPOSE.  See the GNU AGPL for more details.

 As additional permission under GNU AGPL version 3 section 7, you
 may distribute non-source (e.g., minimized or compacted) forms of
 that code without the copy of the GNU GPL normally required by
 section 4, provided you include this license notice and a URL
 through which recipients can access the Corresponding Source.

 As a special exception to the AGPL, any HTML file which merely makes function
 calls to this code, and for that purpose includes it by reference shall be
 deemed a separate work for copyright law purposes. In addition, the copyright
 holders of this code give you permission to combine this code with free
 software libraries that are released under the GNU LGPL. You may copy and
 distribute such a system following the terms of the GNU AGPL for this code
 and the LGPL for the libraries. If you modify this code, you may extend this
 exception to your version of the code, but you are not obligated to do so.
 If you do not wish to do so, delete this exception statement from your
 version.

 This license applies to this entire compilation.
 @licend
 @source: http://www.webodf.org/
 @source: http://gitorious.org/webodf/webodf/
*/
runtime.loadClass("ops.OpAddCursor");runtime.loadClass("ops.OpRemoveCursor");runtime.loadClass("ops.OpMoveCursor");runtime.loadClass("ops.OpInsertTable");runtime.loadClass("ops.OpInsertText");runtime.loadClass("ops.OpRemoveText");runtime.loadClass("ops.OpSplitParagraph");runtime.loadClass("ops.OpSetParagraphStyle");runtime.loadClass("ops.OpUpdateParagraphStyle");runtime.loadClass("ops.OpCloneParagraphStyle");runtime.loadClass("ops.OpDeleteParagraphStyle");
ops.OperationFactory=function(){this.create=function(h){var l=null;"AddCursor"===h.optype?l=new ops.OpAddCursor:"InsertTable"===h.optype?l=new ops.OpInsertTable:"InsertText"===h.optype?l=new ops.OpInsertText:"RemoveText"===h.optype?l=new ops.OpRemoveText:"SplitParagraph"===h.optype?l=new ops.OpSplitParagraph:"SetParagraphStyle"===h.optype?l=new ops.OpSetParagraphStyle:"UpdateParagraphStyle"===h.optype?l=new ops.OpUpdateParagraphStyle:"CloneParagraphStyle"===h.optype?l=new ops.OpCloneParagraphStyle:
"DeleteParagraphStyle"===h.optype?l=new ops.OpDeleteParagraphStyle:"MoveCursor"===h.optype?l=new ops.OpMoveCursor:"RemoveCursor"===h.optype&&(l=new ops.OpRemoveCursor);l&&l.init(h);return l}};
// Input 46
runtime.loadClass("core.Cursor");
ops.OdtCursor=function(h,l){var g=this,e,f;this.removeFromOdtDocument=function(){f.remove(function(e,b){})};this.move=function(f){var b=0;0<f?b=e.movePointForward(f):0>=f&&(b=-e.movePointBackward(-f));g.handleUpdate();return b};this.handleUpdate=function(){};this.getStepCounter=function(){return e.getStepCounter()};this.getMemberId=function(){return h};this.getNode=function(){return f.getNode()};this.getSelection=function(){return f.getSelection()};this.getOdtDocument=function(){return l};(function(){var g=
new core.Selection(l.getDOM());f=new core.Cursor(g,l.getDOM());f.getNode().setAttributeNS("urn:webodf:names:cursor","memberId",h);e=l.getSelectionManager().createSelectionMover(f)})()};
// Input 47
/*

 Copyright (C) 2012 KO GmbH <aditya.bhatt@kogmbh.com>

 @licstart
 The JavaScript code in this page is free software: you can redistribute it
 and/or modify it under the terms of the GNU Affero General Public License
 (GNU AGPL) as published by the Free Software Foundation, either version 3 of
 the License, or (at your option) any later version.  The code is distributed
 WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 FITNESS FOR A PARTICULAR PURPOSE.  See the GNU AGPL for more details.

 As additional permission under GNU AGPL version 3 section 7, you
 may distribute non-source (e.g., minimized or compacted) forms of
 that code without the copy of the GNU GPL normally required by
 section 4, provided you include this license notice and a URL
 through which recipients can access the Corresponding Source.

 As a special exception to the AGPL, any HTML file which merely makes function
 calls to this code, and for that purpose includes it by reference shall be
 deemed a separate work for copyright law purposes. In addition, the copyright
 holders of this code give you permission to combine this code with free
 software libraries that are released under the GNU LGPL. You may copy and
 distribute such a system following the terms of the GNU AGPL for this code
 and the LGPL for the libraries. If you modify this code, you may extend this
 exception to your version of the code, but you are not obligated to do so.
 If you do not wish to do so, delete this exception statement from your
 version.

 This license applies to this entire compilation.
 @licend
 @source: http://www.webodf.org/
 @source: http://gitorious.org/webodf/webodf/
*/
ops.EditInfo=function(h,l){function g(){var e=[],b;for(b in f)f.hasOwnProperty(b)&&e.push({memberid:b,time:f[b].time});e.sort(function(a,b){return a.time-b.time});return e}var e,f={};this.getNode=function(){return e};this.getOdtDocument=function(){return l};this.getEdits=function(){return f};this.getSortedEdits=function(){return g()};this.addEdit=function(e,b){var a,c=e.split("___")[0];if(!f[e])for(a in f)if(f.hasOwnProperty(a)&&a.split("___")[0]===c){delete f[a];break}f[e]={time:b}};this.clearEdits=
function(){f={}};e=l.getDOM().createElementNS("urn:webodf:names:editinfo","editinfo");h.insertBefore(e,h.firstChild)};
// Input 48
runtime.loadClass("core.Cursor");runtime.loadClass("core.PositionIterator");runtime.loadClass("core.PositionFilter");runtime.loadClass("core.LoopWatchDog");
gui.SelectionMover=function(h,l,g,e){function f(a,b,c){b=a;var d=h.getPositionInContainer(q.getNodeFilter());q.setPosition(d.container,d.offset);e=e||p.adaptToCursorRemoval;g=g||p.adaptToInsertedCursor;for(h.remove(e);0<b&&c();)b-=1;0<a-b&&r.collapse(q.container(),q.unfilteredDomOffset());h.updateToSelection(e,g);return a-b}function n(a){var b=h.getPositionInContainer(q.getNodeFilter());q.setPosition(b.container,b.offset);return 1===a.acceptPosition(q)?!0:!1}function b(a,b){var c=h.getPositionInContainer(q.getNodeFilter()),
d=c.container,c=c.offset,e=new core.LoopWatchDog(1E3),f=0,g=0;for(q.setPosition(d,c);0<a&&q.nextPosition();)f+=1,e.check(),1===b.acceptPosition(q)&&(g+=f,f=0,a-=1);q.setPosition(d,c);return g}function a(a,b){var c=h.getPositionInContainer(q.getNodeFilter()),d=c.container,c=c.offset,e=new core.LoopWatchDog(1E3),f=0,g=0;for(q.setPosition(d,c);0<a&&q.previousPosition();)f+=1,e.check(),1===b.acceptPosition(q)&&(g+=f,f=0,a-=1);q.setPosition(d,c);return g}function c(a,b){for(var c=q.container(),d=q.offset(),
e,f=0,h=c.ownerDocument.createRange();0<a;){var g=h,k=b,p=q.container(),m=q.offset(),l=0,n=0,r=null,A=void 0,T=void 0,U=0,N=void 0,W=void 0,S=void 0,Q=void 0,N=void 0;g.setStart(p,m);N=g.getClientRects()[0];Q=W=N.top;for(S=N.left;q.previousPosition();)if(l+=1,1===k.acceptPosition(q)&&(n+=l,l=0,p=q.container(),m=q.offset(),g.setStart(p,m),N=g.getClientRects()[0],N.top!==W)){if(N.top!==Q)break;Q=W;N=Math.abs(S-N.left);if(null===r||N<T)r=p,A=m,T=N,U=n}null!==r?(q.setPosition(r,A),n=U):n=0;e+=n;if(0===
e)break;f+=e;a-=1}h.detach();q.setPosition(c,d);return f}function d(a,b){var c=q.container(),d=q.offset(),f=h.getNode().firstChild,k=new core.LoopWatchDog(1E3),m=0,l=0,n=f.offsetTop;e=e||p.adaptToCursorRemoval;for(g=g||p.adaptToInsertedCursor;0<a&&q.nextPosition();)k.check(),m+=1,1===b.acceptPosition(q)&&(r.collapse(q.container(),q.offset()),h.updateToSelection(e,g),n=f.offsetTop,n!==f.offsetTop&&(l+=m,m=0,a-=1));q.setPosition(c,d);r.collapse(q.container(),q.offset());h.updateToSelection(e,g);return l}
function m(a,b){for(var c=0,d;a.parentNode!==b;)runtime.assert(null!==a.parentNode,"parent is null"),a=a.parentNode;for(d=b.firstChild;d!==a;)c+=1,d=d.nextSibling;return c}function k(a,b,c){runtime.assert(null!==a,"SelectionMover.countStepsToPosition called with element===null");var d=h.getPositionInContainer(q.getNodeFilter()),e=d.container,d=d.offset,f=0,g,k=new core.LoopWatchDog(1E3),p;q.setPosition(a,b);a=q.container();runtime.assert(null!==a,"SelectionMover.countStepsToPosition: positionIterator.container() returned null");
b=q.offset();g=q.unfilteredDomOffset();q.setPosition(e,d);p=a;var l=q.container(),n=q.unfilteredDomOffset();if(p===l)p=n-g;else{var r=p.compareDocumentPosition(l);2===r?r=-1:4===r?r=1:10===r?(g=m(p,l),r=g<n?1:-1):(n=m(l,p),r=n<g?-1:1);p=r}if(0>p){for(;q.nextPosition();)if(k.check(),1===c.acceptPosition(q)&&(f+=1),q.container()===a&&q.offset()===b)return q.setPosition(e,d),f;q.setPosition(e,d)}else if(0<p){for(;q.previousPosition();)if(k.check(),1===c.acceptPosition(q)&&(f-=1),q.container()===a&&q.offset()===
b)return q.setPosition(e,d),f;q.setPosition(e,d)}return f}var p=this,r=h.getSelection(),q;this.movePointForward=function(a,b){return f(a,b,q.nextPosition)};this.movePointBackward=function(a,b){return f(a,b,q.previousPosition)};this.getStepCounter=function(){return{countForwardSteps:b,countBackwardSteps:a,countLineDownSteps:d,countLinesUpSteps:c,countStepsToPosition:k,isPositionWalkable:n}};this.adaptToCursorRemoval=function(a,b){if(!(0===b||null===a||3!==a.nodeType)){var c=q.container();c===a&&q.setPosition(c,
q.offset()+b)}};this.adaptToInsertedCursor=function(a,b){if(!(0===b||null===a||3!==a.nodeType)){var c=q.container(),d=q.offset();if(c===a)if(d<b){do c=c.previousSibling;while(c&&3!==c.nodeType);c&&q.setPosition(c,d)}else q.setPosition(c,q.offset()-b)}};q=gui.SelectionMover.createPositionIterator(l);r.collapse(q.container(),q.offset());e=e||p.adaptToCursorRemoval;g=g||p.adaptToInsertedCursor;h.updateToSelection(e,g)};
gui.SelectionMover.createPositionIterator=function(h){var l=new function(){this.acceptNode=function(h){return"urn:webodf:names:cursor"===h.namespaceURI||"urn:webodf:names:editinfo"===h.namespaceURI?2:1}};return new core.PositionIterator(h,5,l,!1)};(function(){return gui.SelectionMover})();
// Input 49
gui.Avatar=function(h,l){var g=this,e,f,n;this.setColor=function(b){f.style.borderColor=b};this.setImageUrl=function(b){g.isVisible()?f.src=b:n=b};this.isVisible=function(){return"block"===e.style.display};this.show=function(){n&&(f.src=n,n=void 0);e.style.display="block"};this.hide=function(){e.style.display="none"};this.markAsFocussed=function(b){e.className=b?"active":""};(function(){var b=h.ownerDocument,a=b.documentElement.namespaceURI;e=b.createElementNS(a,"div");f=b.createElementNS(a,"img");
f.width=64;f.height=64;e.appendChild(f);e.style.width="64px";e.style.height="70px";e.style.position="absolute";e.style.top="-80px";e.style.left="-34px";e.style.display=l?"block":"none";h.appendChild(e)})()};
// Input 50
runtime.loadClass("gui.Avatar");runtime.loadClass("ops.OdtCursor");
gui.Caret=function(h,l){function g(){a&&b.parentNode&&!c&&(c=!0,f.style.borderColor="transparent"===f.style.borderColor?d:"transparent",runtime.setTimeout(function(){c=!1;g()},500))}function e(a){var b;if("string"===typeof a){if(""===a)return 0;b=/^(\d+)(\.\d+)?px$/.exec(a);runtime.assert(null!==b,"size ["+a+"] does not have unit px.");return parseFloat(b[1])}return a}var f,n,b,a=!1,c=!1,d="";this.setFocus=function(){a=!0;n.markAsFocussed(!0);g()};this.removeFocus=function(){a=!1;n.markAsFocussed(!1);
f.style.borderColor=d};this.setAvatarImageUrl=function(a){n.setImageUrl(a)};this.setColor=function(a){d!==a&&(d=a,"transparent"!==f.style.borderColor&&(f.style.borderColor=d),n.setColor(d))};this.getCursor=function(){return h};this.getFocusElement=function(){return f};this.toggleHandleVisibility=function(){n.isVisible()?n.hide():n.show()};this.showHandle=function(){n.show()};this.hideHandle=function(){n.hide()};this.ensureVisible=function(){var a,b,c,d,g,l,n,w=h.getOdtDocument().getOdfCanvas().getElement().parentNode;
g=n=f;c=runtime.getWindow();runtime.assert(null!==c,"Expected to be run in an environment which has a global window, like a browser.");do{g=g.parentElement;if(!g)break;l=c.getComputedStyle(g,null)}while("block"!==l.display);l=g;g=d=0;if(!l||!w)d=c=0;else{b=!1;do{c=l.offsetParent;for(a=l.parentNode;a!==c;){if(a===w){a=l;var F=w,z=0;b=0;var u=void 0,y=runtime.getWindow();for(runtime.assert(null!==y,"Expected to be run in an environment which has a global window, like a browser.");a&&a!==F;)u=y.getComputedStyle(a,
null),z+=e(u.marginLeft)+e(u.borderLeftWidth)+e(u.paddingLeft),b+=e(u.marginTop)+e(u.borderTopWidth)+e(u.paddingTop),a=a.parentElement;a=z;d+=a;g+=b;b=!0;break}a=a.parentNode}if(b)break;d+=e(l.offsetLeft);g+=e(l.offsetTop);l=c}while(l&&l!==w);c=d;d=g}c+=n.offsetLeft;d+=n.offsetTop;g=c-5;l=d-5;c=c+n.scrollWidth-1+5;n=d+n.scrollHeight-1+5;l<w.scrollTop?w.scrollTop=l:n>w.scrollTop+w.clientHeight-1&&(w.scrollTop=n-w.clientHeight+1);g<w.scrollLeft?w.scrollLeft=g:c>w.scrollLeft+w.clientWidth-1&&(w.scrollLeft=
c-w.clientWidth+1)};(function(){var a=h.getOdtDocument().getDOM();f=a.createElementNS(a.documentElement.namespaceURI,"span");b=h.getNode();b.appendChild(f);n=new gui.Avatar(b,l)})()};
// Input 51
runtime.loadClass("ops.OpAddCursor");runtime.loadClass("ops.OpRemoveCursor");runtime.loadClass("ops.OpMoveCursor");runtime.loadClass("ops.OpInsertText");runtime.loadClass("ops.OpRemoveText");runtime.loadClass("ops.OpSplitParagraph");runtime.loadClass("ops.OpSetParagraphStyle");
gui.SessionController=function(){gui.SessionController=function(h,l){function g(a,b,c){a.addEventListener?a.addEventListener(b,c,!1):a.attachEvent?a.attachEvent("on"+b,c):a["on"+b]=c}function e(a){a.preventDefault?a.preventDefault():a.returnValue=!1}function f(a){e(a)}function n(a){a=runtime.getWindow().getSelection();var b=a.focusNode,c=a.focusOffset,e,f=h.getOdtDocument(),g=f.getOdfCanvas().getElement();if(e=b){for(;e!==g;){if("urn:webodf:names:cursor"===e.namespaceURI&&"cursor"===e.localName)return;
e=e.parentNode}b=f.getDistanceFromCursor(l,b,c);a.removeAllRanges();0!==b&&(a=new ops.OpMoveCursor,a.init({memberid:l,number:b}),h.enqueue(a))}}function b(a){var b=new ops.OpMoveCursor;b.init({memberid:l,number:a});return b}function a(a){var c=a.keyCode,f=null,g=!1;if(37===c)f=b(-1),g=!0;else if(39===c)f=b(1),g=!0;else if(38===c)f=b(-10),g=!0;else if(40===c)f=b(10),g=!0;else if(36===c)f=h.getOdtDocument(),c=null,g=f.getParagraphElement(f.getCursor(l).getNode()),f=f.getDistanceFromCursor(l,g,0),0!==
f&&(c=new ops.OpMoveCursor,c.init({memberid:l,number:f})),f=c,g=!0;else if(35===c){var c=h.getOdtDocument(),f=gui.SelectionMover.createPositionIterator(c.getRootNode()),g=c.getCursor(l).getNode(),n=c.getParagraphElement(g),g=null;runtime.assert(Boolean(n),"SessionController: Cursor outside paragraph");f.moveToEndOfNode(n);c=c.getDistanceFromCursor(l,n,f.offset());0!==c&&(g=new ops.OpMoveCursor,g.init({memberid:l,number:c}));f=g;g=!0}else if(8===c){g=h.getOdtDocument();c=g.getCursorPosition(l);f=null;
if(0<c&&(g=g.getPositionInTextNode(c-1)))f=new ops.OpRemoveText,f.init({memberid:l,position:c,length:-1});g=null!==f}else 46===c&&(c=h.getOdtDocument(),f=c.getCursorPosition(l),g=null,c.getPositionInTextNode(f+1)&&(g=new ops.OpRemoveText,g.init({memberid:l,position:f,length:1})),f=g,g=null!==f);f&&h.enqueue(f);g&&e(a)}function c(a){var b,c;c=null===a.which?String.fromCharCode(a.keyCode):0!==a.which&&0!==a.charCode?String.fromCharCode(a.which):null;13===a.keyCode?(b=h.getOdtDocument().getCursorPosition(l),
c=new ops.OpSplitParagraph,c.init({memberid:l,position:b}),h.enqueue(c),e(a)):c&&(!a.altKey&&!a.ctrlKey&&!a.metaKey)&&(b=new ops.OpInsertText,b.init({memberid:l,position:h.getOdtDocument().getCursorPosition(l),text:c}),h.enqueue(b),e(a))}this.startListening=function(){var b=h.getOdtDocument().getOdfCanvas().getElement();g(b,"keydown",a);g(b,"keypress",c);g(b,"keyup",f);g(b,"copy",f);g(b,"cut",f);g(b,"paste",f);g(b,"mouseup",n)};this.startEditing=function(){var a=new ops.OpAddCursor;a.init({memberid:l});
h.enqueue(a)};this.endEditing=function(){var a=new ops.OpRemoveCursor;a.init({memberid:l});h.enqueue(a)};this.getInputMemberId=function(){return l};this.getSession=function(){return h}};return gui.SessionController}();
// Input 52
runtime.loadClass("gui.SelectionMover");gui.SelectionManager=function(h){function l(f,g){var b;for(b=0;b<e.length;b+=1)e[b].adaptToCursorRemoval(f,g)}function g(f,g){var b;for(b=0;b<e.length;b+=1)e[b].adaptToInsertedCursor(f,g)}var e=[];this.createSelectionMover=function(f){f=new gui.SelectionMover(f,h,g,l);e.push(f);return f}};
// Input 53
ops.UserModel=function(){};ops.UserModel.prototype.getUserDetailsAndUpdates=function(h,l){};ops.UserModel.prototype.unsubscribeUserDetailsUpdates=function(h,l){};
// Input 54
/*

 Copyright (C) 2012 KO GmbH <copyright@kogmbh.com>

 @licstart
 The JavaScript code in this page is free software: you can redistribute it
 and/or modify it under the terms of the GNU Affero General Public License
 (GNU AGPL) as published by the Free Software Foundation, either version 3 of
 the License, or (at your option) any later version.  The code is distributed
 WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 FITNESS FOR A PARTICULAR PURPOSE.  See the GNU AGPL for more details.

 As additional permission under GNU AGPL version 3 section 7, you
 may distribute non-source (e.g., minimized or compacted) forms of
 that code without the copy of the GNU GPL normally required by
 section 4, provided you include this license notice and a URL
 through which recipients can access the Corresponding Source.

 As a special exception to the AGPL, any HTML file which merely makes function
 calls to this code, and for that purpose includes it by reference shall be
 deemed a separate work for copyright law purposes. In addition, the copyright
 holders of this code give you permission to combine this code with free
 software libraries that are released under the GNU LGPL. You may copy and
 distribute such a system following the terms of the GNU AGPL for this code
 and the LGPL for the libraries. If you modify this code, you may extend this
 exception to your version of the code, but you are not obligated to do so.
 If you do not wish to do so, delete this exception statement from your
 version.

 This license applies to this entire compilation.
 @licend
 @source: http://www.webodf.org/
 @source: http://gitorious.org/webodf/webodf/
*/
ops.TrivialUserModel=function(){var h={bob:{memberid:"bob",fullname:"Bob Pigeon",color:"red",imageurl:"avatar-pigeon.png"},alice:{memberid:"alice",fullname:"Alice Bee",color:"green",imageurl:"avatar-flower.png"},you:{memberid:"you",fullname:"I, Robot",color:"blue",imageurl:"avatar-joe.png"}};this.getUserDetailsAndUpdates=function(l,g){var e=l.split("___")[0];g(l,h[e]||null)};this.unsubscribeUserDetailsUpdates=function(h,g){}};
// Input 55
ops.NowjsUserModel=function(){var h={},l={},g=runtime.getNetwork();this.getUserDetailsAndUpdates=function(e,f){var n=e.split("___")[0],b=h[n],a=l[n]=l[n]||[],c;runtime.assert(void 0!==f,"missing callback");for(c=0;c<a.length&&!(a[c].subscriber===f&&a[c].memberId===e);c+=1);c<a.length?runtime.log("double subscription request for "+e+" in NowjsUserModel::getUserDetailsAndUpdates"):(a.push({memberId:e,subscriber:f}),1===a.length&&g.subscribeUserDetailsUpdates(n));b&&f(e,b)};this.unsubscribeUserDetailsUpdates=
function(e,f){var n,b=e.split("___")[0],a=l[b];runtime.assert(void 0!==f,"missing subscriber parameter or null");runtime.assert(a,"tried to unsubscribe when no one is subscribed ('"+e+"')");if(a){for(n=0;n<a.length&&!(a[n].subscriber===f&&a[n].memberId===e);n+=1);runtime.assert(n<a.length,"tried to unsubscribe when not subscribed for memberId '"+e+"'");a.splice(n,1);0===a.length&&(runtime.log("no more subscribers for: "+e),delete l[b],delete h[b],g.unsubscribeUserDetailsUpdates(b))}};g.updateUserDetails=
function(e,f){var g=f?{userid:f.uid,fullname:f.fullname,imageurl:"/user/"+f.avatarId+"/avatar.png",color:f.color}:null,b,a;if(b=l[e]){h[e]=g;for(a=0;a<b.length;a+=1)b[a].subscriber(b[a].memberId,g)}};runtime.assert("ready"===g.networkStatus,"network not ready")};
// Input 56
/*

 Copyright (C) 2013 KO GmbH <copyright@kogmbh.com>

 @licstart
 The JavaScript code in this page is free software: you can redistribute it
 and/or modify it under the terms of the GNU Affero General Public License
 (GNU AGPL) as published by the Free Software Foundation, either version 3 of
 the License, or (at your option) any later version.  The code is distributed
 WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 FITNESS FOR A PARTICULAR PURPOSE.  See the GNU AGPL for more details.

 As additional permission under GNU AGPL version 3 section 7, you
 may distribute non-source (e.g., minimized or compacted) forms of
 that code without the copy of the GNU GPL normally required by
 section 4, provided you include this license notice and a URL
 through which recipients can access the Corresponding Source.

 As a special exception to the AGPL, any HTML file which merely makes function
 calls to this code, and for that purpose includes it by reference shall be
 deemed a separate work for copyright law purposes. In addition, the copyright
 holders of this code give you permission to combine this code with free
 software libraries that are released under the GNU LGPL. You may copy and
 distribute such a system following the terms of the GNU AGPL for this code
 and the LGPL for the libraries. If you modify this code, you may extend this
 exception to your version of the code, but you are not obligated to do so.
 If you do not wish to do so, delete this exception statement from your
 version.

 This license applies to this entire compilation.
 @licend
 @source: http://www.webodf.org/
 @source: http://gitorious.org/webodf/webodf/
*/
ops.OperationRouter=function(){};ops.OperationRouter.prototype.setOperationFactory=function(h){};ops.OperationRouter.prototype.setPlaybackFunction=function(h){};ops.OperationRouter.prototype.push=function(h){};
// Input 57
/*

 Copyright (C) 2012 KO GmbH <copyright@kogmbh.com>

 @licstart
 The JavaScript code in this page is free software: you can redistribute it
 and/or modify it under the terms of the GNU Affero General Public License
 (GNU AGPL) as published by the Free Software Foundation, either version 3 of
 the License, or (at your option) any later version.  The code is distributed
 WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 FITNESS FOR A PARTICULAR PURPOSE.  See the GNU AGPL for more details.

 As additional permission under GNU AGPL version 3 section 7, you
 may distribute non-source (e.g., minimized or compacted) forms of
 that code without the copy of the GNU GPL normally required by
 section 4, provided you include this license notice and a URL
 through which recipients can access the Corresponding Source.

 As a special exception to the AGPL, any HTML file which merely makes function
 calls to this code, and for that purpose includes it by reference shall be
 deemed a separate work for copyright law purposes. In addition, the copyright
 holders of this code give you permission to combine this code with free
 software libraries that are released under the GNU LGPL. You may copy and
 distribute such a system following the terms of the GNU AGPL for this code
 and the LGPL for the libraries. If you modify this code, you may extend this
 exception to your version of the code, but you are not obligated to do so.
 If you do not wish to do so, delete this exception statement from your
 version.

 This license applies to this entire compilation.
 @licend
 @source: http://www.webodf.org/
 @source: http://gitorious.org/webodf/webodf/
*/
ops.TrivialOperationRouter=function(){var h,l;this.setOperationFactory=function(g){h=g};this.setPlaybackFunction=function(g){l=g};this.push=function(g){g=g.spec();g.timestamp=(new Date).getTime();g=h.create(g);l(g)}};
// Input 58
ops.NowjsOperationRouter=function(h,l){function g(d){var g;g=e.create(d);runtime.log(" op in: "+runtime.toJson(d));if(null!==g)if(d=Number(d.server_seq),runtime.assert(!isNaN(d),"server seq is not a number"),d===b+1){f(g);b=d;c=0;for(g=b+1;a.hasOwnProperty(g);g+=1)f(a[g]),delete a[g],runtime.log("op with server seq "+d+" taken from hold (reordered)")}else runtime.assert(d!==b+1,"received incorrect order from server"),runtime.assert(!a.hasOwnProperty(d),"reorder_queue has incoming op"),runtime.log("op with server seq "+
d+" put on hold"),a[d]=g;else runtime.log("ignoring invalid incoming opspec: "+d)}var e,f,n=runtime.getNetwork(),b=-1,a={},c=0,d=1E3;this.setOperationFactory=function(a){e=a};this.setPlaybackFunction=function(a){f=a};n.ping=function(a){null!==l&&a(l)};n.receiveOp=function(a,b){a===h&&g(b)};this.push=function(a){a=a.spec();runtime.assert(null!==l,"Router sequence N/A without memberid");d+=1;a.client_nonce="C:"+l+":"+d;a.parent_op=b+"+"+c;c+=1;runtime.log("op out: "+runtime.toJson(a));n.deliverOp(h,
a)};this.requestReplay=function(a){n.requestReplay(h,function(a){runtime.log("replaying: "+runtime.toJson(a));g(a)},function(b){runtime.log("replay done ("+b+" ops).");a&&a()})};(function(){n.memberid=l;n.joinSession(h,function(a){runtime.assert(a,"Trying to join a session which does not exists or where we are already in")})})()};
// Input 59
gui.EditInfoHandle=function(h){var l=[],g,e=h.ownerDocument,f=e.documentElement.namespaceURI;this.setEdits=function(h){l=h;var b,a,c,d;g.innerHTML="";for(h=0;h<l.length;h+=1)b=e.createElementNS(f,"div"),b.className="editInfo",a=e.createElementNS(f,"span"),a.className="editInfoColor",a.setAttributeNS("urn:webodf:names:editinfo","editinfo:memberid",l[h].memberid),c=e.createElementNS(f,"span"),c.className="editInfoAuthor",c.setAttributeNS("urn:webodf:names:editinfo","editinfo:memberid",l[h].memberid),
d=e.createElementNS(f,"span"),d.className="editInfoTime",d.setAttributeNS("urn:webodf:names:editinfo","editinfo:memberid",l[h].memberid),d.innerHTML=l[h].time,b.appendChild(a),b.appendChild(c),b.appendChild(d),g.appendChild(b)};this.show=function(){g.style.display="block"};this.hide=function(){g.style.display="none"};g=e.createElementNS(f,"div");g.setAttribute("class","editInfoHandle");g.style.display="none";h.appendChild(g)};
// Input 60
runtime.loadClass("ops.EditInfo");runtime.loadClass("gui.EditInfoHandle");
gui.EditInfoMarker=function(h,l){function g(a,c){return window.setTimeout(function(){b.style.opacity=a},c)}var e=this,f,n,b,a,c;this.addEdit=function(d,e){var f=Date.now()-e;h.addEdit(d,e);n.setEdits(h.getSortedEdits());b.setAttributeNS("urn:webodf:names:editinfo","editinfo:memberid",d);a&&window.clearTimeout(a);c&&window.clearTimeout(c);1E4>f?(g(1,0),a=g(0.5,1E4-f),c=g(0.2,2E4-f)):1E4<=f&&2E4>f?(g(0.5,0),c=g(0.2,2E4-f)):g(0.2,0)};this.getEdits=function(){return h.getEdits()};this.clearEdits=function(){h.clearEdits();
n.setEdits([]);b.hasAttributeNS("urn:webodf:names:editinfo","editinfo:memberid")&&b.removeAttributeNS("urn:webodf:names:editinfo","editinfo:memberid")};this.getEditInfo=function(){return h};this.show=function(){b.style.display="block"};this.hide=function(){e.hideHandle();b.style.display="none"};this.showHandle=function(){n.show()};this.hideHandle=function(){n.hide()};(function(){var a=h.getOdtDocument().getDOM();b=a.createElementNS(a.documentElement.namespaceURI,"div");b.setAttribute("class","editInfoMarker");
b.onmouseover=function(){e.showHandle()};b.onmouseout=function(){e.hideHandle()};f=h.getNode();f.appendChild(b);n=new gui.EditInfoHandle(f);l||e.hide()})()};
// Input 61
/*

 Copyright (C) 2012 KO GmbH <copyright@kogmbh.com>

 @licstart
 The JavaScript code in this page is free software: you can redistribute it
 and/or modify it under the terms of the GNU Affero General Public License
 (GNU AGPL) as published by the Free Software Foundation, either version 3 of
 the License, or (at your option) any later version.  The code is distributed
 WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 FITNESS FOR A PARTICULAR PURPOSE.  See the GNU AGPL for more details.

 As additional permission under GNU AGPL version 3 section 7, you
 may distribute non-source (e.g., minimized or compacted) forms of
 that code without the copy of the GNU GPL normally required by
 section 4, provided you include this license notice and a URL
 through which recipients can access the Corresponding Source.

 As a special exception to the AGPL, any HTML file which merely makes function
 calls to this code, and for that purpose includes it by reference shall be
 deemed a separate work for copyright law purposes. In addition, the copyright
 holders of this code give you permission to combine this code with free
 software libraries that are released under the GNU LGPL. You may copy and
 distribute such a system following the terms of the GNU AGPL for this code
 and the LGPL for the libraries. If you modify this code, you may extend this
 exception to your version of the code, but you are not obligated to do so.
 If you do not wish to do so, delete this exception statement from your
 version.

 This license applies to this entire compilation.
 @licend
 @source: http://www.webodf.org/
 @source: http://gitorious.org/webodf/webodf/
*/
runtime.loadClass("gui.Caret");runtime.loadClass("ops.TrivialUserModel");runtime.loadClass("ops.EditInfo");runtime.loadClass("gui.EditInfoMarker");
gui.SessionView=function(){return function(h,l,g){function e(a,b,c){c=c.split("___")[0];return a+"."+b+'[editinfo|memberid^="'+c+'"]'}function f(a,b,c){function d(b,c,f){f=e(b,c,a)+f;a:{var g=k.firstChild;for(b=e(b,c,a);g;){if(3===g.nodeType&&0===g.data.indexOf(b)){b=g;break a}g=g.nextSibling}b=null}b?b.data=f:k.appendChild(document.createTextNode(f))}d("div","editInfoMarker","{ background-color: "+c+"; }");d("span","editInfoColor","{ background-color: "+c+"; }");d("span","editInfoAuthor",':before { content: "'+
b+'"; }')}function n(a){var b,c;for(c in r)r.hasOwnProperty(c)&&(b=r[c],a?b.show():b.hide())}function b(a){var b,c;for(c in m)m.hasOwnProperty(c)&&(b=m[c],a?b.showHandle():b.hideHandle())}function a(a,b){var c=m[a];void 0===b?runtime.log('UserModel sent undefined data for member "'+a+'".'):(null===b&&(b={memberid:a,fullname:"Unknown Identity",color:"black",imageurl:"avatar-joe.png"}),c&&(c.setAvatarImageUrl(b.imageurl),c.setColor(b.color)),p&&f(a,b.fullname,b.color))}function c(b){var c=g.createCaret(b,
s);b=b.getMemberId();var d=l.getUserModel();m[b]=c;a(b,null);d.getUserDetailsAndUpdates(b,a);runtime.log("+++ View here +++ eagerly created an Caret for '"+b+"'! +++")}function d(b){var c=!1,d;delete m[b];for(d in r)if(r.hasOwnProperty(d)&&r[d].getEditInfo().getEdits().hasOwnProperty(b)){c=!0;break}c||l.getUserModel().unsubscribeUserDetailsUpdates(b,a)}var m={},k,p=!0,r={},q=void 0!==h.editInfoMarkersInitiallyVisible?h.editInfoMarkersInitiallyVisible:!0,s=void 0!==h.caretAvatarsInitiallyVisible?h.caretAvatarsInitiallyVisible:
!0;this.enableEditHighlighting=function(){p||(p=!0)};this.disableEditHighlighting=function(){p&&(p=!1)};this.showEditInfoMarkers=function(){q||(q=!0,n(q))};this.hideEditInfoMarkers=function(){q&&(q=!1,n(q))};this.showCaretAvatars=function(){s||(s=!0,b(s))};this.hideCaretAvatars=function(){s&&(s=!1,b(s))};this.getSession=function(){return l};this.getCaret=function(a){return m[a]};(function(){var a=l.getOdtDocument(),b=document.getElementsByTagName("head")[0];a.subscribe(ops.OdtDocument.signalCursorAdded,
c);a.subscribe(ops.OdtDocument.signalCursorRemoved,d);a.subscribe(ops.OdtDocument.signalParagraphChanged,function(a){var b=a.paragraphElement,c=a.memberId;a=a.timeStamp;var d,e="",f=b.getElementsByTagNameNS("urn:webodf:names:editinfo","editinfo")[0];f?(e=f.getAttributeNS("urn:webodf:names:editinfo","id"),d=r[e]):(e=Math.random().toString(),d=new ops.EditInfo(b,l.getOdtDocument()),d=new gui.EditInfoMarker(d,q),f=b.getElementsByTagNameNS("urn:webodf:names:editinfo","editinfo")[0],f.setAttributeNS("urn:webodf:names:editinfo",
"id",e),r[e]=d);d.addEdit(c,new Date(a))});k=document.createElementNS(b.namespaceURI,"style");k.type="text/css";k.media="screen, print, handheld, projection";k.appendChild(document.createTextNode("@namespace editinfo url(urn:webodf:names:editinfo);"));b.appendChild(k)})()}}();
// Input 62
/*

 Copyright (C) 2012 KO GmbH <copyright@kogmbh.com>

 @licstart
 The JavaScript code in this page is free software: you can redistribute it
 and/or modify it under the terms of the GNU Affero General Public License
 (GNU AGPL) as published by the Free Software Foundation, either version 3 of
 the License, or (at your option) any later version.  The code is distributed
 WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 FITNESS FOR A PARTICULAR PURPOSE.  See the GNU AGPL for more details.

 As additional permission under GNU AGPL version 3 section 7, you
 may distribute non-source (e.g., minimized or compacted) forms of
 that code without the copy of the GNU GPL normally required by
 section 4, provided you include this license notice and a URL
 through which recipients can access the Corresponding Source.

 As a special exception to the AGPL, any HTML file which merely makes function
 calls to this code, and for that purpose includes it by reference shall be
 deemed a separate work for copyright law purposes. In addition, the copyright
 holders of this code give you permission to combine this code with free
 software libraries that are released under the GNU LGPL. You may copy and
 distribute such a system following the terms of the GNU AGPL for this code
 and the LGPL for the libraries. If you modify this code, you may extend this
 exception to your version of the code, but you are not obligated to do so.
 If you do not wish to do so, delete this exception statement from your
 version.

 This license applies to this entire compilation.
 @licend
 @source: http://www.webodf.org/
 @source: http://gitorious.org/webodf/webodf/
*/
runtime.loadClass("gui.Caret");
gui.CaretFactory=function(h){this.createCaret=function(l,g){var e=l.getMemberId(),f=h.getSession().getOdtDocument(),n=f.getOdfCanvas().getElement(),b=new gui.Caret(l,g);e===h.getInputMemberId()&&(runtime.log("Starting to track input on new cursor of "+e),f.subscribe(ops.OdtDocument.signalParagraphChanged,function(a){a.memberId===e&&b.ensureVisible()}),l.handleUpdate=b.ensureVisible,n.setAttribute("tabindex",0),n.onfocus=b.setFocus,n.onblur=b.removeFocus,n.focus(),h.startListening());return b}};
// Input 63
runtime.loadClass("xmldom.XPath");runtime.loadClass("odf.Namespaces");
gui.PresenterUI=function(){var h=new xmldom.XPath;return function(l){var g=this;g.setInitialSlideMode=function(){g.startSlideMode("single")};g.keyDownHandler=function(e){if(!e.target.isContentEditable&&"input"!==e.target.nodeName)switch(e.keyCode){case 84:g.toggleToolbar();break;case 37:case 8:g.prevSlide();break;case 39:case 32:g.nextSlide();break;case 36:g.firstSlide();break;case 35:g.lastSlide()}};g.root=function(){return g.odf_canvas.odfContainer().rootElement};g.firstSlide=function(){g.slideChange(function(e,
f){return 0})};g.lastSlide=function(){g.slideChange(function(e,f){return f-1})};g.nextSlide=function(){g.slideChange(function(e,f){return e+1<f?e+1:-1})};g.prevSlide=function(){g.slideChange(function(e,f){return 1>e?-1:e-1})};g.slideChange=function(e){var f=g.getPages(g.odf_canvas.odfContainer().rootElement),h=-1,b=0;f.forEach(function(a){a=a[1];a.hasAttribute("slide_current")&&(h=b,a.removeAttribute("slide_current"));b+=1});e=e(h,f.length);-1===e&&(e=h);f[e][1].setAttribute("slide_current","1");
document.getElementById("pagelist").selectedIndex=e;"cont"===g.slide_mode&&window.scrollBy(0,f[e][1].getBoundingClientRect().top-30)};g.selectSlide=function(e){g.slideChange(function(f,g){return e>=g||0>e?-1:e})};g.scrollIntoContView=function(e){var f=g.getPages(g.odf_canvas.odfContainer().rootElement);0!==f.length&&window.scrollBy(0,f[e][1].getBoundingClientRect().top-30)};g.getPages=function(e){e=e.getElementsByTagNameNS(odf.Namespaces.drawns,"page");var f=[],g;for(g=0;g<e.length;g+=1)f.push([e[g].getAttribute("draw:name"),
e[g]]);return f};g.fillPageList=function(e,f){for(var l=g.getPages(e),b,a,c;f.firstChild;)f.removeChild(f.firstChild);for(b=0;b<l.length;b+=1)a=document.createElement("option"),c=h.getODFElementsWithXPath(l[b][1],'./draw:frame[@presentation:class="title"]//draw:text-box/text:p',xmldom.XPath),c=0<c.length?c[0].textContent:l[b][0],a.textContent=b+1+": "+c,f.appendChild(a)};g.startSlideMode=function(e){var f=document.getElementById("pagelist"),h=g.odf_canvas.slidevisibilitycss().sheet;for(g.slide_mode=
e;0<h.cssRules.length;)h.deleteRule(0);g.selectSlide(0);"single"===g.slide_mode?(h.insertRule("draw|page { position:fixed; left:0px;top:30px; z-index:1; }",0),h.insertRule("draw|page[slide_current]  { z-index:2;}",1),h.insertRule("draw|page  { -webkit-transform: scale(1);}",2),g.fitToWindow(),window.addEventListener("resize",g.fitToWindow,!1)):"cont"===g.slide_mode&&window.removeEventListener("resize",g.fitToWindow,!1);g.fillPageList(g.odf_canvas.odfContainer().rootElement,f)};g.toggleToolbar=function(){var e,
f,h;e=g.odf_canvas.slidevisibilitycss().sheet;f=-1;for(h=0;h<e.cssRules.length;h+=1)if(".toolbar"===e.cssRules[h].cssText.substring(0,8)){f=h;break}-1<f?e.deleteRule(f):e.insertRule(".toolbar { position:fixed; left:0px;top:-200px; z-index:0; }",0)};g.fitToWindow=function(){var e=g.getPages(g.root()),f=(window.innerHeight-40)/e[0][1].clientHeight,e=(window.innerWidth-10)/e[0][1].clientWidth,f=f<e?f:e,e=g.odf_canvas.slidevisibilitycss().sheet;e.deleteRule(2);e.insertRule("draw|page { \n-moz-transform: scale("+
f+"); \n-moz-transform-origin: 0% 0%; -webkit-transform-origin: 0% 0%; -webkit-transform: scale("+f+"); -o-transform-origin: 0% 0%; -o-transform: scale("+f+"); -ms-transform-origin: 0% 0%; -ms-transform: scale("+f+"); }",2)};g.load=function(e){g.odf_canvas.load(e)};g.odf_element=l;g.odf_canvas=new odf.OdfCanvas(g.odf_element);g.odf_canvas.addListener("statereadychange",g.setInitialSlideMode);g.slide_mode="undefined";document.addEventListener("keydown",g.keyDownHandler,!1)}}();
// Input 64
runtime.loadClass("core.PositionIterator");runtime.loadClass("core.Cursor");
gui.XMLEdit=function(h,l){function g(a,b,c){a.addEventListener?a.addEventListener(b,c,!1):a.attachEvent?a.attachEvent("on"+b,c):a["on"+b]=c}function e(a){a.preventDefault?a.preventDefault():a.returnValue=!1}function f(){var a=h.ownerDocument.defaultView.getSelection();a&&(!(0>=a.rangeCount)&&s)&&(a=a.getRangeAt(0),s.setPoint(a.startContainer,a.startOffset))}function n(){var a=h.ownerDocument.defaultView.getSelection(),b,c;a.removeAllRanges();s&&s.node()&&(b=s.node(),c=b.ownerDocument.createRange(),
c.setStart(b,s.position()),c.collapse(!0),a.addRange(c))}function b(a){var b=a.charCode||a.keyCode;if(s=null,s&&37===b)f(),s.stepBackward(),n();else if(16<=b&&20>=b||33<=b&&40>=b)return;e(a)}function a(a){}function c(a){h.ownerDocument.defaultView.getSelection().getRangeAt(0);e(a)}function d(a){for(var b=a.firstChild;b&&b!==a;)1===b.nodeType&&d(b),b=b.nextSibling||b.parentNode;var c,e,f,b=a.attributes;c="";for(f=b.length-1;0<=f;f-=1)e=b.item(f),c=c+" "+e.nodeName+'="'+e.nodeValue+'"';a.setAttribute("customns_name",
a.nodeName);a.setAttribute("customns_atts",c);b=a.firstChild;for(e=/^\s*$/;b&&b!==a;)c=b,b=b.nextSibling||b.parentNode,3===c.nodeType&&e.test(c.nodeValue)&&c.parentNode.removeChild(c)}function m(a,b){for(var c=a.firstChild,d,e,f;c&&c!==a;){if(1===c.nodeType){m(c,b);d=c.attributes;for(f=d.length-1;0<=f;f-=1)e=d.item(f),"http://www.w3.org/2000/xmlns/"===e.namespaceURI&&!b[e.nodeValue]&&(b[e.nodeValue]=e.localName)}c=c.nextSibling||c.parentNode}}function k(){var a=h.ownerDocument.createElement("style"),
b;b={};m(h,b);var c={},d,e,f=0;for(d in b)if(b.hasOwnProperty(d)&&d){e=b[d];if(!e||c.hasOwnProperty(e)||"xmlns"===e){do e="ns"+f,f+=1;while(c.hasOwnProperty(e));b[d]=e}c[e]=!0}a.type="text/css";b="@namespace customns url(customns);\n"+p;a.appendChild(h.ownerDocument.createTextNode(b));l=l.parentNode.replaceChild(a,l)}var p,r,q,s=null;h.id||(h.id="xml"+String(Math.random()).substring(2));r="#"+h.id+" ";p=r+"*,"+r+":visited, "+r+":link {display:block; margin: 0px; margin-left: 10px; font-size: medium; color: black; background: white; font-variant: normal; font-weight: normal; font-style: normal; font-family: sans-serif; text-decoration: none; white-space: pre-wrap; height: auto; width: auto}\n"+
r+":before {color: blue; content: '<' attr(customns_name) attr(customns_atts) '>';}\n"+r+":after {color: blue; content: '</' attr(customns_name) '>';}\n"+r+"{overflow: auto;}\n";(function(d){g(d,"click",c);g(d,"keydown",b);g(d,"keypress",a);g(d,"drop",e);g(d,"dragend",e);g(d,"beforepaste",e);g(d,"paste",e)})(h);this.updateCSS=k;this.setXML=function(a){a=a.documentElement||a;q=a=h.ownerDocument.importNode(a,!0);for(d(a);h.lastChild;)h.removeChild(h.lastChild);h.appendChild(a);k();s=new core.PositionIterator(a)};
this.getXML=function(){return q}};
// Input 65
/*

 Copyright (C) 2012-2013 KO GmbH <copyright@kogmbh.com>

 @licstart
 The JavaScript code in this page is free software: you can redistribute it
 and/or modify it under the terms of the GNU Affero General Public License
 (GNU AGPL) as published by the Free Software Foundation, either version 3 of
 the License, or (at your option) any later version.  The code is distributed
 WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 FITNESS FOR A PARTICULAR PURPOSE.  See the GNU AGPL for more details.

 As additional permission under GNU AGPL version 3 section 7, you
 may distribute non-source (e.g., minimized or compacted) forms of
 that code without the copy of the GNU GPL normally required by
 section 4, provided you include this license notice and a URL
 through which recipients can access the Corresponding Source.

 As a special exception to the AGPL, any HTML file which merely makes function
 calls to this code, and for that purpose includes it by reference shall be
 deemed a separate work for copyright law purposes. In addition, the copyright
 holders of this code give you permission to combine this code with free
 software libraries that are released under the GNU LGPL. You may copy and
 distribute such a system following the terms of the GNU AGPL for this code
 and the LGPL for the libraries. If you modify this code, you may extend this
 exception to your version of the code, but you are not obligated to do so.
 If you do not wish to do so, delete this exception statement from your
 version.

 This license applies to this entire compilation.
 @licend
 @source: http://www.webodf.org/
 @source: http://gitorious.org/webodf/webodf/
*/
runtime.loadClass("gui.SelectionManager");runtime.loadClass("core.EventNotifier");
ops.OdtDocument=function(h){function l(b){var c=gui.SelectionMover.createPositionIterator(a);for(b+=1;0<b&&c.nextPosition();)1===d.acceptPosition(c)&&(b-=1);return c}function g(a){for(;a&&!(("p"===a.localName||"h"===a.localName)&&a.namespaceURI===n);)a=a.parentNode;return a}function e(a){return h.getFormatting().getStyleElement(h.odfContainer().rootElement.styles,a,"paragraph")}var f=this,n="urn:oasis:names:tc:opendocument:xmlns:text:1.0",b="urn:oasis:names:tc:opendocument:xmlns:drawing:1.0",a,c,
d,m={},k=new core.EventNotifier([ops.OdtDocument.signalCursorAdded,ops.OdtDocument.signalCursorRemoved,ops.OdtDocument.signalCursorMoved,ops.OdtDocument.signalParagraphChanged,ops.OdtDocument.signalParagraphStyleModified,ops.OdtDocument.signalStyleCreated,ops.OdtDocument.signalStyleDeleted,ops.OdtDocument.signalTableAdded]);this.getTextNeighborhood=function(a,b){var c=l(a),d=[],e=[],f=c.container(),g,h=!1,k=!0,m;g=0;do{e=c.textNeighborhood();f=c.container();h=!1;for(m=0;m<d.length;m+=1)if(d[m]===
e[0]){h=!0;break}if(!h){0>b&&e.reverse();if(k){for(h=0;h<e.length;h+=1)if(e[h]===f){e.splice(0,h);break}0>b&&e.splice(0,1);k=!1}e.length&&(d=d.concat(e));for(h=0;h<e.length;h+=1)g+=e[h].data.length}}while(!0===(0<b?c.nextPosition():c.previousPosition())&&g<Math.abs(b));return d};this.getText=function(a,b){var c,d=0,e=[],f=this.getTextNeighborhood(a,b);if(1>f.length)return"";for(c=0;c<f.length&&!(void 0!==f[c].textContent&&(e.push(f[c].textContent),d+=f[c].textContent.length,d>=b));c+=1);return e.join("").substr(0,
b)};this.getParagraphStyleElement=e;this.getParagraphElement=g;this.getParagraphStyleAttributes=function(a){return(a=e(a))?h.getFormatting().getInheritedStyleAttributes(h.odfContainer().rootElement.styles,a):null};this.getPositionInTextNode=function(b){var c=gui.SelectionMover.createPositionIterator(a),e=null,f,g=0;runtime.assert(0<=b,"position must be >= 0");1===d.acceptPosition(c)?(f=c.container(),3===f.nodeType&&(e=f,g=0)):b+=1;for(;0<b||null===e;){if(!c.nextPosition())return null;if(1===d.acceptPosition(c))if(b-=
1,f=c.container(),3===f.nodeType)f!==e?(e=f,g=c.domOffset()):g+=1;else if(null!==e){if(0===b){g=e.length;break}e=null}else if(0===b){e=a.ownerDocument.createTextNode("");f.insertBefore(e,c.rightNode());g=0;break}}if(null===e)return null;for(;0===g&&e.previousSibling&&"cursor"===e.previousSibling.localName;)f=e.previousSibling,0<e.length&&(e=a.ownerDocument.createTextNode("")),f.parentNode.insertBefore(e,f);return{textNode:e,offset:g}};this.getNeighboringParagraph=function(a,b){var c=l(0),e=null;c.setPosition(a,
0);do if(1===d.acceptPosition(c)&&(e=g(c.container()),e!==a))return e;while(!0===(0<b?c.nextPosition():c.previousPosition()));if(e===a)return null};this.getDistanceFromCursor=function(a,b,c){a=m[a];var e=0;runtime.assert(null!==b&&void 0!==b,"OdtDocument.getDistanceFromCursor called without node");a&&(a=a.getStepCounter().countStepsToPosition,e=a(b,c,d));return e};this.getCursorPosition=function(b){return-f.getDistanceFromCursor(b,a,0)};this.getPositionFilter=function(){return d};this.getOdfCanvas=
function(){return h};this.getRootNode=function(){return a};this.getDOM=function(){return a.ownerDocument};this.getSelectionManager=function(){return c};this.getCursor=function(a){return m[a]};this.getCursors=function(){var a=[],b;for(b in m)m.hasOwnProperty(b)&&a.push(m[b]);return a};this.addCursor=function(a){runtime.assert(Boolean(a),"OdtDocument::addCursor without cursor");var b=a.getStepCounter().countForwardSteps(1,d),c=a.getMemberId();runtime.assert(Boolean(c),"OdtDocument::addCursor has cursor without memberid");
a.move(b);m[c]=a};this.removeCursor=function(a){var b=m[a];return b?(b.removeFromOdtDocument(),delete m[a],!0):!1};this.getMetaData=function(a){for(var b=h.odfContainer().rootElement.firstChild;b&&"meta"!==b.localName;)b=b.nextSibling;for(b=b&&b.firstChild;b&&b.localName!==a;)b=b.nextSibling;for(b=b&&b.firstChild;b&&3!==b.nodeType;)b=b.nextSibling;return b?b.data:null};this.getFormatting=function(){return h.getFormatting()};this.emit=function(a,b){k.emit(a,b)};this.subscribe=function(a,b){k.subscribe(a,
b)};d=new function(){function a(b){return/^[ \t\r\n]+$/.test(b)}function c(a){if(null===a||a.namespaceURI!==n)return!1;a=a.localName;return"span"===a||"p"===a||"h"===a}function d(a){if(null===a||1!==a.nodeType)return!1;var c=a.namespaceURI,e=a.localName,f=!1;c===n?f="s"===e||"tab"===e||"line-break"===e:c===b&&(f="frame"===e&&"as-char"===a.getAttributeNS(n,"anchor-type"));return f}function e(a){if(null===a.previousSibling)return a.parentNode;for(a=a.previousSibling;null!==a.lastChild&&c(a);)a=a.lastChild;
return a}function f(b){for(var c=!1;b;)if(3===b.nodeType)if(0===b.length)b=e(b);else return!a(b.data.substr(b.length-1,0));else if(b.namespaceURI===n&&("p"===b.localName||"h"===b.localName)){c=!1;break}else if(d(b)){c=!0;break}else b=e(b);return c}function g(b){var e=!1;if(!b||"p"===b.localName||"h"===b.localName)return!1;for(;b;){if(3===b.nodeType&&0<b.length&&!a(b.data)){e=!0;break}else if(d(b)){e=!0;break}b=null!==b.lastChild&&c(b)?b.lastChild:b.previousSibling?b.previousSibling:null}return e}
function h(b,e,m){var n;if(n=e)n=!1,3===e.nodeType&&0<e.length?(n=e.data,n=a(n.substr(n.length-1,1))?1===n.length?f(e.previousSibling||e.parentNode):!a(n.substr(n.length-2,1)):!0):d(e)&&(n=!0);if(n)return k;if(n=null===e)if(n="p"===b.localName||"h"===b.localName){n=m;var s=!1;if(!n||"p"===n.localName||"h"===n.localName)n=!1;else{for(;n;){if(3===n.nodeType&&0<n.length&&!a(n.data)){s=!0;break}else if(d(n)){s=!0;break}n=null!==n.firstChild&&c(n)?n.firstChild:n.nextSibling?n.nextSibling:null}n=s}n=!n}if(n)return k;
if(!(n=null===m))n=!1,3===m.nodeType&&0<m.length?n=!a(m.data.substr(0,1)):d(m)&&(n=!0),n=!n;return n?l:g(e||b.previousSibling||b.parentNode)?l:k}var k=core.PositionFilter.FilterResult.FILTER_ACCEPT,l=core.PositionFilter.FilterResult.FILTER_REJECT;this.acceptPosition=function(b){var d=b.container(),e=d.nodeType,m;if(1!==e&&3!==e)return l;if(3===e){if(!c(d.parentNode))return l;e=b.offset();m=d.data;if(0<e){b=m.substr(e-1,1);if(!a(b))return k;if(1<e)return b=m.substr(e-2,1),a(b)?l:k;e=d.previousSibling||
d.parentNode;return f(e)?k:g(e)?l:k}e=b.leftNode();b=d;d=d.parentNode;d=h(d,e,b)}else c(d)?(e=b.leftNode(),b=b.rightNode(),d=h(d,e,b)):d=l;return d}};a=function(a){for(a=a.rootElement.firstChild;a&&"body"!==a.localName;)a=a.nextSibling;for(a=a&&a.firstChild;a&&"text"!==a.localName;)a=a.nextSibling;return a}(h.odfContainer());c=new gui.SelectionManager(a)};ops.OdtDocument.signalCursorAdded="cursor/added";ops.OdtDocument.signalCursorRemoved="cursor/removed";ops.OdtDocument.signalCursorMoved="cursor/moved";
ops.OdtDocument.signalParagraphChanged="paragraph/changed";ops.OdtDocument.signalTableAdded="table/added";ops.OdtDocument.signalStyleCreated="style/created";ops.OdtDocument.signalStyleDeleted="style/deleted";ops.OdtDocument.signalParagraphStyleModified="paragraphstyle/modified";(function(){return ops.OdtDocument})();
// Input 66
/*

 Copyright (C) 2012-2013 KO GmbH <copyright@kogmbh.com>

 @licstart
 The JavaScript code in this page is free software: you can redistribute it
 and/or modify it under the terms of the GNU Affero General Public License
 (GNU AGPL) as published by the Free Software Foundation, either version 3 of
 the License, or (at your option) any later version.  The code is distributed
 WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 FITNESS FOR A PARTICULAR PURPOSE.  See the GNU AGPL for more details.

 As additional permission under GNU AGPL version 3 section 7, you
 may distribute non-source (e.g., minimized or compacted) forms of
 that code without the copy of the GNU GPL normally required by
 section 4, provided you include this license notice and a URL
 through which recipients can access the Corresponding Source.

 As a special exception to the AGPL, any HTML file which merely makes function
 calls to this code, and for that purpose includes it by reference shall be
 deemed a separate work for copyright law purposes. In addition, the copyright
 holders of this code give you permission to combine this code with free
 software libraries that are released under the GNU LGPL. You may copy and
 distribute such a system following the terms of the GNU AGPL for this code
 and the LGPL for the libraries. If you modify this code, you may extend this
 exception to your version of the code, but you are not obligated to do so.
 If you do not wish to do so, delete this exception statement from your
 version.

 This license applies to this entire compilation.
 @licend
 @source: http://www.webodf.org/
 @source: http://gitorious.org/webodf/webodf/
*/
runtime.loadClass("ops.TrivialUserModel");runtime.loadClass("ops.TrivialOperationRouter");runtime.loadClass("ops.OperationFactory");runtime.loadClass("ops.OdtDocument");
ops.Session=function(h){var l=new ops.OdtDocument(h),g=new ops.TrivialUserModel,e=null;this.setUserModel=function(e){g=e};this.setOperationRouter=function(f){e=f;f.setPlaybackFunction(function(e){e.execute(l)});f.setOperationFactory(new ops.OperationFactory)};this.getUserModel=function(){return g};this.getOdtDocument=function(){return l};this.enqueue=function(f){e.push(f)};this.setOperationRouter(new ops.TrivialOperationRouter)};
// Input 67
var webodf_css="@namespace draw url(urn:oasis:names:tc:opendocument:xmlns:drawing:1.0);\n@namespace fo url(urn:oasis:names:tc:opendocument:xmlns:xsl-fo-compatible:1.0);\n@namespace office url(urn:oasis:names:tc:opendocument:xmlns:office:1.0);\n@namespace presentation url(urn:oasis:names:tc:opendocument:xmlns:presentation:1.0);\n@namespace style url(urn:oasis:names:tc:opendocument:xmlns:style:1.0);\n@namespace svg url(urn:oasis:names:tc:opendocument:xmlns:svg-compatible:1.0);\n@namespace table url(urn:oasis:names:tc:opendocument:xmlns:table:1.0);\n@namespace text url(urn:oasis:names:tc:opendocument:xmlns:text:1.0);\n@namespace runtimens url(urn:webodf); /* namespace for runtime only */\n@namespace cursor url(urn:webodf:names:cursor);\n@namespace editinfo url(urn:webodf:names:editinfo);\n\noffice|document > *, office|document-content > * {\n  display: none;\n}\noffice|body, office|document {\n  display: inline-block;\n  position: relative;\n}\n\ntext|p, text|h {\n  display: block;\n  padding: 0;\n  margin: 0;\n  line-height: normal;\n  position: relative;\n  min-height: 1em; /* prevent empty paragraphs and headings from collapsing if they are empty */\n}\n*[runtimens|containsparagraphanchor] {\n  position: relative;\n}\ntext|s {\n    white-space: pre;\n}\ntext|tab:before {\n  display: inline;\n  content: '        ';\n}\ntext|line-break {\n  content: \" \";\n  display: block;\n}\ntext|tracked-changes {\n  /*Consumers that do not support change tracking, should ignore changes.*/\n  display: none;\n}\noffice|binary-data {\n  display: none;\n}\noffice|text {\n  display: block;\n  width: 210mm; /* default to A4 width */\n  min-height: 297mm;\n  padding-left: 32mm;\n  padding-right: 32mm;\n  padding-top: 25mm;\n  padding-bottom: 13mm;\n  margin: 2px;\n  text-align: left;\n  overflow: hidden;\n  word-wrap: break-word;\n}\noffice|spreadsheet {\n  display: block;\n  border-collapse: collapse;\n  empty-cells: show;\n  font-family: sans-serif;\n  font-size: 10pt;\n  text-align: left;\n  page-break-inside: avoid;\n  overflow: hidden;\n}\noffice|presentation {\n  display: inline-block;\n  text-align: left;\n}\ndraw|page {\n  display: block;\n  height: 21cm;\n  width: 28cm;\n  margin: 3px;\n  position: relative;\n  overflow: hidden;\n}\npresentation|notes {\n    display: none;\n}\n@media print {\n  draw|page {\n    border: 1pt solid black;\n    page-break-inside: avoid;\n  }\n  presentation|notes {\n    /*TODO*/\n  }\n}\noffice|spreadsheet text|p {\n  border: 0px;\n  padding: 1px;\n  margin: 0px;\n}\noffice|spreadsheet table|table {\n  margin: 3px;\n}\noffice|spreadsheet table|table:after {\n  /* show sheet name the end of the sheet */\n  /*content: attr(table|name);*/ /* gives parsing error in opera */\n}\noffice|spreadsheet table|table-row {\n  counter-increment: row;\n}\noffice|spreadsheet table|table-row:before {\n  width: 3em;\n  background: #cccccc;\n  border: 1px solid black;\n  text-align: center;\n  content: counter(row);\n  display: table-cell;\n}\noffice|spreadsheet table|table-cell {\n  border: 1px solid #cccccc;\n}\ntable|table {\n  display: table;\n}\ndraw|frame table|table {\n  width: 100%;\n  height: 100%;\n  background: white;\n}\ntable|table-header-rows {\n  display: table-header-group;\n}\ntable|table-row {\n  display: table-row;\n}\ntable|table-column {\n  display: table-column;\n}\ntable|table-cell {\n  width: 0.889in;\n  display: table-cell;\n}\ndraw|frame {\n  display: block;\n}\ndraw|image {\n  display: block;\n  width: 100%;\n  height: 100%;\n  top: 0px;\n  left: 0px;\n  background-repeat: no-repeat;\n  background-size: 100% 100%;\n  -moz-background-size: 100% 100%;\n}\n/* only show the first image in frame */\ndraw|frame > draw|image:nth-of-type(n+2) {\n  display: none;\n}\ntext|list:before {\n    display: none;\n    content:\"\";\n}\ntext|list {\n    counter-reset: list;\n}\ntext|list-item {\n    display: block;\n}\ntext|number {\n    display:none;\n}\n\ntext|a {\n    color: blue;\n    text-decoration: underline;\n    cursor: pointer;\n}\ntext|note-citation {\n    vertical-align: super;\n    font-size: smaller;\n}\ntext|note-body {\n    display: none;\n}\ntext|note:hover text|note-citation {\n    background: #dddddd;\n}\ntext|note:hover text|note-body {\n    display: block;\n    left:1em;\n    max-width: 80%;\n    position: absolute;\n    background: #ffffaa;\n}\nsvg|title, svg|desc {\n    display: none;\n}\nvideo {\n    width: 100%;\n    height: 100%\n}\n\n/* below set up the cursor */\ncursor|cursor {\n    display: inline;\n    width: 0px;\n    height: 1em;\n    /* making the position relative enables the avatar to use\n       the cursor as reference for its absolute position */\n    position: relative;\n}\ncursor|cursor > span {\n    display: inline;\n    position: absolute;\n    height: 1em;\n    border-left: 2px solid black;\n    outline: none;\n}\n\ncursor|cursor > div {\n    padding: 3px;\n    box-shadow: 0px 0px 5px rgba(50, 50, 50, 0.75);\n    border: none !important;\n    border-radius: 5px;\n    opacity: 0.3;\n}\n\ncursor|cursor > div > img {\n    border-radius: 5px;\n}\n\ncursor|cursor > div.active {\n    opacity: 0.8;\n}\n\ncursor|cursor > div:after {\n    content: ' ';\n    position: absolute;\n    width: 0px;\n    height: 0px;\n    border-style: solid;\n    border-width: 8.7px 5px 0 5px;\n    border-color: black transparent transparent transparent;\n\n    top: 100%;\n    left: 43%;\n}\n\n\n.editInfoMarker {\n    position: absolute;\n    width: 10px;\n    height: 100%;\n    left: -20px;\n    opacity: 0.8;\n    top: 0;\n    border-radius: 5px;\n    background-color: transparent;\n    box-shadow: 0px 0px 5px rgba(50, 50, 50, 0.75);\n}\n.editInfoMarker:hover {\n    box-shadow: 0px 0px 8px rgba(0, 0, 0, 1);\n}\n\n.editInfoHandle {\n    position: absolute;\n    background-color: black;\n    padding: 5px;\n    border-radius: 5px;\n    opacity: 0.8;\n    box-shadow: 0px 0px 5px rgba(50, 50, 50, 0.75);\n    bottom: 100%;\n    margin-bottom: 10px;\n    z-index: 3;\n    left: -25px;\n}\n.editInfoHandle:after {\n    content: ' ';\n    position: absolute;\n    width: 0px;\n    height: 0px;\n    border-style: solid;\n    border-width: 8.7px 5px 0 5px;\n    border-color: black transparent transparent transparent;\n\n    top: 100%;\n    left: 5px;\n}\n.editInfo {\n    font-family: sans-serif;\n    font-weight: normal;\n    font-style: normal;\n    text-decoration: none;\n    color: white;\n    width: 100%;\n    height: 12pt;\n}\n.editInfoColor {\n    float: left;\n    width: 10pt;\n    height: 10pt;\n    border: 1px solid white;\n}\n.editInfoAuthor {\n    float: left;\n    margin-left: 5pt;\n    font-size: 10pt;\n    text-align: left;\n    height: 12pt;\n    line-height: 12pt;\n}\n.editInfoTime {\n    float: right;\n    margin-left: 30pt;\n    font-size: 8pt;\n    font-style: italic;\n    color: yellow;\n    height: 12pt;\n    line-height: 12pt;\n}\n";
