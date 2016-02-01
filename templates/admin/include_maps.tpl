<form class="form-horizontal" action="tiki-admin.php?page=maps" method="post" role="form">
	<input type="hidden" name="ticket" value="{$ticket|escape}">

	<div class="row">
		<div class="form-group col-lg-12 clearfix">
			<div class="pull-right">
				<input type="submit" class="btn btn-primary btn-sm" name="mapsset" title="{tr}Apply Changes{/tr}" value="{tr}Apply{/tr}">
			</div>
		</div>
	</div>

	<fieldset>
		<legend>{tr}Settings{/tr}</legend>

		{preference name=geo_openlayers_version}

		{preference name=geo_tilesets}
		{preference name=geo_google_streetview}
		<div class="adminoptionboxchild" id="geo_google_streetview_childcontainer">
			{preference name=geo_google_streetview_overlay}
		</div>

		{preference name=geo_locate_blogpost}
		{preference name=geo_locate_wiki}
		{preference name=gmap_page_list}
		{preference name=geo_locate_article}
		{preference name=gmap_article_list}
		{preference name=wikiplugin_map}
		{preference name=trackerfield_location}

		{preference name=geo_always_load_openlayers}

		{preference name=gmap_key}

		<div class="form-group adminoptionbox">
			<label for="geo_zoomlevel_to_found_location" class="control-label col-md-4">
				{tr}Zoom level to searched location{/tr}
			</label>
			<div class="col-md-8">
				<select name="geo_zoomlevel_to_found_location" id="geo_zoomlevel_to_found_location" class="form-control">
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
		<legend>{tr}Defaults{/tr}</legend>
		{preference name=gmap_defaultx}
		{preference name=gmap_defaulty}
		{preference name=gmap_defaultz}
	</fieldset>

	<fieldset class="table">
		<div class="row">
			<div class="form-group col-lg-12 clearfix">
				<div class="text-center">
					<input type="submit" class="btn btn-primary btn-sm" name="mapsset" title="{tr}Apply Changes{/tr}" value="{tr}Apply{/tr}" />
				</div>
			</div>
		</div>
	</fieldset>
</form>
