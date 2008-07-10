{if $prefs.feature_gmap eq 'y'}
<h1><a href="tiki-gmap_locator.php{$extraquery}" class="pagetitle">Google Map Locator</a></h1>
<script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key={$prefs.gmap_key}"></script>

<div class="wikitext">
<table>
{if $input eq 'y'}
<tr><td colspan="2">
<a href="{$backurl}" class="linkbut">{$backlink}</a>{if $watch}({$watch}){/if}<br /><br />
<form action="tiki-gmap_locator.php{$extraquery}" method="post">
{if $watch}<input type="hidden" name="view_user" value="{$watch}" />{/if}
<input type="text" name="point[x]" value="{$pointx}" id="pointx" size="16" />
<input type="text" name="point[y]" value="{$pointy}" id="pointy" size="16" />
<input type="text" name="point[z]" value="{$pointz}" id="pointz" size="2" />
<input type="submit" name="act" value="{tr}Save clicked point{/tr}" /><br />
<a href="tiki-gmap_locator.php?for=user&amp;recenter=y{if $watch}&amp;view_user={$watch}{/if}">Center map to saved point</a>
</form>
</td></tr>
{/if}
<tr><td>
<form action="tiki-gmap_locator.php{$extraquery}" method="post">
{if $watch}<input type="hidden" name="view_user" value="{$watch}" />{/if}
<input type="hidden" name="default[x]" value="{$prefs.gmap_defaultx}" id="defx" />
<input type="hidden" name="default[y]" value="{$prefs.gmap_defaulty}" id="defy" />
<input type="hidden" name="default[z]" value="{$prefs.gmap_defaultz}" id="defz" />
<input type="submit" name="act" value="{tr}Save current view as default{/tr}" />
</form>
</td>
<td>
<form action="tiki-gmap_locator.php{$extraquery}" method="post">
{if $watch}<input type="hidden" name="view_user" value="{$watch}" />{/if}
<input type="submit" name="reset_default" value="{tr}Reset view to site-wide default{/tr}" />
</form>
</td>
</tr></table>

<div id="map" style="width: 500px; height: 400px;border: 1px solid #000;"></div>
</div>
<script type="text/javascript">
<!--//--><![CDATA[//><!--
function load() {literal}{{/literal}
  var map = new GMap2(document.getElementById("map"));
  map.addControl(new GLargeMapControl());
  map.addControl(new GMapTypeControl());
  map.addControl(new GScaleControl());
  map.setCenter(new GLatLng({$prefs.gmap_defaulty}, {$prefs.gmap_defaultx}), {$prefs.gmap_defaultz});

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

