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
		<legend>{tr}Namespaces{/tr}</legend>
		{preference name=namespace_enabled}
		{preference name=namespace_separator}
		{preference name=namespace_default}
	</fieldset>


	<fieldset class="admin">
		<legend>{tr}Workspaces{/tr}</legend>
		{preference name=worspace_root_category}
	</fieldset>

	<fieldset class="admin">
		<legend>{tr}Perspective{/tr}</legend>
		{preference name=feature_perspective}
		{preference name=wikiplugin_perspective}
	</fieldset>

	<fieldset>
		<legend>{tr}Multi-domain{/tr}</legend>
		{preference name=multidomain_active}
		{preference name=multidomain_switchdomain}
		<div class="adminoptionboxchild" id="multidomain_active_childcontainer">
			{preference name=multidomain_config}
		</div>
	</fieldset>

	<fieldset>
		<legend>{tr}Category{/tr}</legend>
		{preference name=category_jail}
		{preference name=category_defaults}
	</fieldset>


<div class="heading input_submit_container" style="text-align: center">
	<input type="submit" name="workspacesetprefs" value="{tr}Change preferences{/tr}" />
</div>
</form>
