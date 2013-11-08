<ul class="nav nav-justtified">
	{foreach from=$list item=item}
		{if $item.children|count}
			<li class="{if $item.selected} active{/if}">
				<a href="#menu_option{$item.optionId|escape}" class="collapse-toggle" data-toggle="collapse">
					{$item.name|escape}
					<b class="caret"></b>
				</a>
				<ul id="menu_option{$item.optionId|escape}" class="nav collapse">
					{foreach from=$item.children item=sub}
						<li{if $sub.selected} class="active"{/if}>
							
							<a href="{$sub.url|escape}">{glyph name="minus"} {$sub.name|escape}</a>
						</li>
					{/foreach}
				</ul>
			</li>
		{else}
			<li{if $item.selected} class="active"{/if}><a href="{$item.url|escape}">{$item.name|escape}</a></li>
		{/if}
	{/foreach}
</ul>
