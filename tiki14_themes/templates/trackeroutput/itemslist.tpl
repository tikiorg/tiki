{* Note that when there in only one item it needs to be unformatted as it is often used inline in pretty trackers *}
{if $data.num > 1}
<ul class="arrowLinks">
	{foreach from=$data.items key=id item=label}
		<li>
			{if $data.links}
				{object_link type=trackeritem id=$id title=$label}
			{elseif $data.raw}
				{$label}
			{else}
				{$label|escape}
			{/if}
		</li>
	{/foreach}
</ul>
{elseif $data.num eq 1}
{strip}
	{foreach from=$data.items key=id item=label}
		{if $data.links}
			{object_link type=trackeritem id=$id title=$label}
		{elseif $data.raw}
			{$label}
		{else}
			{$label|escape}
		{/if}
	{/foreach}
{/strip}
{/if}
