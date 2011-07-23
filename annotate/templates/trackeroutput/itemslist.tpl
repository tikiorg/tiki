{* Note that when there in only one item it needs to be unformatted as it is often used inline in pretty trackers *}
{if $field.num > 1}
<ul>
{/if}
	{foreach from=$field.items key=id item=label}
	{if $field.num > 1}
		<li>
	{/if}
			{if $field.links}
				{object_link type=trackeritem id=$id title=$label}
			{else}
				{$label|escape}
			{/if}
	{if $field.num > 1}
		</li>
	{/if}
	{/foreach}
{if $field.num > 1}
</ul>
{/if}
