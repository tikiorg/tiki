<form method="post" name="form_set_project" action="tiki-mantis-main.php">
<input type="hidden" name="action" value="setCurrentProject" />
{tr}Current Project{/tr}:&nbsp;
<select name="project_id" onchange="document.forms.form_set_project.submit();">
	{section name=ix loop=$projectOptions}
		<option value="{$projectOptions[ix].id}"{if $currentProject eq $projectOptions[ix].id} selected="selected"{/if}>{$projectOptions[ix].name}</option>
	{/section}
</select>
<input type="submit" value="switch" />
</form>
