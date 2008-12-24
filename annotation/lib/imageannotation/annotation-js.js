//This code has been written as part of Google Summer of Code 2008 program.
//Mentoring Organization : Mozilla
//GSoC Mentor : David Tenser, Project Manager SUMO
//GSoC Student : Shishir Mittal, B.Tech Part III Student from CSE branch of IT-BHU, India

//The code aims at creating a Screenshot Annotation Editor
// for SUMO, http://support.mozilla.com, which is based on an open source CMS Tikiwiki, http://dev.tikiwiki.org/tiki-index.php

//Variables Initialization
var iaHideTimer = null;     // Hide notes after timeout.
var iaActiveNote = null;    // Currently visible note.
var imageNotes = new Array(1); //Array to hold all the data of annotations
var process = new Array(1); //Array which keeps track of the processes viz. Add,Edit,Delete
var orgImageNoteData = new Array(1); //Contains original ImageNotedata
var editNoteNum = new Array(1);//Keeps track of the the annotation being edited
var editmode =  new Array(1);//Keeps traack of the mode of operation for annotations
var newform = new Array(1); //So that mutiple forms are not created

//AddEvent Manager (c) 2005-2006 Angus Turnbull http://www.twinhelix.com
//http://www.twinhelix.com/javascript/addevent/
var aeOL = {};
function addEvent(o, n, f, l)
{
  var a = 'addEventListener', h = 'on'+n, b = '', s = '';
 if (o[a] && !l)
 {
	
	return o[a](n, f, false);
 }
 
 o._c |= 0;
 if (o[h])
 {
  
  b = '_f' + o._c++;
  o[b] = o[h];
 }
 s = '_f' + o._c++;
 o[s] = f;
 
 o[h] = function(e)
 {
  e = e || window.event;
  var r = true;
  if (b) r = o[b](e) != false && r;
  
  r = o[s](e) != false && r;
  
  return r;
 };
 
 aeOL[aeOL.length] = { o: o, h: h };
}

addEvent(window, 'unload', function() {
 for (var i = 0; i < aeOL.length; i++) with (aeOL[i])
 {
  o[h] = null;
  for (var c = 0; o['_f' + c]; c++) o['_f' + c] = null;
 }
});

function cancelEvent(e, c)
{
 e.returnValue = false;
 if (e.preventDefault) e.preventDefault();
 if (c)
 {
  e.cancelBubble = true;
  if (e.stopPropagation) e.stopPropagation();
 }
};
//Common API code ends

//Function to produce swapping of annotation text effect
function iaElementFade(elm, show)
{
	
	var elmNumbers = elm.id.split("-");
	var imageCounter = parseInt(elmNumbers[2]);
	var noteCtr = parseInt(elmNumbers[4]);
	
	
	
	//alert(imageNotes[imageCounter][noteCtr].hovertext.length+","+imageNotes[imageCounter][noteCtr].hovertext);
 	if(show)
 	{
		if(imageNotes[imageCounter][noteCtr].hovertext.length != 0 && imageNotes[imageCounter][noteCtr].hovertext.length!=undefined){
			document.getElementById("image-annotation-"+imageCounter+"-statictext-"+noteCtr).style.visibility = 'hidden';
			document.getElementById("image-annotation-"+imageCounter+"-hovertext-"+noteCtr).style.visibility = 'inherit';
		}
		document.getElementById("image-annotation-"+imageCounter+"-areaborder-"+noteCtr).style.border = "1px solid #FF0";
 	}
 	else
	{
		if(imageNotes[imageCounter][noteCtr].hovertext.length != 0 && imageNotes[imageCounter][noteCtr].hovertext.length!=undefined){
 			document.getElementById("image-annotation-"+imageCounter+"-hovertext-"+noteCtr).style.visibility = 'hidden';
			document.getElementById("image-annotation-"+imageCounter+"-statictext-"+noteCtr).style.visibility = 'inherit';
		}
		document.getElementById("image-annotation-"+imageCounter+"-areaborder-"+noteCtr).style.border = "1px solid #000"; //if edit this remember to edit the css file also .annotation-border
 	}
}

//One of the most important functions for the complete project.
//This Function helps in implementing mouse hover events for different html divisions 

function iaMouseOverOutHandler(evt, isOver){
 // Called on document.onmouseover & onmouseout, manages tip visibility.

 var node = evt.target || evt.srcElement;
 if (node.nodeType != 1) node = node.parentNode;

var reqnode = node;
var flag =0;
	while(reqnode && isOver){
		if(reqnode.id && (reqnode.id.indexOf('image-annotation-')>-1) ) {
			var imageCounter = getImageCounter(reqnode.id);
			document.getElementById('image-annotation-'+imageCounter+'-container').style.visibility = 'inherit';
			flag =1;
			break;
		}
		reqnode = reqnode.parentNode;
	}	
	if(!flag && isOver){
		for(var imageCounter = 0; imageCounter < editmode.length; imageCounter++) {
			var node = document.getElementById('image-annotation-'+imageCounter+'-container');
			var nodeVisibility = editmode[imageCounter]?'inherit':'hidden' ;
			if(node)
				node.style.visibility = nodeVisibility;
		}
	}


while (node && !((node.className||'').indexOf('annotation-container') > -1))  {
  
  if( node && node.id && (node.id.indexOf('-statictext-') > -1))
	return;
  if ( node && ((node.className||'').indexOf('annotation-complete') > -1))  {
   
   var note = node;
    // Clear any hide timeout, and either show the note, or set a timeout for its hide.
   // We record the currently active note for the hide timer to work, and also elevate
   // its parent area above any previously active area (which is lowered).
   clearTimeout(iaHideTimer);

   if (isOver){
    	if (iaActiveNote && (note != iaActiveNote))
		iaElementFade(iaActiveNote, false); 
    	iaElementFade(note, true);
	iaActiveNote = note;
   }else{
	//original
   	iaHideTimer = setTimeout('if (iaActiveNote) { ' +
     'iaElementFade(iaActiveNote, false); iaActiveNote = null; }', 200);
  }
  }

  // Loop up the DOM.
  node = node.parentNode;
 }
	
	
}


addEvent(document, 'mouseover', new Function('e', 'iaMouseOverOutHandler(e, 1)'));
addEvent(document, 'mouseout', new Function('e', 'iaMouseOverOutHandler(e, 0)'));
 if (document.createElement && document.documentElement) {
    addEvent(document, 'click', checkProcess);
}


function checkProcess(evt){
	// Processes clicks on the document, performs the correct action.
	var node = evt.target || evt.srcElement;
 	if (node.nodeType != 1) node = node.parentNode;
 	while (node && !((node.className||'').indexOf('annotation-container') > -1)) {
  		if (node.className == 'annotation-area'){
			
		var nodeidstr = node.id.split("-")
		var imageCounter = parseInt(nodeidstr[2]);
		var noteCtr = parseInt(nodeidstr[4]);
		if(editmode[imageCounter] && process[imageCounter]=='')
			imageNoteEdit(imageCounter,noteCtr);
		}
			
		// Otherwise, loop up the hierarchy.
  		node = node.parentNode;
 	}
}

//Function to create new annotation or editing an existing one
function createImageAnnotation(imageCounter , noteCtr){
	
	//alert("inside createannotation");
	var containerid = 'image-annotation-'+imageCounter + '-container';
	var containerNode = document.getElementById(containerid);

	var annotationnode = document.createElement('div');
	annotationnode.className = "annotation-complete";
	annotationnode.id= 'image-annotation-'+imageCounter + '-annotation-' + noteCtr;
	annotationnode.style.position = "absolute";
	annotationnode.style.top = imageNotes[imageCounter][noteCtr].topleftY+ "px";
	annotationnode.style.left = imageNotes[imageCounter][noteCtr].topleftX + "px";
	
	
	containerNode.appendChild(annotationnode);
		
	var areabordernode = document.createElement('div');
	areabordernode.className = 'annotation-areaborder';
	areabordernode.id = 'image-annotation-'+imageCounter + '-areaborder-' + noteCtr;
	areabordernode.style.width = 2 + parseInt(imageNotes[imageCounter][noteCtr].width) + "px";
	areabordernode.style.height = 2 + parseInt(imageNotes[imageCounter][noteCtr].height) + "px";
	areabordernode.style.visibility = "visible";
		
 	
	document.getElementById(annotationnode.id).appendChild(areabordernode);
	
	if(imageNotes[imageCounter][noteCtr].zonelink.length)
		var areaanchornode = document.createElement('a');
	else
		var areaanchornode = document.createElement('div');
	areaanchornode.className = 'annotation-area';
	areaanchornode.id = 'image-annotation-'+imageCounter + '-areaanchor-' + noteCtr;
	if(imageNotes[imageCounter][noteCtr].zonelink.length) {
		areaanchornode.href = imageNotes[imageCounter][noteCtr].zonelink;
		areaanchornode.target = "_blank";
	}
	areaanchornode.style.width = imageNotes[imageCounter][noteCtr].width + "px";
	areaanchornode.style.height = imageNotes[imageCounter][noteCtr].height+ "px";
	areaanchornode.style.visibility = "hidden";	
 	
 	
	document.getElementById(areabordernode.id).appendChild(areaanchornode);

	var areadivnode = document.createElement('div');
	areadivnode.className = 'annotation-area';
	areadivnode.id = 'image-annotation-'+imageCounter + '-areadiv-' + noteCtr;
	areadivnode.style.width = imageNotes[imageCounter][noteCtr].width + "px";
	areadivnode.style.height = imageNotes[imageCounter][noteCtr].height + "px";
	areadivnode.style.visibility = "inherit";	
 	
 	
	document.getElementById(areabordernode.id).appendChild(areadivnode);
	
	var staticnode = document.createElement('pre');
	staticnode.className = 'annotation-note';
	staticnode.id = 'image-annotation-'+imageCounter + '-statictext-' + noteCtr;
	staticnode.style.top = 4 + parseInt(imageNotes[imageCounter][noteCtr].height) + "px";
	staticnode.style.left = "0px";
	staticnode.style.visibility = "inherit";

	staticnode.appendChild(document.createTextNode(imageNotes[imageCounter][noteCtr].statictext));
	document.getElementById(annotationnode.id).appendChild(staticnode);
	
	var hovernode = document.createElement('pre');
	hovernode.className = 'annotation-note';
	hovernode.id = 'image-annotation-'+imageCounter + '-hovertext-' + noteCtr;
	hovernode.style.top = 4 + parseInt(imageNotes[imageCounter][noteCtr].height) + "px";
	hovernode.style.left = "0px";
	hovernode.style.visibility = "hidden";
	
	/*
	var hovertextstring = imageNotes[imageCounter][noteCtr].hovertext.substr(1,imageNotes[imageCounter][noteCtr].hovertext.length);
	hovernode.appendChild(document.createTextNode(hovertextstring));
	*/
	hovernode.appendChild(document.createTextNode(imageNotes[imageCounter][noteCtr].hovertext));
	document.getElementById(annotationnode.id).appendChild(hovernode);
	
	//alert("new node created");	
}
	
/*Drag & resize Code begins here */

//© 2005-2006 Angus Turnbull, TwinHelix Designs http://www.twinhelix.com,
// http://www.twinhelix.com/javascript/dragresize/
function DragResize(myName, config)
{
 var props = {
  myName: myName,                  // Name of the object.
  enabled: true,                   // Global toggle of drag/resize.
  handles: ['tl', 'tm', 'tr',
   'ml', 'mr', 'bl', 'bm', 'br'], // Array of drag handles: top/mid/bot/right.
  isElement: null,                 // Function ref to test for an element.
  isHandle: null,                  // Function ref to test for move handle.
  element: null,                   // The currently selected element.
  handle: null,                  // Active handle reference of the element.
  minWidth: 10, minHeight: 10,     // Minimum pixel size of elements.
  minLeft: 0, maxLeft: 9999,       // Bounding box area, in pixels.
  minTop: 0, maxTop: 9999,
  zIndex: 4,                       // The highest Z-Index yet allocated.
  mouseX: 0, mouseY: 0,            // Current mouse position, recorded live.
  lastMouseX: 0, lastMouseY: 0,    // Last processed mouse positions.
  mOffX: 0, mOffY: 0,              // A known offset between position & mouse.
  elmX: 0, elmY: 0,                // Element position.
  elmW: 0, elmH: 0,                // Element size.
  allowBlur: true,                 // Whether to allow automatic blur onclick.
  ondragfocus: null,               // Event handler functions.
  ondragstart: null,
  ondragmove: null,
  ondragend: null,
  ondragblur: null
 };

 for (var p in props)
  this[p] = (typeof config[p] == 'undefined') ? props[p] : config[p];
};


DragResize.prototype.apply = function(node)
{
 // Adds object event handlers to the specified DOM node.

 var obj = this;
 addEvent(node, 'mousedown', function(e) { obj.mouseDown(e) } );
 addEvent(node, 'mousemove', function(e) { obj.mouseMove(e) } );
 addEvent(node, 'mouseup', function(e) { obj.mouseUp(e) } );
};


DragResize.prototype.select = function(newElement) { with (this)
{
 // Selects an element for dragging.

 if (!document.getElementById || !enabled) return;

 // Activate and record our new dragging element.
 if (newElement && (newElement != element) && enabled)
 {
  element = newElement;
  // Elevate it and give it resize handles.
  element.style.zIndex = ++zIndex;
  if (this.resizeHandleSet) this.resizeHandleSet(element, true);
  // Record element attributes for mouseMove().
  elmX = parseInt(element.style.left);
  elmY = parseInt(element.style.top);
  elmW = element.offsetWidth;
  elmH = element.offsetHeight;
  if (ondragfocus) this.ondragfocus();
 }
}};


DragResize.prototype.deselect = function(delHandles) { with (this)
{
 // Immediately stops dragging an element. If 'delHandles' is true, this
 // remove the handles from the element and clears the element flag,
 // completely resetting the .

 if (!document.getElementById || !enabled) return;

 if (delHandles)
 {
  if (ondragblur) this.ondragblur();
  if (this.resizeHandleSet) this.resizeHandleSet(element, false);
  element = null;
 }

 handle = null;
 mOffX = 0;
 mOffY = 0;
}};


DragResize.prototype.mouseDown = function(e) { with (this)
{
 // Suitable elements are selected for drag/resize on mousedown.
 // We also initialise the resize boxes, and drag parameters like mouse position etc.
 if (!document.getElementById || !enabled) return true;

 var elm = e.target || e.srcElement,
  newElement = null,
  newHandle = null,
  hRE = new RegExp(myName + '-([trmbl]{2})', '');

 while (elm)
 {
  // Loop up the DOM looking for matching elements. Remember one if found.
  if (elm.className)
  {
   if (!newHandle && (hRE.test(elm.className) || isHandle(elm))) newHandle = elm;
   if (isElement(elm)) { newElement = elm; break }
  }
  elm = elm.parentNode;
 }

 // If this isn't on the last dragged element, call deselect(),
 // which will hide its handles and clear element.
 if (element && (element != newElement) && allowBlur) deselect(true);

 // If we have a new matching element, call select().
 if (newElement && (!element || (newElement == element)))
 {
  // Stop mouse selections if we're dragging a handle.
  if (newHandle) cancelEvent(e);
  select(newElement, newHandle);
  handle = newHandle;
  if (handle && ondragstart) this.ondragstart(hRE.test(handle.className));
 }
}};


DragResize.prototype.mouseMove = function(e) { with (this)
{
 // This continually offsets the dragged element by the difference between the
 // last recorded mouse position (mouseX/Y) and the current mouse position.
 if (!document.getElementById || !enabled) return true;

 // We always record the current mouse position.
 mouseX = e.pageX || e.clientX + document.documentElement.scrollLeft;
 mouseY = e.pageY || e.clientY + document.documentElement.scrollTop;
 // Record the relative mouse movement, in case we're dragging.
 // Add any previously stored & ignored offset to the calculations.
 var diffX = mouseX - lastMouseX + mOffX;
 var diffY = mouseY - lastMouseY + mOffY;
 mOffX = mOffY = 0;
 // Update last processed mouse positions.
 lastMouseX = mouseX;
 lastMouseY = mouseY;

 // That's all we do if we're not dragging anything.
 if (!handle) return true;

 // If included in the script, run the resize handle drag routine.
 // Let it create an object representing the drag offsets.
 var isResize = false;
 if (this.resizeHandleDrag && this.resizeHandleDrag(diffX, diffY))
 {
  isResize = true;
 }
 else
 {
  // If the resize drag handler isn't set or returns fase (to indicate the drag was
  // not on a resize handle), we must be dragging the whole element, so move that.
  // Bounds check left-right...
  var dX = diffX, dY = diffY;
  if (elmX + dX < minLeft) mOffX = (dX - (diffX = minLeft - elmX));
  else if (elmX + elmW + dX > maxLeft) mOffX = (dX - (diffX = maxLeft - elmX - elmW));
  // ...and up-down.
  if (elmY + dY < minTop) mOffY = (dY - (diffY = minTop - elmY));
  else if (elmY + elmH + dY > maxTop) mOffY = (dY - (diffY = maxTop - elmY - elmH));
  elmX += diffX;
  elmY += diffY;
 }

 // Assign new info back to the element, with minimum dimensions.
 with (element.style)
 {
  left =   elmX + 'px';
  width =  elmW + 'px';
  top =    elmY + 'px';
  height = elmH + 'px';
 }
 //alert(element.id);
 shadowEffect(1,getImageCounter(element.id));

 // Evil, dirty, hackish Opera select-as-you-drag fix.
 if (window.opera && document.documentElement)
 {
  var oDF = document.getElementById('op-drag-fix');
  if (!oDF)
  {
   var oDF = document.createElement('input');
   oDF.id = 'op-drag-fix';
   oDF.style.display = 'none';
   document.body.appendChild(oDF);
  }
  oDF.focus();
 }

 if (ondragmove) this.ondragmove(isResize);

 // Stop a normal drag event.
 cancelEvent(e);
}};


DragResize.prototype.mouseUp = function(e) { with (this)
{
 // On mouseup, stop dragging, but don't reset handler visibility.
 if (!document.getElementById || !enabled) return;

 var hRE = new RegExp(myName + '-([trmbl]{2})', '');
 if (handle && ondragend) this.ondragend(hRE.test(handle.className));
 deselect(false);
}};



/* Resize Code -- can be deleted if you're not using it. */

DragResize.prototype.resizeHandleSet = function(elm, show) { with (this)
{
 // Either creates, shows or hides the resize handles within an element.

 // If we're showing them, and no handles have been created, create 4 new ones.
 if (!elm._handle_tr)
 {
  for (var h = 0; h < handles.length; h++)
  {
   // Create 4 news divs, assign each a generic + specific class.
   var hDiv = document.createElement('div');
   hDiv.className = 'annotation-'+myName + ' ' +  myName + '-' + handles[h];
	hDiv.id = 'image-annotation-'+getImageCounter(elm.id)+'-'+hDiv.className;
	//changes by me
	
	//changes end here
   elm['_handle_' + handles[h]] = elm.appendChild(hDiv);
  }
 }

 // We now have handles. Find them all and show/hide.
 for (var h = 0; h < handles.length; h++)
 {
  elm['_handle_' + handles[h]].style.visibility = show ? 'inherit' : 'hidden';
 }
	//changes by me
	elm['_handle_tl'].style.top = "-8px";
	elm['_handle_tl'].style.left = "-8px";
	elm['_handle_tl'].style.cursor = "nw-resize";

	elm['_handle_tm'].style.top = "-8px";
	elm['_handle_tm'].style.left = "50%";
	elm['_handle_tm'].style.marginLeft = "-4px";
	elm['_handle_tm'].style.cursor = "n-resize";

	elm['_handle_tr'].style.top = "-8px";
	elm['_handle_tr'].style.right = "-8px";
	elm['_handle_tr'].style.cursor = "ne-resize";
	
	elm['_handle_ml'].style.top = "50%";
	elm['_handle_ml'].style.marginTop = "-4px";
	elm['_handle_ml'].style.left = "-8px";
	elm['_handle_ml'].style.cursor = "w-resize";

	elm['_handle_mr'].style.top = "50%";
	elm['_handle_mr'].style.marginTop = "-4px";
	elm['_handle_mr'].style.right = "-8px";
	elm['_handle_mr'].style.cursor = "e-resize";

	elm['_handle_bl'].style.bottom = "-8px";
	elm['_handle_bl'].style.left = "-8px";
	elm['_handle_bl'].style.cursor = "sw-resize";

	elm['_handle_bm'].style.bottom = "-8px";
	elm['_handle_bm'].style.left = "50%";
	elm['_handle_bm'].style.marginLeft = "-4px";
	elm['_handle_bm'].style.cursor = "s-resize";
	
	elm['_handle_br'].style.bottom = "-8px";
	elm['_handle_br'].style.right = "-8px";
	elm['_handle_br'].style.cursor = "se-resize";
	//changes end here
}};


DragResize.prototype.resizeHandleDrag = function(diffX, diffY) { with (this)
{
 // Passed the mouse movement amounts. This function checks to see whether the
 // drag is from a resize handle created above; if so, it changes the stored
 // elm* dimensions and mOffX/Y.

 var hClass = handle && handle.className &&
  handle.className.match(new RegExp(myName + '-([tmblr]{2})')) ? RegExp.$1 : '';

 // If the hClass is one of the resize handles, resize one or two dimensions.
 // Bounds checking is the hard bit -- basically for each edge, check that the
 // element doesn't go under minimum size, and doesn't go beyond its boundary.
 var dY = diffY, dX = diffX, processed = false;
 if (hClass.indexOf('t') >= 0)
 {
  rs = 1;
  if (elmH - dY < minHeight) mOffY = (dY - (diffY = elmH - minHeight));
  else if (elmY + dY < minTop) mOffY = (dY - (diffY = minTop - elmY));
  elmY += diffY;
  elmH -= diffY;
  processed = true;
 }
 if (hClass.indexOf('b') >= 0)
 {
  rs = 1;
  if (elmH + dY < minHeight) mOffY = (dY - (diffY = minHeight - elmH));
  else if (elmY + elmH + dY > maxTop) mOffY = (dY - (diffY = maxTop - elmY - elmH));
  elmH += diffY;
  processed = true;
 }
 if (hClass.indexOf('l') >= 0)
 {
  rs = 1;
  if (elmW - dX < minWidth) mOffX = (dX - (diffX = elmW - minWidth));
  else if (elmX + dX < minLeft) mOffX = (dX - (diffX = minLeft - elmX));
  elmX += diffX;
  elmW -= diffX;
  processed = true;
 }
 if (hClass.indexOf('r') >= 0)
 {
  rs = 1;
  if (elmW + dX < minWidth) mOffX = (dX - (diffX = minWidth - elmW));
  else if (elmX + elmW + dX > maxLeft) mOffX = (dX - (diffX = maxLeft - elmX - elmW));
  elmW += diffX;
  processed = true;
 }

 return processed;
}};


/*drag & resize code ends here */

/*Edit bar Functions begin here */

//function to check the process which is On when a note is clicked

function imageNoteChangeMode(imageCounter){
	var changeModeText;
	var nodeVisibility;
	if((document.getElementById("image-annotation-"+imageCounter+"-change-mode").childNodes[0].nodeValue).indexOf('Edit') > -1){
		
		changeModeText = "Switch to View Mode";
		editmode[imageCounter] = 1;
		nodeVisibility = 'inherit';
		//changeAnnotationCursor(imageCounter, 'pointer');
	}
	else{
		changeModeText = "Switch to Edit Mode";
		editmode[imageCounter] = 0;
		rectifyChanges(imageCounter);
		//changeAnnotationCursor(imageCounter, 'crosshair');
		nodeVisibility = 'hidden';
	}
	document.getElementById("image-annotation-"+imageCounter+"-change-mode").childNodes[0].nodeValue = changeModeText;
	document.getElementById("image-annotation-"+imageCounter+"-AddOrDelete-note").style.visibility = nodeVisibility;	
	document.getElementById("image-annotation-"+imageCounter+"-save-changes").style.visibility = nodeVisibility;
	
	for(var ctr=0; ctr<imageNotes[imageCounter].length; ctr++){
		//alert("ctr kahan se aaya?");
		document.getElementById("image-annotation-"+imageCounter+"-areaanchor-"+ctr).style.visibility = (editmode[imageCounter]?"hidden":"visible");
		if(!imageNotes[imageCounter][ctr].zonelink.length)
			document.getElementById("image-annotation-"+imageCounter+"-areaanchor-"+ctr).style.cursor = (editmode[imageCounter]?"pointer":"crosshair");
		document.getElementById("image-annotation-"+imageCounter+"-areadiv-"+ctr).style.visibility = (editmode[imageCounter]?"inherit":"hidden");
		
	}
}

function performAction(imageCounter){
	if(document.getElementById("image-annotation-"+imageCounter+"-AddOrDelete-note").childNodes[0].nodeValue.indexOf('Add') >-1){
		
		imageNoteAdd(imageCounter);
		process[imageCounter] = 'add';
		changeAnnotationCursor(imageCounter, 'crosshair');
	}
	else {
		process[imageCounter] = 'delete';
		imageNoteDelete(imageCounter,editNoteNum[imageCounter]);		
	}
}

//This function Creates new form for entering data for an annotation
function createNewAreaForm(imageCounter){
	if(newform[imageCounter] == 0){
	var newAFnode = document.createElement('div');
	newAFnode.id = 'image-annotation-'+imageCounter+'-newArea-form'; 
	newAFnode.className = 'annotation-areaform';
	
	document.getElementById("image-annotation-"+imageCounter+"-IEfix").appendChild(newAFnode);
	
	newAFnode = document.getElementById(newAFnode.id);
	newAFnode.innerHTML = "";
	newAFnode.innerHTML += "<form id ='$iaid-newArea-form' name='image-annotation-"+imageCounter+"-newArea-form' method='post' action=''><label id ='image-annotation-"+imageCounter+"-newArea-form-ok' onclick='endAddNote("+imageCounter+");'>Ok</label>&nbsp; <label id ='image-annotation-"+imageCounter+"-newArea-form-cancel'   onclick='rectifyChanges("+imageCounter+");'>Cancel</label>&nbsp;<br/> Zonelink : <input id ='image-annotation-"+imageCounter+"-newArea-form-zonelink' type='text' name = 'zonelink' style='width:90%'/><br>Static Text : <input id ='image-annotation-"+imageCounter+"-newArea-form-statictext' type='text' name = 'statictext' style='width:90%'/><br>Hovering Text : <br/><textarea id ='image-annotation-"+imageCounter+"-newArea-form-hovertext' name = 'hovertext' style='width:90%;'></textarea></form>";
	}
	newform[imageCounter] =1;
}

//Function to remove the created form for entering annotation data
function removeNewAreaForm(imageCounter) {
	if(newform[imageCounter] == 1){
	document.forms["image-annotation-"+imageCounter+"-newArea-form"].elements["statictext"].value = "";
	document.forms["image-annotation-"+imageCounter+"-newArea-form"].elements["hovertext"].value = "";
	document.forms["image-annotation-"+imageCounter+"-newArea-form"].elements["zonelink"].value = "";
	var AFnode = document.getElementById('image-annotation-'+imageCounter+'-newArea-form');
	AFnode.parentNode.removeChild(AFnode); 
	}
	newform[imageCounter] = 0;
}
//function to provide shadow effect while adding new anotation or editing annotation
function shadowEffect(flag,imageCounter){
	var visible;
	if(flag==0)
		visible = 'hidden';
	else
		visible = 'inherit';
	var topID = document.getElementById('image-annotation-'+imageCounter+'-topBg');
	var leftID = document.getElementById('image-annotation-'+imageCounter+'-leftBg');
	var bottomID = document.getElementById('image-annotation-'+imageCounter+'-bottomBg');
	var rightID = document.getElementById('image-annotation-'+imageCounter+'-rightBg');
	topID.style.visibility = visible;
	leftID.style.visibility = visible;
	bottomID.style.visibility = visible;
	rightID.style.visibility = visible;
	if(flag==1){
		var newAreaId = document.getElementById('image-annotation-'+imageCounter+'-newArea');;
		var imageId = document.getElementById('image-annotation-'+imageCounter+'-image');
		topID.style.height = parseInt(newAreaId.style.top) + "px";
		//3 to account for border width
		leftID.style.top = newAreaId.style.top;
		leftID.style.height = (parseInt(newAreaId.style.height)+3) + "px";
		leftID.style.width = newAreaId.style.left;
		
		bottomID.style.top = (parseInt(newAreaId.style.height) + 3 + parseInt(newAreaId.style.top)) + "px";
		bottomID.style.height = (parseInt(imageId.height) - parseInt(newAreaId.style.height) - parseInt(newAreaId.style.top)-3) + "px";
		
		rightID.style.top = newAreaId.style.top;
		rightID.style.height = (parseInt(newAreaId.style.height)+3) + "px";
		rightID.style.width = (parseInt(imageId.width) - parseInt(newAreaId.style.left) - parseInt(newAreaId.style.width)-3) + "px";
		rightID.style.left = (parseInt(newAreaId.style.left) +3 + parseInt(newAreaId.style.width)) + "px";
	}
}
//function to begin adding new note
function imageNoteAdd(imageCounter){

	var newAreaId = 'image-annotation-'+imageCounter+'-newArea';
	//var newAreaFormId = 'image-annotation-'+imageCounter+'-newArea-form';
	document.getElementById(newAreaId).style.visibility = 'inherit';
	shadowEffect(1,imageCounter);
	//document.getElementById(newAreaFormId).style.visibility = 'inherit';
	createNewAreaForm(imageCounter);
	
}

//function to end the operation of Addition/Edition of a note
function endAddNote(imageCounter){
	//alert("inside endADDNOTE");
	var notenum;
	if(process[imageCounter]=='edit')
		notenum = editNoteNum[imageCounter];
	else
		notenum = imageNotes[imageCounter].length;
	
	imageNotes[imageCounter][notenum] = {"topleftX":"","topleftY":"","width":"","height":"","zonelink":"","statictext":"","hovertext":"","parametercount":"0"};
	var newAreaElm = document.getElementById('image-annotation-'+imageCounter+'-newArea');

	imageNotes[imageCounter][notenum].topleftX = newAreaElm.style.left.substr(0,newAreaElm.style.left.indexOf("px"));
	//alert(imageNotes[imageCounter][notenum].leftX);

	imageNotes[imageCounter][notenum].topleftY = newAreaElm.style.top.substr(0,newAreaElm.style.top.indexOf("px"));
	
	imageNotes[imageCounter][notenum].width = newAreaElm.style.width.substr(0,newAreaElm.style.width.indexOf("px"));  

	imageNotes[imageCounter][notenum].height = newAreaElm.style.height.substr(0,newAreaElm.style.height.indexOf("px")); 

	imageNotes[imageCounter][notenum].zonelink = document.forms["image-annotation-"+imageCounter+"-newArea-form"].elements["zonelink"].value;
	imageNotes[imageCounter][notenum].zonelink = stringtrimmer(imageNotes[imageCounter][notenum].zonelink);
	if(imageNotes[imageCounter][notenum].zonelink.length && !(/^((http)|(HTTP))((s)|(S))?:\/\//).test(imageNotes[imageCounter][notenum].zonelink)) {
		imageNotes[imageCounter][notenum].zonelink = "http://"+imageNotes[imageCounter][notenum].zonelink;
	}
	imageNotes[imageCounter][notenum].statictext = document.forms["image-annotation-"+imageCounter+"-newArea-form"].elements["statictext"].value;

	imageNotes[imageCounter][notenum].hovertext = document.forms["image-annotation-"+imageCounter+"-newArea-form"].elements["hovertext"].value;
	if(imageNotes[imageCounter][notenum].hovertext.length)
		imageNotes[imageCounter][notenum].hovertext += "\r";
	/*
	if(document.forms["image-annotation-"+imageCounter+"-newArea-form"].elements["hovertext"].value){
		//alert("hi");
		imageNotes[imageCounter][notenum].hovertext="\n" + imageNotes[imageCounter][notenum].hovertext;
	}
	*/
	if(process[imageCounter]=='edit'){
		var noteNode = document.getElementById('image-annotation-'+imageCounter + '-annotation-' + notenum);
		noteNode.parentNode.removeChild(noteNode);
	}
	createImageAnnotation(imageCounter,notenum );
	rectifyChanges(imageCounter);

	document.forms["image-annotation-"+imageCounter+"-submit-form"].elements["imageNoteData"].value = imageNoteData(imageCounter);
};

//Function to initialize the process of editing an annotation
function imageNoteEdit(imageCounter,noteCtr){
	/*initialisation*/
	editNoteNum[imageCounter] = noteCtr;
	process[imageCounter] = 'edit';
	//Setting the visibility of selected annotation to hidden
	//alert("imageCtr = "+imageCounter+", noteCtr = " + noteCtr);
	document.getElementById('image-annotation-'+imageCounter + '-areaborder-' + noteCtr).style.visibility = "hidden";
	
	var noteNode = document.getElementById('image-annotation-'+imageCounter + '-annotation-' + noteCtr);
	noteNode.style.visibility = "hidden";
	
	//setting the cusor type of annotations
	changeAnnotationCursor(imageCounter, 'crosshair');
	//changing Add to delete
	document.getElementById("image-annotation-"+imageCounter+"-AddOrDelete-note").childNodes[0].nodeValue = "Delete Note";
	//Setting the new Area element to match the selected annotations styling
	var newAreaNode= document.getElementById('image-annotation-'+imageCounter + '-newArea');
	newAreaNode.style.visibility = 'inherit';
	/*
	newAreaNode.style.top = noteNode.style.top;
	newAreaNode.style.left = noteNode.style.left;
	newAreaNode.style.width = noteNode.style.width;
	newAreaNode.style.height = noteNode.style.height;
	*/
	newAreaNode.style.top = imageNotes[imageCounter][noteCtr].topleftY + "px";
	newAreaNode.style.left = imageNotes[imageCounter][noteCtr].topleftX + "px";
	newAreaNode.style.width = imageNotes[imageCounter][noteCtr].width + "px";
	newAreaNode.style.height = imageNotes[imageCounter][noteCtr].height + "px";
	shadowEffect(1,imageCounter);
	//Setting NewArea form values to match with the selected Annotation text values
	//document.getElementById('image-annotation-'+imageCounter + '-newArea-form').style.visibility = 'inherit';
	createNewAreaForm(imageCounter);
	document.forms["image-annotation-"+imageCounter+"-newArea-form"].elements["statictext"].value = imageNotes[imageCounter][noteCtr].statictext;
	/*
	var charToBeSubtracted = (noteCtr == (imageNotes[imageCounter].length-1) ? 1 : 2 );
	*/
	//To delete '\r' from hovertext
	if(imageNotes[imageCounter][noteCtr].hovertext.length !=0 && imageNotes[imageCounter][noteCtr].hovertext.length !=undefined){
		document.forms["image-annotation-"+imageCounter+"-newArea-form"].elements["hovertext"].value = imageNotes[imageCounter][noteCtr].hovertext.substr(0,imageNotes[imageCounter][noteCtr].hovertext.length-1);
	}
	else
		document.forms["image-annotation-"+imageCounter+"-newArea-form"].elements["hovertext"].value = "";	
	//document.forms["image-annotation-"+imageCounter+"-newArea-form"].elements["hovertext"].value = imageNotes[imageCounter][noteCtr].hovertext;
	document.forms["image-annotation-"+imageCounter+"-newArea-form"].elements["zonelink"].value = imageNotes[imageCounter][noteCtr].zonelink;
	
};

function imageNoteDelete(imageCounter,noteCtr){
	//if(confirm("Are you sure you want to delete the note?")){
		var noteNode = document.getElementById('image-annotation-'+imageCounter + '-annotation-' + noteCtr);
		noteNode.parentNode.removeChild(noteNode);
		process[imageCounter]='';
		imageNotes[imageCounter][noteCtr] = {"topleftX":"","topleftY":"","width":"","height":"","zonelink":"","statictext":"","hovertext":"","parametercount":"0"};
		document.forms["image-annotation-"+imageCounter+"-submit-form"].elements["imageNoteData"].value = imageNoteData(imageCounter);
	/*}
	else
		process[imageCounter] = 'edit';*/
	rectifyChanges(imageCounter);
}

//function to hide all notes.
/*
function imageNoteHide(imageCounter){
	var notesvisibility;
	var editBarText;
	if((document.getElementById("image-annotation-"+imageCounter+"-hide-note").childNodes[0].nodeValue).indexOf('Hide') > -1){
		notesvisibility = "hidden";
		editBarText = "Show All Notes";
	}
	else{
		notesvisibility = "inherit";
		editBarText = "Hide All Notes";
	}
	
	for(var noteCtr in imageNotes[imageCounter]){
		if(imageNotes[imageCounter][noteCtr].leftY == "")
			continue;
		document.getElementById('image-annotation-'+imageCounter + '-area-' + noteCtr).style.visibility = notesvisibility;
	}
	document.getElementById("image-annotation-"+imageCounter+"-hide-note").childNodes[0].nodeValue = editBarText;
}
*/

//restoring initial conditions of newAreaElm and newAreaForm
function rectifyChanges(imageCounter){
	if(process[imageCounter] == 'edit'){
		document.getElementById('image-annotation-'+imageCounter + '-annotation-' + editNoteNum[imageCounter]).style.visibility = 'inherit';
		document.getElementById('image-annotation-'+imageCounter + '-areaborder-' + editNoteNum[imageCounter]).style.visibility = 'visible';
		editNoteNum[imageCounter] = -1;
	}
	shadowEffect(0,imageCounter);
	var newAreaElm = document.getElementById('image-annotation-'+imageCounter+'-newArea');
	newAreaElm.style.visibility = 'hidden';
	newAreaElm.style.left= "5px";
	newAreaElm.style.top= "5px";
	newAreaElm.style.width= "50px";
	newAreaElm.style.height= "50px";
	/*var newAreaFormId = 'image-annotation-'+imageCounter+'-newArea-form';
	document.getElementById(newAreaFormId).style.visibility = 'hidden';*/
	
	
	removeNewAreaForm(imageCounter);
	process[imageCounter] = '';
	changeAnnotationCursor(imageCounter, 'pointer');
	document.getElementById("image-annotation-"+imageCounter+"-AddOrDelete-note").childNodes[0].nodeValue = "Add Note";
}

//function to calculate "data" of IMAGENOTE plugin to be send to the server.
function imageNoteData(imageCounter){
	var indata=' ';
	var parameterCount = 0;
	//var hovertexty = '';
	
	//alert(imageNotes[imageCounter].length);
	for(var ctr=0;ctr<imageNotes[imageCounter].length;ctr++){
		//alert("hello");
		if(imageNotes[imageCounter][ctr].topleftY == "")
			continue;
		parameterCount = imageNotes[imageCounter][ctr].parametercount;
		//alert(parameterCount);
		indata += "\n(" + imageNotes[imageCounter][ctr].topleftX + "," + imageNotes[imageCounter][ctr].topleftY + "),(" + imageNotes[imageCounter][ctr].width + "," + imageNotes[imageCounter][ctr].height + ")" + "[" + imageNotes[imageCounter][ctr].zonelink + "]" + imageNotes[imageCounter][ctr].statictext;
		for(var k=0;k<parameterCount ; k++){
			indata += imageNotes[imageCounter][ctr][k];
		 }
		if(imageNotes[imageCounter][ctr].hovertext.length)
			indata += "\n"+imageNotes[imageCounter][ctr].hovertext;
		/* hovertexty += "shir--"+imageNotes[imageCounter][ctr].hovertext + "--shi";*/
		
	}
	//alert(hovertexty);
	//alert(data);
	return indata; 
}

function checkImageNoteData(imageCounter){
	var newImageNoteData = imageNoteData(imageCounter);
	if( newImageNoteData == orgImageNoteData[imageCounter]){
		alert("No changes have been made in the Image Annotations.");
		return false;
	}
	/*
	else{
		if(confirm("Original IMAGENOTE Plugin DATA : \n" + orgImageNoteData[imageCounter] + " Updated IMAGENOTE Plugin DATA : \n" + newImageNoteData + "Are you sure you want to perform the changes?"))
			return true;
	//return false;
	}
	return false;
	*/
	return true;
}
//function to trim strings
function stringtrimmer(str){
	return str.replace(/^\s\s*/, '').replace(/\s\s*$/, '');
}

function getImageCounter(elmId){

	var idArray = elmId.split("-");
	return parseInt(idArray[2]);
}

function changeAnnotationCursor(imageCounter, cursortype){
	
	for(var noteCtr=0; noteCtr<imageNotes[imageCounter].length;noteCtr++){
		if(imageNotes[imageCounter][noteCtr].topleftY == "")
			continue;
		document.getElementById('image-annotation-'+imageCounter + '-areadiv-' + noteCtr).style.cursor = cursortype;
	}
}

 