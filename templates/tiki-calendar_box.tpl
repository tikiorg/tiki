<div class='opaque calBox' style="width:200px">
	{if $calendar_type eq "tiki_actions"}
		<div class='box-title'>
			<a href="{$item_url}">
				{$cellhead}
			</a>
			{if isset($infocals.$cellcalendarId.custompriorities) and $infocals.$cellcalendarId.custompriorities eq 'y'
				and $cellprio}
				<span class='calprio{$cellprio}' id='calprio'>
					{$cellprio}
				</span>
			{/if}
		</div>
	{/if}
	{if $group_by_item ne 'y'}
		<strong{if isset($cellstatus) and $cellstatus eq '2'} style="text-decoration:line-through"{/if}>
			{if $allday}
				{$cellstart|tiki_short_date}
				{if $cellend - $cellstart >=86400}&ndash; {$cellend|tiki_short_date}
					<br>
				{/if}
				({tr}All day{/tr})
			{else}
				{if ($cellend - $cellstart < 86400)}
					{$cellstart|tiki_short_time} &ndash; {$cellend|tiki_short_time}
				{else}
					{$cellstart|tiki_short_date}&nbsp;({$cellstart|tiki_short_time}) &ndash; {$cellend|tiki_short_date}&nbsp;({$cellend|tiki_short_time})
				{/if}
			{/if}
		</strong>
		<br>
	{/if}
	<a href="tiki-calendar_edit_item.php?viewcalitemId={$cellid}" title="{tr}Details{/tr}"{if isset($cellstatus) and $cellstatus eq '2'} style="text-decoration:line-through"{/if}>
		{$cellname|escape}
	</a>
	  <p class="text-muted"><strong>Created by:{$celluser}</strong></p>
	{if $show_description eq 'y'}
		<div class="panel-body">
			{$celldescription|truncate:250:'...'}
		</div>
		<br>
	{/if}
  
	{if isset($show_participants) and $show_participants eq 'y' and isset($cellparticipants) and $cellparticipants}
		<span class="box-title">
			{tr}Organized by:{/tr}
		</span>
		{$cellorganizers}
		<br>
		<span class="box-title">
			{tr}Participants:{/tr}
		</span>
		{$cellparticipants}
		<br>
		<br>
	{/if}
	{* need to check $cellCalendarId separately to eliminate notice fro some reason *}
	{if isset($cellcalendarId) and isset($infocals.$cellcalendarId.custompriorities)
		and $infocals.$cellcalendarId.custompriorities eq 'y' and $cellprio}
		<span class='box-title'>
			{tr}Priority:{/tr}
		</span>
		{$cellprio}
		<br>
	{/if}
	{if isset($show_category) and $show_category eq 'y' and isset($infocals.$cellcalendarId.customcategories)
		and $infocals.$cellcalendarId.customcategories eq 'y' and $cellcategory}
		<span class='box-title'>
			{tr}Classification:{/tr}
		</span>
		{$cellcategory|escape}
		<br>
	{/if}
	{if isset($show_location) and $show_location eq 'y' and isset($infocals.$cellcalendarId.customlocations)
		and $infocals.$cellcalendarId.customlocations eq 'y' and $celllocation}
		<span class='box-title'>
			{tr}Location:{/tr}
		</span>
		{$celllocation|escape}
		<br>
	{/if}
	{if isset($show_url) and $show_url eq 'y' and isset($infocals.$cellcalendarId.customurl)
		and $infocals.$cellcalendarId.customurl eq 'y' and $cellurl}
		<span class='box-title'>
			{tr}Website:{/tr}
		</span>
		<a href="{$cellurl|escape:'url'}" title="{$cellurl|escape:'url'}">
			{$cellurl|truncate:32:'...'}
		</a>
		<br>
	{/if}
	{if isset($show_calname) and $show_calname eq 'y' and $cellcalname}
		<span class='box-title'>
			{tr}Calendar:{/tr}
		</span>
		<span style="height:12px;width:12px;color:#{$infocals.$cellcalendarId.customfgcolor};{if !empty($infocals.$cellcalendarId.custombgcolor)}background-color:#{$infocals.$cellcalendarId.custombgcolor};{/if}{if !empty($infocals.$cellcalendarId.customfgcolor)}border-color:#{$infocals.$cellcalendarId.customfgcolor};{/if}border-width:1px;border-style:solid;">
			&nbsp;{$cellcalname|escape}&nbsp;
		</span>
		<br>
	{/if}
	<br>
	{if isset($show_status) and $show_status eq 'y'}
		<div class="statusbox status{$cellstatus}">
			{if $cellstatus eq 0}
				{tr}Tentative{/tr}
			{elseif $cellstatus eq 1}
				{tr}Confirmed{/tr}
			{elseif $cellstatus eq 2}
				{tr}Cancelled{/tr}
			{/if}
		</div>
	{/if}
</div>
