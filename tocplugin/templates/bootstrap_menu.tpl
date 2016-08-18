<ul class="nav">
	{foreach from=$list item=item}
		{if !empty($item.children)}
			<li class="{$item.class|escape|default:null}{if !empty($item.selected)} active{/if}">
				<a href="#menu_option{$item.optionId|escape}" class="collapse-toggle" data-toggle="collapse">
					{tr}{$item.name}{/tr}
					<span class="caret"></span>
				</a>
				<ul id="menu_option{$item.optionId|escape}" class="nav collapse">
					{foreach from=$item.children item=sub}
						<li class="{$sub.class|escape|default:null}{if !empty($sub.selected)} active{/if}">
							<a href="{$sub.sefurl|escape}">{icon name="menuitem"} {tr}{$sub.name}{/tr}</a>
						</li>
					{/foreach}
				</ul>
			</li>
		{else}
			<li class="{$item.class|escape|default:null}{if !empty($item.selected)}active{/if}"><a href="{$item.sefurl|escape}">{tr}{$item.name}{/tr}</a></li>
		{/if}
	{/foreach}
</ul>
