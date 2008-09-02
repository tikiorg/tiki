{* $Id: tracker_item_field_value.tpl 14576 2008-09-02 01:19:41Z rischconsulting $ *}
{* idem tracker_item_field with no strip *}
	Google Map : X = {$field_value.x} ; Y = {$field_value.y} ; Zoom = {$field_value.z}
	<script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key={$prefs.gmap_key}" type="text/javascript">
	</script>
	<div id="map" style="width: 500px; height: 400px;border: 1px solid #000;">
	</div>
	<script type="text/javascript">
	<!--//--><![CDATA[//><!--
	function load() {literal}{{/literal}
	var map = new GMap2(document.getElementById("map"));
	  map.addControl(new GLargeMapControl());
	  map.addControl(new GMapTypeControl());
	  map.addControl(new GScaleControl());
	  map.setCenter(new GLatLng({$field_value.y}, {$field_value.x}), {$field_value.z});
	  map.addOverlay(new GMarker(new GLatLng({$field_value.y},{$field_value.x})));

/*	  GEvent.addListener(map, "zoomend", function(gold, gnew) {literal}{{/literal}
	    document.getElementById('defz').value = gnew;
	    document.getElementById('pointz').value = gnew;
	  {literal}});{/literal}

	  GEvent.addListener(map, "moveend", function() {literal}{{/literal}
	    document.getElementById('defx').value = map.getCenter().x;
	    document.getElementById('defy').value = map.getCenter().y;
	  {literal}});{/literal}
*/
	{literal}}{/literal}
//	load();
	window.onload=load;
	//--><!]]>
	</script>
