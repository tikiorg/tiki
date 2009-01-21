<div class="clearfix" id="page-bar">
	{foreach from=$controls.tabs item=link}
		{$link.button}
	{/foreach}
	{foreach from=$controls.menus key=name item=menu}
		{if $name !== 'watchgroup' and $name !== 'structwatchgroup' and $name !== 'language' and $name !== 'backlinks'}
			{foreach from=$menu.items key=n item=item}
				{if $n !== 'watch' and $n !== 'structwatch' and $n !== 'view' and n !== 'print'}
					{$item.button}
				{/if}
			{/foreach}
		{/if}
	{/foreach}
</div>
