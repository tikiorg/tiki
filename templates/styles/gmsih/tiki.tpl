{* Index we display a wiki page here *}
{include file="header.tpl"}
{if $feature_bidi eq 'y'}
<table dir="rtl" ><tr><td>
{/if}

<div id="tiki-mid">
{if $feature_top_bar eq 'y'}
<table border="0" cellpadding="0" cellspacing="0" width="100%">
<tr><td>
<div id="tiki-top">{include file="tiki-top_bar.tpl"}</div>
</td></tr></table>
{/if}

<table border="0" cellpadding="0" cellspacing="0" width="100%"><tr>
{if $feature_left_column eq 'y'}
<td id="leftcolumn">
{section name=homeix loop=$left_modules}
{$left_modules[homeix].data}
{/section}
</td>
{/if}
<td id="centercolumn">
<div id="tiki-center">
{include file=$mid}
{if $show_page_bar eq 'y'}
{include file="tiki-page_bar.tpl"}
{/if}
</div>
</td>
{if $feature_right_column eq 'y' and count($right_modules) gt 0}
<td id="rightcolumn">
{section name=homeix loop=$right_modules}
{$right_modules[homeix].data}
{/section}
</td>
{/if}
</tr></table>
</div>

{if $feature_bot_bar eq 'y'}
<div id="tiki-bot">
{include file="tiki-bot_bar.tpl"}
</div>
{/if}

{if $feature_bidi eq 'y'}
</td></tr></table>
{/if}
{include file="footer.tpl"}
