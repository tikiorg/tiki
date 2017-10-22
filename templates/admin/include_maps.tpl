<form class="form-horizontal" action="tiki-admin.php?page=maps" method="post" role="form">
	{ticket}

	<div class="row">
		<div class="form-group col-lg-12 clearfix">
			{include file='admin/include_apply_top.tpl'}
		</div>
	</div>

	<fieldset>
		<legend>{tr}Settings{/tr}</legend>

		{preference name=geo_enabled visible="always"}

		{if $prefs.geo_enabled eq 'y'}

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

		{/if}

	</fieldset>

	<fieldset class="admin">
		<legend>{tr}Defaults{/tr}</legend>
		{preference name=gmap_defaultx}
		{preference name=gmap_defaulty}
		{preference name=gmap_defaultz}
	</fieldset>

	{include file='admin/include_apply_bottom.tpl'}
</form>
