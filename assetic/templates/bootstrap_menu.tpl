<ul class="nav">
	{foreach from=$list item=item}
		{if $item.children|count}
			<li class="dropdown">
				<a href="#" class="dropdown-toggle" data-toggle="dropdown">
					{$item.name|escape}
					<b class="caret"></b>
				</a>
				<ul class="dropdown-menu">
					{foreach from=$item.children item=sub}
						<li{if $sub.selected} class="active"{/if}><a href="{$item.url|escape}">{$sub.name|escape}</a></li>
					{/foreach}
				</ul>
			</li>
		{else}
			<li{if $item.selected} class="active"{/if}><a href="{$item.url|escape}">{$item.name|escape}</a></li>
		{/if}
	{/foreach}
</ul>
