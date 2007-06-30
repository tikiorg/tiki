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
<input type="text" readonly name="pointy" value="{$pointy}" id="pointy" />
<input type="text" readonly name="pointx" value="{$pointx}" id="pointx" />
<input type="hidden" name="pointz" value="{$pointz}" id="pointz" />
{if $pointx && $pointy}<input type="button" value="{tr}Go to saved point{/tr}" onClick="gotoSavedPoint()" />{/if}
<input type="submit" name="save_gmap" value="{tr}Save current point{/tr}" />
<input type="submit" name="remove_geocode" value="{tr}Delete{/tr}" /><br />
<textarea cols="50" rows="5" name="gmapinfowindow">{$gmapinfowindow|escape}</textarea>
</form>
</td></tr>
{/if}
<tr><td>
<form name="googleaddress" onSubmit="showAddress(document.googleaddress.address.value); return false">
<input type="text" size="60" name="address" value="" />
<input type="button" value="{tr}Go!{/tr}" onClick="showAddress(document.googleaddress.address.value); return false" />
</form>
</td></tr></table>
<div id="map" style="width: {$width}px; height: {$height}px;border: 1px solid #000;"></div>
<script type="text/javascript">
//<![CDATA[
function load() {literal}{{/literal}
  map = new GMap2(document.getElementById("map"));
  geocoder = new GClientGeocoder();  
  {if $controller == 'large'}map.addControl(new GLargeMapControl());
	{elseif $controller == 'medium'}map.addControl(new GSmallMapControl());
	{elseif $controller == 'small'}map.addControl(new GSmallZoomControl());
  {/if}
  {if $changetype == 'y'}map.addControl(new GMapTypeControl());{/if}
  {if $scale == 'y'}map.addControl(new GScaleControl());{/if}

	{if $pointx and $pointy}
	{if $mode == 'normal'}map.setCenter(new GLatLng({$pointy}, {$pointx}), {$pointz});
  {elseif $mode == 'satellite'}map.setCenter(new GLatLng({$pointy}, {$pointx}), {$pointz}, G_SATELLITE_MAP);
	{elseif $mode == 'hybrid'}map.setCenter(new GLatLng({$pointy}, {$pointx}), {$pointz}, G_HYBRID_MAP);
	{/if}	
	var marker = new GMarker(new GLatLng({$pointy},{$pointx}));
	map.addOverlay(marker);
	{if $gmapinfowindow}
  marker.openInfoWindow("{$gmapinfowindow}");
  {/if}
	{else}
	{if $mode == 'normal'}map.setCenter(new GLatLng({$gmap_defaulty}, {$gmap_defaultx}), {$gmap_defaultz});
  {elseif $mode == 'satellite'}map.setCenter(new GLatLng({$gmap_defaulty}, {$gmap_defaultx}), {$gmap_defaultz}, G_SATELLITE_MAP);
	{elseif $mode == 'hybrid'}map.setCenter(new GLatLng({$gmap_defaulty}, {$gmap_defaultx}), {$gmap_defaultz}, G_HYBRID_MAP);
	{/if}			
	{/if}						
	
	{if $editable and $tiki_p_edit eq 'y'}
	  GEvent.addListener(map, "click", function(marker, point) {literal}{{/literal}
    	if (!marker) {literal}{{/literal}      	      	
      	document.getElementById('pointx').value = point.x;
      	document.getElementById('pointy').value = point.y;
      	map.clearOverlays();
      	map.setCenter(point);
      	var marker = new GMarker(point);      	
      	map.addOverlay(marker);       	
    	{literal}}{/literal}
  	{literal}});{/literal}	
	
  	GEvent.addListener(map, "zoomend", function(gold, gnew) {literal}{{/literal}
    	 document.getElementById('pointz').value = gnew;
  	{literal}});{/literal}
	{/if}
{literal}}{/literal}
	
function showAddress(address) {literal}{{/literal}			
  geocoder.getLatLng(
    address,
    function(point) {literal}{{/literal}
      if (!point) {literal}{{/literal}
        alert(address + " not found");
      {literal}}{/literal} else {literal}{{/literal}      	
        map.setCenter(point);
        var marker = new GMarker(point);   
        {if $editable and $tiki_p_edit eq 'y'}
        document.getElementById('pointx').value = point.x;
      	document.getElementById('pointy').value = point.y;     
      	{/if}
        map.clearOverlays();
        map.addOverlay(marker);        
        marker.openInfoWindowHtml(address);        
      {literal}}{/literal}
    {literal}}{/literal}
  );  
{literal}}{/literal}

{if $pointx && $pointy}
function gotoSavedPoint() {literal}{{/literal}				
   var point = new GLatLng({$pointy},{$pointx});
   map.clearOverlays();
   var marker = new GMarker(point);
   {if $editable and $tiki_p_edit eq 'y'}
   document.getElementById('pointx').value = point.x;
   document.getElementById('pointy').value = point.y;     
   {/if}
   map.setCenter(point);
   map.addOverlay(marker);   
   {if $gmapinfowindow}
   marker.openInfoWindow("{$gmapinfowindow}");
   {/if}
{literal}}{/literal}
{/if}

var map;
var geocoder;	
setTimeout('load()', 500);
//]]>
</script>
{/if}
