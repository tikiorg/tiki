{if $gmaptoggle}
	<div class="tellafriend" style="width: {$gmapwidth|escape}px; {$togglealign}">
		<a href="#" id="gmap{$gmapname|escape}link" onclick="toggleMap{$gmapname|escape}('#gmap{$gmapname|escape}container', '#' + this.id); return false;">{if $gmaphidden}{$toggletexthid}{else}{$toggletextshown}{/if}</a>
	</div>
{/if}
<div class="wikitext" id="gmap{$gmapname|escape}container" style="display: {if $gmaphidden}none{else}block{/if}">
	{if !$gmap_in_form && ($gmaptype eq 'locator' || $gmap_defaultset && $user)}
		<form{if $gmaptype eq 'locator'} onsubmit="showAddress{$gmapname|escape}(this.address.value);return false;"{/if}>
	{/if}
	{if $gmaptype eq 'locator'}
		<input type="text" size="{$gmapaddresslength}" name="address" value="{tr}enter address{/tr}" />
		{jq}
			var $addr = $("input[name=address]");
			$addr.focus(function(){ if ($addr.val() === "{tr}enter address{/tr}") { $addr.val(""); } }).blur(function(){ if ($addr.val() === "") { $addr.val("{tr}enter address{/tr}"); } });
		{/jq}
		<input type="submit" name="cancel" value="{tr}Find address{/tr}" onclick="showAddress{$gmapname|escape}(this.form.address.value);return false;" /><br>
		{tr}Lon.:{/tr} <input type="text" name="point[x]" value="{$pointx}" id="{$gmapname|escape}pointx" size="6" />
		{tr}Lat.:{/tr} <input type="text" name="point[y]" value="{$pointy}" id="{$gmapname|escape}pointy" size="6" />
		{tr}Zoom:{/tr} <input type="text" name="point[z]" value="{$pointz}" id="{$gmapname|escape}pointz" size="2" />
	{/if}
	{if $gmap_defaultset && $user}
		<input type="submit" name="cancel" onclick="document.getElementById('gmap{$gmapname|escape}_ajax_msg').innerHTML = '{tr}saving...{/tr}';saveGmapDefaultxyz{$gmapname|escape}();return false;" value="{tr}Save current map view as user default{/tr}" />
	{/if}
	{if $gmaptype eq 'locator' && $gmapitemtype eq 'user'}
		<input type="submit" name="cancel" onclick="document.getElementById('gmap{$gmapname|escape}_ajax_msg').innerHTML = '{tr}saving...{/tr}';saveGmapUser{$gmapname|escape}();return false;" value="{tr}Save as user location{/tr}" />
	{/if}
	{if $gmaptype eq 'locator' && $gmapitemtype neq 'user' && $gmapitemtype neq 'trackeritem'}
		<input type="submit" name="cancel" onclick="document.getElementById('gmap{$gmapname|escape}_ajax_msg').innerHTML = '{tr}saving...{/tr}';saveGmapItem{$gmapname|escape}();return false;" value="{tr}Save as object location{/tr}" />
	{/if}
	{if !$gmap_in_form && ($gmaptype eq 'locator' || $gmap_defaultset && $user)}
		</form>
	{/if}
	<span id="gmap{$gmapname|escape}_ajax_msg">&nbsp;</span>
	<div id="gmap{$gmapname|escape}" style="width: {$gmapwidth|escape}px; height: {$gmapheight|escape}px;{if $gmapframeborder}border: 1px solid #000;{/if}"></div>
</div>
{jq notonready=true}
	function toggleMap{$gmapname|escape}(id, linkid) {literal}{{/literal}
		$(id).toggle('slow');
		if ($(linkid).html() == '{$toggletexthid}') {literal}{{/literal}
			$(linkid).html('{$toggletextshown}');
		{literal}}{/literal}
		 else if ($(linkid).html() == '{$toggletextshown}') {literal}{{/literal}
			$(linkid).html('{$toggletexthid}');
		{literal}}{/literal}
	{literal}}{/literal}
	
	function showAddress{$gmapname|escape}(address) {literal}{{/literal}
		if (geocoder) {literal}{{/literal}
			geocoder.getLatLng(
				address,
				function(point) {literal}{{/literal}
					if (!point) {literal}{{/literal}
						alert("\"" + address + "\" not found!");
					{literal}} else {{/literal}
						document.getElementById('{$gmapname|escape}pointx').value = point.x;
						document.getElementById('{$gmapname|escape}pointy').value = point.y;
						document.getElementById('{$gmapname|escape}pointz').value = gmap{$gmapname|escape}map.getZoom();
						{if isset($gmapautozoom)}
							gmap{$gmapname|escape}map.setCenter(point,{$gmapautozoom});
						{else}
							gmap{$gmapname|escape}map.setCenter(point);
						{/if}
						gmap{$gmapname|escape}map.clearOverlays();
						var marker = new GMarker(point);
						gmap{$gmapname|escape}map.addOverlay(marker);
						marker.openInfoWindowHtml(address);
					{literal}}{/literal}
					updateGmapInput{$gmapname|escape}();
				{literal}}{/literal}
			);
		{literal}}{/literal}
	{literal}}{/literal}
	
	function saveGmapDefaultxyz{$gmapname|escape}() {literal}{{/literal}	// AJAX_TODO
	//	xajax.config.requestURI = '{$smarty.server.REQUEST_URI}';
	//	xajax_saveGmapDefaultxyz('gmap{$gmapname|escape}_ajax_msg', gmap{$gmapname|escape}map.getCenter().x, gmap{$gmapname|escape}map.getCenter().y, gmap{$gmapname|escape}map.getZoom());
	{literal}}{/literal}
	
	function saveGmapUser{$gmapname|escape}() {literal}{{/literal}
	//	xajax.config.requestURI = '{$smarty.server.REQUEST_URI}';
	//	xajax_saveGmapUser('gmap{$gmapname|escape}_ajax_msg', document.getElementById('{$gmapname|escape}pointx').value, document.getElementById('{$gmapname|escape}pointy').value, document.getElementById('{$gmapname|escape}pointz').value, '{$gmapitem}');
	{literal}}{/literal}
	
	function updateGmapInput{$gmapname|escape}() {ldelim}
		{if $gmaptrackerinputid}
			document.getElementById('{$gmaptrackerinputid|escape}').value = document.getElementById('{$gmapname|escape}pointx').value + ',' + document.getElementById('{$gmapname|escape}pointy').value + ',' + document.getElementById('{$gmapname|escape}pointz').value;
		{/if}
		return;
	{rdelim}
	
	function saveGmapItem{$gmapname|escape}() {literal}{{/literal}
		updateGmapInput{$gmapname|escape}();
		{if $gmapitem}
	//	xajax.config.requestURI = '{$smarty.server.REQUEST_URI}';
	//	xajax_saveGmapItem('gmap{$gmapname|escape}_ajax_msg', document.getElementById('{$gmapname|escape}pointx').value, document.getElementById('{$gmapname|escape}pointy').value, document.getElementById('{$gmapname|escape}pointz').value, '{$gmapitemtype}', '{$gmapitem}', '{$gmaptrackerfieldid}');
		{/if}
	{literal}}{/literal}
{/jq}

{jq}
	function loadgmap{$gmapname|escape}() {literal}{{/literal}
		var gmapSize = new GSize({$gmapwidth|escape}, {$gmapheight|escape});
		{* size has to be specified explicitly in case div is hidden *}
		gmap{$gmapname|escape}map = new GMap2(document.getElementById("gmap{$gmapname|escape}"), {literal}{{/literal}size: gmapSize{literal}}{/literal});
		{if !isset($gmap_controls) or $gmap_controls ne 'n'}
			{if ($gmap_controls eq 'a' ) or ($gmap_controls eq 'z' ) or ($gmap_controls eq 'zt') or ($gmap_controls eq 'zs' )}
				gmap{$gmapname|escape}map.addControl(new GLargeMapControl());
			{/if}
			{if ($gmap_controls eq 'a' ) or ($gmap_controls eq 't' ) or ($gmap_controls eq 'zt') or ($gmap_controls eq 'ts' )}
				gmap{$gmapname|escape}map.addControl(new GMapTypeControl());
			{/if}
			{if ($gmap_controls eq 'a' ) or ($gmap_controls eq 's' ) or ($gmap_controls eq 'zs') or ($gmap_controls eq 'ts' )}
				gmap{$gmapname|escape}map.addControl(new GScaleControl());
			{/if}
		{/if}
		{if $pointx and $pointy and $pointz}
			gmap{$gmapname|escape}map.setCenter(new GLatLng({$pointy|escape}, {$pointx|escape}), {$pointz|escape});
			{if isset($pointicon) && isset($pointiconx) && isset($pointicony) && $pointicon && $pointiconx && $pointicony}
				var markericon = new GIcon();
				markericon.image = '{$pointicon}';
				markericon.iconSize = new GSize({$pointiconx}, {$pointicony});
				markericon.iconAnchor = new GPoint(5, {$pointicony} - 2);
				markericon.infoWindowAnchor = new GPoint(5, 2);
				var point = new GMarker(new GLatLng({$pointy|escape},{$pointx|escape}), {literal}{{/literal}icon:markericon{literal}}{/literal});
			{else}
				var point = new GMarker(new GLatLng({$pointy|escape},{$pointx|escape}));
			{/if}
			gmap{$gmapname|escape}map.addOverlay(point);
			{if $gmap_markerstart eq 'show'}
				{* $gmap_markertext content has already been escaped in the related php file *}
				point.openInfoWindowHtml('{$gmap_markertext}');
			{else}
				GEvent.addListener(point, "click", function() {literal}{{/literal}
					point.openInfoWindowHtml('{$gmap_markertext}');
				{literal}}{/literal});
			{/if}
		{else}
			var point = new GLatLng({$gmap_defaulty|escape}, {$gmap_defaultx|escape});
			gmap{$gmapname|escape}map.setCenter(point, {$gmap_defaultz|escape});
			{if $gmap_marker eq 1}
				var marker = new GMarker(point);
				gmap{$gmapname|escape}map.addOverlay(marker);
				{if $gmap_markerstart eq 'show'}
					marker.openInfoWindowHtml('{$gmap_markertext}');
				{else}
					GEvent.addListener(marker, "click", function() {literal}{{/literal}
						marker.openInfoWindowHtml('{$gmap_markertext}');
					{literal}}{/literal});
				{/if}
			{/if}
		{/if}
		{if $gmapmode eq 'normal'}
			gmap{$gmapname|escape}map.setMapType(G_NORMAL_MAP);  
		{elseif $gmapmode eq 'satellite'}
			gmap{$gmapname|escape}map.setMapType(G_SATELLITE_MAP);
		{elseif $gmapmode eq 'hybrid'}
			gmap{$gmapname|escape}map.setMapType(G_HYBRID_MAP);
		{/if}
		geocoder = new GClientGeocoder();
	
		{foreach key=i item=u from=$gmapmarkers}
			{if isset($u[3]) && isset($u[4]) && isset($u[5]) && $u[3] && $u[4] && $u[5]}
				var markericon = new GIcon();
				markericon.image = '{$u[3]}';
				markericon.iconSize = new GSize({$u[4]}, {$u[5]});
				markericon.iconAnchor = new GPoint(5, {$u[5]} - 2);
				markericon.infoWindowAnchor = new GPoint(5, 2);
				var marker{$i} = new GMarker(new GLatLng({$u[0]},{$u[1]}), {literal}{{/literal}icon:markericon{literal}}{/literal});    
			{else}
				var marker{$i} = new GMarker(new GLatLng({$u[0]},{$u[1]}));
			{/if}
			gmap{$gmapname|escape}map.addOverlay(marker{$i});
			GEvent.addListener(marker{$i},"click", function() {literal}{{/literal}
				marker{$i}.openInfoWindowHtml('{$u[2]}', {literal}{{/literal}maxWidth:200{literal}}{/literal});
			{literal}});{/literal}
		{/foreach}
		
		{if $gmaptype eq 'locator'}
			GEvent.addListener(gmap{$gmapname|escape}map, "click", function(marker, point) {literal}{{/literal}
				if (marker) {literal}{{/literal}
					gmap{$gmapname|escape}map.removeOverlay(marker);
					{literal}} else {{/literal}
					document.getElementById('{$gmapname|escape}pointx').value = point.x;
					document.getElementById('{$gmapname|escape}pointy').value = point.y;
					document.getElementById('{$gmapname|escape}pointz').value = gmap{$gmapname|escape}map.getZoom();
					gmap{$gmapname|escape}map.clearOverlays();
					gmap{$gmapname|escape}map.addOverlay(new GMarker(point));
					updateGmapInput{$gmapname|escape}();
				{literal}}{/literal}
			{literal}});{/literal}
		
			GEvent.addListener(gmap{$gmapname|escape}map, "zoomend", function(gold, gnew) {literal}{{/literal}
				document.getElementById('{$gmapname|escape}pointz').value = gnew;
				updateGmapInput{$gmapname|escape}();
			{literal}});{/literal}
		{/if}
	{literal}}{/literal}
	loadgmap{$gmapname|escape}();
{/jq}
