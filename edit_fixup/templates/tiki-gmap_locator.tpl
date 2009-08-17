{if $prefs.feature_gmap eq 'y'}

{title help="gmap"}{tr}Google Map Locator{/tr}{/title}
{if $watch}({$watch}){/if}

<script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key={$prefs.gmap_key}"></script>
<div clas="navbar">
{button href="$backurl" _text="$backlink"}
</div>

<form action="tiki-gmap_locator.php{$extraquery}" method="post">
<input type="text" name="point[x]" value="{$pointx}" id="pointx" size="16" />
<input type="text" name="point[y]" value="{$pointy}" id="pointy" size="16" />
<input type="text" name="point[z]" value="{$pointz}" id="pointz" size="2" />
{if $input eq 'y'}
{if $watch}<input type="hidden" name="view_user" value="{$watch|escape}" />{/if}
{if $itemId}<input type="hidden" name="itemId" value="{$itemId}" />{/if}
{if $fieldId}<input type="hidden" name="fieldId" value="{$fieldId}" />{/if}
<input type="submit" name="act" value="{tr}Save clicked point{/tr}" /><br /><br />
<input type="submit" name="reset_default" value="{tr}Reset view to default{/tr}" />
<input type="submit" name="act" value="{tr}Save current view as default{/tr}" />
<input type="submit" name="recenter" value="{tr}Center map to saved point{/tr}" />
{/if}
<input type="hidden" name="default[x]" value="{$pointx}" id="defx" />
<input type="hidden" name="default[y]" value="{$pointy}" id="defy" />
<input type="hidden" name="default[z]" value="{$pointz}" id="defz" />
<input type="submit" name="reset_site_default" value="{tr}Reset view to site default{/tr}" />
</form>

{if $pointx eq ''}
	{assign var=pointx value=$prefs.gmap_defaultx}
	{assign var=pointy value=$prefs.gmap_defaulty}
	{assign var=pointz value=$prefs.gmap_defaultz}
{/if}

<div id="map" style="width: 500px; height: 400px;border: 1px solid #000;"></div>
<script type="text/javascript">
<!--//--><![CDATA[//><!--
function load() {literal}{{/literal}
  var map = new GMap2(document.getElementById("map"));
  map.addControl(new GLargeMapControl());
  map.addControl(new GMapTypeControl());
  map.addControl(new GScaleControl());
  map.setCenter(new GLatLng({$pointy}, {$pointx}), {$pointz});

{if $input eq 'y'}
{if $pointx and $pointy}
	map.addOverlay(new GMarker(new GLatLng({$pointy},{$pointx})));
{/if}

  GEvent.addListener(map, "click", function(marker, point) {literal}{{/literal}
    if (marker) {literal}{{/literal}
      map.removeOverlay(marker);
    {literal}} else {{/literal}
      document.getElementById('pointx').value = point.x;
      document.getElementById('pointy').value = point.y;
      map.clearOverlays();
      map.addOverlay(new GMarker(point));
    {literal}}{/literal}
  {literal}});{/literal}
{/if}

  GEvent.addListener(map, "zoomend", function(gold, gnew) {literal}{{/literal}
    document.getElementById('defz').value = gnew;
    document.getElementById('pointz').value = gnew;
  {literal}});{/literal}

  GEvent.addListener(map, "moveend", function() {literal}{{/literal}
    document.getElementById('defx').value = map.getCenter().x;
    document.getElementById('defy').value = map.getCenter().y;
  {literal}});{/literal}

{literal}}{/literal}
//load();
//--><!]]>
window.onload=load;
</script>
{else}
Google Maps is not enabled.
{/if}

