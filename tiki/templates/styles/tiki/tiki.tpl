{include file="header.tpl"}
{if $feature_bidi eq 'y'}
<table dir="rtl" width="100%"><tr><td>
{/if}

<div id="tiki-main">

{if $feature_top_bar eq 'y'}
<div id="tiki-top">
{include file="tiki-top_tiki_bar.tpl"}
</div>
{/if}

<div id="tiki-mid">

<div id="topmenu">
{if $feature_featuredLinks eq 'y'}
{include file="tiki-top_links.tpl"}
{/if}
{if $user}
<span id="usermenu">
{include file="tiki-top_user_menu.tpl"}
</span>
{/if}
</div>

{if $feature_left_column eq 'y' and count($left_modules) gt 0}
<div id="leftcolumn">
{section name=homeix loop=$left_modules}
{$left_modules[homeix].data}
{/section}
</div>
{/if}

<div id="centercolumn" style="left:{if $feature_left_column eq 'y' and count($left_modules) gt 0}inherit{else}20px{/if};right:{if $feature_right_column eq 'y' and count($right_modules) gt 0}inherit{else}20px{/if};">
<div id="tiki-center">
{include file=$mid}
</div>
</div>

{if $feature_right_column eq 'y' and count($right_modules) gt 0}
<div id="rightcolumn">
{section name=homeix loop=$right_modules}
{$right_modules[homeix].data}
{/section}
</div>
{/if}
   
</div>

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
