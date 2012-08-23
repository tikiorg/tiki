<ul>
	{foreach from=$list item=template}
		<li>
			{$template.name|escape}
			<a title="{tr}Permissions{/tr}" class="link" href="tiki-objectpermissions.php?objectName={$template.name|escape:"url"}&amp;objectType=workspacetemplate&amp;permType=workspacetemplate&amp;objectId={$template.templateId}">{icon _id='key' alt="{tr}Permissions{/tr}"}</a>
		</li>
	{/foreach}
</ul>
