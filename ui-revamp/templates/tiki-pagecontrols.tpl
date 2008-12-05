<div class="pagecontrols">
	<div class="top">
		<h1>{$controls.heading}</h1>
		{foreach from=$controls.menus item=menu}
			<div class="menu">
				<div>{$menu}</div>
				<ul>
					{foreach from=$menu.items item=item}
						<li>{$item}</li>
					{/foreach}
				</ul>
			</div>
		{/foreach}
	</div>
	<div class="tabs">
		{foreach from=$controls.tabs item=tab}
			<div class="tab {if $tab.selected} active{/if}">
				{$tab}
			</div>
		{/foreach}
	</div>
</div>
