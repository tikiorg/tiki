{* Index we display a wiki page here *}
{include file="header.tpl"}
<div id="tiki-top">
{include file="tiki-top_bar.tpl"}
</div>
<table border="0" cellpadding="0" cellspacing="0" width="100%">
<tr valign="top">
<td id="leftcolumn">

<table cellpadding="4" cellspacing="0">
<tr valign="top">
<td class="sidebar">
{section name=homeix loop=$left_modules}
{$left_modules[homeix].data}
{/section}
</td></tr></table>
</td>

<td id="vertline">
<img src="/styles/smarty/spacer.gif" width="2" height="2" border="0" alt="" >
</td>

<td>
<table width="600" cellpadding="10" cellspacing="0">
<tr><td valign="top">
{include file=$mid}
{if $show_page_bar eq 'y'}
{include file="tiki-page_bar.tpl"}
{/if}

</td></tr></table>
</td>

{if count($right_modules)}
<td bgcolor="#f0ead8" background="/styles/smarty/checkerboard-orange.gif" width="2">
<img src="/styles/smarty/spacer.gif" width="2" height="2" border="0" alt="" ><br></td>

<td bgcolor="#f0ead8" width="170" >

<table width="170" cellpadding="4" cellspacing="0">
<tr valign="top">
<td class="memberbar">
{section name=homeix loop=$right_modules}
{$right_modules[homeix].data}
{/section}
</td></tr></table>

</td>
{/if}
</tr></table>
<div id="tiki-bottom">
{include file="tiki-bot_bar.tpl"}
</div>
{include file="footer.tpl"}
