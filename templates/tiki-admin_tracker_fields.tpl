{* $Id$ *}

{title help="Adding+fields+to+a+tracker" url="tiki-admin_tracker_fields.php?trackerId=$trackerId"}{tr}Admin Tracker:{/tr} {$tracker_info.name|escape}{/title}

<div class="navbar">
	{button href="tiki-list_trackers.php" _text="{tr}List Trackers{/tr}"}
	
	{if $tiki_p_admin_trackers eq 'y'}
		{button href="tiki-admin_trackers.php" _text="{tr}Admin Trackers{/tr}"}
		{button href="tiki-admin_trackers.php?trackerId=$trackerId&show=mod" _text="{tr}Edit This Tracker{/tr}"}
	{/if}
	{button href="tiki-view_tracker.php?trackerId=$trackerId" _text="{tr}View This Tracker's Items{/tr}"}
</div>

{tabset}
	<!-- {$plug} -->
	<a name="list"></a>
	{tab name="{tr}Tracker fields{/tr}"}
		<table class="findtable">
			<tr>
				<td>{tr}Find{/tr}</td>
				<td>
					<form method="get" action="tiki-admin_tracker_fields.php">
						<input type="text" name="find" value="{$find|escape}" />
						<input type="submit" value="{tr}Find{/tr}" name="search" />
						<input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
						<input type="hidden" name="trackerId" value="{$trackerId|escape}" />
						&nbsp;
						<input type="text" name="max" value="{$max|escape}" size="5"/>
						{tr}Rows{/tr}
					</form>
				</td>
			</tr>
		</table>
		
		<form>
			<table class="normal">
				<tr>
					<th>&nbsp;</th>
					<th>{self_link _sort_arg='sort_mode' _sort_field='fieldId'}{tr}Id{/tr}{/self_link}</th>
					<th>{self_link _sort_arg='sort_mode' _sort_field='name'}{tr}Name{/tr}{/self_link}</th>
					<th>{self_link _sort_arg='sort_mode' _sort_field='type'}{tr}Type{/tr}{/self_link}</th>
					<th>{self_link _sort_arg='sort_mode' _sort_field='options'}{tr}Options{/tr}{/self_link}</th>
					<th>{self_link _sort_arg='sort_mode' _sort_field='position' _title="{tr}Position of field in list{/tr}"}{tr}Pos{/tr}{/self_link}</th>
					<th>{self_link _sort_arg='sort_mode' _sort_field='isMandatory' _title="{tr}Is mandatory/required?{/tr}"}{tr}Req.{/tr}{/self_link}</th>
					<th>{self_link _sort_arg='sort_mode' _sort_field='isTblVisible' _title="{tr}Is column visible when listing tracker items?{/tr}"}{tr}List{/tr}{/self_link}</th>
					<th>{self_link _sort_arg='sort_mode' _sort_field='isMain' _title="{tr}Column links to edit/view item?{/tr}"}{tr}Main{/tr}{/self_link}</th>
					<th>{self_link _sort_arg='sort_mode' _sort_field='isMultilingual'}{tr}Multilingual{/tr}{/self_link}</th>
					<th>{self_link _sort_arg='sort_mode' _sort_field='isSearchable' _title="{tr}Is column searchable?{/tr}"}{tr}Search{/tr}{/self_link}</th>
					<th>{self_link _sort_arg='sort_mode' _sort_field='isPublic' _title="{tr}Field is public? (viewed in trackerlist plugin){/tr}"}{tr}Public{/tr}{/self_link}</th>
					<th>{self_link _sort_arg='sort_mode' _sort_field='isHidden'}{tr}Hidden{/tr}{/self_link}</th>
					<th>{self_link _sort_arg='sort_mode' _sort_field='description' _title="{tr}Description{/tr}"}{tr}Descr.{/tr}{/self_link}</th>
					<th>{self_link _sort_arg='sort_mode' _sort_field='validation'}{tr}Validation{/tr}{/self_link}</th>
					<th style="text-align: right; padding-right: .5em">{select_all checkbox_names='action[]'}</th>
				</tr>
				{cycle values="odd,even" print=false}
				{section name=user loop=$channels}
					<tr class="{cycle}">
						<td>
							{if $tracker_info.useRatings ne 'y' or $channels[user].name ne "Rating"}
								{self_link _icon='page_edit' cookietab='2' _anchor="anchor2" fieldId=$channels[user].fieldId}
									{tr}Edit{/tr}
								{/self_link}
							{/if}
						</td>
						<td>
							{if $tracker_info.useRatings ne 'y' or $channels[user].name ne "Rating"}
								{self_link cookietab='2' _anchor="anchor2" fieldId=$channels[user].fieldId _title="{tr}Edit{/tr}"}
									{$channels[user].fieldId}
								{/self_link}
							{else}
									{$channels[user].fieldId}
							{/if}
						</td>
						<td>{$channels[user].name|escape}</td>
						<td>{assign var=x value=$channels[user].type}{$field_types[$x].label}</td>
						<td>{$channels[user].options|truncate:42:"..."|escape}</td>
						<td>{$channels[user].position}</td>
						<td>{if $channels[user].isMandatory eq 'y'}<a title="{tr}Mandatory{/tr}">*</a>{else}-{/if}</td>
						<td>{if $channels[user].isTblVisible eq 'y'}{icon _id='table' title="{tr}Is column visible when listing tracker items?{/tr}"}{else}-{/if}</td>
						<td>{$channels[user].isMain}</td>
						<td>{$channels[user].isMultilingual}</td>
						<td>{if $channels[user].isSearchable eq 'y'}{icon _id='magnifier' title="{tr}Searchable{/tr}"}{else}-{/if}</td>
						<td>{$channels[user].isPublic}</td>
						<td>{$channels[user].isHidden}
							{if !empty($channels[user].visibleBy)}<br />{icon _id=magnifier width=10 height=10}{foreach from=$channels[user].visibleBy item=g}{$g|escape} {/foreach}{/if}
							{if !empty($channels[user].editableBy)}<br />{icon _id=page_edit width=10 height=10}{foreach from=$channels[user].editableBy item=g}{$g|escape} {/foreach}{/if}
						</td>
						<td>{$channels[user].description|truncate:14|escape}</td>
						<td>{$channels[user].validation|escape}</td>
						<td style="white-space: nowrap;">
							{if $tracker_info.useRatings ne 'y' or $channels[user].name ne "Rating"}
								{self_link trackerId=$trackerId fieldId=$channels[user].fieldId up=1 delta=-1}{icon _id='resultset_up'}{/self_link}
								{self_link trackerId=$trackerId fieldId=$channels[user].fieldId up=1}{icon _id='resultset_down'}{/self_link}
								<a class="link" href="tiki-admin_tracker_fields.php?trackerId={$trackerId}{if $max and $max ne $prefs.maxRecords}&amp;max={$max}{/if}{if $offset}&amp;offset={$offset}{/if}&amp;sort_mode={$sort_mode}&amp;remove={$channels[user].fieldId}" title="{tr}Remove{/tr}">
									{icon _id='cross' alt="{tr}Remove{/tr}"}
								</a> 
								<input type="checkbox" name="action[]" value='{$channels[user].fieldId}' />
							{/if}
						</td>
					</tr>
				{/section}
			</table>
			<div style="text-align:right">
				{tr}Perform action with checked:{/tr}
				<select name="batchaction">
					<option value="">{tr}...{/tr}</option>
					<option value="delete">{tr}Delete{/tr}</option>
				</select>
				<input type="hidden" name="trackerId" value="{$trackerId}" />
				<input type="submit" name="act" value="{tr}OK{/tr}" />
			</div>
		</form>
		
		{pagination_links cant=$cant step=$max offset=$offset}{/pagination_links}
	{/tab}
	
	{if $fieldId eq "0"}
		{assign var='title' value="{tr}New tracker field{/tr}"}
	{else}
		{assign var='title' value="{tr}Edit tracker field{/tr}"}
	{/if}
	{tab name=$title}
		{if $error}
			{remarksbox type="warning" title="{tr}Errors{/tr}"}{tr}{$error}{/tr}{/remarksbox}
		{/if}
		<form action="tiki-admin_tracker_fields.php" method="post">
			{if $find}<input type="hidden" name="find" value="{$find|escape}" />{/if}
			{if $max and $max ne $prefs.maxRecords}<input type="hidden" name="max" value="{$max|escape}" />{/if}
			{if $offset}<input type="hidden" name="offset" value="{$offset|escape}" />{/if}
			{if $sort_mode}<input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />{/if}
			<input type="hidden" name="fieldId" value="{$fieldId|escape}" />
			<input type="hidden" name="trackerId" value="{$trackerId|escape}" />
			<table class="formcolor">
				<tr><td>{tr}Name:{/tr}</td><td><input type="text" name="name" value="{$name|escape}" /></td></tr>
				<tr>
					<td>{tr}Type:{/tr}
						{assign var=fld value="z"}
						{foreach key=fk item=fi from=$field_types name=foreachname}
							{if $fi.help}
								<div id='{$fk}' {if $type eq $fk or (($type eq 'o' or $type eq '') and $smarty.foreach.foreachname.first)}style="display:block;font-style:italic;"{else}style="display:none;font-style:italic;"{/if}>{$fi.help}</div>
							{assign var=fld value=$fld|cat:$fk}
							{/if}
						{/foreach}
					</td>
					<td>
						<select name="type" id='trkfldtype' onchange='javascript:chgTrkFld("{$fld}",this.options[selectedIndex].value);javascript:chgTrkFld("{$fld}",this.options[selectedIndex].value);javascript:chgTrkLingual(this.options[selectedIndex].value);'>
							{sortlinks case=false}
								{foreach key=fk item=fi from=$field_types}
									<option value="{$fk}" {if $type eq $fk}selected="selected"{/if}{if $fi.opt and ($type eq $fk or $type eq 'o' or $type eq '')}{assign var=showit value=true}{/if}>{$fi.label}</option>
								{/foreach}
							{/sortlinks}
						</select>
		
						{if $prefs.feature_help eq 'y'}
							<a href="{$prefs.helpurl}Tracker+Field+Type" target="tikihelp" class="tikihelp" title="{tr}Trackers{/tr}">
								{icon _id='help' alt="{tr}help{/tr}"}
							</a>
						{/if}
		
						<div id='z' {if $showit}style="display:block;"{else}style="display:none;"{/if}><input type="text" name="options" value="{$options|escape}" size="50" /></div>
					</td>
				</tr>
		
		{* Section that allows to reduce the user list item choices through a multiselect list of all list items of this field type (if supported by this fieldtype) *}
		
				<tr id='itemChoicesRow' {if empty($field_types.$type.itemChoicesList)}style="display:none;"{/if}>
					<td>{tr}Select list items that will be displayed:{/tr}</td>
					<td>
						{foreach key=fk item=fi from=$field_types name=foreachname}
							{if isset($fi.itemChoicesList)}
								<select name="itemChoices[]" id='{$fk}itemChoices' {if $type eq $fk or (($type eq 'o' or $type eq '') and $smarty.foreach.foreachname.first)}style="display:block;"{else}style="display:none;"{/if} size="{math equation="min(10,x)" x=$fi.itemChoicesList|@count}" multiple="multiple">
									<option value="">&nbsp;</option>
									{sortlinks case=false}
										{foreach key=choice_k item=choice_i from=$fi.itemChoicesList}
										{$choice_k}
											<option value="{$choice_k|escape}"{if !empty($itemChoices) and in_array($choice_k, $itemChoices)} selected="selected"{/if}>{if $type eq 'u'}{$choice_i|username}{else}{tr}{$choice_i}{/tr}{/if}</option>
										{/foreach}
									{/sortlinks}
								</select>
							{/if}
						{/foreach}
					</td>
				</tr>
				<tr>
					<td>{tr}Validation:{/tr}</td>
					<td>
						<select name="validation">
							<option value="" {if $validation eq ''} selected="selected"{/if}>{tr}None{/tr}</option>
							{foreach item=validator from=$validators}
								<option value="{$validator|escape}" {if $validation eq $validator} selected="selected"{/if}>{$validator|escape}</option>
							{/foreach}
						</select>
					</td>
				</tr>
				<tr><td>{tr}Validation parameter:{/tr}</td><td><input type="text" size="30" name="validationParam" value="{$validationParam}" /></td></tr>
				<tr><td>{tr}Validation error message:{/tr}</td><td><input type="text" size="40" name="validationMessage" value="{$validationMessage}" /></td></tr>
				<tr><td>{tr}Order:{/tr}</td><td><input type="text" size="5" name="position" value="{$position}" /></td></tr>
				<tr><td>{tr}Field is mandatory?{/tr}</td><td><input type="checkbox" name="isMandatory" {if $isMandatory eq 'y'}checked="checked"{/if} /></td></tr>
				<tr>
					<td>{tr}Is column visible when listing tracker items?{/tr}</td>
					<td><input type="checkbox" name="isTblVisible" {if empty($fieldId) || $isTblVisible eq 'y'}checked="checked"{/if} /></td>
				</tr>
				<tr>
					<td colspan="2">{remarksbox type="info" title="{tr}Important note{/tr}"}{tr}The first field in the tracker to have column links to edit/view item (i.e. isMain) will be what is shown as the name of the item in category and search listings{/tr}{/remarksbox}</td>
				</tr>
				<tr>
					<td>{tr}Column links to edit/view item?{/tr}</td>
					<td><input type="checkbox" name="isMain" {if empty($fieldId) ||$isMain eq 'y'}checked="checked"{/if} /></td>
				</tr>
				<tr id='multilabelRow'{if $type neq 'a' && $type neq 't' && $type neq 'o' && $type neq '' && $type neq 'C'} style="display:none;"{/if}>
					<td>{tr}Multilingual content:{/tr}</td><td><input type="checkbox" name="isMultilingual" {if $isMultilingual eq 'y'}checked="checked"{/if} /></td>
				</tr>
				<tr>
					<td>{tr}Column is searchable?{/tr}</td>
					<td><input type="checkbox" name="isSearchable" {if $isSearchable eq 'y'}checked="checked"{/if} /></td>
				</tr>
				<tr>
					<td>{tr}Field is public? (viewed in trackerlist plugin){/tr}</td>
					<td><input type="checkbox" name="isPublic" {if empty($fieldId) || $isPublic eq 'y'}checked="checked"{/if} /></td>
				</tr>
				<tr>
					<td>{tr}Field is hidden?{/tr}</td>
					<td>
						<select name="isHidden">
							<option value="n"{if $isHidden eq 'n'} selected="selected"{/if}>{tr}not hidden{/tr}</option>
							<option value="y"{if $isHidden eq 'y'} selected="selected"{/if}>{tr}visible to admin only{/tr}</option>
							<option value="p"{if $isHidden eq 'p'} selected="selected"{/if}>{tr}editable by admin only{/tr}</option>
							<option value="c"{if $isHidden eq 'c'} selected="selected"{/if}>{tr}visible by creator &amp; admin only{/tr}</option>
						</select><br /><i>{tr}The option creator needs a field of type user selector and option 1{/tr}</i><br />
						{tr}Visible by:{/tr}
						<select name="visibleBy[]" size="3" multiple>
							<option value="">&nbsp;</option>
							{foreach item=group from=$allGroups}
								<option value="{$group|escape}"{if in_array($group, $visibleBy)} selected="selected"{/if}>{$group|escape}</option>
							{/foreach}
						</select><br />
						{tr}Editable by:{/tr}
						<select name="editableBy[]" size="3" multiple>
							<option value="">&nbsp;</option>
							{foreach item=group from=$allGroups}
								<option value="{$group|escape}"{if in_array($group, $editableBy)} selected="selected"{/if}>{$group|escape}</option>
							{/foreach}
						</select>
					</td>
				</tr>
				<tr>
					<td>{tr}Description:{/tr}</td>
					<td>
						<div id='zDescription' {if $type eq 'S'}style="display:none;"{else}style="display:block;"{/if}style="display:block;" >
						{if $type ne 'S'}
							{tr}Description text is wiki-parsed:{/tr} <input type="checkbox" name="descriptionIsParsed" {if $descriptionIsParsed eq 'y'}checked="checked"{/if} />
						{/if}
						<textarea style="width:95%;" rows="4" name="description">{$description|escape}</textarea></div>
						<div id='zStaticText' {if $type neq 'S'}style="display:none;"{/if}>
							<div id="zStaticTextToolbars" {if $type neq 'S'}style="display:none;"{/if}>
								{toolbars qtnum="staticText" area_id="staticTextArea"}
							</div>
							<textarea id="staticTextArea" name="descriptionStaticText" rows="20" cols="80" >{$description|escape}</textarea>
						</div>
					</td>
				</tr>
				<tr><td>{tr}Error message:{/tr}</td><td><input type="text" name="errorMsg" value="{$errorMsg|escape}" /></td></tr>
				<tr><td>&nbsp;</td><td><input type="submit" name="save" value="{tr}Save{/tr}" /></td></tr>
			</table>
		</form>
	{/tab}
	
	{tab name="{tr}Import/Export Trackers Fields{/tr}"}
		<form action="tiki-admin_tracker_fields.php" method="post">
			{if $find}<input type="hidden" name="find" value="{$find|escape}" />{/if}
			{if $offset}<input type="hidden" name="offset" value="{$offset|escape}" />{/if}
			{if $sort_mode}<input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />{/if}
			<input type="hidden" name="trackerId" value="{$trackerId|escape}" />
			<label for="rows-export">{tr}Rows{/tr}</label>
			<input type="text" name="max" value="{$max|escape}" size="5" id="rows-export" />
			<br />
			<label for="fieldId-export">{tr}Export fieldId also{/tr}</label>
			<input type="checkbox" name="exportAll"{if $export_all eq 'y'} checked="checked"{/if} id="fieldId-export" />
			<input type="submit" name="refresh" value="{tr}Refresh{/tr}" />
			{remarksbox}{tr}Check the box to re-import in this tracker and change the fields.{/tr}<br />{tr}Uncheck the box to import in another database.{/tr}{/remarksbox}
		</form>
		
		<form action="tiki-admin_tracker_fields.php" method="post">
			{if $find}<input type="hidden" name="find" value="{$find|escape}" />{/if}
			{if $max and $max ne $prefs.maxRecords}<input type="hidden" name="max" value="{$max|escape}" />{/if}
			{if $offset}<input type="hidden" name="offset" value="{$offset|escape}" />{/if}
			{if $sort_mode}<input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />{/if}
			<input type="hidden" name="trackerId" value="{$trackerId|escape}" />
			<input type="hidden" name="import" value="1" />
			<textarea name="rawmeat" cols="62" rows="32" wrap="soft">{* please do not indent *}
{section name=user loop=$channels}
{if $tracker_info.useRatings ne 'y' or $channels[user].name ne "Rating"}
[FIELD{$channels[user].fieldId}]
{if $export_all eq 'y'}
fieldId = {$channels[user].fieldId}
{/if}
name = {$channels[user].name|escape}
position = {$channels[user].position}
type = {$channels[user].type}
options = {$channels[user].options}
isMain = {$channels[user].isMain}
isTblVisible = {$channels[user].isTblVisible}
isSearchable = {$channels[user].isSearchable}
isPublic = {$channels[user].isPublic}
isHidden = {$channels[user].isHidden}
isMandatory = {$channels[user].isMandatory}
{if $channels[user].type eq 'S'}
descriptionStaticText = {$channels[user].description}
{else}
description = {$channels[user].description}
descriptionIsParsed = {$channels[user].descriptionIsParsed}
{/if}
{/if}
{/section}
</textarea><br />
			<input type="submit" name="save" value="{tr}Import{/tr}" />
		</form>
	{pagination_links cant=$cant step=$max offset=$offset}{/pagination_links}
	{/tab}
{/tabset}
