{* Index we display a wiki page here *}
{include file="header.tpl"}
<div id="tiki-main">
<div id="tiki-top">
  {include file="tiki-top_bar.tpl"}
</div>
<div id="tiki-mid">
  {include file=$mid}
</div>
<div id="tiki-bot">
{if $show_page_bar eq 'y'}
{include file="tiki-page_bar.tpl}
{/if}
{include file="tiki-bot_bar.tpl"}
</div>
</div>
{include file="footer.tpl"}
