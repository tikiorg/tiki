{include file="header.tpl"}

{assign var=cols value=1}
{assign var=rcol value="n"}
{assign var=lcol value="n"}
{if $feature_left_column eq 'y' and count($left_modules) gt 0}
{assign var=cols value=$cols+1}
{assign var=lcol value="y"}
{/if}
{if $feature_right_column eq 'y' and count($right_modules) gt 0}
{assign var=cols value=$cols+1}
{assign var=rcol value="y"}
{/if}
<table {if $feature_bidi eq 'y'}dir="rtl"{/if} cellpadding="0" cellspacing="0" border="0" width="100%">
{if $feature_top_bar eq 'y'}
<tr><td {if $cols gt 1}colspan="{$cols}"{/if}>
<div id="tiki-top">{include file="tiki-top_tiki_bar.tpl"}</div>
</td></tr>
{/if}

<tr>
{if $lcol eq "y"}
<td valign="top" id="leftcolumn">
<div>{section name=homeix loop=$left_modules}{$left_modules[homeix].data}{/section}</div>
</td>
{/if}

<td valign="top" id="tiki-mid">
<div id="tiki-center">{include file=$mid}</div>
</td>

{if $rcol eq "y"}
<td valign="top" id="rightcolumn">
<div>{section name=homeix loop=$right_modules}{$right_modules[homeix].data}{/section}</div>
</td>
{/if}

</tr>

{if $feature_bot_bar eq 'y'}
<tr><td {if $cols gt 1}colspan="{$cols}"{/if}>
<div id="tiki-bot">{include file="tiki-bot_bar.tpl"}</div>
</td></tr>
{/if}

</table>

{include file="footer.tpl"}
