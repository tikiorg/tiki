{if !empty($calendarId)}
	{title help=Calendar url="tiki-admin_calendars.php?calendarId=$calendarId" admpage="calendar"}{tr}Admin Calendars{/tr}{/title}
{else}
	{title help=Calendar url="tiki-admin_calendars.php" admpage="calendar"}{tr}Admin Calendars{/tr}{/title}
{/if}

<div class="t_navbar margin-bottom-md">
	{if !empty($calendarId) && $tiki_p_admin_calendar eq 'y'}
		<a role="button" href="tiki-admin_calendars.php?cookietab=2" class="btn btn-default">
			{icon name="create"} {tr}Create Calendar{/tr}
		</a>
	{/if}
	<a role="link" href="tiki-calendar.php" class="btn btn-link">
		{icon name="view"} {tr}View Calendars{/tr}
	</a>
	{if $tiki_p_admin_calendar eq 'y'}
		<a role="link" href="tiki-calendar_import.php" class="btn btn-link">
			{icon name="import"} {tr}Import{/tr}
		</a>
	{/if}
</div>

{tabset name='tabs_admin_calendars'}
	{tab name="{tr}List of Calendars{/tr}"}
		<h2>{tr}List of Calendars{/tr}</h2>

		{include file='find.tpl' find_in="<ul><li>{tr}Calendar name{/tr}</li></ul>"}
		{* Use css menus as fallback for item dropdown action menu if javascript is not being used *}
		{if $prefs.javascript_enabled !== 'y'}
			{$js = 'n'}
			{$libeg = '<li>'}
			{$liend = '</li>'}
		{else}
			{$js = 'y'}
			{$libeg = ''}
			{$liend = ''}
		{/if}
		<table class="table table-striped table-hover">
			<tr>
				<th>
					<a href="tiki-admin_calendars.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'calendarId_desc'}calendarId_asc{else}calendarId_desc{/if}">
						{tr}ID{/tr}
					</a>
				</th>
				<th>
					<a href="tiki-admin_calendars.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'name_desc'}name_asc{else}name_desc{/if}">
						{tr}Name{/tr}
					</a>
				</th>
				<th>
					<a href="tiki-admin_calendars.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'customlocations_desc'}customlocations_asc{else}customlocations_desc{/if}">
						{tr}Loc{/tr}
					</a>
				</th>
				<th>
					<a href="tiki-admin_calendars.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'customparticipants_desc'}customparticipants_asc{else}customparticipants_desc{/if}">
						{tr}Participants{/tr}
					</a>
				</th>
				<th>
					<a href="tiki-admin_calendars.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'customcategories_desc'}customcategories_asc{else}customcategories_desc{/if}">
						{tr}Cat{/tr}
					</a>
				</th>
				<th>
					<a href="tiki-admin_calendars.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'customlanguages_desc'}customlanguages_asc{else}customlanguages_desc{/if}">
						{tr}Lang{/tr}
					</a>
				</th>
				<th>{tr}Url{/tr}</th>
				<th>
					<a href="tiki-admin_calendars.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'custompriorities_desc'}custompriorities_asc{else}custompriorities_desc{/if}">
						{tr}Prio{/tr}
					</a>
				</th>
				<th>
					<a href="tiki-admin_calendars.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'customsubscription_desc'}customsubscription_asc{else}customsubscription_desc{/if}">
						{tr}Subscription{/tr}
					</a>
				</th>
				<th>
					<a href="tiki-admin_calendars.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'personal_desc'}personal_asc{else}personal_desc{/if}">
						{tr}Perso{/tr}
					</a>
				</th>
				<th></th>
			</tr>

			{foreach key=id item=cal from=$calendars}
				<tr>
					<td class="id">{$id}</td>
					<td class="text">
						<a class="tablename" href="tiki-admin_calendars.php?calendarId={$id}&cookietab=2" title="{tr}Edit{/tr}">{$cal.name|escape}</a>
						{if $cal.show_calname eq 'y'} {icon name="list-alt" class="tips" title="{tr}Name{/tr}:{tr}Field will show in popup{/tr}"}{/if}
					</td>
					<td class="text">
						{$cal.customlocations|yesno}{if $cal.show_location eq 'y'}{icon name="list-alt" class="tips" title="{tr}Custom locations{/tr}:{tr}Field will show in popup{/tr}"}{/if}
					</td>
					<td class="text">
						{$cal.customparticipants|yesno}{if $cal.show_participants eq 'y'}{icon name="list-alt" class="tips" title="{tr}Custom participants{/tr}:{tr}Field will show in popup{/tr}"}{/if}
					</td>
					<td class="text">
						{$cal.customcategories|yesno}{if $cal.show_category eq 'y'}{icon name="list-alt" class="tips" title="{tr}Custom categories{/tr}:{tr}Field will show in popup{/tr}"}{/if}
					</td>
					<td class="text">
						{$cal.customlanguages|yesno}{if $cal.show_language eq 'y'}{icon name="list-alt" class="tips" title="{tr}Custom languages{/tr}:{tr}Field will show in popup{/tr}"}{/if}
					</td>
					<td class="text">
						{$cal.customurl|yesno}{if $cal.show_url eq 'y'}{icon name="list-alt" class="tips" title="{tr}Custom url{/tr}:{tr}Field will show in popup{/tr}"}{/if}
					</td>
					<td class="text">{$cal.custompriorities|yesno}</td>
					<td class="text">{$cal.customsubscription|yesno}</td>
					<td class="text">{$cal.personal|yesno}</td>
					<td class="action">
						{capture name=admin_calendar_actions}
							{strip}
								{$libeg}<a href="tiki-admin_calendars.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;calendarId={$id}&cookietab=2">
									{icon name='edit' _menu_text='y' _menu_icon='y' alt="{tr}Edit{/tr}"}
								</a>{$liend}
								{$libeg}<a href="tiki-calendar.php?calIds[]={$id}">
									{icon name='view' _menu_text='y' _menu_icon='y' alt="{tr}View{/tr}"}
								</a>{$liend}
								{$libeg}<a href="tiki-calendar_edit_item.php?calendarId={$id}">
									{icon name='create' _menu_text='y' _menu_icon='y' alt="{tr}Add event{/tr}"}
								</a>{$liend}
								{$libeg}{permission_link mode=text type=calendar id=$id title=$cal.name}{$liend}
								{$libeg}<a href="tiki-admin_calendars.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;drop={$id}&amp;calendarId={$id}">
									{icon name='remove' _menu_text='y' _menu_icon='y' alt="{tr}Delete{/tr}"}
								</a>{$liend}
							{/strip}
						{/capture}
						{if $js === 'n'}<ul class="cssmenu_horiz"><li>{/if}
						<a
							class="tips"
							title="{tr}Actions{/tr}"
							href="#"
							{if $js === 'y'}{popup delay="0|2000" fullhtml="1" center=true text=$smarty.capture.admin_calendar_actions|escape:"javascript"|escape:"html"}{/if}
							style="padding:0; margin:0; border:0"
						>
							{icon name='wrench'}
						</a>
						{if $js === 'n'}
							<ul class="dropdown-menu" role="menu">{$smarty.capture.admin_calendar_actions}</ul></li></ul>
						{/if}
					</td>
				</tr>
			{foreachelse}
				{norecords _colspan=12}
			{/foreach}
		</table>

		{pagination_links cant=$cant step=$maxRecords offset=$offset}{/pagination_links}
	{/tab}

	{if $calendarId gt 0}
		{assign var="edtab" value="{tr}Edit Calendar{/tr}"}
	{else}
		{assign var="edtab" value="{tr}Create Calendar{/tr}"}
	{/if}
	{tab name=$edtab}
		<h2>{$edtab}</h2>

		<form action="tiki-admin_calendars.php" method="post" class="form-horizontal" role="form">
			<fieldset>
			<input type="hidden" name="calendarId" value="{$calendarId|escape}">
			{if $tiki_p_modify_object_categories eq 'y'}
				<div class="form-group">
					<div class="col-sm-12">
						{include file='categorize.tpl'}
					</div>
				</div>
			{/if}
			<div class="form-group">
				<label class="col-sm-3 control-label" for="calendarName">
					{tr}Name{/tr}
				</label>
				<div class="col-sm-6">
					<input type="text" class="form-control" name="name" id="calendarName" value="{$name|escape}">
				</div>
				<div class="checkbox col-sm-3">
					<label for="showCalnamePopup">
						<input type="checkbox" name="show[calname]" id="showCalnamePopup" value="on"{if $show_calname eq 'y'} checked="checked"{/if}>
						{tr}Show in popup box{/tr}
					</label>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-3 control-label" for="calendarDescription">
					{tr}Description{/tr}
				</label>
				<div class="col-sm-6">
					<textarea name="description" rows="5" wrap="virtual" class="form-control" id="calendarDescription">
						{$description|escape}
					</textarea>
				</div>
				<div class="checkbox col-sm-3">
					<label for="showCalDescriptionPopup" class="control-label">
						<input type="checkbox" id="showCalDescriptionPopup" name="show[description]" value="on"{if $show_description eq 'y'} checked="checked"{/if}>
						{tr}Show in popup box{/tr}
					</label>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-3 control-label" for="customlocations">
					{tr}Custom Locations{/tr}
				</label>
				<div class="col-sm-2">
					<select name="customlocations" id="customlocations" class="form-control">
						<option value='y' {if $customlocations eq 'y'}selected="selected"{/if}>{tr}Yes{/tr}</option>
						<option value='n' {if $customlocations eq 'n'}selected="selected"{/if}>{tr}No{/tr}</option>
					</select>
				</div>
				<div class="checkbox col-sm-3">
					<label>
						<input type="checkbox" name="show[location]" id="showCustomLocationsPopup" value="on"{if $show_location eq 'y'} checked="checked"{/if}>
						{tr}Show in popup box{/tr}
					</label>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-3 control-label" for="customparticipants">
					{tr}Custom Participants{/tr}
				</label>
				<div class="col-sm-2">
					<select name="customparticipants" id="customparticipants" class="form-control">
						<option value='y' {if $customparticipants eq 'y'}selected="selected"{/if}>{tr}Yes{/tr}</option>
						<option value='n' {if $customparticipants eq 'n'}selected="selected"{/if}>{tr}No{/tr}</option>
					</select>
				</div>
				<div class="checkbox col-sm-3">
					<label>
						<input type="checkbox" name="show[participants]" value="on"{if $show_participants eq 'y'} checked="checked"{/if}>
						{tr}Show in popup box{/tr}
					</label>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-3 control-label" for="customcategories">
					{tr}Custom Classifications{/tr}
				</label>
				<div class="col-sm-2">
					<select name="customcategories" id="customcategories" class="form-control">
						<option value='y' {if $customcategories eq 'y'}selected="selected"{/if}>{tr}Yes{/tr}</option>
						<option value='n' {if $customcategories eq 'n'}selected="selected"{/if}>{tr}No{/tr}</option>
					</select>
				</div>
				<div class="checkbox col-sm-3">
					<label>
						<input type="checkbox" name="show[category]" value="on"{if $show_category eq 'y'} checked="checked"{/if}>
						{tr}Show in popup box{/tr}
					</label>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-3 control-label" for="customlanguages">
					{tr}Custom Languages{/tr}
				</label>
				<div class="col-sm-2">
					<select name="customlanguages" id="customlanguages" class="form-control">
						<option value='y' {if $customlanguages eq 'y'}selected="selected"{/if}>{tr}Yes{/tr}</option>
						<option value='n' {if $customlanguages eq 'n'}selected="selected"{/if}>{tr}No{/tr}</option>
					</select>
				</div>
				<div class="checkbox col-sm-3">
					<label for="showlanguagepopup">
						<input type="checkbox" name="show[language]" id="showlanguagepopup" value="on"{if $show_language eq 'y'} checked="checked"{/if}>
						{tr}Show in popup box{/tr}
					</label>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-3 control-label" for="customurl">
					{tr}Custom URL{/tr}
				</label>
				<div class="col-sm-2">
					<select name="options[customurl]" id="customurl" class="form-control">
						<option value='y' {if $customurl eq 'y'}selected="selected"{/if}>{tr}Yes{/tr}</option>
						<option value='n' {if $customurl eq 'n'}selected="selected"{/if}>{tr}No{/tr}</option>
					</select>
				</div>
				<div class="checkbox col-sm-3">
					<label for="showurlpopup" class="control-label">
						<input type="checkbox" id="showurlpopup" name="show[url]" value="on"{if $show_url eq 'y'} checked="checked"{/if}>
						{tr}Show in popup box{/tr}
					</label>
				</div>
			</div>
			{if $prefs.feature_newsletters eq 'y'}
				<div class="form-group">
					<label class="col-sm-3 control-label" for="customsubscription">
						{tr}Custom Subscription List{/tr}
					</label>
					<div class="col-sm-2">
						<select name="customsubscription" id="customsubscription" class="form-control">
							<option value='y' {if $customsubscription eq 'y'}selected="selected"{/if}>{tr}Yes{/tr}</option>
							<option value='n' {if $customsubscription eq 'n'}selected="selected"{/if}>{tr}No{/tr}</option>
						</select>
					</div>
				</div>
			{/if}
			<div class="form-group">
				<label class="col-sm-3 control-label" for="custompriorities">
					{tr}Custom Priorities{/tr}
				</label>
				<div class="col-sm-2">
					<select name="custompriorities" id="custompriorities" class="form-control">
						<option value='y' {if $custompriorities eq 'y'}selected="selected"{/if}>{tr}Yes{/tr}</option>
						<option value='n' {if $custompriorities eq 'n'}selected="selected"{/if}>{tr}No{/tr}</option>
					</select>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-3 control-label" for="personal">
					{tr}Personal Calendar{/tr}
				</label>
				<div class="col-sm-2">
					<select name="personal" id="personal" class="form-control">
						<option value='y' {if $personal eq 'y'}selected="selected"{/if}>{tr}Yes{/tr}</option>
						<option value='n' {if $personal eq 'n'}selected="selected"{/if}>{tr}No{/tr}</option>
					</select>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-3 control-label" for="customcategories">
					{tr}Start of day{/tr}
				</label>
				<div class="col-sm-2">
					{html_select_time prefix="startday_" time=$info.startday display_minutes=false display_seconds=false use_24_hours=$use_24hr_clock}
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-3 control-label" for="customcategories">
					{tr}End of day{/tr}
				</label>
				<div class="col-sm-9">
					{html_select_time prefix="endday_" time=$info.endday display_minutes=false display_seconds=false use_24_hours=$use_24hr_clock}
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-3 control-label" for="customcategories">
					{tr}Days to display{/tr}
				</label>
				<div class="col-sm-9">
					{section name="viewdays" start=0 loop=7}
					<div class="checkbox-inline">
						<label class="control-label"><input type="checkbox" name="viewdays[]" value="{$smarty.section.viewdays.index}" {if !empty($info.viewdays) && in_array($smarty.section.viewdays.index,$info.viewdays)} checked="checked" {/if}>
							{$days_names[$smarty.section.viewdays.index]}
						</label>
					</div>
					{/section}
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-3 control-label" for="customcategories">
					{tr}Standard Colors{/tr}
				</label>
				<div class="col-sm-2">
					<select class="form-control" id="customcategories" name="options[customcolors]" onChange="javascript:document.getElementById('fgColorField').disabled=(this.options[this.selectedIndex].value != 0);document.getElementById('bgColorField').disabled=(this.options[this.selectedIndex].value != 0);">
						<option value="" />
						<option value="008400-99fa99" style="background-color:#99fa99;color:#008400" {if ($customColors) eq '008400-99fa99'}selected{/if}>{tr}Green{/tr}</option>
						<option value="3333ff-aaccff" style="background-color:#aaccff;color:#3333ff" {if ($customColors) eq '3333ff-aaccff'}selected{/if}>{tr}Blue{/tr}</option>
						<option value="996699-c2a6d2" style="background-color:#e0cae5;color:#996699" {if ($customColors) eq '996699-c2a6d2'}selected{/if}>{tr}Purple{/tr}</option>
						<option value="cc0000-ff9966" style="background-color:#ff9966;color:#cc0000" {if ($customColors) eq 'cc0000-ff9966'}selected{/if}>{tr}Red{/tr}</option>
						<option value="996600-ffcc66" style="background-color:#ffcc66;color:#996600" {if ($customColors) eq '996600-ffcc66'}selected{/if}>{tr}Orange{/tr}</option>
						<option value="666600-ffff00" style="background-color:#ffff00;color:#666600" {if ($customColors) eq '666600-ffff00'}selected{/if}>{tr}Yellow{/tr}</option>
					</select>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-3 control-label" for="fgColorField">
					{tr}Custom foreground color{/tr}
				</label>
				<div class="col-sm-9">
					<input id="fgColorField" type="text" name="options[customfgcolor]" value="{$customfgcolor}" size="6"> <i>{tr}Ex:{/tr} FFFFFF</i>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-3 control-label" for="bgColorField">
					{tr}Custom background color{/tr}
				</label>
				<div class="col-sm-9">
					<input id="bgColorField" type="text" name="options[custombgcolor]" value="{$custombgcolor}" size="6"> <i>{tr}Ex:{/tr} 000000</i>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-3 control-label" for="customstatus">
					{tr}Status{/tr}
				</label>
				<div class="col-sm-2">
					<select name="customstatus" id="customstatus" class="form-control">
						<option value='y' {if $info.customstatus ne 'n'}selected="selected"{/if}>{tr}Yes{/tr}</option>
						<option value='n' {if $info.customstatus eq 'n'}selected="selected"{/if}>{tr}No{/tr}</option>
					</select>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-3 control-label">
					{tr}Default event status:{/tr}
				</label>
				<div class="col-sm-3">
					{html_options class="form-control" name='options[defaulteventstatus]' options=$eventstatus selected=$defaulteventstatus}
				</div>
			</div>
			<div class="form-group">
				<div class="checkbox col-sm-4 col-sm-offset-3">
					<label class="control-label">
						<input type="checkbox" name="show[status]" value="on"{if $info.show_status eq 'y'} checked="checked"{/if}>
						{tr}Show in popup view{/tr}
					</label>
				</div>
			</div>
			<div class="form-group">
				<div class="checkbox col-sm-4 col-sm-offset-3">
					<label class="control-label">
						<input type="checkbox" name="show[status_calview]" value="on"{if $info.show_status_calview ne 'n'} checked="checked"{/if}>
						{tr}Show in calendar view{/tr}
					</label>
				</div>
			</div>
			{if $prefs.feature_groupalert eq 'y'}
				<div class="form-group">
					<label class="col-sm-3 control-label" for="groupforAlert">
						{tr}Group of users alerted when calendar event is modified{/tr}
					</label>
					<div class="col-sm-2">
						<select id="groupforAlert" name="groupforAlert" class="form-control">
							<option value="">&nbsp;</option>
							{foreach key=k item=i from=$groupforAlertList}
								<option value="{$k|escape}" {$i}>{$k|escape}</option>
							{/foreach}
						</select>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-3 control-label" for="showeachuser">
						{tr}Allows each user to be selected for small groups{/tr}
					</label>
					<div class="col-sm-2">
						<input type="checkbox" name="showeachuser" id="showeachuser" {if $showeachuser eq 'y'}checked="checked"{/if}>
					</div>
				</div>
			{/if}
			<div class="form-group">
				<label class="col-sm-3 control-label" for="allday">
					{tr}Default event to all day{/tr}
				</label>
				<div class="col-sm-9">
					<input type="checkbox" id="allday" name="allday"{if $info.allday eq 'y'} checked="checked"{/if}>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-3 control-label" for="nameoneachday">
					{tr}Event name on each day in calendar view{/tr}
				</label>
				<div class="col-sm-9">
					<input type="checkbox" name="nameoneachday"{if $info.nameoneachday eq 'y'} checked="checked"{/if}>
				</div>
			</div>
			<div class="form-group">
				<input type="submit" class="btn btn-primary col-sm-offset-3" name="save" value="{tr}Save{/tr}">
			</div>
			</fieldset>
			<fieldset>
				<legend>Delete old events</legend>
				<div class="form-group">
					<label class="col-sm-3 control-label" for="days">
						{tr}Delete events older than:{/tr}
					</label>
					<div class="col-sm-2 input-group">
						<input type="text" name="days" id="days" value="0" class="form-control">
						<span class="input-group-addon">
							{tr}days{/tr}
						</span>
					</div>
				</div>
				<div class="form-group">
					<input type="submit" class="btn btn-warning col-sm-offset-3" name="clean" value="{tr}Delete{/tr}">				</div>
			</fieldset>
		</form>
	{/tab}
{/tabset}
