{extends 'layout_view.tpl'}

{block name="title"}
	{title}{$title|escape}{/title}
{/block}

{block name="content"}
<ul>
	{foreach from=$list item=template}
		<li>
			{$template.name|escape}
			{permission name=admin}
				<a title="{tr}Edit{/tr}" class="link service-dialog reload" href="{service controller=workspace action=edit_template id=$template.templateId}">{icon _id='wrench' alt="{tr}Edit{/tr}"}</a>
				{permission_link mode=icon type=workspace id=$template.templateId title=$template.name}
			{/permission}
		</li>
	{/foreach}
</ul>
{/block}
