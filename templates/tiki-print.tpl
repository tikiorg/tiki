{* Index we display a wiki page here *}
{include file="header.tpl"}
{if $feature_bidi eq 'y'}
<table dir="rtl" width="100%"><tr><td>
{/if}
<div  id="tiki-mid">
{include file=$mid}
</div>
{if $feature_bidi eq 'y'}
</td></tr></table>
{/if}
{include file="footer.tpl"}
