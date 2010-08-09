<div class='opaque calBox' style="width:200px">
{if $calendar_type eq "tiki_actions"}
	<div class='box-title'><a href="{$item_url}">{$cellhead}</a>
	{if $infocals.$cellcalendarId.custompriorities eq 'y' and $cellprio}
		<span class='calprio{$cellprio}' id='calprio'>{$cellprio}</span>
	{/if}
	{if $prefs.calendar_sticky_popup eq "y"}
		<span style="right:2px; position:absolute">
			<a href="javascript:void(0)" onClick="nd();return false;">{icon _id="close" alt="{tr}Close{/tr}" width="16" height="16"}</a>
		</span>
	{/if}
	</div>
{elseif $prefs.calendar_sticky_popup eq "y"}
	<div style="float:right">
			{button _noborder='y' _text="{tr}Export event{/tr}" _icon="task_submitted" href="tiki-calendar_export_ical.php" calendarItem=$cellid export='y' _auto_args="calendarItem,export"}
		{if $tiki_p_change_events eq 'y'}
			{button _noborder='y' _text="{tr}Edit event{/tr}" _icon="page_edit" href="tiki-calendar_edit_item.php" calitemId=$cellid _auto_args="calitemId"}
			{button _noborder='y' _text="{tr}Delete event{/tr}" _icon="cross" href="tiki-calendar_edit_item.php" calitemId=$cellid delete="y" _auto_args="calitemId,delete"}
		{/if}
		{button _noborder='y' _text="{tr}View event{/tr}" _icon="magnifier" href="tiki-calendar_edit_item.php" viewcalitemId=$cellid _auto_args="viewcalitemId"}
		{button _noborder='y' _text="{tr}Close{/tr}" _icon="close" href="javascript:void(0)" _onclick="nd();return false;"}
	</div>
{/if}
{if $group_by_item ne 'y'}
<strong{if $cellstatus eq '2'} style="text-decoration:line-through"{/if}>
{if $allday}
	{$cellstart|tiki_short_date} ({tr}All day{/tr})
{else}
  {if ($cellend - $cellstart < 86400)}
	{$cellstart|tiki_date_format:"%H:%M"} &ndash; {$cellend|tiki_date_format:"%H:%M"}
  {else}
	{$cellstart|tiki_date_format:"%e %B (%H:%M)"} &ndash; {$cellend|tiki_date_format:"%e %B (%H:%M)"}
  {/if}
{/if}
</strong>
<br />
{/if}
<a href="tiki-calendar_edit_item.php?viewcalitemId={$cellid}" title="{tr}Details{/tr}"{if $cellstatus eq '2'} style="text-decoration:line-through"{/if}>{$cellname|escape}</a><br />
{if $show_description eq 'y'}<div class="box-data">{$celldescription|truncate:250:'...'}</div><br />{/if}
{if $show_participants eq 'y' and $cellparticipants}
<span class="box-title">{tr}Organized by:{/tr}</span> {$cellorganizers}<br />
<span class="box-title">{tr}Participants:{/tr}</span> {$cellparticipants}<br />
<br />
{/if}
{if $infocals.$cellcalendarId.custompriorities eq 'y' and $cellprio}<span class='box-title'>{tr}Priority:{/tr}</span> {$cellprio}<br />{/if}
{if $show_category eq 'y' and $infocals.$cellcalendarId.customcategories eq 'y' and $cellcategory}<span class='box-title'>{tr}Classification:{/tr}</span> {$cellcategory|escape}<br />{/if}
{if $show_location eq 'y' and $infocals.$cellcalendarId.customlocations eq 'y' and $celllocation}<span class='box-title'>{tr}Location:{/tr}</span> {$celllocation|escape}<br />{/if}
{if $show_url eq 'y' and $infocals.$cellcalendarId.customurl eq 'y' and $cellurl}<span class='box-title'>{tr}Website:{/tr}</span> <a href="{$cellurl|escape:'url'}" title="{$cellurl|escape:'url'}">{$cellurl|truncate:32:'...'}</a><br />{/if}
{if $show_calname eq 'y' and $cellcalname}<span class='box-title'>{tr}Calendar:{/tr}</span>
<span style="height:12px;width:12px;background-color:#{$infocals.$cellcalendarId.custombgcolor};border-color:#{$infocals.$cellcalendarId.customfgcolor};border-width:1px;border-style:solid;">&nbsp;{$cellcalname|escape}&nbsp;</span><br />
{/if}
<br />
{if $show_status eq 'y'}
<div class="statusbox status{$cellstatus}">{if $cellstatus eq 0}{tr}Tentative{/tr}{elseif $cellstatus eq 1}{tr}Confirmed{/tr}{elseif $cellstatus eq 2}{tr}Cancelled{/tr}{/if}</div>
{/if}
</div>
