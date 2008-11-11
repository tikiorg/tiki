{* $Id$ *}
{popup_init src="lib/overlib.js"}

{title admpage="calendar"}
	{if $displayedcals|@count eq 1}
		{tr}Calendar{/tr}: {assign var=x value=$displayedcals[0]}{$infocals[$x].name}
	{else}
		{tr}Calendar{/tr}
	{/if}
{/title}

<div id="calscreen">

	<div id="configlinks" style="float:right;margin:5px;">
		{if $displayedcals|@count eq 1 and $user and $prefs.feature_user_watches eq 'y'}
			{if $user_watching eq 'y'}
				<a href="tiki-calendar.php?watch_event=calendar_changed&amp;watch_action=remove" class="icon">{icon _id='no_eye' alt="{tr}Stop Monitoring this Page{/tr}"}</a>
			{else}
				<a href="tiki-calendar.php?watch_event=calendar_changed&amp;watch_action=add" class="icon">{icon _id='eye' alt="{tr}Monitor this Page{/tr}"}</a>
			{/if}
		{/if}

		{if $tiki_p_admin_calendar eq 'y' or $tiki_p_admin eq 'y'}
			<span class="button2">
				<a href="tiki-admin_calendars.php{if $displayedcals|@count eq 1}?calendarId={$displayedcals[0]}{/if}">{tr}Admin Calendar{/tr}</a>
			</span>
		{/if}

		{if $tiki_p_add_events eq 'y'}
			<span class="button2">
				<a href="tiki-calendar_edit_item.php">{tr}Add Event{/tr}</a>
			</span>
		{/if}

		{if count($listcals) >= 1}
			<span class="button2">
				<a href="#" title="{tr}Click to select visible calendars{/tr}" onclick="toggle('filtercal');">{tr}Visible Calendars{/tr}</a>
			</span>

			{if count($thiscal)}
				{foreach item=k from=$listcals name=listc}
					{if $thiscal.$k}
						<span class="button2">
							<a href="#" style="background-color:#{$infocals.$k.custombgcolor};color:#{$infocals.$k.customfgcolor}" onclick="toggle('filtercal');">{$infocals.$k.name}</a>
						</span>
					{/if}
				{/foreach}
			{else}
				<span class="button2" style="background-color:#fff;padding:0 4px;">
				none
				</span>
			{/if}
		{/if}

		<span class="button2">
			{if $viewlist eq 'list'}
				<a href="{$myurl}?viewlist=table" title="{tr}Calendar View{/tr}">{tr}Calendar View{/tr}</a>
			{else}
				<a href="{$myurl}?viewlist=list" title="{tr}List View{/tr}">{tr}List View{/tr}</a>
			{/if}
		</span>

	</div>

	<br />

	<div class="navbar" align="right">
		{if $user and $prefs.feature_user_watches eq 'y'}
			{if $category_watched eq 'y'}
				{tr}Watched by categories{/tr}:
				{section name=i loop=$watching_categories}
					{button href="tiki-browse_categories?parentId=$watching_categories[i].categId" _text=$watching_categories[i].name}
					&nbsp;
				{/section}
			{/if}	
		{/if}
	</div>

	{if count($listcals) >= 1}
		<form id="filtercal" method="get" action="{$myurl}" name="f" style="display:none;">
			<div class="caltitle">{tr}Group Calendars{/tr}</div>
			<div class="caltoggle">
				<input name="calswitch" id="calswitch" type="checkbox" onclick="switchCheckboxes(this.form,'calIds[]',this.checked);"/> 
				<label for="calswitch">{tr}Check / Uncheck All{/tr}</label>
			</div>
			{foreach item=k from=$listcals}
				<div class="calcheckbox">
					<input type="checkbox" name="calIds[]" value="{$k|escape}" id="groupcal_{$k}" {if $thiscal.$k}checked="checked"{/if} />
					<label for="groupcal_{$k}" class="calId{$k}">{$infocals.$k.name} (id #{$k})</label>
				</div>
			{/foreach}
			<div class="calinput"><input type="submit" name="refresh" value="{tr}Refresh{/tr}"/></div>
		</form>
	{/if}

	{include file="tiki-calendar_nav.tpl"}

	{if $viewlist eq 'list'}
		{include file="tiki-calendar_listmode.tpl"'}
	{elseif $viewmode eq 'day'}
		{include file="tiki-calendar_daymode.tpl"}
	{else}
		{include file="tiki-calendar_calmode.tpl"}
	{/if}
</div>
