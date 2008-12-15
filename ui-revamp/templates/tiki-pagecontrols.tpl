<div class="pagecontrols">
	<div class="top">
		<h1>{$controls.heading}</h1>
		<div class="pageactions">
			<ul class="cssmenu">
				{foreach from=$controls.menus item=menu}
					<li><a href="#" title="{$menu} items">{$menu}</a>
						<ul>
							{foreach from=$menu.items item=item}
								{if $item.selected}
								<li class="selected">{$item}</li>
								{else}
								<li>{$item}</li>
								{/if}
							{/foreach}
						</ul>
					</li>
				{/foreach}
				</ul>
			</div>
		</div>
	<div class="tabs">
		{foreach from=$controls.tabs item=tab}
			<span class="tabmark {if $tab.selected}tabactive{else}tabinactive{/if}">
				{$tab}
			</span>
		{/foreach}
	</div>
</div>
