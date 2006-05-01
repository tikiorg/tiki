// functions to be used in the TikiMaps feature

function map_mouseclick(evt) {
  selected=xGetElementById('zoom').selectedIndex;
  if (selected==3) {
    //we have a query
  
		xPreventDefault(evt);
		xStopPropagation(evt);
	
	  //locate the mouse	
		var e = new xEvent(evt);
		var X = e.pageX;
		var Y = e.pageY;
	
		xMoveTo('queryWindow',X+5,Y+5);
		xShow('queryWindow');
		var innerBox=xGetElementById('innerBox');
		var innerBoxContent=xGetElementById('innerBoxContent');
	  xInnerHtml(innerBoxContent,'....');
    
    var cp = new cpaint();
    //cp.set_debug(2);
    cp.set_response_type('TEXT');

    cp.call('lib/map/map_cp.php', 'cp_map_query', query_results, 
    	mapfile ,xGetElementById('xx').value, xGetElementById('yy').value, 
    	minx, maxx, miny, maxy,
    	xsize, ysize, layers, labels);
		return false;
	} else {
		return true;
	}
	
	return true;
}

function query_results(result) {
  imgurl=result.substring(0,result.indexOf('\n'));
 	result=result.substring(result.indexOf('\n'));
	result=result.substring(result.indexOf('\n'));
	var innerBoxContent=xGetElementById('innerBoxContent');
	xInnerHtml(innerBoxContent,result);
	var resultBox=xGetElementById('resultBox');
	xInnerHtml(resultBox,result);
	var map=xGetElementById('map');
	map.src=imgurl;
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
    if (y >= -(xHeight(sc) - xHeight(ib))) {
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
	var map=xGetElementById('map');
	
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
	if (x==3) {
		map.style.cursor='help';
	} else if (x==4) {
		map.style.cursor='move';
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
