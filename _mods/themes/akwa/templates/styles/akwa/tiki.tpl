{include file="header.tpl"}

<div id="tiki-main">

{if $feature_top_bar eq 'y'}
<div id="tiki-top">
{include file="tiki-top_bar.tpl"}
</div>
{/if}

<div id="tiki-mid">

<div id="centercolumn" style="left:{if $feature_left_column eq 'y' and count($left_modules) gt 0}180px{else}20px{/if};right:{if $feature_right_column eq 'y' and count($right_modules) gt 0}180px{else}20px{/if};">
<div id="tiki-center">
{include file=$mid}
{if $show_page_bar eq 'y'}
{include file="tiki-page_bar.tpl"}
{/if}
</div>
</div>

{if $feature_left_column eq 'y' and count($left_modules) gt 0}
<div id="leftcolumn">
{section name=homeix loop=$left_modules}
{$left_modules[homeix].data}
{/section}
</div>
{/if}

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

{include file="footer.tpl"}
