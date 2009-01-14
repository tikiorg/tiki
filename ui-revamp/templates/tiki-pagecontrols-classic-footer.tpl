<div class="clearfix" id="page-bar">
	{foreach from=$controls.tabs item=link}
		{$link.button}
	{/foreach}
	{foreach from=$controls.menus key=name item=menu}
		{if $name neq 'watchgroup' and $name neq 'structwatchgroup' and $name neq 'language' and $name neq 'backlinks'}
			{foreach from=$menu.items key=n item=item}
				{if $n neq 'watch' and $n neq 'structwatch' and $n neq 'view' and n neq 'print'}
					{$item.button}
				{/if}
			{/foreach}
		{/if}
	{/foreach}
</div>
