<form action="tiki-admin.php?page=freetags" method="post">
	<div class="row">
		<div class="form-group col-lg-12 clearfix">
			<a role="button" class="btn btn-default btn-sm" href="tiki-browse_freetags.php" title="{tr}List{/tr}">
				{icon name="list"} {tr}Tags{/tr}
			</a>
			<div class="pull-right">
				<input type="submit" class="btn btn-primary btn-sm" title="{tr}Apply Changes{/tr}" value="{tr}Apply{/tr}">
			</div>
		</div>
	</div>

	{tabset name="admin_freetags"}
		{tab name="{tr}General Settings{/tr}"}
			<h2>{tr}General Settings{/tr}</h2>

			<fieldset class="table">
				<legend>{tr}Activate the feature{/tr}</legend>
				{preference name=feature_freetags visible="always"}
			</fieldset>

			<fieldset class="table">
				<legend>{tr}Plugins{/tr}</legend>
				{preference name=wikiplugin_freetagged}
				{preference name=wikiplugin_addfreetag}
			</fieldset>

			<fieldset>
				<legend>{tr}Tags{/tr}{help url="Tags"}</legend>
				<input type="hidden" name="freetagsfeatures" />
				{preference name=freetags_browse_show_cloud}

				<div class="adminoptionboxchild" id="freetags_browse_show_cloud_childcontainer">
					{preference name=freetags_browse_amount_tags_in_cloud}
				</div>

				{preference name=freetags_3d_autoload}
				{preference name=freetags_show_middle}

				<div class="adminoptionbox">
					<div class="adminoptionlabel">
						<label for="freetags_cloud_colors">{tr}Random tag cloud colors:{/tr}</label>
						<input type="text" name="freetags_cloud_colors" id="freetags_cloud_colors" value="{foreach from=$prefs.freetags_cloud_colors item=color name=colors}{$color}{if !$smarty.foreach.colors.last},{/if}{/foreach}" />
						<br>
						<em>{tr}Separate colors with a comma (,){/tr}.</em>
					</div>
				</div>

				{preference name=freetags_browse_amount_tags_suggestion}
				{preference name=freetags_normalized_valid_chars}
				<div class="adminoptionboxchild">
					<a class="button" href='#Browsing' onclick="$('input[name=freetags_normalized_valid_chars]').val('a-zA-Z0-9');return false;">{tr}Alphanumeric ASCII characters only{/tr}</a>
					({tr}No accents or special characters{/tr}.)
					<br>
					<a class="button" href='#Browsing' onclick="$('input[name=freetags_normalized_valid_chars]').val('');return false;">{tr}Accept all characters{/tr}</a>
				</div>
				{preference name=freetags_lowercase_only}
				{preference name=freetags_multilingual}
				{preference name=morelikethis_algorithm}
				{preference name=morelikethis_basic_mincommon}
			</fieldset>

			<fieldset class="admin">
				<legend>{tr}Freetag search page{/tr}</legend>
				{preference name=freetags_sort_mode}
				{preference name=freetags_preload_random_search}
				<em>{tr}When arriving on <a href="tiki-browse_freetags.php">freetag search page</a>{/tr}.</em>
			</fieldset>

			<fieldset>
				<legend>{tr}Tag Management{/tr}</legend>
				<div class="adminoptionbox">
					<div class="adminoptionlabel">
						<div align="center">
							<input type="submit" class="btn btn-default btn-sm" value="{tr}Cleanup unused tags{/tr}" name="cleanup" />
						</div>
					</div>
				</div>
			</fieldset>
		{/tab}

		{if $prefs.feature_morcego eq 'y'}
			{tab name="{tr}3D Tag Browser Configuration{/tr}"}
				<h2>{tr}3D Tag Browser Configuration{/tr}</h2>
				{preference name=freetags_feature_3d}
				<div id=freetags_feature_3d_childcontainer>
					<fieldset>
						<legend>{tr}General{/tr}</legend>
						{preference name=freetags_3d_width}
						{preference name=freetags_3d_height}
					</fieldset>
					<fieldset>
						<legend>{tr}Graph appearance{/tr}</legend>
						{preference name=freetags_3d_navigation_depth}
						{preference name=freetags_3d_node_size}
						{preference name=freetags_3d_text_size}
						{preference name=freetags_3d_spring_size}
						{preference name=freetags_3d_existing_page_color}
						{preference name=freetags_3d_missing_page_color}
					</fieldset>
					<fieldset>
						<legend>{tr}Camera settings{/tr}</legend>
						{preference name=freetags_3d_adjust_camera}
						{preference name=freetags_3d_camera_distance}
						{preference name=freetags_3d_fov}
						{preference name=freetags_3d_feed_animation_interval}
					</fieldset>
					<fieldset>
						<legend>{tr}Physics engine{/tr}</legend>
						{preference name=freetags_3d_friction_constant}
						{preference name=freetags_3d_elastic_constant}
						{preference name=freetags_3d_eletrostatic_constant}
						{preference name=freetags_3d_node_mass}
						{preference name=freetags_3d_node_charge}
					</fieldset>
				</div>
			{/tab}
		{/if}

	{/tabset}

	<br>{* I cheated. *}
	<div class="row">
		<div class="form-group col-lg-12 clearfix">
			<div class="text-center">
				<input type="submit" class="btn btn-primary btn-sm" title="{tr}Apply Changes{/tr}" value="{tr}Apply{/tr}">
			</div>
		</div>
	</div>
</form>
