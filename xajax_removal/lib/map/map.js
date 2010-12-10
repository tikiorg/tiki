// functions to be used in the TikiMaps feature

var imgClicked=false;
var mapcursor= {X:0, Y:0, imagex:0, imagey:0};
var firstpoint=false;
var changeleg=false;

function map_mouseclick(evt) {
	var e = new xEvent(evt);
	//window.status='mouseclick '+e.target.id;
	
  selected=xGetElementById('zoom').selectedIndex;
  if (selected==1) {
    //we have a query
  
		xPreventDefault(evt);
		xStopPropagation(evt);
	
	  //locate the mouse	
		var X = e.pageX;
		var Y = e.pageY;
	
		xMoveTo('queryWindow',X+5,Y+5);
		xShow('queryWindow');
		var innerBox=xGetElementById('innerBox');
		var innerBoxContent=xGetElementById('innerBoxContent');
	  xInnerHtml(innerBoxContent,'....');
	  xTop(innerBoxContent, 0);
    
		var zoom=3;
		xajax_map_redraw(mapfile ,xGetElementById('xx').value, xGetElementById('yy').value, minx, maxx, miny, maxy, xsize, ysize, layers, labels,zoom,changeleg);
		changeleg=false;

		return false;
	} else if (selected==2) {
		xPreventDefault(evt);
		xStopPropagation(evt);
		return false;
	} else {
		if(!imgClicked) {
		  imgClicked = false; // in case of later cancellation
			return true;
		}
		var e = new xEvent(evt);
		var X = e.pageX;
		var Y = e.pageY;
		window.setTimeout("map_submit("+X.toString()+","+Y.toString()+")",50); // don't submit on this event
		return false;
	}
	
	return true;
}

function map_submit(X,Y)
{
	var form = xGetElementById('frmmap');
	//alert("map_submit("+X+","+Y+")");
	var el = xCreateElement("INPUT");
	el.type="hidden";
	el.name="xx";
	el.value=X; // whatever
	//alert(el);
	xAppendChild(form,el);
	var el2 = xCreateElement("INPUT");
	el2.type="hidden";
	el2.name="yy";
	el2.value=Y; // idem
	xAppendChild(form,el2);
	//form.submit();
	return true;
}

function query_close(evt) {
	xHide('queryWindow');
}

function query_down()
{
  if (!scrollActive) {
    scrollStop = false;
    onScrollDn();
  }
}
function onScrollDn()
{
  if (!scrollStop) {
    scrollActive = true;
    setTimeout('onScrollDn()', scrollInterval);
    var sc = xGetElementById('innerBoxContent');
    var ib = xGetElementById('innerBox');
    var y = xTop(sc) - scrollIncrement;   
    if (y >= -(xHeight(sc) - xHeight(ib))-20) {
      xTop(sc, y);
    }
    else {
      scrollStop = true;
      scrollActive = false;
    }
  }
}
function query_up()
{
  if (!scrollActive) {
    scrollStop = false;
    onScrollUp();
  }
}
function onScrollUp()
{
  if (!scrollStop) {
    scrollActive = true;
    setTimeout('onScrollUp()', scrollInterval);
    var sc = xGetElementById('innerBoxContent');
    var y = xTop(sc) + scrollIncrement;
    if (y <= 0) {
      xTop(sc, y);
    }
    else {
      scrollStop = true;
      scrollActive = false;
    }
  }
}
function query_scroll_stop()
{
  scrollStop = true;
  scrollActive = false;
}

// functions to enable the query window to be dragged
var highZ = 3;

function queryOnDragStart(ele, mx, my)
{
  xZIndex('queryWindow', highZ++);
}

function queryOnDrag(ele, mdx, mdy)
{
  xMoveTo('queryWindow', xLeft('queryWindow') + mdx, xTop('queryWindow') + mdy);
}



function map_mousemove(evt) {
	var e = new xEvent(evt);
	//window.status='mousemove '+e.target.id;
	var selected=xGetElementById('zoom').selectedIndex;
	
	var X = e.pageX;
	var Y = e.pageY;

	obj=xGetElementById('map');
	var imagex = xPageX(obj);
	var imagey = xPageY(obj); 
	
	if (xIE4Up) {
		X=X-imagex;
		Y=Y-imagey;
	}

	var posx=((X-imagex)*(maxx-minx)/(xsize))+minx;
	var posy=((ysize-Y+imagey)*(maxy-miny)/(ysize))+miny;

	xGetElementById('xx').value=posx;
	xGetElementById('yy').value=posy;
	if ((selected==3 || selected==0) && firstpoint) {

		var zoomselect=xGetElementById('zoomselect');
		if (X<mapcursor.X) {
			var selX=X;
		} else {
			var selX=mapcursor.X;
		}
		if (Y<mapcursor.Y) {
			var selY=Y;
		} else {
			var selY=mapcursor.Y;
		}
		var selwidth=Math.abs(X-mapcursor.X);
		var selheight=Math.abs(Y-mapcursor.Y);
		var zoomselect=xGetElementById('zoomselect');
		zoomselect.style.visibility="visible";
		zoomselect.style.left=selX.toString()+"px";
		zoomselect.style.top=selY.toString()+"px";
		zoomselect.style.width=selwidth.toString()+"px";
		zoomselect.style.height=selheight.toString()+"px";
		
		//xGetElementById('yy').value=selX+" "+selY+" "+selwidth+" "+selheight;
	}

  return true;
}



function map_mousedown(evt) {
	var e = new xEvent(evt);
	//window.status='mousedown '+e.target.id;

	var selected=xGetElementById('zoom').selectedIndex;
  if (selected==2 || selected==3 || selected==0) {
 		
		var X = e.pageX;
		var Y = e.pageY;
	
		obj=xGetElementById('map');
		var imagex = xPageX(obj);
		var imagey = xPageY(obj);
	  if (xIE4Up) {
			mapcursor.X=X-imagex;
			mapcursor.Y=Y-imagey;
		} else {
			mapcursor.X=X;
			mapcursor.Y=Y;		
		}
		mapcursor.imagex=imagex;
		mapcursor.imagey=imagey;

		if (firstpoint) {
			firstpoint=false;
		} else {
			firstpoint=true;
		}		
		if ((selected==3 || selected==0) && firstpoint) {
			var zoomselect=xGetElementById('zoomselect');
			zoomselect.style.visibility="visible";
			zoomselect.style.left=X.toString()+"px";
			zoomselect.style.top=Y.toString()+"px";
			zoomselect.style.width="0px";
			zoomselect.style.height="0px";
			xAddEventListener(xGetElementById('zoomselect'),'mousemove',map_mousemove,false);
			xAddEventListener(xGetElementById('zoomselect'),'mouseup',map_mouseup,false);
			xPreventDefault(evt);
			xStopPropagation(evt);
			return false;
		}
		
		return true;
	} else {
		return true;
	}
	
}

function map_mouseup(evt) {
	var e = new xEvent(evt);
	//window.status='mouseup '+e.target.id;
	
	imgClicked=true;
	var selected=xGetElementById('zoom').selectedIndex;
  if (selected==2 && firstpoint) {

		var X = e.pageX;
		var Y = e.pageY;
	
		var map=xGetElementById('map');
		var imagex = xPageX(map);
		var imagey = xPageY(map);		
		
		map.style.cursor='wait';
	
		
		var zoom=4;
		var xx=mapcursor.imagex-imagex+(xsize/2);
		var yy=mapcursor.imagey-imagey+(ysize/2);
		
  	xajax_map_redraw(mapfile ,xx, yy, 
	    	minx, maxx, miny, maxy,
  	  	xsize, ysize, layers, labels, zoom, changeleg);
  	imgClicked=false;
  	firstpoint=false;
  	changeleg=false;
  	return false;
	}	else if (selected==3 && firstpoint && e.target.id=='zoomselect') {
		firstpoint=false;
		
		var map=xGetElementById('map');
		map.style.cursor='wait';
		
		//code to redraw the map
		var X = e.pageX;
		var Y = e.pageY;

		obj=xGetElementById('map');
		var imagex = xPageX(obj);
		var imagey = xPageY(obj); 
		
		
		zoom=5;
		
		if (xIE4Up) {
			var xx=mapcursor.X;
			var yy=mapcursor.Y;
			var xx2=X-imagex;
			var yy2=Y-imagey;
		} else {
			var xx=mapcursor.X-imagex;
			var yy=mapcursor.Y-imagey;
			var xx2=X-imagex;
			var yy2=Y-imagey;
		}
		//window.status='zooming '+xx+'-'+yy+'/'+xx2+'-'+yy2;
		xajax_map_redraw(mapfile ,xx, yy, 
	    	minx, maxx, miny, maxy,
  	  	xsize, ysize, layers, labels, zoom, changeleg, xx2, yy2);
  	changeleg=false;
		return false;
	}	else if (selected==0 && firstpoint && e.target.id=='zoomselect') {
		firstpoint=false;
		
		var map=xGetElementById('map');
		map.style.cursor='wait';
		
		//code to redraw the map
		var X = e.pageX;
		var Y = e.pageY;

		obj=xGetElementById('map');
		var imagex = xPageX(obj);
		var imagey = xPageY(obj); 
		
		
		zoom=5;
		
		if (xIE4Up) {
			var xx=xWidth(map)+(X-imagex);
			var yy=-xHeight(map)+(Y-imagey);
			var xx2=xWidth(map)+(mapcursor.X);;
			var yy2=xHeight(map)+(mapcursor.Y);
		} else {
			var xx=-xWidth(map)+(X-imagex);
			var yy=-xHeight(map)+(Y-imagey);
			var xx2=xWidth(map)+(mapcursor.X-imagex);
			var yy2=xHeight(map)+(mapcursor.Y-imagey);
		}
		//window.status='zooming '+xx+'-'+yy+'/'+xx2+'-'+yy2;
		xajax_map_redraw(mapfile ,xx, yy, 
	    	minx, maxx, miny, maxy,
  	  	xsize, ysize, layers, labels, zoom, changeleg, xx2, yy2);
  	changeleg=false;
		return false;
	} else {
		return true;
	}
}

function changelayer(x)
{
  if (layers[x]) {	
		layers[x]=false;
	} else {
		layers[x]=true;
	}
	changeleg=true;
}

function changelabel(x)
{
  if (labels[x]) {	
		labels[x]=false;
	} else {
		labels[x]=true;
	}
	changeleg=true;
}

function selectimgzoom(x)
{
	var arrimgzoom = new Array(4);
	var map=xGetElementById('map');
	
	arrimgzoom[0]=xGetElementById('imgzoom2');
	arrimgzoom[1]=xGetElementById('imgzoom3');
	arrimgzoom[2]=xGetElementById('imgzoom4');
	arrimgzoom[3]=xGetElementById('imgzoom5');

	for(var i=0;i<=3;i++)
	{
	  arrimgzoom[i].border=0;
	  if (i==x)
	  {
	    arrimgzoom[i].border=1;
	  }
	}
	xDisableDrag(xGetElementById('map'));
	if (x==1) {
		map.style.cursor='help';
	} else if (x==2) {
		map.style.cursor='move';
		xEnableDrag(xGetElementById('map'));
	} else {
	  map.style.cursor='auto';
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
