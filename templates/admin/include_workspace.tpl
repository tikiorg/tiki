{* $Id$ *}
<form class="form-horizontal" action="tiki-admin.php?page=workspace" method="post">
	<input type="hidden" name="ticket" value="{$ticket|escape}">
	<div class="row">
		<div class="form-group col-lg-12">
			{if $prefs.workspace_ui eq "y"}
				<a class="btn btn-default btn-sm" href="{service controller=workspace action=list_templates}" title="{tr}List{/tr}">
					{icon name="list"} {tr}Workspace Templates{/tr}
				</a>
			{/if}
			<div class="pull-right">
				<input type="submit" class="btn btn-primary btn-sm" name="workspacesetprefs" title="{tr}Apply Changes{/tr}" value="{tr}Apply{/tr}" />
			</div>
		</div>
	</div>
	{tabset name="admin_workspaces_areas"}
		{tab name="{tr}Workspaces{/tr}"}
			<h2>{tr}Workspaces{/tr}</h2>

			<fieldset>
				<legend>{tr}Activate the feature{/tr}</legend>
				{preference name=workspace_ui visible="always"}
			</fieldset>

			<fieldset>
				<legend>{tr}Dependencies{/tr}</legend>
				{preference name=namespace_enabled}
				{preference name=namespace_separator}
				{preference name=namespace_force_links}
				{preference name=feature_perspective}
				{preference name=feature_categories}
				{preference name=feature_wiki}
			</fieldset>

			<fieldset>
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
			<h2>{tr}Areas{/tr}</h2>

			<fieldset>
				<legend>{tr}Activate the feature{/tr}</legend>
				{preference name=feature_areas}
			</fieldset>
			{if isset($error)}
				{remarksbox type="warning" title="{tr}Error{/tr}"}{$error} {tr}Nothing was updated.{/tr}{/remarksbox}
			{/if}
			<fieldset>
				<legend>{tr}Areas{/tr}</legend>
				<div class="table-responsive">
					<table class="table">
						<tr>
							<th>{tr}On{/tr}</th>
							<th colspan="2">{tr}Category{/tr}</th>
							<th>{tr}Perspectives{/tr}</th>
							<th>{tr}Exclusive{/tr}</th>
							<th>{tr}Share Common{/tr}</th>
							<th>{tr}Description{/tr}</th>
						</tr>

						{if $areas|count}
							{foreach from=$areas item=area}
								<tr class="{cycle}{if $area.enabled neq 'y'} disabled{/if}">
									<td><input type="checkbox" name="enabled[{$area.categId}]"{if $area.enabled eq 'y'} checked="checked"{/if} class="enabledChecks"></td>
									<td>{$area.categId}</td>
									<td>{$area.categName}</td>
									<td>
										{foreach from=$area.perspectives item=persp}
											<a href="tiki-edit_perspective.php?action=edit&id={$persp.perspectiveId}" title="{tr}Edit perspective{/tr} {$persp.name}">{$persp.name}</a>{if not $persp@last},{/if}
										{/foreach}
									</td>
									<td><input type="checkbox" name="exclusive[{$area.categId}]"{if $area.exclusive eq 'y'} checked="checked"{/if}{if $area.enabled neq 'y'} disabled="disabled"{/if} class="otherChecks"></td>
									<td><input type="checkbox" name="share_common[{$area.categId}]"{if $area.share_common eq 'y'} checked="checked"{/if}{if $area.enabled neq 'y'} disabled="disabled"{/if} class="otherChecks"></td>
									<td>{$area.description}</td>
								</tr>
							{/foreach}
							{jq}
								$(".enabledChecks").click(function() {
									var checked = ! $(this).prop("checked");
									$(".otherChecks", $(this).parents("tr:first")).each(function() {
										$(this).prop("disabled", checked);
									});
								});
							{/jq}
						{else}
							<td colspan="7">{tr}No areas found. Click "Update Areas" to populate this list{/tr}</td>
						{/if}
					</table>
				</div>
			</fieldset>
			{remarksbox type="info" title="{tr}Hint{/tr}"}{tr}This tab shows you an overview of categories affected by the areas feature. <br> More help here: <a href="http://doc.tiki.org/Areas" target="tikihelp">doc.tiki.org/Areas</a> {/tr}{/remarksbox}
			<div class="form-group heading input_submit_container" style="text-align: center">
				<input type="submit" class="btn btn-primary btn-sm" name="update_areas" value="{tr}Update areas{/tr}" />
			</div>
			<div class="adminoptionboxchild" id="feature_areas_childcontainer">
				{preference name=areas_root}
			</div>

		{/tab}
	{/tabset}
	<div class="row">
		<div class="form-group col-lg-12">
			<div class="text-center">
				<input type="submit" class="btn btn-primary btn-sm" name="workspacesetprefs" title="{tr}Apply Changes{/tr}" value="{tr}Apply{/tr}" />
			</div>
		</div>
	</div>
</form>
