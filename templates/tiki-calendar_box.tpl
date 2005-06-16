<div class='opaque'>
<div class='box-title'>{$cellhead}
{if $custompriorities eq 'y'}{if $cellprio}<span class='calprio{$cellprio}' id='calprio'>{$cellprio}</span>{/if}{/if}
{if $calendar_sticky_popup eq "y" and $cellid}&nbsp;<a onmouseover="javascript:cClick()" title="{tr}close{/tr}">{html_image file='img/icons/close.gif' alt="{tr}close{/tr}"}</a>{/if}
</div>

{if $cellcalname}<div class='box-title'><b>{$cellcalname}</b></div>{/if}

{if $cellid}
<div style="text-align:right" class='box-title'>
{if $calendar_sticky_popup eq "y"}
{if $calendar_view_tab eq "y"}<a href="{$cellurl|replace:"editmode=1":"editmode=details"}{if $feature_tabs ne 'y'}#details{/if}" title="{tr}details{/tr}"><img src="img/icons/zoom.gif" border="0" width="16" height="16" alt="{tr}zoom{/tr}" /></a>&nbsp;{/if}{if $cellmodif eq "y"}<a href="{$cellurl}{if $feature_tabs ne 'y'}#add{/if}" title="{tr}edit{/tr}"><img src="img/icons/edit.gif" border="0"  width="20" height="16" alt="{tr}edit{/tr}" /></a><a href="tiki-calendar.php?calitemId={$cellid}&amp;delete=1" title="{tr}remove{/tr}"><img src="img/icons2/delete.gif" border="0" width="16" height="16" alt="{tr}remove{/tr}" /></a>{/if}
{elseif $cellmodif eq "y"}
... {tr}click to edit{/tr}
{elseif $calendar_view_tab eq "y"}
... {tr}click to view{/tr}
{/if}
</div>
{/if}

{if $customlocations eq 'y'}{if $celllocation}<div class='box-title'><b>{$celllocation}</b></div>{/if}{/if}
{if $customcategories eq 'y'}{if $cellcategory}<div class='box-title'><b>{$cellcategory}</b></div>{/if}{/if}
<div class='box-data'>
<b>{$cellname}</b><br />
{$celldescription}
</div>
</div>
