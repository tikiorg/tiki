{if $feature_gmap eq 'y'}
<script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key={$gmap_key}"></script>
<table>
{if $editable and $tiki_p_edit eq 'y'}
<tr><td>
{if !$pointx || !$pointy}<small>{tr}map and geotagging for this page not yet saved{/tr}</small> <a class="link" href="javascript:flip('pageGeoTags');"><img src="img/icons/plus.gif" border="0" height="9" width="9" title="{tr}edit geotag for this page{/tr}" alt="edit geotag" /></a>{else}<small>{tr}map and geotagging for this page{/tr}</small> <a class="link" href="javascript:flip('pageGeoTags');"><img src="img/icons/plus.gif" border="0" height="9" width="9" title="{tr}edit geotag for this page{/tr}" alt="edit geotag" /></a>{/if}
<form id="pageGeoTags" action="gmap_wikiplugin_save.php" method="post" style="display:none;">
{if $page_ref_id}
<input type="hidden" name="page_ref_id" value="{$page_ref_id|escape}" />
{else}
<input type="hidden" name="page" value="{$page|escape}" />
{/if}
<textarea cols="50" rows="5" name="gmapinfowindow">{$gmapinfowindow|escape}</textarea>
<input type="text" readonly name="pointy" value="{$pointy}" id="pointy" />
<input type="text" readonly name="pointx" value="{$pointx}" id="pointx" />
<input type="hidden" name="pointz" value="{$pointz}" id="pointz" />
{if $pointx && $pointy}<input type="button" value="{tr}Go to saved point{/tr}" onClick="gotoSavedPoint()" />{/if}
<input type="submit" name="save_gmap" value="{tr}Save current point{/tr}" />
<input type="submit" name="remove_geocode" value="{tr}Delete{/tr}" /><br />
</form>
</td></tr>
<tr><td>
{if !$pointx || !$pointy}<small>{tr}map view not yet saved{/tr}</small>{else}<small>{tr}map of pages in this structure{/tr}</small>{/if}
<form action="gmap_wikiplugin_save.php" method="post">
<input type="hidden" name="page_ref_id" value="{$page_ref_id|escape}" />
<input type="hidden" name="groutey" value="{$groutey}" id="groutey" />
<input type="hidden" name="groutex" value="{$groutex}" id="groutex" />
<input type="hidden" name="groutez" value="{$groutez}" id="groutez" />
<input type="submit" name="save_groute" value="{tr}Save current view{/tr}" />
</form>
</td></tr>
{/if}
<tr><td>
<form name="cgoogleaddress" onSubmit="cshowAddress(document.cgoogleaddress.caddress.value); return false">
<input type="text" size="60" name="caddress" value="" />
<input type="button" value="{tr}Go!{/tr}" onClick="cshowAddress(document.cgoogleaddress.caddress.value); return false" />
</form>
</td></tr></table>
<form><input type="button" value="{tr}Go to selected object{/tr}" style="display:none;" id="cnavButton" onClick="cnavOut(); return false" /></form>
<div id="cmap" style="width: {$width}px; height: {$height}px;border: 1px solid #000;"></div>
<script type="text/javascript">
//<![CDATA[
function cload() {literal}{{/literal}  	
	cmap = new GMap2(document.getElementById("cmap"));  
  cgeocoder = new GClientGeocoder();  
  {if $controller == 'large'}cmap.addControl(new GLargeMapControl());
	{elseif $controller == 'medium'}cmap.addControl(new GSmallMapControl());
	{elseif $controller == 'small'}cmap.addControl(new GSmallZoomControl());
  {/if}
  {if $changetype == 'y'}cmap.addControl(new GMapTypeControl());{/if}
  {if $scale == 'y'}cmap.addControl(new GScaleControl());{/if}  
  
	{if $pointx and $pointy}
	{if $mode == 'normal'}cmap.setCenter(new GLatLng({$groutey}, {$groutex}), {$groutez});
  {elseif $mode == 'satellite'}cmap.setCenter(new GLatLng({$groutey}, {$groutex}), {$groutez}, G_SATELLITE_MAP);
	{elseif $mode == 'hybrid'}cmap.setCenter(new GLatLng({$groutey}, {$groutex}), {$groutez}, G_HYBRID_MAP);
	{/if}	
	{else}
	{if $mode == 'normal'}cmap.setCenter(new GLatLng({$gmap_defaulty}, {$gmap_defaultx}), {$gmap_defaultz});
  {elseif $mode == 'satellite'}cmap.setCenter(new GLatLng({$gmap_defaulty}, {$gmap_defaultx}), {$gmap_defaultz}, G_SATELLITE_MAP);
	{elseif $mode == 'hybrid'}cmap.setCenter(new GLatLng({$gmap_defaulty}, {$gmap_defaultx}), {$gmap_defaultz}, G_HYBRID_MAP);
	{/if}			
	{/if}
	
	// Create a base icon for all of our markers that specifies the
	// shadow, icon dimensions, etc.
	var baseIcon = new GIcon();
	baseIcon.shadow = "http://www.google.com/mapfiles/shadow50.png";
	baseIcon.iconSize = new GSize(20, 34);
	baseIcon.shadowSize = new GSize(37, 34);
	baseIcon.iconAnchor = new GPoint(9, 34);
	baseIcon.infoWindowAnchor = new GPoint(9, 2);
	baseIcon.infoShadowAnchor = new GPoint(18, 25);
	
	var tempCoordArr = new Array();
	{foreach key=i item=u from=$users}
	tempCoord = new GLatLng({$u[0]},{$u[1]});		
	tempCoordArr[{$i}] = tempCoord;
	// Create a lettered icon for this point using our icon class
	var letter = String.fromCharCode("A".charCodeAt(0) + {$i});
	var icon = new GIcon(baseIcon);
	icon.image = "http://www.google.com/mapfiles/marker" + letter + ".png";
	var marker{$i} = new GMarker(tempCoord, icon);		
	cmap.addOverlay(marker{$i});
	GEvent.addListener(marker{$i},"click", function() {literal}{{/literal}
		if (document.getElementById('cnavButton').style.display=='none' || clastClicked != {$i}) {literal}{{/literal}
			marker{$i}.openInfoWindowHtml('{$u[2]}<br /><font color=#ff0000>Click on point once more to go to selected object</font>');
			cnavURL = escape('{$u[3]}');
			show('cnavButton');
		{literal}}{/literal} else {literal}{{/literal}
			cnavOut();
      return false;
    {literal}}{/literal}
  clastClicked = {$i};
	{literal}});{/literal}	
	{/foreach}
		
	GEvent.addListener(cmap, "click", function(marker, point) {literal}{{/literal}
   	if (!marker) {literal}{{/literal}      	
    		hide('cnavButton');     		    	
      	{if $editable and $tiki_p_edit eq 'y'}
   			document.getElementById('pointx').value = point.x;
   			document.getElementById('pointy').value = point.y;     
   			{/if}
      	var marker = new GMarker(point);
      	if (cmarker) cmap.removeOverlay(cmarker);
      	cmarker = marker;
      	cmap.addOverlay(marker);     
      	cmap.setCenter(point);  	     	
   	{literal}}{/literal}
  {literal}});{/literal}	  		
	
	{if $editable and $tiki_p_edit eq 'y'}	  	
  GEvent.addListener(cmap, "zoomend", function(gold, gnew) {literal}{{/literal}
    document.getElementById('groutez').value = gnew;
    document.getElementById('pointz').value = gnew;
  {literal}});{/literal}

  GEvent.addListener(cmap, "moveend", function() {literal}{{/literal}
    document.getElementById('groutex').value = cmap.getCenter().x;
    document.getElementById('groutey').value = cmap.getCenter().y;
  {literal}});{/literal}  	
	{/if}	
	
{literal}}{/literal}
	
function cshowAddress(address) {literal}{{/literal}			
  cgeocoder.getLatLng(
    address,
    function(point) {literal}{{/literal}
      if (!point) {literal}{{/literal}
        alert(address + " not found");
      {literal}}{/literal} else {literal}{{/literal}      	
        cmap.setCenter(point);
        var marker = new GMarker(point);   
        if (cmarker) cmap.removeOverlay(cmarker);             
        cmap.addOverlay(marker);
        cmarker = marker;
        marker.openInfoWindowHtml(address);
        {if $editable and $tiki_p_edit eq 'y'}
   			document.getElementById('pointx').value = point.x;
   			document.getElementById('pointy').value = point.y;     
   			{/if}
      {literal}}{/literal}
    {literal}}{/literal}
  );  
{literal}}{/literal}

function cnavOut() {literal}{{/literal}				
  window.location.replace(cnavURL);
{literal}}{/literal}

{if $pointx && $pointy}
function gotoSavedPoint() {literal}{{/literal}				
   var point = new GLatLng({$pointy},{$pointx});
   if (cmarker) cmap.removeOverlay(cmarker);
   var marker = new GMarker(point);
   {if $editable and $tiki_p_edit eq 'y'}
   document.getElementById('pointx').value = point.x;
   document.getElementById('pointy').value = point.y;     
   {/if}
   cmap.setCenter(point);
   cmap.addOverlay(marker);
   cmarker = marker;
   {if $gmapinfowindow}
   marker.openInfoWindow("{$gmapinfowindow}");
   {/if}
{literal}}{/literal}
{/if}
	
var cmap;
var cgeocoder;	
var cnavURL;
var clastClicked;
var cmarker;
setTimeout('cload()', 500);
//]]>
</script>
{/if}
