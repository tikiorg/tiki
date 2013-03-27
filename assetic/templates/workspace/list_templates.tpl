<ul>
	{foreach from=$list item=template}
		<li>
			{$template.name|escape}
			{permission name=admin}
				<a title="{tr}Edit{/tr}" class="link service-dialog reload" href="{service controller=workspace action=edit_template id=$template.templateId}">{icon _id='wrench' alt="{tr}Edit{/tr}"}</a>
				<a title="{tr}Permissions{/tr}" class="link" href="tiki-objectpermissions.php?objectName={$template.name|escape:"url"}&amp;objectType=workspace&amp;permType=workspace&amp;objectId={$template.templateId}">{icon _id='key' alt="{tr}Permissions{/tr}"}</a>
			{/permission}
		</li>
	{/foreach}
</ul>
