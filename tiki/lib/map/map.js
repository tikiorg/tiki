// functions to be used in the TikiMaps feature


// xGetElementById, Copyright 2001-2005 Michael Foster (Cross-Browser.com)
// Part of X, a Cross-Browser Javascript Library, Distributed under the terms of the GNU LGPL

function xGetElementById(e)
{
  if(typeof(e)!='string') return e;
  if(document.getElementById) e=document.getElementById(e);
  else if(document.all) e=document.all[e];
  else e=null;
  return e;
}
// xDef, Copyright 2001-2005 Michael Foster (Cross-Browser.com)
// Part of X, a Cross-Browser Javascript Library, Distributed under the terms of the GNU LGPL

function xDef()
{
  for(var i=0; i<arguments.length; ++i){if(typeof(arguments[i])=='undefined') return false;}
  return true;
}
// xPageY, Copyright 2001-2005 Michael Foster (Cross-Browser.com)
// Part of X, a Cross-Browser Javascript Library, Distributed under the terms of the GNU LGPL

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
// xPageX, Copyright 2001-2005 Michael Foster (Cross-Browser.com)
// Part of X, a Cross-Browser Javascript Library, Distributed under the terms of the GNU LGPL

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

function map_mousemove(e) {
  var X = (e.pageX)   ? e.pageX   : e.clientX;
	var Y = (e.pageY)   ? e.pageY   : e.clientY;

	obj=xGetElementById('map');
	var imagex = xPageX(obj);
	var imagey = xPageY(obj); 

	var posx=((X-imagex)*(maxx-minx)/(xsize))+minx;
	var posy=((ysize-Y+imagey)*(maxy-miny)/(ysize))+miny;
	status="x= "+posx+", y= "+posy;
  document.getElementById("stat").innerHTML = status;
}

function selectimgzoom(x)
{
	var arrimgzoom = new Array(8)

	arrimgzoom[0]=document.frmmap.imgzoom0
	arrimgzoom[1]=document.frmmap.imgzoom1
	arrimgzoom[2]=document.frmmap.imgzoom2
	arrimgzoom[3]=document.frmmap.imgzoom3
	arrimgzoom[4]=document.frmmap.imgzoom4
	arrimgzoom[5]=document.frmmap.imgzoom5
	arrimgzoom[6]=document.frmmap.imgzoom6
	arrimgzoom[7]=document.frmmap.imgzoom7
	

	for(var i=0;i<=7;i++)
	{
	  arrimgzoom[i].border=0
	  if (i==x)
	  {
	    arrimgzoom[i].border=1
	  }
	}
}

function zoomin(x){
	document.frmmap.zoom.options[x].selected=true
	selectimgzoom(x)
}

function cbzoomchange() {
	var selected
	selected=document.frmmap.zoom.selectedIndex
	selectimgzoom(selected)

}
