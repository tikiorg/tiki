<form action="tiki-admin.php?page=freetags" method="post">
		<div class="heading input_submit_container" style="text-align: right">
			<input type="submit" value="{tr}Change preferences{/tr}" />
		</div>
		{tabset name="admin_freetags"}
			{tab name="{tr}General Settings{/tr}"}
				<fieldset>
					<legend>{tr}Freetags{/tr}{if $prefs.feature_help eq 'y'} {help url="Tags"}{/if}</legend>
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
						<a class="button" href='#Browsing' onclick="document.getElementById('freetags_normalized_valid_chars').value='a-zA-Z0-9';">{tr}Alphanumeric ASCII characters only{/tr}</a>
						({tr}No accents or special characters{/tr}.)
						<br />
						<a class="button" href='#Browsing' onclick="document.getElementById('freetags_normalized_valid_chars').value='';">{tr}Accept all charactrs{/tr}</a>
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

			{tab name="{tr}3D Tag Browser{/tr}"}
				<input type="hidden" name="freetagsset3d" />	
				{if $prefs.feature_morcego ne "y"}
					<div class="adminoptionbox">
						<div class="adminoptionlabel">
							{icon _id=information} {tr}Morcego 3D browser disabled{/tr}. <a href=" tiki-admin.php?page=features" title="features">{tr}Enable now{/tr}</a>.
						</div>
					</div>
				{else}
					<div class="adminoptionbox">
						<div class="adminoption">
							<input type="checkbox" id="freetags_feature_3d" name="freetags_feature_3d" {if $prefs.freetags_feature_3d eq 'y'}checked="checked" {/if}onclick="flip('use3d');" />
						</div>
						<div class="adminoptionlabel">
							<label for="freetags_feature_3d">{tr}Enable freetags 3D browser{/tr}</label>
						</div>
					</div>
					<br />
				{/if}
				<div id="use3d" style="display:{if $prefs.freetags_feature_3d eq 'y'}block{else}none{/if};">
					<fieldset>
						<legend>{tr}General{/tr}</legend>
						<div class="adminoptionbox">
							<div class="adminoptionlabel">
								<label for="freetags_3d_width">{tr}Browser width{/tr}:</label>
								<input type="text" name="freetags_3d_width" id="freetags_3d_width" value="{$prefs.freetags_3d_width|escape}" size="3" />
							</div>
						</div>
						<div class="adminoptionbox">
							<div class="adminoptionlabel">
								<label for="freetags_3d_height">{tr}Browser height{/tr}: </label>
								<input type="text" name="freetags_3d_height" id="freetags_3d_height" value="{$prefs.freetags_3d_height|escape}" size="3" />
							</div>
						</div>
					</fieldset>
						<fieldset>
						<legend>{tr}Graph appearance{/tr}</legend>
						<div class="adminoptionbox">
							<div class="adminoptionlabel">
								<label for="freetags_3d_navigation_depth">{tr}Navigation depth{/tr}: <input type="text" name="freetags_3d_navigation_depth" id="freetags_3d_navigation_depth" value="{$prefs.freetags_3d_navigation_depth|escape}" size="2" /></label>
							</div>
						</div>
						<div class="adminoptionbox">
							<div class="adminoptionlabel">
								<label for="freetags_3d_node_size">{tr}Node size{/tr}: </label>
								<input type="text" name="freetags_3d_node_size" id="freetags_3d_node_size" value="{$prefs.freetags_3d_node_size}" size="2" />
							</div>
						</div>
						<div class="adminoptionbox">
							<div class="adminoptionlabel">
								<label for="freetags_3d_text_size">{tr}Text size{/tr}: </label>
								<input type="text" name="freetags_3d_text_size" id="freetags_3d_text_size" value="{$prefs.freetags_3d_text_size}" size="3" />
							</div>
						</div>
						<div class="adminoptionbox">
							<div class="adminoptionlabel">
								<label for="freetags_3d_spring_size">{tr}Spring (connection) size{/tr}: <input type="text" name="freetags_3d_spring_size" id="freetags_3d_spring_size" value="{$prefs.freetags_3d_spring_size}" size="3" /></label>
							</div>
						</div>
						<div class="adminoptionbox">
							<div class="adminoptionlabel">
								<label for="freetags_3d_existing_page_color">{tr}Node color{/tr}: <input type="text" name="freetags_3d_existing_page_color" id="freetags_3d_existing_page_color" value="{$prefs.freetags_3d_existing_page_color|escape}" size="7" /></label>
							</div>
						</div>
					</fieldset>

					<fieldset>
						<legend>{tr}Camera settinsg{/tr}</legend>
						<div class="adminoptionbox">
							<div class="adminoption">
								<input type="checkbox" id="freetags_3d_adjust_camera" name="freetags_3d_adjust_camera" {if $prefs.freetags_3d_adjust_camera eq 'true'}checked="checked"{/if} />
							</div>
							<div class="adminoptionlabel">
								<label for="freetags_3d_adjust_camera">{tr}Camera distance adjusted relative to nearest node{/tr}.</label>
							</div>
						</div>
						<div class="adminoptionbox">
							<div class="adminoptionlabel">
								<label for="freetags_3d_camera_distance">{tr}Camera distance{/tr}: </label>
								<input type="text" name="freetags_3d_camera_distance" id="freetags_3d_camera_distance" value="{$prefs.freetags_3d_camera_distance}" size="3" />
							</div>
						</div>
						<div class="adminoptionbox">
							<div class="adminoptionlabel">
								<label for="freetags_3d_fov">{tr}Field of view{/tr}: </label>
								<input type="text" name="freetags_3d_fov" id="freetags_3d_fov" value="{$prefs.freetags_3d_fov}" size="3" />
							</div>
						</div>
						<div class="adminoptionbox">
							<div class="adminoptionlabel">
								<label for="freetags_3d_feed_animation_interval">{tr}Feed animation interval (milisecs){/tr}: <input type="text" id="freetags_3d_feed_animation_interval" name="freetags_3d_feed_animation_interval" value="{$prefs.freetags_3d_feed_animation_interval|escape}" size="4" /></label>
							</div>
						</div>
					</fieldset>

					<fieldset>
						<legend>{tr}Physics engine{/tr}</legend>
						<div class="adminoptionbox">
							<div class="adminoptionlabel">
								<label for="freetags_3d_friction_constant">{tr}Friction constant{/tr}:</label>
								<input type="text" name="freetags_3d_friction_constant" id="freetags_3d_friction_constant" value="{$prefs.freetags_3d_friction_constant}" size="7" />
							</div>
						</div>
						<div class="adminoptionbox">
							<div class="adminoptionlabel">
								<label for="freetags_3d_elastic_constant">{tr}Elastic constant{/tr}: </label>
								<input type="text" name="freetags_3d_elastic_constant" id="freetags_3d_elastic_constant" value="{$prefs.freetags_3d_elastic_constant}" size="7" />
							</div>
						</div>
						<div class="adminoptionbox">
							<div class="adminoptionlabel">
								<label for="freetags_3d_eletrostatic_constant">{tr}Eletrostatic constant{/tr}: </label>
								<input type="text" name="freetags_3d_eletrostatic_constant" id="freetags_3d_eletrostatic_constant" value="{$prefs.freetags_3d_eletrostatic_constant}" size="7" />
							</div>
						</div>
						<div class="adminoptionbox">
							<div class="adminoptionlabel">
								<label for="freetags_3d_node_mass">{tr}Node mass{/tr}: </label>
								<input type="text" name="freetags_3d_node_mass" id="freetags_3d_node_mass" value="{$prefs.freetags_3d_node_mass}" size="7" />
							</div>
						</div>
						<div class="adminoptionbox">
							<div class="adminoptionlabel">
								<label for="freetags_3d_node_charge">{tr}Node charge{/tr}: </label>
								<input type="text" name="freetags_3d_node_charge" id="freetags_3d_node_charge" value="{$prefs.freetags_3d_node_charge}" size="7" />
							</div>
						</div>
					</fieldset>
				</div>
			{/tab}
		{/tabset}
		<div class="heading input_submit_container" style="text-align: center">
			<input type="submit" value="{tr}Change preferences{/tr}" />
		</div>
</form>
