{include file="header.tpl"}
{if $feature_bidi eq 'y'}
	<!-- <table dir="rtl" ><tr><td> -->
{/if}

<div id="tiki-main">
	{if $feature_top_bar eq 'y'}
		{include file="tiki-top_bar.tpl"}
	{/if}
	<div id="tiki-mid">
	{if $feature_left_column eq 'y'}
		<div id="left">
			{section name=homeix loop=$left_modules}
				{$left_modules[homeix].data}
			{/section}
		</div>
	{/if}
	<div id="tiki-center"><br />
		<div class="cbox">
			<div class="cbox-title">{tr}Error{/tr}</div>
			<div class="cbox-data">
				{$msg}<br /><br />
				{if $page and ($tiki_p_admin eq 'y' or  $tiki_p_admin_wiki eq 'y')}
					<a href="tiki-editpage.php?page={$page}">{tr}Create this page{/tr}</a><br /><br />
				{/if}
				<a href="javascript:history.back()">{tr}Go back{/tr}</a><br /><br />
				<a href="{$tikiIndex}">{tr}Return to home page{/tr}</a>
				</div>
			</div>
		</div>
	</td>
	{if $feature_right_column eq 'y'}
		<div id="right">
			{section name=homeix loop=$right_modules}
				{$right_modules[homeix].data}
			{/section}
		</div>
	{/if}
	{if $feature_bot_bar eq 'y'}
		<div id="tiki-bot">
			{include file="tiki-bot_bar.tpl"}
		</div>
	{/if}
</div>

{if $feature_bidi eq 'y'}
	</td></tr></table>
{/if}
{include file="footer.tpl"}