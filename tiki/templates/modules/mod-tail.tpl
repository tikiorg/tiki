{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-tail.tpl,v 1.2 2003-08-07 20:56:53 zaufi Exp $ *}

{if $feature_tail eq 'y'}
{popup_init src="lib/overlib.js"}
<div class="box">
<div class="box-title">
{include file="modules/module-title.tpl" module_title="{$tailtitle}" module_name="tail"}
</div>
<div class="box-data">
{section name=ix loop=$tail}
<div class="module">{$tail[ix]}</div>
{/section}
</div>
</div>
{/if}
