<ul>
	{foreach from=$field.items key=id item=label}
		<li>
			{if $field.links}
				{object_link type=trackeritem id=$id title=$label}
			{else}
				{$label|escape}
			{/if}
		</li>
	{/foreach}
</ul>
