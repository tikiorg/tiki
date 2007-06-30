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
{if !$pointx || !$pointy}<small>{tr}map view not yet saved{/tr}</small>{else}<small>{tr}map of pages that link to this page{/tr}</small>{/if}
<form action="gmap_wikiplugin_save.php" method="post">
{if $page_ref_id}
<input type="hidden" name="page_ref_id" value="{$page_ref_id|escape}" />
{else}
<input type="hidden" name="page" value="{$page|escape}" />
{/if}
<input type="hidden" name="backlinkgmapy" value="{$backlinkgmapy}" id="backlinkgmapy" />
<input type="hidden" name="backlinkgmapx" value="{$backlinkgmapx}" id="backlinkgmapx" />
<input type="hidden" name="backlinkgmapz" value="{$backlinkgmapz}" id="backlinkgmapz" />
<input type="submit" name="save_backlinkgmap" value="{tr}Save current view{/tr}" />
</form>
</td></tr>
{/if}
<tr><td>
<form name="bgoogleaddress" onSubmit="bshowAddress(document.bgoogleaddress.baddress.value); return false">
<input type="text" size="60" name="baddress" value="" />
<input type="button" value="{tr}Go!{/tr}" onClick="bshowAddress(document.bgoogleaddress.baddress.value); return false" />
</form>
</td></tr></table>
<form><input type="button" value="{tr}Go to selected object{/tr}" style="display:none;" id="bnavButton" onClick="bnavOut(); return false" /></form>
<div id="bmap" style="width: {$width}px; height: {$height}px;border: 1px solid #000;"></div>
<script type="text/javascript">
//<![CDATA[
function bload() {literal}{{/literal}
  bmap = new GMap2(document.getElementById("bmap"));
  bgeocoder = new GClientGeocoder();  
  {if $controller == 'large'}bmap.addControl(new GLargeMapControl());
	{elseif $controller == 'medium'}bmap.addControl(new GSmallMapControl());
	{elseif $controller == 'small'}bmap.addControl(new GSmallZoomControl());
  {/if}
  {if $changetype == 'y'}bmap.addControl(new GMapTypeControl());{/if}
  {if $scale == 'y'}bmap.addControl(new GScaleControl());{/if}		

	{if $pointx and $pointy}
	{if $mode == 'normal'}bmap.setCenter(new GLatLng({$backlinkgmapy}, {$backlinkgmapx}), {$backlinkgmapz});
  {elseif $mode == 'satellite'}bmap.setCenter(new GLatLng({$backlinkgmapy}, {$backlinkgmapx}), {$backlinkgmapz}, G_SATELLITE_MAP);
	{elseif $mode == 'hybrid'}bmap.setCenter(new GLatLng({$backlinkgmapy}, {$backlinkgmapx}), {$backlinkgmapz}, G_HYBRID_MAP);
	{/if}	
	{else}
	{if $mode == 'normal'}bmap.setCenter(new GLatLng({$gmap_defaulty}, {$gmap_defaultx}), {$gmap_defaultz});
  {elseif $mode == 'satellite'}bmap.setCenter(new GLatLng({$gmap_defaulty}, {$gmap_defaultx}), {$gmap_defaultz}, G_SATELLITE_MAP);
	{elseif $mode == 'hybrid'}bmap.setCenter(new GLatLng({$gmap_defaulty}, {$gmap_defaultx}), {$gmap_defaultz}, G_HYBRID_MAP);
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
	
	{foreach key=i item=u from=$users}
	tempCoord = new GLatLng({$u[0]},{$u[1]});		
	// Create a lettered icon for this point using our icon class
	var letter = String.fromCharCode("A".charCodeAt(0) + {$i});
	var icon = new GIcon(baseIcon);
	icon.image = "http://www.google.com/mapfiles/marker" + letter + ".png";
	var marker{$i} = new GMarker(tempCoord, icon);			
	bmap.addOverlay(marker{$i});
	GEvent.addListener(marker{$i},"click", function() {literal}{{/literal}
		if (document.getElementById('bnavButton').style.display=='none' || blastClicked != {$i}) {literal}{{/literal}
			marker{$i}.openInfoWindowHtml('{$u[2]}<br /><font color=#ff0000>Click on point once more to go to selected object</font>');
			bnavURL = escape('{$u[3]}');
			show('bnavButton');
		{literal}}{/literal} else {literal}{{/literal}
			bnavOut();
      return false;
    {literal}}{/literal}
  blastClicked = {$i};
	{literal}});{/literal}	
	{/foreach}
	
	GEvent.addListener(bmap, "click", function(marker, point) {literal}{{/literal}
   	if (!marker) {literal}{{/literal}      	
    		hide('bnavButton'); 
    		{if $editable and $tiki_p_edit eq 'y'}
   			document.getElementById('pointx').value = point.x;
   			document.getElementById('pointy').value = point.y;     
   			{/if}
      	var marker = new GMarker(point);
      	if (bmarker) bmap.removeOverlay(bmarker);
      	bmarker = marker;
      	bmap.addOverlay(marker);     
      	bmap.setCenter(point);  	        	
   	{literal}}{/literal}
  {literal}});{/literal}	  		
	
	{if $editable and $tiki_p_edit eq 'y'}	  	
  GEvent.addListener(bmap, "zoomend", function(gold, gnew) {literal}{{/literal}
    document.getElementById('backlinkgmapz').value = gnew;
    document.getElementById('pointz').value = gnew;
  {literal}});{/literal}

  GEvent.addListener(bmap, "moveend", function() {literal}{{/literal}
    document.getElementById('backlinkgmapx').value = bmap.getCenter().x;
    document.getElementById('backlinkgmapy').value = bmap.getCenter().y;
  {literal}});{/literal}  	
	{/if}	
	
{literal}}{/literal}
	
function bshowAddress(address) {literal}{{/literal}			
  bgeocoder.getLatLng(
    address,
    function(point) {literal}{{/literal}
      if (!point) {literal}{{/literal}
        alert(address + " not found");
      {literal}}{/literal} else {literal}{{/literal}      	
        bmap.setCenter(point);
        var marker = new GMarker(point);                
        if (bmarker) bmap.removeOverlay(bmarker);
        bmap.addOverlay(marker);                 
        bmarker = marker;
        marker.openInfoWindowHtml(address);
        {if $editable and $tiki_p_edit eq 'y'}
   			document.getElementById('pointx').value = point.x;
   			document.getElementById('pointy').value = point.y;     
   			{/if}        
      {literal}}{/literal}
    {literal}}{/literal}
  );  
{literal}}{/literal}

function bnavOut() {literal}{{/literal}				
  window.location.replace(bnavURL);
{literal}}{/literal}

{if $pointx && $pointy}
function gotoSavedPoint() {literal}{{/literal}				
   var point = new GLatLng({$pointy},{$pointx});
   if (bmarker) bmap.removeOverlay(bmarker);
   var marker = new GMarker(point);
   {if $editable and $tiki_p_edit eq 'y'}
   document.getElementById('pointx').value = point.x;
   document.getElementById('pointy').value = point.y;     
   {/if}
   bmap.setCenter(point);
   bmap.addOverlay(marker);
   bmarker = marker;
   {if $gmapinfowindow}
   marker.openInfoWindow("{$gmapinfowindow}");
   {/if}
{literal}}{/literal}
{/if}

var bmap;
var bgeocoder;	
var bnavURL;
var blastClicked;
var bmarker;
setTimeout('bload()', 500);
//]]>
</script>
{/if}
