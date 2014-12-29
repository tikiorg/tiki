<ul class="nav navbar-nav">
	{foreach from=$list item=item}
		{if $item.children|count}
			<li class="dropdown{if $item.selected} active{/if}">
				<a href="#" class="dropdown-toggle" data-toggle="dropdown">
					{tr}{$item.name|escape}{/tr}
					<b class="caret"></b>
				</a>
				<ul class="dropdown-menu">
					{foreach from=$item.children item=sub}
						<li{if $sub.selected} class="active"{/if}><a href="{$sub.sefurl|escape}">{tr}{$sub.name|escape}{/tr}</a></li>
					{/foreach}
				</ul>
			</li>
		{else}
			<li{if $item.selected} class="active"{/if}><a href="{$item.sefurl|escape}">{tr}{$item.name|escape}{/tr}</a></li>
		{/if}
	{/foreach}
</ul>
