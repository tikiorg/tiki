{* $Id$ *}

{title admpage="calendar"}
	{if $displayedcals|@count eq 1}
		{tr}Calendar:{/tr} {assign var=x value=$displayedcals[0]}{$infocals[$x].name|escape}
	{else}
		{tr}Calendar{/tr}
	{/if}
{/title}

<div id="calscreen">

	<div class="navbar">
		{if $displayedcals|@count eq 1 and $prefs.feature_group_watches eq 'y' and ( $tiki_p_admin_users eq 'y' or $tiki_p_admin eq 'y' )}
			<a href="tiki-object_watches.php?objectId={$displayedcals[0]|escape:"url"}&amp;watch_event=calendar_changed&amp;objectType=calendar&amp;objectName={$infocals[$x].name|escape:"url"}&amp;objectHref={'tiki-calendar.php?calIds[]='|cat:$displayedcals[0]|escape:"url"}" class="icon">{icon _id='eye_group' alt="{tr}Group Monitor{/tr}" align='right' hspace="1"}</a>
		{/if}
		{if $displayedcals|@count eq 1 and $user and $prefs.feature_user_watches eq 'y'}
			{if $user_watching eq 'y'}
				<a href="tiki-calendar.php?watch_event=calendar_changed&amp;watch_action=remove" class="icon">{icon _id='no_eye' alt="{tr}Stop Monitoring this Page{/tr}" align="right" hspace="1"}</a>
			{else}
				<a href="tiki-calendar.php?watch_event=calendar_changed&amp;watch_action=add" class="icon">{icon _id='eye' alt="{tr}Monitor this Page{/tr}" align="right" hspace="1"}</a>
			{/if}
		{/if}

		{if $tiki_p_admin_calendar eq 'y' or $tiki_p_admin eq 'y'}
			{if $displayedcals|@count eq 1}
				{button href="tiki-admin_calendars.php?calendarId=$displayedcals[0]" _text="{tr}Edit Calendar{/tr}"}
			{/if}
			{button href="tiki-admin_calendars.php?cookietab=1" _text="{tr}Admin Calendars{/tr}"}
		{/if}
{* avoid Add Event being shown if no calendar is displayed *}
		{if $tiki_p_add_events eq 'y'}
			{button href="tiki-calendar_edit_item.php" _text="{tr}Add Event{/tr}"}
		{/if}

		{if $tiki_p_admin_calendar eq 'y'}
			{button href="#" _onclick="toggle('exportcal');return false;" _text="{tr}Export Calendars{/tr}" _title="{tr}Click to export calendars{/tr}"}
		{/if}

		{if $viewlist eq 'list'}
			{capture name=href}?viewlist=table{if $smarty.request.todate}&amp;todate={$smarty.request.todate}{/if}{/capture}
			{button href="`$smarty.capture.href`" _text="{tr}Calendar View{/tr}"}
		{else}
			{capture name=href}?viewlist=list{if $smarty.request.todate}&amp;todate={$smarty.request.todate}{/if}{/capture}
			{button href="`$smarty.capture.href`" _text="{tr}List View{/tr}"}
		{/if}

		{if count($listcals) >= 1}
			{button href="#" _onclick="toggle('filtercal');return false;" _text="{tr}Visible Calendars{/tr}" _title="{tr}Click to select visible calendars{/tr}"}

			{if count($thiscal)}
				<div id="configlinks">
				{assign var='maxCalsForButton' value=20}
				{if count($checkedCals) > $maxCalsForButton}<select size="5">{/if}
				{foreach item=k from=$listcals name=listc}
					{if $thiscal.$k}
						{assign var=thiscustombgcolor value=$infocals.$k.custombgcolor}
						{assign var=thiscustomfgcolor value=$infocals.$k.customfgcolor}
						{assign var=thisinfocalsname value=$infocals.$k.name|escape}
						{if count($checkedCals) > $maxCalsForButton}
							<option style="background-color:#{$thiscustombgcolor};color:#{$thiscustomfgcolor};" onclick="toggle('filtercal')">{$thisinfocalsname}</option>
						{else}
							{button href="#" _style="background-color:#$thiscustombgcolor;color:#$thiscustomfgcolor;border:1px solid #$thiscustomfgcolor;" _onclick="toggle('filtercal');return false;" _text="$thisinfocalsname"}
						{/if}
					{/if}
				{/foreach}
				{if count($checkedCals) > $maxCalsForButton}</select>{/if}
				</div>
			{else}
				{button href="" _style="background-color:#fff;padding:0 4px;" _text="{tr}None{/tr}"}
			{/if}
		{/if}
{* show jscalendar if set *}
{if $prefs.feature_jscalendar eq 'y'}
<div class="jscalrow">
<form action="{$myurl}" method="post" name="f">
{jscalendar date="$focusdate" id="trig" goto="$jscal_url" align="Bc"}
</form>
</div>
{/if}
	</div>



	<div class="categbar" align="right">
		{if $user and $prefs.feature_user_watches eq 'y'}
			{if $category_watched eq 'y'}
				{tr}Watched by categories:{/tr}
				{section name=i loop=$watching_categories}
					{assign var=thiswatchingcateg value=$watching_categories[i].categId}
					{button href="tiki-browse_categories.php?parentId=$thiswatchingcateg" _text=$watching_categories[i].name|escape}
					&nbsp;
				{/section}
			{/if}	
		{/if}
	</div>

	{if count($listcals) >= 1}
		<form id="filtercal" method="get" action="{$myurl}" name="f" style="display:none;">
			<div class="caltitle">{tr}Group Calendars{/tr}</div>
			<div class="caltoggle">
				{select_all checkbox_names='calIds[]' label="{tr}Check / Uncheck All{/tr}"}				
			</div>
			{foreach item=k from=$listcals}
				<div class="calcheckbox">
					<input type="checkbox" name="calIds[]" value="{$k|escape}" id="groupcal_{$k}" {if $thiscal.$k}checked="checked"{/if} />
					<label for="groupcal_{$k}" class="calId{$k}">{$infocals.$k.name|escape} (id #{$k})</label>
				</div>
			{/foreach}
			<div class="calinput">
				<input type="hidden" name="todate" value="{$focusdate}"/>
				<input type="submit" name="refresh" value="{tr}Refresh{/tr}"/>
			</div>
		</form>
	{/if}

	{if $tiki_p_admin_calendar eq 'y'}
		<form id="exportcal" method="post" action="{$exportUrl}" name="f" style="display:none;">
			<input type="hidden" name="export" value="y"/>
			<div class="caltitle">{tr}Export calendars{/tr}</div>
			<div class="caltoggle">
				{select_all checkbox_names='calendarIds[]' label="{tr}Check / Uncheck All{/tr}"}
			</div>
			{foreach item=k from=$listcals}
				<div class="calcheckbox">
					<input type="checkbox" name="calendarIds[]" value="{$k|escape}" id="groupcal_{$k}" {if $thiscal.$k}checked="checked"{/if} />
					<label for="groupcal_{$k}" class="calId{$k}">{$infocals.$k.name|escape}</label>
				</div>
			{/foreach}
			<div class="calcheckbox">
				<a href="{$iCalAdvParamsUrl}">{tr}advanced parameters{/tr}</a>
			</div>
			<div class="calinput">
				<input type="submit" name="ical" value="{tr}Export as iCal{/tr}"/>
				<input type="submit" name="csv" value="{tr}Export as CSV{/tr}"/>
			</div>
		</form>
	{/if}

	{include file='tiki-calendar_nav.tpl'}
	{if $viewlist eq 'list'}
		{include file='tiki-calendar_listmode.tpl''}
	{elseif $viewmode eq 'day'}
		{include file='tiki-calendar_daymode.tpl'}
	{elseif $viewmode eq 'week'}
		{include file='tiki-calendar_weekmode.tpl'}
	{else}
		{include file='tiki-calendar_calmode.tpl'}
	{/if}
<p>&nbsp;</p>
</div>
