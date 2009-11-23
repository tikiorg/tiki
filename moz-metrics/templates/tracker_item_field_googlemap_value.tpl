{* $Id: tracker_item_field_value.tpl 14576 2008-09-02 01:19:41Z rischconsulting $ *}
{* idem tracker_item_field with no strip *}
	<script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key={$prefs.gmap_key}&amp;sensor=false" type="text/javascript">
	</script>
	<div id="map{$field_value.fieldId}_{$item.itemId}" style="width: {$width}px; height: {$height}px;border: 1px solid #000;overflow:hidden;">
	</div>
	<div class="description">{tr}Latitude{/tr} (Y) = {$field_value.y}<br /> {tr}Longitude{/tr} (X) = {$field_value.x} {if $control ne 'n'}<br />Zoom = {$field_value.z}{/if}</div>
	<script type="text/javascript">
	<!--//--><![CDATA[//><!--
	function load_googlemap{$field_value.fieldId}_{$item.itemId}() {literal}{{/literal}
	var map = new GMap2(document.getElementById("map{$field_value.fieldId}_{$item.itemId}"));
	  map.setCenter(new GLatLng({$field_value.y}, {$field_value.x}), {$field_value.z});
	{if $control ne 'n'}
	  map.addControl(new GLargeMapControl());
	  map.addControl(new GMapTypeControl());
	  map.addControl(new GScaleControl());
	{/if}
	  map.addOverlay(new GMarker(new GLatLng({$field_value.y},{$field_value.x})));

{*	  GEvent.addListener(map, "zoomend", function(gold, gnew) {literal}{{/literal}
	    document.getElementById('defz').value = gnew;
	    document.getElementById('pointz').value = gnew;
	  {literal}});{/literal}

	  GEvent.addListener(map, "moveend", function() {literal}{{/literal}
	    document.getElementById('defx').value = map.getCenter().x;
	    document.getElementById('defy').value = map.getCenter().y;
	  {literal}});{/literal}
*}
	{literal}}{/literal}
	window.unload=GUnload;
	load_googlemap{$field_value.fieldId}_{$item.itemId}();
	//--><!]]>
	</script>
