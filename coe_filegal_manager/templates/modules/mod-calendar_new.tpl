{* $Id: mod-calendar_new.tpl 12242 2008-03-30 13:22:01Z luciash $ *}

{tikimodule error=$module_params.error title=$module_params.title name=$module_params.name flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle}
{if $viewlist eq 'list'}
	{include file='tiki-calendar_listmode.tpl'}
{else}
<div style="text-align:center; font-size:110%" class="cal_title">
<div style="width:100%; white-space: nowrap">{strip}
{if $viewmode eq "day"}
{self_link _class="next" todate=$daybefore _title="{tr}Day{/tr}" _alt="{tr}Day{/tr}" _icon=resultset_previous"}{/self_link}
{elseif $viewmode eq "week"}
{self_link _class="next" todate=$weekbefore _title="{tr}Week{/tr}" _alt="{tr}Week{/tr}" _icon=resultset_previous"}{/self_link}
{elseif $viewmode eq "month"}
{self_link _class="next" todate=$monthbefore _title="{tr}Month{/tr}" _alt="{tr}Month{/tr}" _icon=resultset_previous"}{/self_link}
{elseif $viewmode eq "quarter"}
{self_link _class="next" todate=$quarterbefore _title="{tr}Quarter{/tr}" _alt="{tr}Quarter{/tr}" _icon=resultset_previous"}{/self_link}
{elseif $viewmode eq "semester"}
{self_link _class="next" todate=$semesterbefore _title="{tr}Semester{/tr}" _alt="{tr}Semester{/tr}" _icon=resultset_previous"}{/self_link}
{elseif $viewmode eq "year"}
{self_link _class="next" todate=$yearbefore _title="{tr}Semester{/tr}" _alt="{tr}Semester{/tr}" _icon=resultset_previous"}{/self_link}
{/if}
		<a href="tiki-calendar.php?todate={$focusdate}&amp;viewmode={$viewmode}">{tr}{$focusdate|tiki_date_format:"%B"|ucfirst}{/tr}</a>
{if $viewmode eq "day"}
{self_link _class="next" todate=$dayafter _title="{tr}Day{/tr}" _alt="{tr}Day{/tr}" _icon=resultset_next"}{/self_link}
{elseif $viewmode eq "week"}
{self_link _class="next" todate=$weekafter _title="{tr}Week{/tr}" _alt="{tr}Week{/tr}" _icon=resultset_next"}{/self_link}
{elseif $viewmode eq "month"}
{self_link _class="next" todate=$monthafter _title="{tr}Month{/tr}" _alt="{tr}Month{/tr}" _icon=resultset_next"}{/self_link}
{elseif $viewmode eq "quarter"}
{self_link _class="next" todate=$quarterafter _title="{tr}Quarter{/tr}" _alt="{tr}Quarter{/tr}" _icon=resultset_next"}{/self_link}
{elseif $viewmode eq "semester"}
{self_link _class="next" todate=$semesterafter _title="{tr}Semester{/tr}" _alt="{tr}Semester{/tr}" _icon=resultset_next"}{/self_link}
{elseif $viewmode eq "year"}
{self_link _class="next" todate=$yearafter _title="{tr}Semester{/tr}" _alt="{tr}Semester{/tr}" _icon=resultset_next"}{/self_link}
{/if}
{/strip}
</div>
</div>

	<table cellpadding="0" cellspacing="0" border="0" id="caltable" style="text-align:center;">
		<tr>
			{section name=dn loop=$daysnames}
				<th class="days" width="14%">{$daysnames[dn][0]|ucfirst}</th>
			{/section}
		</tr>
		{cycle values="odd,even" print=false}
		{section name=w loop=$cell}
			<tr>
				{section name=d loop=$weekdays}
					{assign var=day_cursor value=$cell[w][d].day|tiki_date_format:"%d"}
					{assign var=month_cursor value=$cell[w][d].day|tiki_date_format:"%m"}
					{assign var=day_today value=$smarty.now|tiki_date_format:"%d"}
					{assign var=month_today value=$smarty.now|tiki_date_format:"%m"}

					{if $cell[w][d].focus}
						{cycle values="calodd,caleven" print=false}
					{else}
						{cycle values="caldark" print=false}
					{/if}
					<td class="{if $cell[w][d].day eq $smarty.session.CalendarFocusDate}calfocuson{else}{cycle advance=false}{/if}{if isset($cell[w][d].items[0]) and ($cell[w][d].items[0].modifiable eq "y" || $cell[w][d].items[0].visible eq 'y')} focus{/if}" width="14%" style="text-align:center; font-size:0.8em;">

						{if isset($cell[w][d].items[0])}
							{assign var=over value=$cell[w][d].items[0].over}{else}{assign var=over value=""}
						{/if}
						{if $month_cursor neq $focusmonth }
							<span style="color:lightgrey">{$day_cursor}</span>
						{elseif isset($cell[w][d].items[0]) and ($cell[w][d].items[0].modifiable eq "y" || $cell[w][d].items[0].visible eq 'y')}
							<a style="text-decoration: underline; font-weight: bold" href="{$myurl}?todate={$cell[w][d].day}&amp;viewmode=week" {if $prefs.calendar_sticky_popup eq "y" and $cell[w][d].items[0].calitemId}{popup sticky=true fullhtml="1" text=$over|escape:"javascript"|escape:"html"}
								{else}
									{popup fullhtml="1" text=$over|escape:"javascript"|escape:"html"}
								{/if}
								>
								{$day_cursor}
							</a>
						{else}
							{$day_cursor}
						{/if}

					</td>
				{/section}
			</tr>
		{/section}
	</table>
{/if}
{/tikimodule}
