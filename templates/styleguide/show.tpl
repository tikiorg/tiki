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

	{preference name="header_custom_css" syntax="css"}
	{preference name="header_custom_less" syntax="css"}

	<div class="sg-footer">
		<div class="container">

			<div class="footer-ui">
<!--
				<button class="btn btn-danger apply-custom-css">Apply Custom CSS</button>
				<button id="generate-var" class="btn btn-primary generate-var" download="styleguide.less">
					Generate LESS
				</button>
				<button id="generate-css" class="btn btn-primary generate-css" download="styleguide.css">
					Generate CSS
				</button>
-->
				<button id="generate-custom-css" class="btn btn-primary generate-custom-css">
					Generate Custom CSS
				</button>
				<button id="generate-custom-less" class="btn btn-primary generate-custom-less">
					Generate Custom LESS
				</button>
				<label><input class="keep-changes" type="checkbox"><span>Keep changes after refresh</span></label>
			</div>

			<div class="dropup">
				<a id="dLabel" data-target="#" href="" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
					Select a section: <span class="caret"></span>
				</a>
				<ul class="dropdown-menu" aria-labelledby="dLabel">
				</ul>
			</div>
		</div>
	</div>
{/block}
