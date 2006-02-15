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
		innerBox=xGetElementById('innerBox');
	  xInnerHtml(innerBox,'....');
	  innerBox.slideTop = 0;
    innerBox.slideLeft = 0;	
    innerBox.slideLinear = true ;
    xSlideTo(innerBox, 0, 0 , 5);
    
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
	innerBox=xGetElementById('innerBox');
	xInnerHtml(innerBox,result);
	resultBox=xGetElementById('resultBox');
	xInnerHtml(resultBox,result);
	map=xGetElementById('map');
	map.src=imgurl;
}

function query_close(evt) {
	xHide('queryWindow');
}

function query_up(evt) {
	innerBox=xGetElementById('innerBox');
	y=innerBox.slideTop;
	minST = 700;
  maxST = 6000;
	st = parseInt(xLinearScale(-xTop(innerBox), innerBox.slideTop, xHeight(innerBox), minST, maxST));
	xSlideTo(innerBox, innerBox.slideLeft, y, st);
}

function query_down(evt) {
  innerBox=xGetElementById('innerBox');
	y = -xHeight(innerBox);
	minST = 6000;
  maxST = 700;
	st = parseInt(xLinearScale(-xTop(innerBox), innerBox.slideTop, xHeight(innerBox), minST, maxST));
  xSlideTo(innerBox, innerBox.slideLeft, y, st);
}

function query_scroll_stop(evt) {
	xGetElementById('innerBox').stop = true;
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
