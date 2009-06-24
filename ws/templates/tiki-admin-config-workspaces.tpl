{remarksbox type="info" title="{tr}You must to configure Workspaces before to get ready!{/tr}"}
	{if $warning}
		{$warning}
	{else}
		{"{tr}Please select one option bellow. Remember this step is very important, so be careful when you select the workspace category container, because this will destroy any category o whatever inside it if the names are equal! If you don't know what are you doing, select the default option.{/tr}"}
	{/if}
{/remarksbox}

	<div class="cbox">
	<div class="cbox-title">
		Workspaces Configuration
	</div>
	<form class="admin" method="post" action="tiki-admin.php?page=workspaces">
	<div style="padding:0.5em;clear:both">
		<div style="float:left;margin-righ:1em;">
			<input type="radio" id="selected_workspace_name" name="selected_radio" checked="checked" value="selected_workspace_holder" />
		</div>
		<div>
			<label for="selected_workspace">{tr}Create a new Workspace container, called 'Workspaces' (Default option){/tr}</label>
		</div>
	</div>
	{if $prefs.feature_categories eq 'y'}
	<div style="padding:0.5em;clear:both">
		<div style="float:left;margin-right:1em;">
			<input type="radio" id="selected_used_category"	name="selected_radio" value="selected_used_category" />
		</div>
		<div>
			<label for="selected_used_category">{tr}Select a Category and convert it to a Workspace container{/tr}</label>
			<select id="workspaces_category_list">
				<option title="Example 1" value="1">Category example 1</option>
				<option title="Example 2 " value="2">Category example 2</option>
				<option title="Example 3" value="3">Category example 3</option>
			</select>
			{if $prefs.feature_help eq 'y'} {help url="Workspaces" desc="{tr}Workspaces manual page{/tr}"}{/if} <br />
		</div>
	</div>
	{/if}
	<div style="padding:0.5em;clear:both">
		<div style="float:left;margin-right:1em;">
			<input type="radio" id="selected_new_container" name="selected_radio" value="selected_new_container"/>
		</div>
		<div>
			<label for="selected_new_container">{tr}Create a new Workspace container{/tr}</label>
			<input type="text" id="new_container_name" name="new_container_name" />
			{if $prefs.feature_help eq 'y'} {help url="Workpaces" desc="{tr}Workspaces manual page{/tr}"}{/if} <br />
		</div>
	</div>
	<div class="heading input_submit_container" style="text-align: center;padding:1em;">
		<input type="submit" name="save" value="{tr}Apply{/tr}" />
	</div>
	</form>
	</div>
