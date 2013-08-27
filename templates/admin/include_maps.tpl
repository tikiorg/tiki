<form action="tiki-admin.php?page=maps" method="post">
	<div class="heading input_submit_container" style="text-align: right">
		<input type="submit" name="mapsset" value="{tr}Change preferences{/tr}" />
	</div>
	<fieldset class="admin">
		<legend>{tr}Settings{/tr}</legend>

		{preference name=geo_tilesets}
		{preference name=geo_google_streetview}
		<div class="adminoptionboxchild" id="geo_google_streetview_childcontainer">
			{preference name=geo_google_streetview_overlay}
		</div>

		{preference name=geo_locate_blogpost}
		{preference name=geo_locate_wiki}
		{preference name=geo_locate_article}
		{preference name=wikiplugin_map}
		{preference name=trackerfield_location}

		{preference name=geo_always_load_openlayers}

		{preference name=gmap_key}

		<div class="adminoptionbox">
			<div class="adminoptionlabel"><label for="geo_zoomlevel_to_found_location">{tr}Zoom level to searched location:{/tr}</label> 
			<select name="geo_zoomlevel_to_found_location" id="geo_zoomlevel_to_found_location">
			  <option value="street"{if $prefs.geo_zoomlevel_to_found_location eq 'street'} selected="selected"{/if}>{tr}Street level{/tr}</option>
			  <option value="town"{if $prefs.geo_zoomlevel_to_found_location eq 'town'} selected="selected"{/if}>{tr}Town level{/tr}</option>
			  <option value="region"{if $prefs.geo_zoomlevel_to_found_location eq 'region'} selected="selected"{/if}>{tr}Region level{/tr}</option>
			  <option value="country"{if $prefs.geo_zoomlevel_to_found_location eq 'country'} selected="selected"{/if}>{tr}Country level{/tr}</option>
			  <option value="continent"{if $prefs.geo_zoomlevel_to_found_location eq 'continent'} selected="selected"{/if}>{tr}Continent level{/tr}</option>
			  <option value="world"{if $prefs.geo_zoomlevel_to_found_location eq 'world'} selected="selected"{/if}>{tr}World{/tr}</option>
			</select> 
			</div>
		</div>
	</fieldset>
	
	<fieldset class="admin">			
		<legend>{tr}MapServer settings{/tr}</legend>
		
		{preference name=feature_maps}
		<div class="adminoptionboxchild" id="feature_maps_childcontainer">
			{if $map_error neq ''}
				{remarksbox type=warning title="{tr}Warning{/tr}"}{$map_error}{/remarksbox}
			{/if}
			{preference name=map_path}
			{preference name=default_map}
			{preference name=map_help}
			{preference name=map_comments}
			{preference name=gdaltindex}
			{preference name=ogr2ogr}
			{preference name=mapzone}
		</div>			
		
		<div class="heading input_submit_container" style="text-align: center">
			<input type="submit" name="mapsset" value="{tr}Change preferences{/tr}" />
			<input type="submit" name="mapuser" value="{tr}Generate User Map{/tr}" />
		</div>
	</fieldset>
</form>
