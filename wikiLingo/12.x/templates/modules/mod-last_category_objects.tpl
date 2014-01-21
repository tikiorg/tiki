{* $Id$ *}
{if $mod_can_view}
{*if !isset($tpl_module_title)}{assign var=tpl_module_title value="{tr}Last{/tr} $type"}{/if*}
{tikimodule error=$module_params.error title=$tpl_module_title name="last_category_objects" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle}
{section name=ix loop=$last}
<div><a class="linkmodule" href="{$last[ix].href|escape}" title="{$last[ix].type|escape}">
{if $maxlen > 0}
{$last[ix].name|truncate:$maxlen:"...":true|escape}
{else}
{$last[ix].name|escape}
{/if}
</a></div>
{/section}
{/tikimodule}
{/if}
