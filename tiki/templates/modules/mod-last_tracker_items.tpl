{if $feature_trackers eq 'y'}
<div class="box">
<div class="box-title">
{tr}Last Items{/tr}
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
