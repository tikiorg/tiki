<div class='opaque' style="width:300px">
<div style="float:right"><a href="#" onClick="javascript:nd();"><img src="pics/icons/cross.png" alt="{tr}close{/tr}" border="0"/></a></div>
<strong>
  {if ($cellend - $cellstart < 86400)}
	{$cellstart|tiki_date_format:"%H:%M"} &gt {$cellend|tiki_date_format:"%H:%M"}
  {else}
	{$cellstart|tiki_date_format:"%e %B (%H:%M)"} &gt {$cellend|tiki_date_format:"%e %B (%H:%M)"}
  {/if}
</strong>
<br />
<a href="tiki-calendar_edit_item.php?viewcalitemId={$cellid}" title="{tr}Details{/tr}">{$cellname}</a><br />
<!-- {if $cellmodif eq "y"}<a href="tiki-calendar_edit_item.php?calitemId={$cellid}" title="{tr}Edit{/tr}">{icon _id='page_edit'}</a><br />{/if} -->
{if $show_category eq 'y' and $infocals.$cellcalendarId.customcategories eq 'y' and $cellcategory}<span class='box-title'>{tr}Category{/tr}:</span> {$cellcategory}<br />{/if}
{if $show_location eq 'y' and $infocals.$cellcalendarId.customlocations eq 'y' and $celllocation}<span class='box-title'>{tr}Location{/tr}:</span> {$celllocation}<br />{/if}
{if $show_url eq 'y' and $infocals.$cellcalendarId.customurl eq 'y' and $cellurl}<span class='box-title'><a href="{$cellurl|escape:'url'}" title="{$cellurl|escape:'url'}">{$url|truncate:32:'...'}</a></span><br />{/if}
<br />
<span class="box-data">{$celldescription}</span>
</div>
