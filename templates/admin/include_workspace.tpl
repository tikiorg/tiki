{* $Id$ *}
<form action="tiki-admin.php?page=workspace" method="post">
	<div class="heading input_submit_container" style="text-align: right">
		<input type="submit" name="workspacesetprefs" value="{tr}Change preferences{/tr}" />
	</div>
	{tabset}
		{tab name="{tr}Workspaces{/tr}"}

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

			<fieldset class="admin">
				<legend>{tr}Advanced{/tr}</legend>
				{preference name=workspace_root_category}
			</fieldset>

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
		{/tab}
		{tab name="{tr}Areas{/tr}"}

			<fieldset class="admin">
				<legend>{tr}Activate the feature{/tr}</legend>
				{preference name=feature_areas}
			</fieldset>
			{if isset($error)}
				{remarksbox type="warning" title="{tr}Error{/tr}"}{$error} {tr}Nothing was updated.{/tr}{/remarksbox}
			{/if}
			<fieldset class="admin">
				<legend>{tr}Areas{/tr}</legend>
				<table class="normal">
					<tr>
						<th colspan="2">{tr}Category{/tr}</th>
						<th>{tr}Perspectives{/tr}</th>
						<th>{tr}Description{/tr}</th>
					</tr>
					{cycle values="odd,even" print=false}
					{if $no_area eq '0'}
						{foreach from=$areas item=area}
							<tr class="{cycle}">
								<td>{$area.categId}</td>
								<td>{$area.categName}</td>
								<td>
									{foreach from=$area.perspectives item=persp}
										<a href="tiki-edit_perspective.php?action=edit&id={$persp.perspectiveId}" title="{tr}Edit perspective{/tr} {$persp.name}">{$persp.name}</a>,
									{/foreach}
								</td>
								<td>{$area.description}</td>
							</tr>
						{/foreach}
					{else}
						<th class="{cycle}" colspan="4">{tr}No category found in area{/tr}</th>
					{/if}
				</table>
			</fieldset>
			{remarksbox type="info" title="{tr}Hint{/tr}"}{tr}This tab shows you an overview of categories affected by the areas feature. The category with the smallest id should be the category set as areas root in the settings tab. If not so, update this overview with the button below.{/tr}{/remarksbox}
			<div class="heading input_submit_container" style="text-align: center">
				<input type="submit" name="update_areas" value="{tr}Update areas{/tr}" />
			</div>
			<div class="adminoptionboxchild" id="feature_areas_childcontainer">
				{preference name=areas_root}
			</div>

		{/tab}
	{/tabset}
	<div class="heading input_submit_container" style="text-align: center">
		<input type="submit" name="workspacesetprefs" value="{tr}Change preferences{/tr}" />
	</div>
</form>
