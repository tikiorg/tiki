{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-last_tracker_items.tpl,v 1.6 2003-11-23 03:53:04 zaufi Exp $ *}

{if $feature_trackers eq 'y'}
{tikimodule title="{tr}Last Items{/tr}" name="last_tracker_items"}
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
{/tikimodule}
{/if}
