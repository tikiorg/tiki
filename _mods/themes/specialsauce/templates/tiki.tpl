{include file="header.tpl"}{* This must be included as the first thing in a document to be XML compliant *}
{* Main index template. Please leave the order of the columns as is.  They should appear from top to bottom as left, right, then center. *}
	{if $feature_bidi eq 'y'}<div dir="rtl">{/if}
{* top *}	{if $feature_top_bar eq 'y'}
			<div id="top"><div class="column-in">
				{include file="tiki-top_bar.tpl"}
			</div></div>
		{/if}{* 			end top *}
{* left *}	{if $feature_left_column eq 'y'}
			<div id="left">
				{section name=homeix loop=$left_modules}
					{$left_modules[homeix].data}
				{/section}
			</div>
		{/if}{* 			end left *}
{* right *}	{if $feature_right_column eq 'y'}
			<div id="right">
				{section name=homeix loop=$right_modules}
					{$right_modules[homeix].data}
				{/section}
			</div>
		{/if}{* 			end right *}
{* middle *}	<div id="middle"><div class="column-in">
			{include file=$mid}
			{if $show_page_bar eq 'y'}
				{include file="tiki-page_bar.tpl"}
			{/if}
		</div></div>{* 			end middle *}
		{if $feature_bidi eq 'y'}</div>{/if}
		{* 				end rtl div *}
{* bottom *}	{if $feature_bot_bar eq 'y'}
			<div id="tiki-bot">
				{include file="tiki-bot_bar.tpl"}
			</div>
		{/if}{* 			end bottom bar *}
{include file="footer.tpl"}
