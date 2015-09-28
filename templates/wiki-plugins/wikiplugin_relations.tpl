{if $wp_relations|@count}
	{if $wp_singlelist}
		<ul>
			{foreach from=$wp_relations.singlelist item=object}
				<li>{object_link type=$object.type id=$object.object}</li>
			{/foreach}
		</ul>
	{else}
		<ul>
			{foreach from=$wp_relations key=label item=list}
				<li>
					{$label|escape}
					<ul>
						{foreach from=$list item=object}
							<li>{object_link type=$object.type id=$object.object}</li>
						{/foreach}
					</ul>
				</li>
			{/foreach}
		</ul>
	{/if}
{else}
	{tr}{$wp_emptymsg}{/tr}
{/if}
