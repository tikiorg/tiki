<div class='opaque' style="position:absolute;left:50%;margin-left:-150px;width:400px">
<div style="float:right"><a href="#" onClick="javascript:nd();"><img src="pics/icons/cross.png" alt="{tr}close{/tr}" border="0"/></a></div>
<strong>
{if $allday}
	{tr}All-Day{/tr}
{else}
  {if ($cellend - $cellstart < 86400)}
	{$cellstart|tiki_date_format:"%H:%M"} &gt {$cellend|tiki_date_format:"%H:%M"}
  {else}
	{$cellstart|tiki_date_format:"%e %B (%H:%M)"} &gt {$cellend|tiki_date_format:"%e %B (%H:%M)"}
  {/if}
{/if}
</strong>
<br />
<a href="tiki-calendar_edit_item.php?viewcalitemId={$cellid}" title="{tr}Details{/tr}">{$cellname}</a><br />
{if $show_description eq 'y'}<span class="box-data">{$celldescription}</span><br />{/if}
{if $show_participants eq 'y' and $cellparticipants}
<span class="box-title">{tr}Organized by{/tr}:</span> {$cellorganizers}<br />
<span class="box-title">{tr}Participants{/tr}:</span> {$cellparticipants}<br />
<br />
{/if}
{if $infocals.$cellcalendarId.custompriorities eq 'y' and $cellprio}<span class='box-title'>{tr}Priority{/tr}:</span> {$cellprio}<br />{/if}
{if $show_category eq 'y' and $infocals.$cellcalendarId.customcategories eq 'y' and $cellcategory}<span class='box-title'>{tr}Category{/tr}:</span> {$cellcategory}<br />{/if}
{if $show_location eq 'y' and $infocals.$cellcalendarId.customlocations eq 'y' and $celllocation}<span class='box-title'>{tr}Location{/tr}:</span> {$celllocation}<br />{/if}
{if $show_url eq 'y' and $infocals.$cellcalendarId.customurl eq 'y' and $cellurl}<span class='box-title'>{tr}Website{/tr}:</span> <a href="{$cellurl|escape:'url'}" title="{$cellurl|escape:'url'}">{$cellurl|truncate:32:'...'}</a><br />{/if}
{if $show_calname eq 'y' and $cellcalname}<span class='box-title'>{tr}Calendar{/tr}:</span>
<span style=";height:12px;width:12px;background-color:#{$infocals.$cellcalendarId.custombgcolor};border-color:#{$infocals.$cellcalendarId.customfgcolor};border-width:1px;border-style:solid;">&nbsp;{$cellcalname}&nbsp;</span><br />
{/if}
<br />
{if $show_status eq 'y'}
<div class="statusbox status{$cellstatus}">{if $cellstatus eq 0}{tr}Tentative{/tr}{elseif $cellstatus eq 1}{tr}Confirmed{/tr}{elseif $cellstatus eq 2}{tr}Cancelled{/tr}{/if}</div>
{/if}
</div>
</div>
