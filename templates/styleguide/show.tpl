{extends 'layout_view.tpl'}

{block name="title"}
	{title}{$title|escape}{/title}
{/block}

{block name="content"}
	<div class="style-guide">
		{if in_array('colors', $sections)}{include file='templates/styleguide/sections/colors.tpl'}{/if}
		{if in_array('fonts', $sections)}{include file='templates/styleguide/sections/fonts.tpl'}{/if}
		{if in_array('headings', $sections)}{include file='templates/styleguide/sections/headings.tpl'}{/if}
		{if in_array('tables', $sections)}{include file='templates/styleguide/sections/tables.tpl'}{/if}
		{if in_array('buttons', $sections)}{include file='templates/styleguide/sections/buttons.tpl'}{/if}
		{if in_array('forms', $sections)}{include file='templates/styleguide/sections/forms.tpl'}{/if}
		{if in_array('lists', $sections)}{include file='templates/styleguide/sections/lists.tpl'}{/if}
		{if in_array('navbars', $sections)}{include file='templates/styleguide/sections/navbars.tpl'}{/if}
		{if in_array('dropdowns', $sections)}{include file='templates/styleguide/sections/dropdowns.tpl'}{/if}
		{if in_array('tabs', $sections)}{include file='templates/styleguide/sections/tabs.tpl'}{/if}
		{if in_array('alerts', $sections)}{include file='templates/styleguide/sections/alerts.tpl'}{/if}
		{if in_array('icons', $sections)}{include file='templates/styleguide/sections/icons.tpl'}{/if}
	</div>
{/block}
