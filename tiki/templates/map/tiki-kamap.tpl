<script type="text/javascript" src="lib/kamap/getcjs.php?name=DHTMLapi.js,xhr.js,kaMap.js,kaKeymap.js,kaLegend.js,kaTool.js,kaQuery.js,kaMouseTracker.js,startUp.js,kaRubberZoom.js,scalebar/scalebar.js,search/kaSearch.js,tooltip/kaToolTip.js"></script>


<!--
<script type="text/javascript" src="lib/kamap/DHTMLapi.js"></script>
<script type="text/javascript" src="lib/kamap/xhr.js"></script>
<script type="text/javascript" src="lib/kamap/kaMap.js"></script>
<script type="text/javascript" src="lib/kamap/kaKeymap.js"></script>
<script type="text/javascript" src="lib/kamap/kaLegend.js"></script>
<script type="text/javascript" src="lib/kamap/kaTool.js"></script>
<script type="text/javascript" src="lib/kamap/kaQuery.js"></script>
<script type="text/javascript" src="lib/kamap/kaMouseTracker.js"></script>
<script type="text/javascript" src="lib/kamap/startUp.js"></script>
<script type="text/javascript" src="lib/kamap/kaRubberZoom.js"></script>
<script type="text/javascript" src="lib/kamap/scalebar/scalebar.js"></script>
<script type="text/javascript" src="lib/kamap/search/kaSearch.js"></script>
<script type="text/javascript" src="lib/kamap/tooltip/kaToolTip.js"></script>
-->
<style type="text/css">
#mapDiv {literal}{{/literal}
		-moz-box-sizing:border-box;
    position: relative;
    overflow: hidden;
    cursor: move;
    background-color:  white;
    border: 1px solid black;
    height: 100%;
{literal}}{/literal}
</style>

<link rel="stylesheet" type="text/css" href="lib/kamap/scalebar/scalebar-fat.css">
<link href="lib/kamap/screen.css" rel="stylesheet" type="text/css" media="all">
<link href="lib/kamap/tools.css" rel="stylesheet" type="text/css" media="all">
<link href="lib/kamap/tooltip/tooltip.css" rel="stylesheet" type="text/css" media="all">

<script type="text/javascript">
var myKaMap;

window.onload=function() {literal}{{/literal}
	myOnLoad('{$tikiroot}lib/kamap/');
{literal}}{/literal}
</script>

<div id="viewport">
<form name="toolbarform" id="toolbarform">
<div id="toolbar">
    <div id="toolbarBackground" class="transparentBackground"></div>
    <div id="mapTitle" class="kmTitle">{$pagelink}</div>
    <div style="text-align:center">    
    	<select name="maps" onchange="mySetMap(this.options[this.selectedIndex].value)">
      	  <option value=''>Choose a Map!</option>
	        <option value=''>-------------</option>
    	</select>    
    	<img id="toolQuery" onclick="switchMode(this.id)" title="Click and drag or double click to query the Map" alt="Click and drag or double click to query the Map" src="img/icons/trdot.gif" >
    	<img id="toolPan" onclick="switchMode(this.id)" title="Click and drag to Navigate the Map" alt="Click and drag to Navigate the Map" src="img/icons/trdot.gif">
    	<img id="toolZoomOut" onclick="myZoomOut();" title="zoom Out" alt="zoom Out" src="img/icons/trdot.gif">
    	<span id="zoomer"></span>
    	<img id="toolZoomIn" onclick="myZoomIn()" title="zoom in" alt="zoom in" src="img/icons/trdot.gif" >
    	<img id="toolZoomRubber" onclick="switchMode(this.id)" title="rubber zoom" alt="rubber zoom" src="img/icons/trdot.gif" >
    	<img id="toolZoomFull" onclick="getFullExtent()" title="zoom to full extents" alt="zoom to full extents" src="img/icons/trdot.gif" >
    	<a id='linkToView' href='#'>link to this view</a>
    </div>
    <div id="geoPosition"></div>
</div>
<form/>

<img id="toolbarToggler" onclick="toggleToolbar(this);" alt="{tr}toggle toolbar{/tr}" src="img/icons/trdot.gif">
<img id="keymapToggler" onclick="toggleKeymap(this);" alt="{tr}toggle keymap{/tr}" src="img/icons/trdot.gif">
<div id="keymap"></div>

<img id="refToggler" onclick="toggleReference(this);" alt="{tr}toggle reference{/tr}" src="img/icons/trdot.gif">
<div id="reference">
    <div id="scaleReference">
        <div id="scaleBackground" class="transparentBackground"></div>
        <div id="scalebar"></div>
        <div id="scale">current scale</div>
    </div>
    <div id="legend"></div>
</div>

<img id="searchToggler" onclick="toggleSearch(this);" alt="{tr}toggle search{/tr}" src="img/icons/trdot.gif">
<div id="searchOut">
</div>

<div id="info">
<div style="text-align:right">
<a href="#" onclick="infoClose(this);">{tr}close{/tr}</a>
</div>
<div id="infoText">
</div>
</div>

</div>


