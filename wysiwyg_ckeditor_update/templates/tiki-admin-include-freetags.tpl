<form action="tiki-admin.php?page=freetags" method="post">
	<div class="heading input_submit_container" style="text-align: right">
		<input type="submit" value="{tr}Change preferences{/tr}" />
	</div>
	{tabset name="admin_freetags"}
		{tab name="{tr}General Settings{/tr}"}
			{if $prefs.feature_freetags ne 'y'}{preference name=feature_freetags}{/if}
			<fieldset>
				<legend>{tr}Freetags{/tr}{help url="Tags"}</legend>
				<input type="hidden" name="freetagsfeatures" />
				{preference name=freetags_browse_show_cloud}

				<div class="adminoptionboxchild" id="freetags_browse_show_cloud_childcontainer">
					{preference name=freetags_browse_amount_tags_in_cloud}
				</div>

				{preference name=freetags_show_middle}
				{preference name=freetags_preload_random_search}
				<em>{tr}When arriving on <a href="tiki-browse_freetags.php">freetag search page</a>{/tr}.</em>

				<div class="adminoptionbox">
					<div class="adminoptionlabel">
						<label for="freetags_cloud_colors">{tr}Random tag cloud colors{/tr}:</label>
						<input type="text" name="freetags_cloud_colors" id="freetags_cloud_colors" value="{foreach from=$prefs.freetags_cloud_colors item=color name=colors}{$color}{if !$smarty.foreach.colors.last},{/if}{/foreach}" />
						<br />
						<em>{tr}Separate colors with a comma (,){/tr}.</em>
					</div>
				</div>

				{preference name=freetags_browse_amount_tags_suggestion}
				{preference name=freetags_normalized_valid_chars}
				<div class="adminoptionboxchild">
					<a class="button" href='#Browsing' onclick="$('input[name=freetags_normalized_valid_chars]').val('a-zA-Z0-9');return false;">{tr}Alphanumeric ASCII characters only{/tr}</a>
					({tr}No accents or special characters{/tr}.)
					<br />
					<a class="button" href='#Browsing' onclick="$('input[name=freetags_normalized_valid_chars]').val('');return false;">{tr}Accept all characters{/tr}</a>
				</div>
				{preference name=freetags_lowercase_only}
				{preference name=freetags_multilingual}
				{preference name=morelikethis_algorithm}
				{preference name=morelikethis_basic_mincommon}
			</fieldset>

			<fieldset>
				<legend>{tr}Tag Management{/tr}</legend>
				<div class="adminoptionbox">
					<div class="adminoptionlabel">
						<div align="center">
							<input type="submit" value="{tr}Cleanup unused tags{/tr}" name="cleanup" />
						</div>
					</div>
				</div>
			</fieldset>
		{/tab}

		{if $prefs.feature_morcego eq 'y'}
			{tab name="{tr}3D Tag Browser Configuration{/tr}"}
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
	<div class="heading input_submit_container" style="text-align: center">
		<input type="submit" value="{tr}Change preferences{/tr}" />
	</div>
</form>
