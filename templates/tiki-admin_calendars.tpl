{if !empty($calendarId)}
	{title help=Calendar url="tiki-admin_calendars.php?calendarId=$calendarId" admpage="calendar"}{tr}Admin Calendars{/tr}{/title}
{else}
	{title help=Calendar url="tiki-admin_calendars.php" admpage="calendar"}{tr}Admin Calendars{/tr}{/title}
{/if}

<div class="navbar">
	{if !empty($calendarId) && $tiki_p_admin_calendar eq 'y'}
		{button _text="{tr}Create Calendar{/tr}" href="tiki-admin_calendars.php?show=mod"}
	{/if}
	{button _text="{tr}View Calendars{/tr}" href="tiki-calendar.php"}
	{if $tiki_p_admin_calendar eq 'y'}
		{button _text="{tr}Import{/tr}" href="tiki-calendar_import.php"}
	{/if}
</div>

{tabset name='tabs_admin_calendars'}
	{tab name="{tr}List of Calendars{/tr}"}
		<h2>{tr}List of Calendars{/tr}</h2>

			{include file='find.tpl'}
			<table class="normal">
				<tr>
					<th>
						<a href="tiki-admin_calendars.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'calendarId_desc'}calendarId_asc{else}calendarId_desc{/if}">{tr}ID{/tr}</a>
					</th>
					<th>
						<a href="tiki-admin_calendars.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'name_desc'}name_asc{else}name_desc{/if}">{tr}Name{/tr}</a>
					</th>
					<th>
						<a href="tiki-admin_calendars.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'customlocations_desc'}customlocations_asc{else}customlocations_desc{/if}">{tr}Loc{/tr}</a>
					</th>
					<th>
						<a href="tiki-admin_calendars.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'customparticipants_desc'}customparticipants_asc{else}customparticipants_desc{/if}">{tr}Participants{/tr}</a>
					</th>
					<th>
						<a href="tiki-admin_calendars.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'customcategories_desc'}customcategories_asc{else}customcategories_desc{/if}">{tr}Cat{/tr}</a>
					</th>
					<th>
						<a href="tiki-admin_calendars.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'customlanguages_desc'}customlanguages_asc{else}customlanguages_desc{/if}">{tr}Lang{/tr}</a>
					</th>
					<th>{tr}Url{/tr}</th>
					<th>
						<a href="tiki-admin_calendars.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'custompriorities_desc'}custompriorities_asc{else}custompriorities_desc{/if}">{tr}Prio{/tr}</a>
					</th>
					<th>
						<a href="tiki-admin_calendars.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'customsubscription_desc'}customsubscription_asc{else}customsubscription_desc{/if}">{tr}Subscription{/tr}</a>
					</th>
					<th>
						<a href="tiki-admin_calendars.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'personal_desc'}personal_asc{else}personal_desc{/if}">{tr}Perso{/tr}</a>
					</th>
					<th>{tr}Perms{/tr}</th>
					<th>{tr}Action{/tr}</th>
				</tr>
				{cycle values="odd,even" print=false}
				{foreach key=id item=cal from=$calendars}
					<tr class="{cycle}">
						<td class="id">{$id}</td>
						<td class="text">
							<a class="tablename" href="tiki-admin_calendars.php?calendarId={$id}" title="{tr}Edit{/tr}">{$cal.name|escape}</a>
							{if $cal.show_calname eq 'y'} {icon _id=layers alt="{tr}Show in popup box{/tr}"}{/if}
						</td>
						<td class="text">
							{$cal.customlocations|yesno}{if $cal.show_location eq 'y'}{icon _id=layers alt="{tr}Show in popup box{/tr}"}{/if}
						</td>
						<td class="text">
							{$cal.customparticipants|yesno}{if $cal.show_participants eq 'y'}{icon _id=layers alt="{tr}Show in popup box{/tr}"}{/if}
						</td>
						<td class="text">
							{$cal.customcategories|yesno}{if $cal.show_category eq 'y'}{icon _id=layers alt="{tr}Show in popup box{/tr}"}{/if}
						</td>
						<td class="text">
							{$cal.customlanguages|yesno}{if $cal.show_language eq 'y'}{icon _id=layers alt="{tr}Show in popup box{/tr}"}{/if}
						</td>
						<td class="text">
							{$cal.customurl|yesno}{if $cal.show_url eq 'y'}{icon _id=layers alt="{tr}Show in popup box{/tr}"}{/if}
						</td>
						<td class="text">{$cal.custompriorities|yesno}</td>
						<td class="text">{$cal.customsubscription|yesno}</td>
						<td class="text">{$cal.personal|yesno}</td>
						<td class="text">
							<a title="{tr}Permissions{/tr}" class="link" href="tiki-objectpermissions.php?objectName={$cal.name|escape:"url"}&amp;objectType=calendar&amp;permType=calendar&amp;objectId={$id}">{if $cal.individual gt 0}{icon _id='key_active' alt="{tr}Permissions{/tr}"}</a>&nbsp;{$cal.individual}{else}{icon _id='key' alt="{tr}Permissions{/tr}"}</a>{/if}
						</td>
						<td>
							<a title="{tr}Edit{/tr}" class="link" href="tiki-admin_calendars.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;calendarId={$id}">{icon _id='page_edit'}</a>
							<a title="{tr}View Calendar{/tr}" class="link" href="tiki-calendar.php?calIds[]={$id}">{icon _id='magnifier' alt="{tr}View{/tr}"}</a>
							<a title="{tr}Delete{/tr}" class="link" href="tiki-admin_calendars.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;drop={$id}&amp;calendarId={$id}" title="{tr}Delete{/tr}">{icon _id='cross' alt="{tr}Delete{/tr}"}</a>
							<a title="{tr}Add Event{/tr}" class="link" href="tiki-calendar_edit_item.php?calendarId={$id}">{icon _id='add' alt="{tr}Add Event{/tr}"}</a>
						</td>
					</tr>
				{foreachelse}
					<tr class="even"><td class="norecords" colspan="12">No records found</td></tr>
				{/foreach}
			</table>

			{pagination_links cant=$cant step=$maxRecords offset=$offset}{/pagination_links}
	{/tab}

	{tab name="{tr}Create / Edit Calendar{/tr}"}
		<h2>{tr}Create/Edit Calendars{/tr}</h2>

		<form action="tiki-admin_calendars.php" method="post">
			<input type="hidden" name="calendarId" value="{$calendarId|escape}" />
			<table class="formcolor">
				{if $tiki_p_modify_object_categories eq 'y'}
					{include file='categorize.tpl'}
				{/if}
				<tr>
					<td>{tr}Name:{/tr}</td>
					<td>
						<input type="text" name="name" value="{$name|escape}" />
						{tr}Show in popup box{/tr}
						<input type="checkbox" name="show[calname]" value="on"{if $show_calname eq 'y'} checked="checked"{/if} />
					</td>
				</tr>
				<tr>
					<td>{tr}Description:{/tr}</td>
					<td>
						<textarea name="description" rows="5" wrap="virtual" style="width:100%;">{$description|escape}</textarea>
						<br />
						{tr}Show in popup box{/tr}
						<input type="checkbox" name="show[description]" value="on"{if $show_description eq 'y'} checked="checked"{/if} />
					</td>
				</tr>
				<tr>
					<td>{tr}Custom Locations:{/tr}</td>
					<td>
						<select name="customlocations">
							<option value='y' {if $customlocations eq 'y'}selected="selected"{/if}>{tr}Yes{/tr}</option>
							<option value='n' {if $customlocations eq 'n'}selected="selected"{/if}>{tr}No{/tr}</option>
						</select>
						{tr}Show in popup box{/tr}
						<input type="checkbox" name="show[location]" value="on"{if $show_location eq 'y'} checked="checked"{/if} />
					</td>
				</tr>
				<tr>
					<td>{tr}Custom Participants:{/tr}</td>
					<td>
						<select name="customparticipants">
							<option value='y' {if $customparticipants eq 'y'}selected="selected"{/if}>{tr}Yes{/tr}</option>
							<option value='n' {if $customparticipants eq 'n'}selected="selected"{/if}>{tr}No{/tr}</option>
						</select>
						{tr}Show in popup box{/tr}
						<input type="checkbox" name="show[participants]" value="on"{if $show_participants eq 'y'} checked="checked"{/if} />
					</td>
				</tr>
				<tr>
					<td>{tr}Custom Classifications:{/tr}</td>
					<td>
						<select name="customcategories">
							<option value='y' {if $customcategories eq 'y'}selected="selected"{/if}>{tr}Yes{/tr}</option>
							<option value='n' {if $customcategories eq 'n'}selected="selected"{/if}>{tr}No{/tr}</option>
						</select>
						{tr}Show in popup box{/tr}
						<input type="checkbox" name="show[category]" value="on"{if $show_category eq 'y'} checked="checked"{/if} />
					</td>
				</tr>
				<tr>
					<td>{tr}Custom Languages:{/tr}</td>
					<td>
						<select name="customlanguages">
							<option value='y' {if $customlanguages eq 'y'}selected="selected"{/if}>{tr}Yes{/tr}</option>
							<option value='n' {if $customlanguages eq 'n'}selected="selected"{/if}>{tr}No{/tr}</option>
						</select>
						{tr}Show in popup box{/tr}
						<input type="checkbox" name="show[language]" value="on"{if $show_language eq 'y'} checked="checked"{/if} />
					</td>
				</tr>
				<tr>
					<td>{tr}Custom URL:{/tr}</td>
					<td>
						<select name="options[customurl]">
							<option value='y' {if $customurl eq 'y'}selected="selected"{/if}>{tr}Yes{/tr}</option>
							<option value='n' {if $customurl eq 'n'}selected="selected"{/if}>{tr}No{/tr}</option>
						</select>
						{tr}Show in popup box{/tr}
						<input type="checkbox" name="show[url]" value="on"{if $show_url eq 'y'} checked="checked"{/if} />
					</td>
				</tr>
				{if $prefs.feature_newsletters eq 'y'}
					<tr>
						<td>{tr}Custom Subscription List:{/tr}</td>
						<td>
							<select name="customsubscription">
								<option value='y' {if $customsubscription eq 'y'}selected="selected"{/if}>{tr}Yes{/tr}</option>
								<option value='n' {if $customsubscription eq 'n'}selected="selected"{/if}>{tr}No{/tr}</option>
							</select>
						</td>
					</tr>
				{/if}
				<tr>
					<td>{tr}Custom Priorities:{/tr}</td>
					<td>
						<select name="custompriorities">
							<option value='y' {if $custompriorities eq 'y'}selected="selected"{/if}>{tr}Yes{/tr}</option>
							<option value='n' {if $custompriorities eq 'n'}selected="selected"{/if}>{tr}No{/tr}</option>
						</select>
					</td>
				</tr>
				<tr>
					<td>{tr}Personal Calendar:{/tr}</td>
					<td>
						<select name="personal">
							<option value='y' {if $personal eq 'y'}selected="selected"{/if}>{tr}Yes{/tr}</option>
							<option value='n' {if $personal eq 'n'}selected="selected"{/if}>{tr}No{/tr}</option>
						</select>
					</td>
				</tr>
				<tr>
					<td>{tr}Start of day:{/tr}</td>
					<td>
						<select name="startday_Hour">
							{foreach key=h item=d from=$hours}
								<option value="{$h}"{if $h eq $startday} selected="selected"{/if}>{$d}</option>
							{/foreach}
						</select>
					</td>
				</tr>
				<tr>
					<td>{tr}End of day:{/tr}</td>
					<td>
						<select name="endday_Hour">
							{foreach key=h item=d from=$hours}
								<option value="{$h}"{if $h eq $endday} selected="selected"{/if}>{$d}</option>
							{/foreach}
						</select>
					</td>
				</tr>
				<tr>
					<td>{tr}Days to display:{/tr}</td>
					<td>
						{section name="viewdays" start=0 loop=7}
							{$days_names[$smarty.section.viewdays.index]}&nbsp;<input type="checkbox" name="viewdays[]" value="{$smarty.section.viewdays.index}" {if !empty($info.viewdays) && in_array($smarty.section.viewdays.index,$info.viewdays)} checked="checked" {/if} />
						{/section}
					</td>
				</tr>
				<tr>
					<td>{tr}Standard Colors:{/tr}</td>
					<td>
						<select name="options[customcolors]" onChange="javascript:document.getElementById('fgColorField').disabled=(this.options[this.selectedIndex].value != 0);document.getElementById('bgColorField').disabled=(this.options[this.selectedIndex].value != 0);">
							<option value="" />
							<option value="008400-99fa99" style="background-color:#99fa99;color:#008400" {if ($customColors) eq '008400-99fa99'}selected{/if}>{tr}Green{/tr}</option>
							<option value="3333ff-aaccff" style="background-color:#aaccff;color:#3333ff" {if ($customColors) eq '3333ff-aaccff'}selected{/if}>{tr}Blue{/tr}</option>
							<option value="996699-c2a6d2" style="background-color:#e0cae5;color:#996699" {if ($customColors) eq '996699-c2a6d2'}selected{/if}>{tr}Purple{/tr}</option>
							<option value="cc0000-ff9966" style="background-color:#ff9966;color:#cc0000" {if ($customColors) eq 'cc0000-ff9966'}selected{/if}>{tr}Red{/tr}</option>
							<option value="996600-ffcc66" style="background-color:#ffcc66;color:#996600" {if ($customColors) eq '996600-ffcc66'}selected{/if}>{tr}Orange{/tr}</option>
							<option value="666600-ffff00" style="background-color:#ffff00;color:#666600" {if ($customColors) eq '666600-ffff00'}selected{/if}>{tr}Yellow{/tr}</option>
							<option value="0">{tr}Let me select my own colors{/tr}</option>
						</select>
					</td>
				</tr>
				<tr>
					<td>{tr}Custom foreground color:{/tr}</td>
					<td>
						<input id="fgColorField" type="text" name="options[customfgcolor]" value="{$customfgcolor}" size="6" /><i>{tr}Ex:{/tr} FFFFFF</i>
					</td>
				</tr>
				<tr>
					<td>{tr}Custom background color:{/tr}</td>
					<td>
						<input id="bgColorField" type="text" name="options[custombgcolor]" value="{$custombgcolor}" size="6" /><i>{tr}Ex:{/tr} 000000</i>
					</td>
				</tr>
				<tr>
					<td>{tr}Status{/tr}</td>
					<td>
						<select name="customstatus">
							<option value='y' {if $info.customstatus ne 'n'}selected="selected"{/if}>{tr}Yes{/tr}</option>
							<option value='n' {if $info.customstatus eq 'n'}selected="selected"{/if}>{tr}No{/tr}</option>
						</select>
						<br />
						{tr}Default event status:{/tr}
						{html_options name='options[defaulteventstatus]' options=$eventstatus selected=$defaulteventstatus}
						<br />
						{tr}Show in popup box{/tr}<input type="checkbox" name="show[status]" value="on"{if $info.show_status eq 'y'} checked="checked"{/if} />
						{tr}Show in calendar view{/tr}<input type="checkbox" name="show[status_calview]" value="on"{if $info.show_status_calview ne 'n'} checked="checked"{/if} />
					</td>
				</tr>
				{if $prefs.feature_groupalert eq 'y'}
					<tr>
						<td>{tr}Group of users alerted when calendar event is modified{/tr}</td>
						<td>
							<select id="groupforAlert" name="groupforAlert">
								<option value="">&nbsp;</option>
								{foreach key=k item=i from=$groupforAlertList}
									<option value="{$k|escape}" {$i}>{$k|escape}</option>
								{/foreach}
							</select>
						</td>
					</tr>
					<tr>
						<td>{tr}Allows to select each user for small groups{/tr}</td>
						<td>
							<input type="checkbox" name="showeachuser" {if $showeachuser eq 'y'}checked="checked"{/if} />
						</td>
					</tr>
				{/if}
				<tr>
					<td>&nbsp;</td>
					<td>
						<input type="submit" name="save" value="{tr}Save{/tr}" />
					</td>
				</tr>
			</table>
			<br />
			{if $calendarId}{$name|escape} : {/if}
			{tr}Delete events older than:{/tr} <input type="text" name="days" value="0"/> {tr}days{/tr} <input type="submit" name="clean" value="{tr}Delete{/tr}" />
		</form>
	{/tab}
{/tabset}
