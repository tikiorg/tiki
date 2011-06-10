{* $Id$ *}

{title help="Trackers" admpage="trackers"}{tr}Admin Trackers{/tr}{/title}

<div class="navbar">
	{include file="tracker_actions.tpl"}
</div>

{tabset name='tabs_admtrackers'}

{* --- tab with list --- *}
{tab name="{tr}Trackers{/tr}"}
<a name="view"></a>
	<h2>{tr}Trackers{/tr}</h2>
	{if ($channels) or ($find)}
		{include file='find.tpl' filters=''}
		{if ($find) and ($channels)}
			<p>{tr}Found{/tr} {$channels|@count} {tr}trackers:{/tr}</p>
		{/if}
	{/if}

	<table class="normal">
		<tr>
			<th>{self_link _sort_arg='sort_mode' _sort_field='trackerId'}{tr}Id{/tr}{/self_link}</th>
			<th>{self_link _sort_arg='sort_mode' _sort_field='name'}{tr}Name{/tr}{/self_link}</th>
			<th>{self_link _sort_arg='sort_mode' _sort_field='description'}{tr}Description{/tr}{/self_link}</th>
			<th>{self_link _sort_arg='sort_mode' _sort_field='created'}{tr}Created{/tr}{/self_link}</th>
			<th>{self_link _sort_arg='sort_mode' _sort_field='lastModif'}{tr}Last Modif{/tr}{/self_link}</th>
			<th style="text-align:right;">{self_link _sort_arg='sort_mode' _sort_field='items'}{tr}Items{/tr}{/self_link}</th>
			<th>{tr}Action{/tr}</th>
		</tr>
		{cycle values="odd,even" print=false}
		{section name=user loop=$channels}
			<tr class="{cycle}">
				<td class="id">
					<a class="tablename" href="tiki-admin_trackers.php?trackerId={$channels[user].trackerId}&amp;cookietab=2" title="{tr}Edit{/tr}">{$channels[user].trackerId}</a>
				</td>
				<td class="text">
					<a class="tablename" href="tiki-admin_trackers.php?trackerId={$channels[user].trackerId}&amp;cookietab=2" title="{tr}Edit{/tr}">{$channels[user].name|escape}</a>
				</td>
				{if $channels[user].descriptionIsParsed eq 'y'}
					<td class="text">{wiki}{$channels[user].description}{/wiki}</td>
				{else}
					<td class="text">{$channels[user].description|escape|nl2br}</td>
				{/if}
				<td class="date">{$channels[user].created|tiki_short_date}</td>
				<td class="date">{$channels[user].lastModif|tiki_short_date}</td>
				<td class="integer">{$channels[user].items}</td>
				<td class="action">
					<a title="{tr}Edit{/tr}" href="tiki-admin_trackers.php?trackerId={$channels[user].trackerId}&amp;cookietab=2">{icon _id='page_edit'}</a>
					<a title="{tr}View{/tr}" href="tiki-view_tracker.php?trackerId={$channels[user].trackerId}">{icon _id='magnifier' alt="{tr}View{/tr}"}</a>
					<a title="{tr}Fields{/tr}" class="link" href="tiki-admin_tracker_fields.php?trackerId={$channels[user].trackerId}">{icon _id='table' alt="{tr}Fields{/tr}"}</a>
					{if $channels[user].individual eq 'y'}
						<a title="{tr}Active Permissions{/tr}" class="link" href="tiki-objectpermissions.php?objectName={$channels[user].name|escape:"url"}&amp;objectType=tracker&amp;permType=trackers&amp;objectId={$channels[user].trackerId}">{icon _id='key_active' alt="{tr}Active Permissions{/tr}"}</a>
					{else}
						<a title="{tr}Permissions{/tr}" class="link" href="tiki-objectpermissions.php?objectName={$channels[user].name|escape:"url"}&amp;objectType=tracker&amp;permType=trackers&amp;objectId={$channels[user].trackerId}">{icon _id='key' alt="{tr}Permissions{/tr}"}</a>
					{/if}
					<a title="{tr}Delete{/tr}" class="link" href="tiki-admin_trackers.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$channels[user].trackerId}">{icon _id='cross' alt="{tr}Delete{/tr}"}</a>
				</td>
			</tr>
		{sectionelse}
			{if $find}
				{norecords _colspan=7 _text="No records found with: $find"}
			{else}
				{norecords _colspan=7}
			{/if}
		{/section}
	</table>
	{pagination_links cant=$cant step=$maxRecords offset=$offset}{/pagination_links}
{/tab}

{if $trackerId}
	{capture assign='tabeditcreatetrk_admtrk'}{tr}Edit Tracker{/tr} <i>{$name|escape} (#{$trackerId})</i>{/capture}
{else}
	{assign var='tabeditcreatetrk_admtrk' value="{tr}Create Tracker{/tr}"}
{/if}
	
{tab name=$tabeditcreatetrk_admtrk}
{* --- tab with form --- *}
<a name="mod"></a>
	<h2>{tr}Create/Edit Tracker{/tr}</h2>
	{if $trackerId}
		{include file='object_perms_summary.tpl' objectName=$name objectType='tracker' objectId=$trackerId permType=$permsType}
	{/if}
	<form action="tiki-admin_trackers.php" method="post" name="editpageform" id="editpageform">
		<input type="hidden" name="trackerId" value="{$trackerId|escape}" />
		<table class="formcolor">
			<tr>
				<td>{tr}Name:{/tr}</td>
				<td>
					<input type="text" name="name" value="{$name|escape}" />
				</td>
			</tr>

			<tr>
				<td>{tr}Description:{/tr}</td>
				<td>
					{tr}Description text is wiki-parsed:{/tr} 
					<input type="checkbox" name="descriptionIsParsed" {if $descriptionIsParsed eq 'y'}checked="checked"{/if} onclick="toggleBlock('trackerDesc');" />
					<div id="trackerDesc" style="display:none;" >
						{toolbars qtnum="trackerDesc" area_id="trackerDescription"}
						{if $descriptionIsParsed eq 'y'}
							{jq}toggleBlock('trackerDesc');{/jq}
						{/if}
					</div>
					<textarea id="trackerDescription" name="description" rows="4" cols="40">{$description|escape}</textarea>
				</td>
			</tr>

			{if $prefs.feature_categories eq 'y'}
				{include file='categorize.tpl' colsCategorize=2}
				<tr>
					<td>{tr}Auto create corresponding categories{/tr}</td>
					<td>
						<input type="checkbox" name="autoCreateCategories" {if $autoCreateCategories eq 'y'}checked="checked"{/if} />
					</td>
				</tr>
			{/if}

			{if $prefs.groupTracker eq 'y'}
				<tr>
					<td>
						<label for="autoCreateGroup">{tr}Create a group for each item{/tr}</label>
					</td>
					<td>
						<input type="checkbox" id="autoCreateGroup" name="autoCreateGroup" {if $info.autoCreateGroup eq 'y'}checked="checked"{/if} onclick="toggleTrTd('autoCreateGroupOptions');toggleTrTd('autoCreateGroupOptions2');toggleTrTd('autoCreateGroupOptions3');toggleTrTd('autoCreateGroupOptions4');"/>
					</td>
				<tr id="autoCreateGroupOptions"{if $info.autoCreateGroup ne 'y' and $prefs.javascript_enabled eq 'y'} style="display:none;"{/if}>
					<td></td>
					<td>
						<label for="autoCreateGroupInc">{tr}Groups will include{/tr}</label>
						<select id="autoCreateGroupInc" name="autoCreateGroupInc">
							<option value="">{tr}None{/tr}</option>
							{foreach item=gr from=$all_groupIds}
								<option value="{$gr.id|escape}" {if $gr.id eq $info.autoCreateGroupInc} selected="selected"{/if}>{$gr.groupName|truncate:"52":" ..."|escape}</option>
							{/foreach}
						</select>
					</td>
				</tr>
				<tr id="autoCreateGroupOptions2"{if $info.autoCreateGroup ne 'y' and $prefs.javascript_enabled eq 'y'} style="display:none;"{/if}>
					<td></td>
					<td>
						<label for="autoAssignCreatorGroup">{tr}Creator is assigned to the group{/tr}</label>
						<input type="checkbox" name="autoAssignCreatorGroup" id="autoAssignCreatorGroup" {if $info.autoAssignCreatorGroup eq 'y'}checked="checked"{/if} />
					</td>
				</tr>
				<tr id="autoCreateGroupOptions3"{if $info.autoCreateGroup ne 'y' and $prefs.javascript_enabled eq 'y'} style="display:none;"{/if}>
					<td></td>
					<td>
						<label for="autoAssignCreatorGroupDefault">{tr}and it becomes his default group{/tr}</label>
						<input type="checkbox" name="autoAssignCreatorGroupDefault" id="autoAssignCreatorGroupDefault" {if $info.autoAssignCreatorGroupDefault eq 'y'}checked="checked"{/if} />
					</td>
				</tr>
				<tr id="autoCreateGroupOptions4"{if $info.autoCreateGroup ne 'y' and $prefs.javascript_enabled eq 'y'} style="display:none;"{/if}>
					<td></td>
					<td>
						<label for="autoAssignGroupItem">{tr}and it becomes the new item group creator{/tr}</label>
						<input type="checkbox" name="autoAssignGroupItem" id="autoAssignGroupItem" {if $info.autoAssignGroupItem eq 'y'}checked="checked"{/if} onclick="toggleTrTd('autoCreateGroupOptions5');"/>
					</td>
				</tr>
				<tr id="autoCreateGroupOptions5"{if ($info.autoCreateGroup ne 'y' or $info.autoAssignGroupItem ne 'y') and $prefs.javascript_enabled eq 'y'} style="display:none;"{/if}>
					<td></td>
					<td>
						<label for="autoCopyGroup">{tr}But copy the default group in this fiedlId before updating the group{/tr}</label>
						<input type="text" name="autoCopyGroup" id="autoCopyGroup" value="{$info.autoCopyGroup}" />
					</td>
				</tr>
			{/if}

			<tr>
				<td>{tr}Show status{/tr}</td>
				<td><input type="checkbox" name="showStatus" {if $showStatus eq 'y'}checked="checked"{/if} /></td>
			</tr>

			<tr>
				<td>{tr}Default status displayed in list mode{/tr}</td>
				<td>
					{foreach key=st item=stdata from=$status_types}
						<input type="checkbox" name="defaultStatus[]" value="{$st}"{if $defaultStatusList.$st} checked="checked"{/if} />
						{$stdata.label}
						<br />
					{/foreach}
				</td>
			</tr>

			<tr>
				<td>{tr}Show status to tracker admin only{/tr}</td>
				<td>
					<input type="checkbox" name="showStatusAdminOnly" {if $showStatusAdminOnly eq 'y'}checked="checked"{/if} />
				</td>
			</tr>

			<tr>
				<td>{tr}Send copies of all activity in this tracker to this e-mail address{/tr}</td>
				<td>
					<input type="text" size="60" name="outboundEmail" value="{$outboundEmail|escape}" />
					<br />
					<i>{tr}You can add several email addresses by separating them with commas.{/tr}</i>
				</td>
			</tr>

			<tr>
				<td>{tr}Use simplified e-mail format{/tr}</td>
				<td>
					<input type="checkbox" name="simpleEmail" {if $simpleEmail eq 'y'}checked="checked"{/if} />
					<br />
					<i>{tr}The tracker will use the text field named Subject if any as subject and will use the user email or for anonymous the email field if any as sender{/tr}</i>
				</td>
			</tr>

			<tr>
				<td>{tr}New items are created with status{/tr}</td>
				<td>
					<select name="newItemStatus">
						{foreach key=st item=stdata from=$status_types}
							<option value="{$st}"{if $newItemStatus eq $st} selected="selected"{/if}>{$stdata.label}</option>
						{/foreach}
					</select>
				</td>
			</tr>

			<tr>
				<td>{tr}Authoritative status for modified items{/tr}</td>
				<td>
					<select name="modItemStatus">
						<option value="">{tr}No change{/tr}</option>
						{foreach key=st item=stdata from=$status_types}
							<option value="{$st}"{if $modItemStatus eq $st} selected="selected"{/if}>{$stdata.label}</option>
						{/foreach}
					</select>
				</td>
			</tr>

			<tr>
				<td>{tr}Item creator can modify his items?{/tr}</td>
				<td>
					<input type="checkbox" name="writerCanModify" {if $writerCanModify eq 'y'}checked="checked"{/if} />
					<br />
					<i>{tr}The tracker needs a user field with the option 1{/tr}</i>
					<br />{tr}User can take ownership of item created by anonymous{/tr}<input type="checkbox" name="userCanTakeOwnership" {if $userCanTakeOwnership eq 'y'}checked="checked"{/if} />
				</td>
			</tr>

			<tr>
				<td>{tr}Only one item per user or IP{/tr}</td>
				<td>
					<input type="checkbox" name="oneUserItem" {if $oneUserItem eq 'y'}checked="checked"{/if} />
					<br />
					<i>{tr}The tracker needs a user or IP field with the option 1{/tr}</i>
				</td>
			</tr>

			<tr>
				<td>{tr}Member of the creator group can modify items?{/tr}</td>
				<td>
					<input type="checkbox" name="writerGroupCanModify" {if $writerGroupCanModify eq 'y'}checked="checked"{/if} />
					<br />
					<i>{tr}The tracker needs a group field with the option 1{/tr}</i>
				</td>
			</tr>

			<tr>
				<td>{tr}Show creation date when listing tracker items?{/tr}</td>
				<td>
					<input type="checkbox" name="showCreated" {if $showCreated eq 'y'}checked="checked"{/if} onclick="toggleTrTd('showCreatedOptions')" />
				</td>
			</tr>
			<tr id="showCreatedOptions" {if $showCreated ne 'y'}style="display:none;"{/if}>
				<td class="sub" colspan="2">
					{tr}Format if not the default short one:{/tr}
					<input type="text" name="showCreatedFormat" value="{$showCreatedFormat}"/>
					<a class="link" target="strftime" href="http://www.php.net/manual/en/function.strftime.php">{tr}Date and Time Format Help{/tr}</a>
				</td>
			</tr>

			<tr>
				<td>{tr}Show creation date when viewing tracker item?{/tr}</td>
				<td>
					<input type="checkbox" name="showCreatedView" {if $showCreatedView eq 'y'}checked="checked" {/if}onclick="toggleTrTd('showCreatedUser')" />
				</td>
			</tr>
			<tr id="showCreatedUser" {if $showCreatedView ne 'y'}style="display:none;"{/if}>
				<td class="sub" colspan="2">
					{tr}Identify creation user in tracker item?{/tr}
					<input type="checkbox" name="showCreatedBy"{if $showCreatedBy eq 'y'} checked="checked"{/if} />
				</td>
			</tr>

			<tr>
				<td>{tr}Show lastModif date when listing tracker items?{/tr}</td>
				<td>
					<input type="checkbox" name="showLastModif" {if $showLastModif eq 'y'}checked="checked" {/if}onclick="toggleTrTd('showLastModifOptions')" />
				</td>
			</tr>
			<tr id="showLastModifOptions" {if $showLastModif ne 'y'}style="display:none;"{/if}>
				<td class="sub" colspan="2">
					{tr}Format if not the default short one:{/tr}
					<input type="text" name="showLastModifFormat" value="{$showLastModifFormat}"/>
					<a class="link" target="strftime" href="http://www.php.net/manual/en/function.strftime.php">{tr}Date and Time Format Help{/tr}</a>
				</td>
			</tr>

			<tr>
				<td>{tr}Show lastModif date when viewing tracker item?{/tr}</td>
				<td>
					<input type="checkbox" name="showLastModifView" {if $showLastModifView eq 'y'}checked="checked" {/if}onclick="toggleTrTd('showLastModifUser')" />
				</td>
			</tr>
			<tr id="showLastModifUser" {if $showLastModifView ne 'y'}style="display:none;"{/if}>
				<td class="sub" colspan="2">
					{tr}Identify lastModif user in tracker item?{/tr}
					<input type="checkbox" name="showLastModifBy" {if $showLastModifBy eq 'y'}checked="checked" {/if}/>
				</td>
			</tr>
			<tr>
				<td>{tr}What field is used for default sort?{/tr}</td>
				<td>
					<select name="defaultOrderKey">
						{section name=x loop=$fields}
							<option value="{$fields[x].fieldId}"{if $defaultOrderKey eq $fields[x].fieldId} selected="selected"{/if}>{$fields[x].name|truncate:42:" ..."|escape}</option>
						{/section}
						<option value="-1"{if $defaultOrderKey eq -1} selected="selected"{/if}>{tr}LastModif{/tr}</option>
						<option value="-2"{if $defaultOrderKey eq -2} selected="selected"{/if}>{tr}Created{/tr}</option>
						<option value="-3"{if $defaultOrderKey eq -3} selected="selected"{/if}>{tr}ItemId{/tr}</option>
					</select>
				</td>
			</tr>

			<tr>
				<td>{tr}What is default sort order in list?{/tr}</td>
				<td>
					<select name="defaultOrderDir">
						<option value="asc" {if $defaultOrderDir eq 'asc'}selected="selected"{/if}>{tr}ascending{/tr}</option>
						<option value="desc" {if $defaultOrderDir eq 'desc'}selected="selected"{/if}>{tr}descending{/tr}</option>
					</select>
				</td>
			</tr>

			<tr>
				<td>{tr}Tracker items allow ratings?{/tr}</td>
				<td>
					<input type="checkbox" name="useRatings" {if $useRatings eq 'y'}checked="checked"{/if} onclick="toggleTrTd('ratingoptions');toggleTrTd('ratinginlisting');" />
				</td>
			</tr>
			<tr id="ratingoptions" {if $useRatings ne 'y'}style="display:none;"{/if}>
				<td class="sub">{tr}with values{/tr}</td>
				<td>
					<input type="text" name="ratingOptions" value="{if $ratingOptions}{$ratingOptions}{else}-2,-1,0,1,2{/if}" />
				</td>
			</tr>
			<tr id="ratinginlisting" {if $useRatings ne 'y'}style="display:none;"{/if}>
				<td class="sub">{tr}and display rating results in listing?{/tr}</td>
				<td>
					<input type="checkbox" name="showRatings" {if $showRatings eq 'y'}checked="checked"{/if} />
				</td>
			</tr>

			<tr>
				<td>{tr}Tracker items allow comments?{/tr}</td>
				<td>
					<input type="checkbox" name="useComments" {if $useComments eq 'y'}checked="checked"{/if} onclick="toggleTrTd('commentsoptions');toggleTrTd('commentsoptions2');" />
				</td>
			</tr>
			<tr id="commentsoptions" {if $useComments ne 'y'and $prefs.javascript_enabled eq 'y'}style="display:none;"{/if}>
				<td class="sub">{tr}and display comments in listing?{/tr}</td>
				<td>
					<input type="checkbox" name="showComments" {if $showComments eq 'y'}checked="checked"{/if} />
				</td>
			</tr>
			<tr id="commentsoptions2" {if $useComments ne 'y' and $prefs.javascript_enabled eq 'y'}style="display:none;"{/if}>
				<td class="sub">{tr}and display last comment user/date?{/tr}</td>
				<td>
					<input type="checkbox" name="showLastComment" {if $showLastComment eq 'y'}checked="checked"{/if} />
				</td>
			</tr>
			<tr>
				<td>{tr}Tracker items allow attachments?{/tr}</td>
				<td>
					<input type="checkbox" name="useAttachments" {if $useAttachments eq 'y'}checked="checked"{/if} onclick="toggleTrTd('attachmentsoptions');toggleTrTd('attachmentsconf');" />
				</td>
			</tr>
			<tr id="attachmentsoptions" {if $useAttachments ne 'y' and $prefs.javascript_enabled eq 'y'}style="display:none;"{/if}>
				<td class="sub">{tr}and display attachments in listing?{/tr}</td>
				<td>
					<input type="checkbox" name="showAttachments" {if $showAttachments eq 'y'}checked="checked"{/if} />
				</td>
			</tr>
			<tr id="attachmentsconf" {if $useAttachments ne 'y' and $prefs.javascript_enabled eq 'y'}style="display:none;"{/if}>
				<td class="sub" colspan="5">
					{tr}Attachment display options (Use numbers to order items, 0 will not be displayed, and negative values display in popups){/tr}
					<table class="normal">
						<tr>
							<td>{tr}Filename{/tr}</td>
							<td>{tr}Created{/tr}</td>
							<td>{tr}Downloads{/tr}</td>
							<td>{tr}Comment{/tr}</td>
							<td>{tr}Filesize{/tr}</td>
							<td>{tr}Version{/tr}</td>
							<td>{tr}Filetype{/tr}</td>
							<td>{tr}LongDesc{/tr}</td>
							<td>{tr}User{/tr}</td>
						</tr>
						<tr>
							<td><input type="text" size="2" name="ui[filename]" value="{$ui.filename}" /></td>
							<td><input type="text" size="2" name="ui[created]" value="{$ui.created}" /></td>
							<td><input type="text" size="2" name="ui[hits]" value="{$ui.hits}" /></td>
							<td><input type="text" size="2" name="ui[comment]" value="{$ui.comment}" /></td>
							<td><input type="text" size="2" name="ui[filesize]" value="{$ui.filesize}" /></td>
							<td><input type="text" size="2" name="ui[version]" value="{$ui.version}" /></td>
							<td><input type="text" size="2" name="ui[filetype]" value="{$ui.filetype}" /></td>
							<td><input type="text" size="2" name="ui[longdesc]" value="{$ui.longdesc}" /></td>
							<td><input type="text" size="2" name="ui[user]" value="{$ui.user}" /></td>
						</tr>
					</table>
				</td>
			</tr>

			<tr>
				<td>{tr}Items can be created only during a certain time{/tr}</td>
				<td>
					&nbsp;&nbsp;&nbsp;&nbsp;{tr}After:{/tr} 
					<input type="checkbox" name="start"{if $info.start} checked="checked"{/if} /> 
					{html_select_date prefix="start_" time=$info.start start_year="0" end_year="+10" field_order=$prefs.display_field_order} 
					<span dir="ltr">{html_select_time prefix="start_" time=$info.start display_seconds=false use_24_hours=$use_24hr_clock}</span>
					&nbsp;{$siteTimeZone}
					<br />
					{tr}Before:{/tr}
					<input type="checkbox" name="end"{if $info.end} checked="checked"{/if} /> 
					{html_select_date prefix="end_" time=$info.end start_year="0" end_year="+10" field_order=$prefs.display_field_order} 
					<span dir="ltr">{html_select_time prefix="end_" time=$info.end display_seconds=false use_24_hours=$use_24hr_clock}</span>
					&nbsp;{$siteTimeZone}
				</td>
			</tr>

			<tr>
				<td>{tr}Do not show empty fields in item view?{/tr}</td>
				<td>
					<input type="checkbox" name="doNotShowEmptyField" {if $doNotShowEmptyField eq 'y'}checked="checked"{/if} />
				</td>
			</tr>

			<tr>
				<td>{tr}Show these fields (ID comma separated) in a popup on item link when listing tracker items?{/tr}</td>
				<td><input type="text" name="showPopup" value="{$showPopup|escape}" /></td>
			</tr>

			{if $prefs.feature_groupalert eq 'y'}
				<tr>
					<td>{tr}Group of users alerted when tracker is modified{/tr}</td>
					<td>
						<select id="groupforAlert" name="groupforAlert">
							<option value="">&nbsp;</option>
							{foreach key=k item=i from=$groupforAlertList}
								<option value="{$k}" {$i}>{$k}</option>
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
				<td>{tr}Wiki page to display an item{/tr}</td>
				<td><input type="text" name="viewItemPretty" value="{$info.viewItemPretty|escape}" />
				<br /><em>{tr}wiki:pageName for a wiki page or tpl:tplName for a template{/tr}</em></td>
			</tr>

			<tr>
				<td>{tr}Wiki page to edit an item{/tr}</td>
				<td><input type="text" name="editItemPretty" value="{$info.editItemPretty|escape}" />
				<br /><em>{tr}wiki:pageName for a wiki page or tpl:tplName for a template{/tr}</em></td>
			</tr>

			{if !empty($info.todos)}
				<tr>
					<td>{tr}Status changes list{/tr}</td>
					<td>
						{cycle values="odd,even" print=false}
						<table class="normal">
						<tr><th>{tr}From{/tr}</th><th>{tr}To{/tr}</th><th>{tr}Delay{/tr}</th><th>{tr}After{/tr}</th><th>{tr}Notification{/tr}</th><th>{tr}Action{/tr}</th></tr>
						{foreach from=$info.todos item=todo}
							<tr class="{cycle}">
								<td>{$todo.from.status|escape}</td>
								<td>{$todo.to.status|escape}</td>
								<td>{$todo.after|duration|escape}</td>
								<td>{tr}{$todo.event}{/tr}</td>
								<td>
									{foreach from=$todo.notifs item=notif name=notif}
										{if !$smarty.foreach.notif.first}<br />{/if}
										{foreach from=$notif.to key=i item=j name=notif2}
											{if !$smarty.foreach.notif2.first}<br />{/if}
											{$i|escape}: {if $i eq 'before'}{$j|duration|escape}{else}{$j|escape}{/if}
										{/foreach}
									{/foreach}
								</td>
								<td><a title="{tr}Delete todo{/tr}" class="link" href="tiki-admin_trackers.php?trackerId={$trackerId}&amp;deltodo={$todo.todoId}">{icon _id='cross' alt="{tr}Delete{/tr}"}</a></td>
							</tr>
						{/foreach}
						</table>
					</td>
				</tr>
			{/if}
			<tr>
				<td>{tr}Status changes{/tr}</td>
				<td>			
					<label>
						{tr}From{/tr}
						<select name="todo_from">
							<option value=""></option>
							{foreach key=st item=stdata from=$status_types}
								<option value="{$st|escape}">{$stdata.label|escape}</option>
							{/foreach}
						</select>
					</label>
					<label>
						{tr}To{/tr}
						<select name="todo_to">
							<option value=""></option>
							{foreach key=st item=stdata from=$status_types}
								<option value="{$st|escape}">{$stdata.label|escape}</option>
							{/foreach}
						</select>
					</label><br />
					{html_select_duration prefix='todo_after'}
					<select name="todo_event">
						<option value="creation">{tr}After creation{/tr}</option>
						<option value="modification">{tr}After last modification{/tr}</option>
					</select>
					<fieldset>
						<legend>{tr}Notification{/tr}</legend>
						{tr}Warn creator of an upcoming status change{/tr}{html_select_duration prefix='todo_notif'}{tr}before{/tr}<br />
						<label>{tr}Mail subject text{/tr}<input type="text" name="todo_subject" /></label><br />
						<label>{tr}Mail body ressource{/tr}<input type="text" name="todo_body" /></label><em><br />{tr}wiki:pageName for a wiki page or tplName.tpl for a template{/tr}</em>
					</fieldset>
				</td>
			</tr>
				
			<tr>
				<td></td>
				<td><input type="submit" name="save" value="{tr}Save{/tr}" /></td>
			</tr>
		</table>
	</form>
{/tab}

{tab name="{tr}Import/Export{/tr}"}
<h2>{tr}Tracker Import/Export{/tr}</h2>
{tabset}
{* --- tab with raw form --- *}
{tab name="{tr}Import/export trackers{/tr}"}
	
	<h3>{tr}Tracker Definition{/tr}</h3>
	<form action="tiki-admin_trackers.php" method="post">
		<input type="hidden" name="trackerId" value="{$trackerId|escape}" />
		<input type="hidden" name="import" value="1" />
		<textarea name="rawmeat" cols="62" rows="32">
{if $trackerId}
[TRACKER]
trackerId = {$trackerId}
name = {$name|escape}
description = {$description|escape}
descriptionIsParsed = {$descriptionIsParsed}
useExplicitNames = {$useExplicitNames}
showStatus = {$showStatus}
defaultStatus = {foreach key=st item=stdata from=$status_types}{if $defaultStatusList.$st}{$st}{/if}{/foreach}

showStatusAdminOnly = {$showStatusAdminOnly}
outboundEmail = {$outboundEmail|escape}
simpleEmail = {$simpleEmail}
newItemStatus = {$newItemStatus}
modItemStatus = {$modItemStatus}
writerCanModify = {$writerCanModify}
writerGroupCanModify = {$writerGroupCanModify}
showCreated = {$showCreated}
showLastModif = {$showLastModif}
defaultOrderKey = {$defaultOrderKey}
defaultOrderDir = {$defaultOrderDir}
useComments = {$useComments}
showComments = {$showComments}
useAttachments = {$useAttachments}
showAttachments = {$showAttachments}
attachmentsconf = {$ui.filename|default:0},{$ui.created|default:0},{$ui.hits|default:0},{$ui.comment|default:0},{$ui.filesize|default:0},{$ui.version|default:0},{$ui.filetype|default:0},{$ui.longdesc|default:0}
useRatings = {$useRatings}
ratingOptions = {$ratingOptions}
categories = {$catsdump}
{/if}
		</textarea>
		<br />
		<input type="submit" name="save" value="{tr}Import{/tr}" />
	</form>
	{if $trackerId}
		<h3>{tr}Export for profile{/tr}</h3>
		{button href="tiki-admin_trackers.php?trackerId=$trackerId&exportTrackerProfile=y" _text="{tr}Export tracker{/tr}"}
	{/if}
	{/tab}
	
	{if $trackerId}
		{include file='tiki-export_tracker.tpl'}

		{tab name="{tr}Import CSV data{/tr}"}
		<h3>{tr}Tracker Items Import{/tr}</h3>
		<form action="tiki-import_tracker.php?trackerId={$trackerId}" method="post" enctype="multipart/form-data">
			<table class="formcolor">
				<tr>
					<td>{tr}File{/tr}</td>
					<td><input name="importfile" type="file" /></td>
				</tr>
				<tr>
					<td>{tr}Date Format{/tr}</td>
					<td>
						<input type="radio" name="dateFormat" value="mm/dd/yyyy" checked="checked"/>
						{tr}month{/tr}/{tr}day{/tr}/{tr}year{/tr}(01/31/2008)
						<br />
						<input type="radio" name="dateFormat" value="dd/mm/yyyy" />
						{tr}day{/tr}/{tr}month{/tr}/{tr}year{/tr}(31/01/2008)
						<br />
						<input type="radio" name="dateFormat" value="yyyy-mm-dd" />
						{tr}year{/tr}-{tr}month{/tr}-{tr}day{/tr}(2008-01-31)
						<br />
						<input type="radio" name="dateFormat" value="" />{tr}timestamp{/tr}
					</td>
				</tr>
				<tr>
					<td>{tr}Charset encoding{/tr}</td>
					<td>
						<select name="encoding">
							<option value="UTF-8" selected="selected">{tr}UTF-8{/tr}</option>
							<option value="ISO-8859-1">{tr}ISO-8859-1{/tr}</option>
						</select>
					</td>
				</tr>
				<tr>
					<td>{tr}Separator{/tr}</td>
					<td><input type="text" name="separator" value="," size="2" /></td>
				</tr>
				<tr>
					<td>{tr}Add as new items:{/tr}</td>
					<td><input type="checkbox" name="add_items" /></td>
				</tr>
				<tr>
					<td>{tr}Update lastModif date if updating items (status and created are updated only if the fields are specified in the csv):{/tr}</td>
					<td><input type="checkbox" name="updateLastModif" checked="checked" /></td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td><input type="submit" name="save" value="{tr}Import{/tr}" /></td>
				</tr>
			</table>
		</form>
		{remarksbox type="note" title="{tr}Note{/tr}"}
			<ul>
				<li>{tr}The order of the fields does not matter, but you need to add a header with the field names{/tr}</li>
				<li>{tr}Add " -- " to the end of the fields in the header that you would like to import!{/tr}</li>
				<li>{tr}Auto-incremented itemid fields shall be included with no matter what values{/tr}</li>
			</ul>
		{/remarksbox}
		{/tab}
	{/if}
{/tabset}
{/tab}

{tab name="{tr}Duplicate Tracker{/tr}"}
{* --- tab with raw form --- *}
	<h2>{tr}Duplicate Tracker{/tr}</h2>

	<form action="tiki-admin_trackers.php" method="post">
		<table class="formcolor">
			<tr>
				<td>{tr}Name{/tr}</td>
				<td><input type="text" name="name" /></td>
			</tr>
			<tr>
				<td>{tr}Description{/tr}</td>
				<td colspan="2">
					{tr}Description text is wiki-parsed:{/tr} 
					<input type="checkbox" name="duplicateDescriptionIsParsed" {if $descriptionIsParsed eq 'y'}checked="checked"{/if} onclick="toggleBlock('duplicateTrackerDesc');" />
					<div id="duplicateTrackerDesc" style="display:none;" >
						{toolbars qtnum="duplicateTrackerDesc" area_id="duplicateTrackerDescription"}
						{if $descriptionIsParsed eq 'y'}
							{jq}toggleBlock('duplicateTrackerDesc');{/jq}
						{/if}
					</div>
					<br />
					<textarea id="duplicateTrackerDescription" name="description" rows="4" cols="40">{$description|escape}</textarea>
				</td>
			</tr>
			<tr>
				<td>{tr}Tracker{/tr}</td>
				<td>
					{section name=ix loop=$trackers}
						{if $smarty.section.ix.first}
							<select name="trackerId">
						{/if}
						<option value="{$trackers[ix].trackerId}"{if $trackerId eq $trackers[ix].trackerId} selected="selected"{/if}>{$trackers[ix].name|escape}</option>
						{if $smarty.section.ix.last}
							</select>
						{/if}
					{/section}
				</td>
			</tr>
			<tr>
				<td>{tr}Duplicate categories{/tr}</td>
				<td><input type="checkbox" name="dupCateg" /></td>
			</tr>
			<tr>
				<td>{tr}Duplicate perms{/tr}</td>
				<td><input type="checkbox" name="dupPerms" /></td>
			</tr>
			<tr>
				<td></td>
				<td><input type="submit" name="duplicate" value="{tr}Duplicate Tracker{/tr}" /></td>
			</tr>
		</table>
	</form>
{/tab}

{/tabset}
