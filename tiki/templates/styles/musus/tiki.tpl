{* Index we display a wiki page here *}
{include file="header.tpl"}
{if $feature_bidi eq 'y'}
	<table dir="rtl"><tr><td>
{/if}
	{if $feature_top_bar eq 'y'}
		<div class="column-in">
			{include file="tiki-top_bar.tpl"}
		</div>
	{/if}

	{if $feature_left_column eq 'y'}
		<div id="left" class="column-in">
			{section name=homeix loop=$left_modules}
				{$left_modules[homeix].data}
			{/section}
		</div>
	{/if}

	<div id="middle" class="column-in">{include file=$mid}
		{if $show_page_bar eq 'y'}
			{include file="tiki-page_bar.tpl"}
		{/if}
	</div>

	{if $feature_right_column eq 'y'}
		<div id="right" class="column-in">
			{section name=homeix loop=$right_modules}
				{$right_modules[homeix].data}
			{/section}
		</div>
	{/if}

	<div class="cleaner">&nbsp;</div>

	{if $feature_bot_bar eq 'y'}
		<div id="tiki-bot"  class="column-in">
			{include file="tiki-bot_bar.tpl"}
		</div>
	{/if}
{if $feature_bidi eq 'y'}
	</td></tr></table>
{/if}
{include file="footer.tpl"}