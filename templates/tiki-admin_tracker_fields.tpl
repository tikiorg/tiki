{* $Id$ *}

{title help="Adding+fields+to+a+tracker" url="tiki-admin_tracker_fields.php?trackerId=$trackerId"}{tr}Admin Tracker:{/tr} {$tracker_info.name}{/title}

<div  class="navbar">
	{button href="tiki-list_trackers.php" _text="{tr}List Trackers{/tr}"}
	
	{if $tiki_p_admin_trackers eq 'y'}
		{button href="tiki-admin_trackers.php" _text="{tr}Admin Trackers{/tr}"}
		{button href="tiki-admin_trackers.php?trackerId=$trackerId" _text="{tr}Edit This Tracker{/tr}"}
	{/if}
	{button href="tiki-view_tracker.php?trackerId=$trackerId" _text="{tr}View This Tracker's Items{/tr}"}
</div>

{if $fieldId eq "0"}
<h2>{tr}New tracker field{/tr}</h2>
{else}
<h2>{tr}Edit tracker field{/tr}</h2>
{/if}
{if $error}
	{remarksbox  type="warning" title="{tr}Errors{/tr}"}{tr}{$error}{/tr}{/remarksbox}
{/if}
<form action="tiki-admin_tracker_fields.php" method="post">
{if $find}<input type="hidden" name="find" value="{$find|escape}" />{/if}
{if $max and $max ne $prefs.maxRecords}<input type="hidden" name="max" value="{$max|escape}" />{/if}
{if $offset}<input type="hidden" name="offset" value="{$offset|escape}" />{/if}
{if $sort_mode}<input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />{/if}
<input type="hidden" name="fieldId" value="{$fieldId|escape}" />
<input type="hidden" name="trackerId" value="{$trackerId|escape}" />
<table class="normal">
<tr class="formcolor"><td>{tr}Name{/tr}:</td><td><input type="text" name="name" value="{$name|escape}" /></td></tr>
<tr class="formcolor"><td>{tr}Type{/tr}:
{assign var=fld value="z"}
{foreach key=fk item=fi from=$field_types name=foreachname}
{if $fi.help}
<div id='{$fk}' {if $type eq $fk or (($type eq 'o' or $type eq '') and $smarty.foreach.foreachname.first)}style="display:block;font-style:italic;"{else}style="display:none;font-style:italic;"{/if}>{$fi.help}</div>
{assign var=fld value=$fld|cat:$fk}
{/if}
{/foreach}
</td><td>
<select name="type" id='trkfldtype' onchange='javascript:chgTrkFld("{$fld}",this.options[selectedIndex].value);javascript:chgTrkFld("{$fld}",this.options[selectedIndex].value);javascript:chgTrkLingual(this.options[selectedIndex].value);'>
{sortlinks case=false}
{foreach key=fk item=fi from=$field_types}
<option value="{$fk}" {if $type eq $fk}selected="selected"{/if}{if $fi.opt and ($type eq $fk or $type  eq 'o' or $type eq '')}{assign var=showit value=true}{/if}>{$fi.label}</option>
{/foreach}
{/sortlinks}
</select>

{if $prefs.feature_help eq 'y'}
<a href="{$prefs.helpurl}Tracker+Field+Type" target="tikihelp" class="tikihelp" title="{tr}Trackers{/tr}">
{icon _id='help' alt='{tr}help{/tr}'}</a>{/if}

<div  id='z' {if $showit}style="display:block;"{else}style="display:none;"{/if}><input type="text" name="options" value="{$options|escape}" size="50" /></div>
</td></tr>

{* Section that allows to reduce the user list item choices through a multiselect list of all list items of this field type (if supported by this fieldtype) *}

<tr class="formcolor" id='itemChoicesRow' {if empty($field_types.$type.itemChoicesList)}style="display:none;"{/if}><td>{tr}Select list items that will be displayed:{/tr}</td><td>
{foreach key=fk item=fi from=$field_types name=foreachname}
{if isset($fi.itemChoicesList)}
<select name="itemChoices[]" id='{$fk}itemChoices' {if $type eq $fk or (($type eq 'o' or $type eq '') and $smarty.foreach.foreachname.first)}style="display:block;"{else}style="display:none;"{/if} size="{math equation="min(10,x)" x=$fi.itemChoicesList|@count}" multiple="multiple">
<option value="">&nbsp;</option>
{sortlinks case=false}
{foreach key=choice_k item=choice_i from=$fi.itemChoicesList}
{$choice_k}
<option value="{$choice_k|escape}"{if !empty($itemChoices) and in_array($choice_k, $itemChoices)} selected="selected"{/if}>{if $type eq 'u'}{$choice_i|username|escape}{else}{tr}{$choice_i}{/tr}{/if}</option>
{/foreach}
{/sortlinks}
</select>
{/if}
{/foreach}
</td></tr>

<tr class="formcolor"><td>{tr}Is column visible when listing tracker items?{/tr}</td><td><input type="checkbox" name="isTblVisible" {if $isTblVisible eq 'y'}checked="checked"{/if} /></td></tr>
<tr class="formcolor"><td>{tr}Column links to edit/view item?{/tr}</td><td><input type="checkbox" name="isMain" {if $isMain eq 'y'}checked="checked"{/if} /></td></tr>
<tr class="formcolor" id='multilabelRow'{if $type neq 'a' && $type neq 't' && $type neq 'o' && $type neq '' && $type neq 'C'} style="display:none;"{/if}><td>{tr}Multilingual content{/tr}:</td><td><input type="checkbox" name="isMultilingual" {if $isMultilingual eq 'y'}checked="checked"{/if} /></td></tr>
<tr class="formcolor"><td>{tr}Column is searchable?{/tr}</td><td><input type="checkbox" name="isSearchable" {if $isSearchable eq 'y'}checked="checked"{/if} /></td></tr>
<tr class="formcolor"><td>{tr}Field is public? (viewed in trackerlist plugin){/tr}</td><td><input type="checkbox" name="isPublic" {if $isPublic eq 'y'}checked="checked"{/if} /></td></tr>
<tr class="formcolor"><td>{tr}Field is hidden?{/tr}</td><td>
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
</td></tr>
<tr class="formcolor"><td>{tr}Field is mandatory?{/tr}</td><td><input type="checkbox" name="isMandatory" {if $isMandatory eq 'y'}checked="checked"{/if} /></td></tr>
<tr class="formcolor"><td>{tr}Order{/tr}:</td><td><input type="text" size="5" name="position" value="{$position}" /></td></tr>
<tr class="formcolor"><td>{tr}Description{/tr}:
{if $prefs.quicktags_over_textarea neq 'y'}
	<div id="zStaticTextQuicktags" {if $type neq 'S'}style="display:none;"{/if}>
	{include file=tiki-edit_help_tool.tpl qtnum="staticText" area_name="staticTextArea"}
	</div>
{/if}
</td><td><div id='zDescription' {if $type eq 'S'}style="display:none;"{else}style="display:block;"{/if}style="display:block;" >{if $type ne 'S'}{tr}Description text is wiki-parsed:{/tr} <input type="checkbox" name="descriptionIsParsed" {if $descriptionIsParsed eq 'y'}checked="checked"{/if} />{/if}
<textarea style="width:95%;" rows="4" name="description">{$description|escape}</textarea></div>
<div id='zStaticText' {if $type neq 'S'}style="display:none;"{/if}>
{if $prefs.quicktags_over_textarea eq 'y'}
	<div id="zStaticTextQuicktags" {if $type neq 'S'}style="display:none;"{/if}>
	{include file=tiki-edit_help_tool.tpl qtnum="staticText" area_name="staticTextArea"}
	</div>
{/if}
<textarea id="staticTextArea" name="descriptionStaticText" rows="20" cols="80" >{$description|escape}</textarea></div></td></tr>
<tr class="formcolor"><td>{tr}Error message:{/tr}</td><td><input type="text" name="errorMsg" value="{$errorMsg|escape}" /></td></tr>
<tr class="formcolor"><td>&nbsp;</td><td><input type="submit" name="save" value="{tr}Save{/tr}" /></td></tr>
</table>
</form>

<!-- {$plug} -->
<a name="list"></a>
<h2>{tr}Tracker fields{/tr}</h2>

<table class="findtable">
<tr><td>{tr}Find{/tr}</td>
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
<table class="normal">
<tr>
<th>&nbsp;</th>
<th>{self_link _sort_arg='sort_mode' _sort_field='fieldId'}{tr}Id{/tr}{/self_link}</th>
<th>{self_link _sort_arg='sort_mode' _sort_field='name'}{tr}Name{/tr}{/self_link}</th>
<th>{self_link _sort_arg='sort_mode' _sort_field='type'}{tr}Type{/tr}{/self_link}</th>
<th>{self_link _sort_arg='sort_mode' _sort_field='options'}{tr}Options{/tr}{/self_link}</th>
<th>{self_link _sort_arg='sort_mode' _sort_field='position'}{tr}Position{/tr}{/self_link}</th>
<th>{self_link _sort_arg='sort_mode' _sort_field='isMain'}{tr}isMain{/tr}{/self_link}</th>
<th>{self_link _sort_arg='sort_mode' _sort_field='isMultilingual'}{tr}Multilingual{/tr}{/self_link}</th>
<th>{self_link _sort_arg='sort_mode' _sort_field='isTblVisible'}{tr}Tbl vis{/tr}{/self_link}</th>
<th>{self_link _sort_arg='sort_mode' _sort_field='isSearchable'}{tr}Searchable{/tr}{/self_link}</th>
<th>{self_link _sort_arg='sort_mode' _sort_field='isPublic'}{tr}Public{/tr}{/self_link}</th>
<th>{self_link _sort_arg='sort_mode' _sort_field='isHidden'}{tr}Hidden{/tr}{/self_link}</th>
<th>{self_link _sort_arg='sort_mode' _sort_field='isMandatory'}{tr}Mandatory{/tr}{/self_link}</th>
<th>{self_link _sort_arg='sort_mode' _sort_field='description'}{tr}Description{/tr}{/self_link}</th>
<th>&nbsp;</th>
</tr>
{cycle values="odd,even" print=false}
{section name=user loop=$channels}
<tr class="{cycle}">
<td>{if $tracker_info.useRatings ne 'y' or $channels[user].name ne "Rating"}
<a class="link" href="tiki-admin_tracker_fields.php?trackerId={$trackerId}{if $max and $max ne $prefs.maxRecords}&amp;max={$max}{/if}{if $offset}&amp;offset={$offset}{/if}&amp;sort_mode={$sort_mode}&amp;fieldId={$channels[user].fieldId}" title="{tr}Edit{/tr}">{icon _id='page_edit'}</a>
{/if}</td>
<td>{if $tracker_info.useRatings ne 'y' or $channels[user].name ne "Rating"}
<a class="link" href="tiki-admin_tracker_fields.php?trackerId={$trackerId}{if $max and $max ne $prefs.maxRecords}&amp;max={$max}{/if}{if $offset}&amp;offset={$offset}{/if}&amp;sort_mode={$sort_mode}&amp;fieldId={$channels[user].fieldId}" title="{tr}Edit{/tr}">{$channels[user].fieldId}</a>{else}{$channels[user].fieldId}{/if}</td>
<td>{$channels[user].name}</td>
<td>{assign var=x value=$channels[user].type}{$field_types[$x].label}</td>
<td>{$channels[user].options|truncate:42:"..."|escape}</td>
<td>{$channels[user].position}</td>
<td>{$channels[user].isMain}</td>
<td>{$channels[user].isMultilingual}</td>
<td>{$channels[user].isTblVisible}</td>
<td>{$channels[user].isSearchable}</td>
<td>{$channels[user].isPublic}</td>
<td>{$channels[user].isHidden}
{if !empty($channels[user].visibleBy)}<br />{icon _id=magnifier width=10 height=10}{foreach from=$channels[user].visibleBy item=g}{$g|escape} {/foreach}{/if}
{if !empty($channels[user].editableBy)}<br />{icon _id=page_edit width=10 height=10}{foreach from=$channels[user].editableBy item=g}{$g|escape} {/foreach}{/if}
</td>
<td>{$channels[user].isMandatory}</td>
<td>{$channels[user].description|truncate:14:"..."}</td>
<td>{if $tracker_info.useRatings ne 'y' or $channels[user].name ne "Rating"}
<a class="link" href="tiki-admin_tracker_fields.php?trackerId={$trackerId}{if $max and $max ne $prefs.maxRecords}&amp;max={$max}{/if}{if $offset}&amp;offset={$offset}{/if}&amp;sort_mode={$sort_mode}&amp;remove={$channels[user].fieldId}" title="{tr}Remove{/tr}">{icon _id='cross' alt="{tr}Remove{/tr}"}</a> 
<a class="link" href="tiki-admin_tracker_fields.php?trackerId={$trackerId}&amp;fieldId={$channels[user].fieldId}&amp;up=1{if $offset > 1}&amp;offset={$offset}{/if}{if $max and $max ne $prefs.maxRecords}&amp;max={$max}{/if}">{icon _id='resultset_down'}</a>
{/if}</td>
</tr>
{/section}
</table>

{pagination_links cant=$cant step=$max offset=$offset}{/pagination_links}

<h2>{tr}Import/Export Trackers Fields{/tr}</h2>

<form action="tiki-admin_tracker_fields.php" method="post">
{if $find}<input type="hidden" name="find" value="{$find|escape}" />{/if}
{if $max and $max ne $prefs.maxRecords}<input type="hidden" name="max" value="{$max|escape}" />{/if}
{if $offset}<input type="hidden" name="offset" value="{$offset|escape}" />{/if}
{if $sort_mode}<input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />{/if}
<input type="hidden" name="trackerId" value="{$trackerId|escape}" />
{tr}Export fieldId also{/tr}
<input type="checkbox" name="exportAll"{if $export_all eq 'y'} checked="checked"{/if}/>
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
<textarea name="rawmeat" cols="62" rows="32" wrap="soft">
{section name=user loop=$channels}
{if $tracker_info.useRatings ne 'y' or $channels[user].name ne "Rating"}
[FIELD{$channels[user].fieldId}]
{if $export_all eq 'y'}
fieldId = {$channels[user].fieldId}
{/if}
name = {$channels[user].name}
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

