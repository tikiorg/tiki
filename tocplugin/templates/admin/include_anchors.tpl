{*$Id$*}
{foreach from=$admin_icons key=page item=info}
	{if ! $info.disabled}
		<li><a href="tiki-admin.php?page={$page}" alt="{$info.title} {$info.description}" class="tips icon text-muted" title="{$info.title}|{$info.description}">
			{icon name="admin_$page"}
		</a></li>
	{/if}
{/foreach}