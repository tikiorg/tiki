{* $Id: $ *}
{if $welcome}
{remarksbox type="info" title="{tr}You must to configure Workspaces before to get ready!{/tr}"}
	{$welcome}
{/remarksbox}
{/if}

<div class="cbox">
<div class="cbox-title">
Workspaces Configuration
</div>
<form class="admin" method="post" action="tiki-admin.php?page=workspaces">
<div style="padding:0.5em;clear:both">
	<div style="float:left;margin-right:1em;">
		<input type="radio" id="feature_workspaces" name="feature_workspaces" checked="checked"/>
	</div>
	<div>
		<label for="feature_workspaces">{tr}Select a Category and convert it to a Workspace container{/tr}</label>
		<select id="workspaces_category_list">
			<option title="Example 1" value="1">Category example 1</option>
			<option title="Example 2 " value="2">Category example 2</option>
			<option title="Example 3" value="3">Category example 3</option>
		</select>
		{if $prefs.feature_help eq 'y'} {help url="Workspaces" desc="{tr}Workspaces manual page{/tr}"}{/if} <br />
	</div>
</div>
<div style="padding:0.5em;clear:both">
	<div style="float:left;margin-right:1em;">
		<input type="radio" id="feature_workspaces_new_container_selection" name="feature_workspaces" />
	</div>
	<div>
		<label for="feature_sefurl_filter">{tr}Create a new Workspace container{/tr}</label>
		<input type="text" id="feature_workspaces_new_container" name="feature_workspaces_new_container" />
		{if $prefs.feature_help eq 'y'} {help url="Workpaces" desc="{tr}Workspaces manual page{/tr}"}{/if} <br />
	</div>
</div>
<div class="heading input_submit_container" style="text-align: center;padding:1em;">
	 <input type="submit" name="save" value="{tr}Apply{/tr}" />
</div>
</form>
</div>
