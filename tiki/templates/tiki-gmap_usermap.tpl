<h1><a href="tiki-gmap_usermap.php" class="pagetitle">{tr}Users Map{/tr}</a></h1>
<br /><br />

<script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key={$gmap_key}"></script>

<div class="wikitext">
<div id="map" style="width: 500px; height: 400px;border: 1px solid #000;"></div>
</div>

<script type="text/javascript">
//<![CDATA[
function load() {literal}{{/literal}
  var map = new GMap2(document.getElementById("map"));
  map.addControl(new GLargeMapControl());
  map.addControl(new GMapTypeControl());
  map.addControl(new GScaleControl());
  map.setCenter(new GLatLng({$gmap_defaulty}, {$gmap_defaultx}), {$gmap_defaultz});

{foreach key=i item=u from=$users}
	marker{$i} = new GMarker(new GLatLng({$u[0]},{$u[1]}));
	map.addOverlay(marker{$i});
	GEvent.addListener(marker{$i},"click", function() {literal}{{/literal}
		marker{$i}.openInfoWindowHtml('{$u[2]}');
	{literal}});{/literal}
{/foreach}

{literal}}{/literal}
load();
//]]>
</script>

