<form class="form-horizontal" action="tiki-admin.php?page=maps" method="post" role="form">
	{include file='access/include_ticket.tpl'}

	<div class="row">
		<div class="form-group col-lg-12 clearfix">
			<div class="pull-right">
				<input type="submit" class="btn btn-primary btn-sm tips" name="mapsset" title=":{tr}Apply changes{/tr}" value="{tr}Apply{/tr}">
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
		{preference name=geo_zoomlevel_to_found_location}

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
					<input type="submit" class="btn btn-primary btn-sm tips" name="mapsset" title=":{tr}Apply changes{/tr}" value="{tr}Apply{/tr}">
				</div>
			</div>
		</div>
	</fieldset>
</form>
