<div class="simplebox">
	{capture name=permType}{$objectType}s{/capture}
	<a title="{tr}Permissions{/tr}" class="link" href="tiki-objectpermissions.php?objectName={$name|escape:"url"}&amp;objectType={$objectType}&amp;permType={$smarty.capture.permType}&amp;objectId={$objectId}">
		{if $permsType eq 'category'}
			{icon _id='key' alt="{tr}Permissions{/tr}"}
			</a>
			{tr}No individual permissions, category permissions apply{/tr}
		{elseif $permsType eq 'object'}
			{icon _id='key' alt="{tr}Permissions{/tr}"}
			</a>
			{tr}There are individual permissions set for this tracker{/tr}
		{else}
			{icon _id='key_active' alt="{tr}Active Perms{/tr}"}
			</a>
			{tr}No individual permissions. Global permissions apply.{/tr}
		{/if}
</div>