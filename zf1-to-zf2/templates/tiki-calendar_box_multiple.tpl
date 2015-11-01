<div class='opaque' style="position:absolute;top:10px;left:50%;margin-left:-150px;width:300px">
	{foreach key=k2 item=evt from=$currHrows}
		{assign var=calendarId value=$evt.calendarId}
		<div style="position:relative">
			<strong{if $evt.status eq '2'} style="text-decoration:line-through"{/if}>
				{if $evt.result.allday}
					{tr}All day{/tr}
				{else}
					{if $evt.endTimeStamp - $evt.startTimeStamp < 86400}
						{$evt.startTimeStamp|tiki_short_time} &gt {$evt.endTimeStamp|tiki_short_time}
					{else}
						{$evt.startTimeStamp|tiki_short_date}&nbsp;({$evt.startTimeStamp|tiki_short_time}) &gt
						{$evt.endTimeStamp|tiki_short_date}&nbsp;({$evt.endTimeStamp|tiki_short_time})
					{/if}
				{/if}
			</strong>
			<br>
			<span style="float:right;height:16px;width:16px;color:#{$infocals.$calendarId.customfgcolor};background-color:#{$infocals.$calendarId.custombgcolor};border-color:#{$infocals.$calendarId.customfgcolor};border-width:1px;border-style:solid;opacity:0.7;">&nbsp;</span>
			<a href="tiki-calendar_edit_item.php?viewcalitemId={$evt.calitemId}" title="{tr}Details{/tr}"{if $evt.status eq '2'} style="text-decoration:line-through"{/if}>{$evt.name}</a><br>
			<!-- {if $cellmodif eq "y"}<a href="tiki-calendar_edit_item.php?calitemId={$cellid}" title="{tr}Edit{/tr}">{icon name='edit'}</a><br>{/if} -->
			{if $show_category eq 'y' and $infocals.$cellcalendarId.customcategories eq 'y' and $cellcategory}<span class='box-title'>{tr}Classification:{/tr}</span> {$evt.category}<br>{/if}
			{if $show_location eq 'y' and $infocals.$cellcalendarId.customlocations eq 'y' and $celllocation}<span class='box-title'>{tr}Location:{/tr}</span> {$evt.location}<br>{/if}
			{if $show_url eq 'y' and $infocals.$cellcalendarId.customurl eq 'y' and $evt.url}<span class='box-title'><a href="{$cellurl|escape:'url'}" title="{$evt.url|escape:'url'}">{$url|truncate:32:'...'}</a></span><br>{/if}
		</div>
		<hr>
	{/foreach}
</div>
