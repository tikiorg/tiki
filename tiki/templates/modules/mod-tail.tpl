{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-tail.tpl,v 1.3 2003-11-20 23:49:04 mose Exp $ *}

{if $feature_tail eq 'y'}
{popup_init src="lib/overlib.js"}
<div class="box">
<div class="box-title">
{include file="module-title.tpl" module_title="{$tailtitle}" module_name="tail"}
</div>
<div class="box-data">
{section name=ix loop=$tail}
<div class="module">{$tail[ix]}</div>
{/section}
</div>
</div>
{/if}
