{* $Id$ *}
<div class="panel {if $permsType eq 'object'}panel-warning{else}panel-default{/if}">
	<div class="panel-body">
		{capture name=permType}{$objectType}s{/capture}
		<a title="{tr}Permissions{/tr}" class="link {if $permsType eq 'object'}btn btn-warning btn-sm{/if}" href="tiki-objectpermissions.php?objectName={$name|escape:"url"}&amp;objectType={$objectType}&amp;permType={$smarty.capture.permType}&amp;objectId={$objectId}">
		{if $permsType eq 'category'}
			{icon name="permission"}
			</a>
			{tr}No individual permissions, category permissions apply{/tr}
		{elseif $permsType eq 'object'}
			{icon name="permission"}
			</a>
			{tr}There are individual permissions set for this object{/tr}
		{else}
			{icon name="permission"}
			</a>
			{tr}No individual permissions. Global permissions apply.{/tr}
		{/if}
	</div>
</div>
