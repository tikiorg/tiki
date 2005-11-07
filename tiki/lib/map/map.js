// functions to be used in the TikiMaps feature

// BEGIN: Part of X, a Cross-Browser Javascript Library, Distributed under the terms of the GNU LGPL
var xOp7Up,xOp6Dn,xIE4Up,xIE4,xIE5,xNN4,xUA=navigator.userAgent.toLowerCase();
if(window.opera){
	var i=xUA.indexOf('opera');
	if(i!=-1){
		var v=parseInt(xUA.charAt(i+6));
		xOp7Up=v>=7;
		xOp6Dn=v<7;
	}
}else if(navigator.vendor!='KDE' && document.all && xUA.indexOf('msie')!=-1){
	xIE4Up=parseFloat(navigator.appVersion)>=4;
	xIE4=xUA.indexOf('msie 4')!=-1;
	xIE5=xUA.indexOf('msie 5')!=-1;
}else if(document.layers){
	xNN4=true;
}
xMac=xUA.indexOf('mac')!=-1;

function xGetElementById(e)
{
  if(typeof(e)!='string') return e;
  if(document.getElementById) e=document.getElementById(e);
  else if(document.all) e=document.all[e];
  else e=null;
  return e;
}

function xNum()
{
  for(var i=0; i<arguments.length; ++i){if(isNaN(arguments[i]) || typeof(arguments[i])!='number') return false;}
  return true;
}

function xDef()
{
  for(var i=0; i<arguments.length; ++i){if(typeof(arguments[i])=='undefined') return false;}
  return true;
}

function xPageY(e)
{
  if (!(e=xGetElementById(e))) return 0;
  var y = 0;
  while (e) {
    if (xDef(e.offsetTop)) y += e.offsetTop;
    e = xDef(e.offsetParent) ? e.offsetParent : null;
  }
//  if (xOp7Up) return y - document.body.offsetTop; // v3.14, temporary hack for opera bug 130324 (reported 1nov03)
  return y;
}

function xPageX(e)
{
  if (!(e=xGetElementById(e))) return 0;
  var x = 0;
  while (e) {
    if (xDef(e.offsetLeft)) x += e.offsetLeft;
    e = xDef(e.offsetParent) ? e.offsetParent : null;
  }
  return x;
}

function xAddEventListener(e,eT,eL,cap)
{
  if(!(e=xGetElementById(e))) return;
  eT=eT.toLowerCase();
  if((!xIE4Up && !xOp7Up) && e==window) {
    if(eT=='resize') { window.xPCW=xClientWidth(); window.xPCH=xClientHeight(); window.xREL=eL; xResizeEvent(); return; }
    if(eT=='scroll') { window.xPSL=xScrollLeft(); window.xPST=xScrollTop(); window.xSEL=eL; xScrollEvent(); return; }
  }
  var eh='e.on'+eT+'=eL';
  if(e.addEventListener) e.addEventListener(eT,eL,cap);
  else if(e.attachEvent) e.attachEvent('on'+eT,eL);
  else eval(eh);
}
// called only from the above
function xResizeEvent()
{
  if (window.xREL) setTimeout('xResizeEvent()', 250);
  var cw = xClientWidth(), ch = xClientHeight();
  if (window.xPCW != cw || window.xPCH != ch) { window.xPCW = cw; window.xPCH = ch; if (window.xREL) window.xREL(); }
}
function xScrollEvent()
{
  if (window.xSEL) setTimeout('xScrollEvent()', 250);
  var sl = xScrollLeft(), st = xScrollTop();
  if (window.xPSL != sl || window.xPST != st) { window.xPSL = sl; window.xPST = st; if (window.xSEL) window.xSEL(); }
}

function xClientWidth()
{
  var w=0;
  if(xOp6Dn) w=window.innerWidth;
  else if(document.compatMode == 'CSS1Compat' && !window.opera && document.documentElement && document.documentElement.clientWidth)
    w=document.documentElement.clientWidth;
  else if(document.body && document.body.clientWidth)
    w=document.body.clientWidth;
  else if(xDef(window.innerWidth,window.innerHeight,document.height)) {
    w=window.innerWidth;
    if(document.height>window.innerHeight) w-=16;
  }
  return w;
}

function xClientHeight()
{
  var h=0;
  if(xOp6Dn) h=window.innerHeight;
  else if(document.compatMode == 'CSS1Compat' && !window.opera && document.documentElement && document.documentElement.clientHeight)
    h=document.documentElement.clientHeight;
  else if(document.body && document.body.clientHeight)
    h=document.body.clientHeight;
  else if(xDef(window.innerWidth,window.innerHeight,document.width)) {
    h=window.innerHeight;
    if(document.width>window.innerWidth) h-=16;
  }
  return h;
}

function xScrollLeft(e, bWin)
{
  var offset=0;
  if (!xDef(e) || bWin || e == document || e.tagName.toLowerCase() == 'html' || e.tagName.toLowerCase() == 'body') {
    var w = window;
    if (bWin && e) w = e;
    if(w.document.documentElement && w.document.documentElement.scrollLeft) offset=w.document.documentElement.scrollLeft;
    else if(w.document.body && xDef(w.document.body.scrollLeft)) offset=w.document.body.scrollLeft;
  }
  else {
    e = xGetElementById(e);
    if (e && xNum(e.scrollLeft)) offset = e.scrollLeft;
  }
  return offset;
}

function xScrollTop(e, bWin)
{
  var offset=0;
  if (!xDef(e) || bWin || e == document || e.tagName.toLowerCase() == 'html' || e.tagName.toLowerCase() == 'body') {

    var w = window;
    if (bWin && e) w = e;
    if(w.document.documentElement && w.document.documentElement.scrollTop) offset=w.document.documentElement.scrollTop;
    else if(w.document.body && xDef(w.document.body.scrollTop)) offset=w.document.body.scrollTop;
  }
  else {
    e = xGetElementById(e);
    if (e && xNum(e.scrollTop)) offset = e.scrollTop;
  }
  return offset;
}

function xEvent(evt) // object prototype
{
  var e = evt || window.event;
  if(!e) return;
  if(e.type) this.type = e.type;
  if(e.target) this.target = e.target;
  else if(e.srcElement) this.target = e.srcElement;

  // Section B
  if (e.relatedTarget) this.relatedTarget = e.relatedTarget;
  else if (e.type == 'mouseover' && e.fromElement) this.relatedTarget = e.fromElement;
  else if (e.type == 'mouseout') this.relatedTarget = e.toElement;
  // End Section B

  if(xOp6Dn) { this.pageX = e.clientX; this.pageY = e.clientY; }
  else if(xDef(e.pageX,e.pageY)) { this.pageX = e.pageX; this.pageY = e.pageY; }
  else if(xDef(e.clientX,e.clientY)) { this.pageX = e.clientX + xScrollLeft(); this.pageY = e.clientY + xScrollTop(); }

  // Section A
  if (xDef(e.offsetX,e.offsetY)) {
    this.offsetX = e.offsetX;
    this.offsetY = e.offsetY;
  }
  else if (xDef(e.layerX,e.layerY)) {
    this.offsetX = e.layerX;
    this.offsetY = e.layerY;
  }
  else {
    this.offsetX = this.pageX - xPageX(this.target);
    this.offsetY = this.pageY - xPageY(this.target);
  }
  // End Section A
  
  if (e.keyCode) { this.keyCode = e.keyCode; } // for moz/fb, if keyCode==0 use which
  else if (xDef(e.which) && e.type.indexOf('key')!=-1) { this.keyCode = e.which; }

  this.shiftKey = e.shiftKey;
  this.ctrlKey = e.ctrlKey;
  this.altKey = e.altKey;
}

// END: Part of X, a Cross-Browser Javascript Library, Distributed under the terms of the GNU LGPL

function map_mousemove(evt) {

	var e = new xEvent(evt);
	var X = e.pageX;
	var Y = e.pageY;

	obj=xGetElementById('map');
	var imagex = xPageX(obj);
	var imagey = xPageY(obj); 

	var posx=((X-imagex)*(maxx-minx)/(xsize))+minx;
	var posy=((ysize-Y+imagey)*(maxy-miny)/(ysize))+miny;

	xGetElementById('xx').value=posx;
	xGetElementById('yy').value=posy;

  return true;
}

function selectimgzoom(x)
{
	var arrimgzoom = new Array(8);

	arrimgzoom[0]=xGetElementById('imgzoom0');
	arrimgzoom[1]=xGetElementById('imgzoom1');
	arrimgzoom[2]=xGetElementById('imgzoom2');
	arrimgzoom[3]=xGetElementById('imgzoom3');
	arrimgzoom[4]=xGetElementById('imgzoom4');
	arrimgzoom[5]=xGetElementById('imgzoom5');
	arrimgzoom[6]=xGetElementById('imgzoom6');
	arrimgzoom[7]=xGetElementById('imgzoom7');
	

	for(var i=0;i<=7;i++)
	{
	  arrimgzoom[i].border=0;
	  if (i==x)
	  {
	    arrimgzoom[i].border=1;
	  }
	}
}

function zoomin(x){
	xGetElementById('zoom').options[x].selected=true;
	selectimgzoom(x);
}

function cbzoomchange() {
	var selected;
	selected=xGetElementById('zoom').selectedIndex;
	selectimgzoom(selected);

}
