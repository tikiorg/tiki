<div class='opaque'>
<div class='box-title'>{$cellhead}
{if $infocals.$cellcalendarId.custompriorities eq 'y' and $cellprio}<span class='calprio{$cellprio}' id='calprio'>{$cellprio}</span>{/if}
{if $calendar_sticky_popup eq "y" and $cellid}&nbsp;<a onmouseover="javascript:cClick()" title="{tr}close{/tr}">{html_image file='img/icons/close.gif' alt="{tr}close{/tr}"}</a>{/if}
</div>

{if $show_calname eq 'y' and $cellcalname}<div class='box-title' style="background-color:#{$infocals.$cellcalendarId.custombgcolor};color:#{$infocals.$cellcalendarId.customfgcolor};"><b>{$cellcalname}</b></div>{/if}

{if $cellid}
<div style="text-align:right" class='box-title'>
{if $calendar_sticky_popup eq "y"}
{if $calendar_view_tab eq "y"}<a href="{$cellurl|replace:"editmode=1":"editmode=details"}{if $feature_tabs ne 'y'}#details{/if}" title="{tr}details{/tr}"><img src="pics/icons/magnifier.png" border="0" width="16" height="16"
alt="{tr}zoom{/tr}" /></a>&nbsp;{/if}{if $cellmodif eq "y"}<a href="tiki-calendar_edit_item.php?calitemId={$cellid}" title="{tr}edit{/tr}"><img src="pics/icons/page_edit.png" border="0" width="16" height="16" alt="{tr}edit{/tr}"
/></a><a href="tiki-calendar_edit_item.php?calitemId={$cellid}&amp;delete=1" title="{tr}remove{/tr}"><img src="pics/icons/cross.png" border="0" width="16" height="16" alt="{tr}remove{/tr}" /></a>{/if}
{elseif $cellmodif eq "y"}
... {tr}click to edit{/tr}
{elseif $calendar_view_tab eq "y"}
... {tr}click to view{/tr}
{/if}
</div>
{/if}

{if $show_location eq 'y' and $infocals.$cellcalendarId.customlocations eq 'y' and $celllocation}<div class='box-title'><b>{$celllocation}</b></div>{/if}
{if $show_category eq 'y' and $infocals.$cellcalendarId.customcategories eq 'y' and $cellcategory}<div class='box-title'><b>{$cellcategory}</b></div>{/if}
{if $show_url eq 'y' and $infocals.$cellcalendarId.customurl eq 'y' and $cellurl}<div class='box-title'><a href="{$cellurl|escape:'url'}" title="{$cellurl|escape:'url'}">{$url|truncate:32:'...'}</a></div>{/if}
<div class='box-data'>
<b>{$cellname}</b>
{if $show_description eq 'y'}
<br />{$celldescription}
{/if}
</div>
</div>
