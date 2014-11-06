{*$Id$*}
{foreach from=$icons key=page item=info}
	{if ! $info.disabled and $info.icon}
		<li><a href="tiki-admin.php?page={$page}" alt="{$info.title}" class="tips icon text-muted" title="{$info.title}|{$info.description}">
			{icon name="admin_$page"}
		</a></li>
	{/if}
{/foreach}