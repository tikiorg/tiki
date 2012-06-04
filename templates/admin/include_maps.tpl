<form action="tiki-admin.php?page=maps" method="post">
	<div class="heading input_submit_container" style="text-align: right">
		<input type="submit" name="mapsset" value="{tr}Change preferences{/tr}" />
	</div>
	<fieldset class="admin">
		<legend>{tr}Settings{/tr}</legend>

		{preference name=geo_tilesets}
		{preference name=geo_google_streetview}

		{preference name=geo_locate_blogpost}
		{preference name=geo_locate_wiki}
		{preference name=geo_locate_article}
		{preference name=wikiplugin_map}
		{preference name=trackerfield_location}

		{preference name=geo_always_load_openlayers}
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
