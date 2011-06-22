{if $wp_relations|@count}
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
{else}
	{tr}No relations found.{/tr}
{/if}
