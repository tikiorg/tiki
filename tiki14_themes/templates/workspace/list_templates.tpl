{extends 'layout_view.tpl'}

{block name="title"}
	{title help="" admpage="workspace"}{$title|escape}{/title}
{/block}

{block name="navigation"}
	<div class="navbar">
		<a class="btn btn-default" href="{bootstrap_modal controller=workspace action=add_template}">
			{icon name="create"} {tr}Create Workspace Template{/tr}
		</a>
		<a class="btn btn-default" href="{bootstrap_modal controller=workspace action=create}">
			{icon name="create"} {tr}Create Workspace{/tr}
		</a>
	</div>
{/block}

{block name="content"}
	<div class="table-responsive">
		<table class="table table-hover">
			<tr>
				<th>{self_link _sort_arg='sort_mode' _sort_field='id'}{tr}Id{/tr}{/self_link}</th>
				<th>{self_link _sort_arg='sort_mode' _sort_field='name'}{tr}Name{/tr}{/self_link}</th>
				<th>{tr}Action{/tr}</th>
			</tr>
			{foreach from=$list item=template}
				<tr>
					<td>
						{$template.templateId}
					</td>
					<td>
						{$template.name|escape}
					</td>
					<td>
						{permission name=admin}
							<a title="{tr}Edit{/tr}" class="btn btn-default btn-sm service-dialog reload" href="{service controller=workspace action=edit_template id=$template.templateId}">{icon name="edit"}</a>
							<span class="btn btn-default btn-sm">
								{permission_link mode=icon type=workspace id=$template.templateId title=$template.name}
							</span>
						{/permission}
					</td>
				</tr>
			{/foreach}
		</table>
	</div>
{/block}
