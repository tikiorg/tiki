<ul>
	{foreach from=$list item=template}
		<li>
			{$template.name|escape}
			<a title="{tr}Permissions{/tr}" class="link" href="tiki-objectpermissions.php?objectName={$template.name|escape:"url"}&amp;objectType=workspace&amp;permType=workspace&amp;objectId={$template.templateId}">{icon _id='key' alt="{tr}Permissions{/tr}"}</a>
		</li>
	{/foreach}
</ul>
