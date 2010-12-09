{if $prefs.feature_gmap eq 'y'}

{if $prefs.ajax_xajax eq 'y'}
{* Ajax version using new plugin *}
{title help="gmap"}{tr}Google Map Locator{/tr} - {$userwatch}{/title}
<p>{button _script="tiki-user_preferences.php"  view_user=$smarty.request.view_user|escape _text="{tr}Back to preferences{/tr}"}</p>
{wikiplugin _name="googlemap" type="locator" setdefaultxyz="y" locateitemtype="user" locateitemid="$userwatch"}{/wikiplugin}
{else}
{* Old non-ajax version which can be removed once Ajax becomes always on *}
{title help="gmap"}{tr}Google Map Locator{/tr}{/title}
{if $watch}({$watch}){/if}

<script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key={$prefs.gmap_key}"></script>
<div class="navbar">
{button href="$backurl" _text="$backlink"}
</div>

<form action="tiki-gmap_locator.php{$extraquery}" method="post">
<input type="text" size="60" name="address" value="{tr}enter address{/tr}" />
<input type="button" value="{tr}Find address{/tr}" onclick="showAddress(this.form.address.value)"/><br />
<input type="text" name="point[x]" value="{$pointx}" id="pointx" size="16" />
<input type="text" name="point[y]" value="{$pointy}" id="pointy" size="16" />
<input type="text" name="point[z]" value="{$pointz}" id="pointz" size="2" />
{if $input eq 'y'}
{if $watch}<input type="hidden" name="view_user" value="{$watch|escape}" />{/if}
{if $itemId}<input type="hidden" name="itemId" value="{$itemId}" />{/if}
{if $fieldId}<input type="hidden" name="fieldId" value="{$fieldId}" />{/if}
{if $fromPage}<input type="hidden" name="fromPage" value="{$fromPage}" />{/if}
<input type="submit" name="act" value="{tr}Save clicked point{/tr}" /><br /><br />
<input type="submit" name="reset_default" value="{tr}Reset view to default{/tr}" />
<input type="submit" name="set_default" value="{tr}Save current view as default{/tr}" />
<input type="submit" name="recenter" value="{tr}Center map to saved point{/tr}" />
{/if}
<input type="hidden" name="default[x]" value="{$pointx}" id="defx" />
<input type="hidden" name="default[y]" value="{$pointy}" id="defy" />
<input type="hidden" name="default[z]" value="{$pointz}" id="defz" />
<input type="submit" name="reset_site_default" value="{tr}Reset view to site default{/tr}" />
<input type="hidden" name="for" value="{$for|escape}" />
</form>

{if $pointx eq ''}
	{assign var=pointx value=$prefs.gmap_defaultx}
	{assign var=pointy value=$prefs.gmap_defaulty}
	{assign var=pointz value=$prefs.gmap_defaultz}
{/if}

<div id="map" style="width: 500px; height: 400px;border: 1px solid #000;"></div>
{jq notonready=true}
var map = null;
var geocoder = null;
function load() {literal}{{/literal}
  map = new GMap2(document.getElementById("map"));
  map.addControl(new GLargeMapControl());
  map.addControl(new GMapTypeControl());
  map.addControl(new GScaleControl());
  map.setCenter(new GLatLng({$pointy}, {$pointx}), {$pointz});
  geocoder = new GClientGeocoder();

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

function showAddress(address) {literal}{{/literal}
  if (geocoder) {literal}{{/literal}
    geocoder.getLatLng(
      address,
      function(point) {literal}{{/literal}
        if (!point) {literal}{{/literal}
          alert(address + " not found!");
        {literal}} else {{/literal}
          map.setCenter(point,14);
          var marker = new GMarker(point);
          map.addOverlay(marker);
          marker.openInfoWindowHtml(address);
        {literal}}{/literal}
      {literal}}{/literal}
    );
  {literal}}{/literal}
{literal}}{/literal}

{literal}$("input[name=address]").focus(function () { if ($(this).val() == "{/literal}{tr}enter address{/tr}{literal}") {$(this).val("");}}){/literal}

window.onload=load;
{/jq}

{/if} {*end if ajax *}
{else}
Google Maps is not enabled.
{/if}