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
			<span class="tabmark {if $tab.selected}tabactive{else}tabinactive{/if}">
				{$tab}
			</span>
		{/foreach}
	</div>
</div>
