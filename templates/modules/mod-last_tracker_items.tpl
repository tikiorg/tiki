{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-last_tracker_items.tpl,v 1.2 2003-08-07 20:56:53 zaufi Exp $ *}

{if $feature_trackers eq 'y'}
<div class="box">
<div class="box-title">
{include file="modules/module-title.tpl" module_title="{tr}Last Items{/tr}" module_name="last_tracker_items"}
</div>
<div class="box-data">
<table width="100%" border="0" cellpadding="0" cellspacing="0">
{section name=ix loop=$modLastItems}
<tr><td class="module" width="5%">{$smarty.section.ix.index_next})</td><td class="module">&nbsp;<a class="linkmodule" href="tiki-view_tracker_item.php?itemId={$modLastItems[ix].itemId}&amp;trackerId={$modLastItems[ix].trackerId}">
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
