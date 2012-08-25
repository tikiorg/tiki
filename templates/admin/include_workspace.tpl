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
		<legend>{tr}Administration{/tr}</legend>
		<a class="button service-dialog" href="{service controller=workspace action=create}">{tr}Create a workspace{/tr}</a>
		
		<div id="template-list">
		</div>
		<a class="button service-dialog reload" href="{service controller=workspace action=add_template}">{tr}Add a workspace template{/tr}</a>
	</fieldset>

	<fieldset class="admin">
		<legend>{tr}Dependencies{/tr}</legend>
		{preference name=namespace_enabled}
		{preference name=namespace_separator}
		{preference name=feature_perspective}
		{preference name=feature_categories}
		{preference name=feature_wiki}
	</fieldset>

	<div class="heading input_submit_container" style="text-align: center">
		<input type="submit" name="workspacesetprefs" value="{tr}Change preferences{/tr}" />
	</div>
</form>
{jq}
	$('#tiki-center').on('click', '.service-dialog', function () {
		$(this).serviceDialog({
			title: $(this).text(),
			success: function () {
				if ($(this).is('.reload')) {
					$('#template-list').load($.service('workspace', 'list_templates'));
				}
			}
		});

		return false;
	});
	$('#template-list').load($.service('workspace', 'list_templates'));
{/jq}
