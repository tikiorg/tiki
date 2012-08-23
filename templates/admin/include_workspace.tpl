{* $Id *}

<form action="tiki-admin.php?page=workspace" method="post">
	<div class="heading input_submit_container" style="text-align: right">
		<input type="submit" name="workspacesetprefs" value="{tr}Change preferences{/tr}" />
	</div>

	<fieldset class="admin">
		<legend>{tr}Activate the feature{/tr}</legend>
		{preference name=workspace_ui visible="always"}
	</fieldset>

	<fieldset class="admin">
		<legend>{tr}Create a workspace{/tr}</legend>
		<a href"{service controller=workspace action=create}">{tr}Create a workspace{/tr}</a>
	</fieldset>

	<fieldset class="admin">
		<legend>{tr}Namespaces{/tr}</legend>
		{preference name=namespace_enabled}
		{preference name=namespace_separator}
	</fieldset>

	<fieldset class="admin">
		<legend>{tr}Perspective{/tr}</legend>
		{preference name=feature_perspective}
		{preference name=wikiplugin_perspective}
	</fieldset>

	<div class="heading input_submit_container" style="text-align: center">
		<input type="submit" name="workspacesetprefs" value="{tr}Change preferences{/tr}" />
	</div>
</form>
