{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-last_tracker_items.tpl,v 1.4 2003-10-20 01:13:16 zaufi Exp $ *}

{if $feature_trackers eq 'y'}
<div class="box">
<div class="box-title">
{include file="modules/module-title.tpl" module_title="{tr}Last Items{/tr}" module_name="last_tracker_items"}
</div>
<div class="box-data">
<table  border="0" cellpadding="0" cellspacing="0">
{section name=ix loop=$modLastItems}
<tr>{if $nonums != 'y'}<td class="module" >{$smarty.section.ix.index_next})</td>{/if}
<td class="module"><a class="linkmodule" href="tiki-view_tracker_item.php?itemId={$modLastItems[ix].itemId}&amp;trackerId={$modLastItems[ix].trackerId}">
{section name=jjj loop=$modLastItems[ix].field_values}
{if $modlifn eq $modLastItems[ix].field_values[jjj].name}
{$modLastItems[ix].field_values[jjj].value}
{/if}
{/section}
</a></td></tr>
{/section}
</table>
</div>
</div>
{/if}
