<a class="pagetitle" href="tiki-admin_tracker_fields.php?trackerId={$trackerId}">{tr}Admin tracker{/tr}: {$tracker_info.name}</a><br /><br />

<div>
<span class="button2"><a href="tiki-list_trackers.php" class="linkbut">{tr}List trackers{/tr}</a></span>
{if $tiki_p_admin_trackers eq 'y'}
<span class="button2"><a href="tiki-admin_trackers.php" class="linkbut">{tr}Admin trackers{/tr}</a></span>
<span class="button2"><a href="tiki-admin_trackers.php?trackerId={$trackerId}" class="linkbut">{tr}Edit this tracker{/tr}</a></span>
{/if}
<span class="button2"><a href="tiki-view_tracker.php?trackerId={$trackerId}" class="linkbut">{tr}View this tracker items{/tr}</a></span>
</div>

<br /><br />
<h2>{tr}Edit tracker fields{/tr}</h2>
<form action="tiki-admin_tracker_fields.php" method="post">
{if $find}<input type="hidden" name="find" value="{$find|escape}" />{/if}
{if $max and $max ne $maxRecords}<input type="hidden" name="max" value="{$max|escape}" />{/if}
{if $offset}<input type="hidden" name="offset" value="{$offset|escape}" />{/if}
{if $sort_mode}<input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />{/if}
<input type="hidden" name="fieldId" value="{$fieldId|escape}" />
<input type="hidden" name="trackerId" value="{$trackerId|escape}" />
<table class="normal">
<tr class="formcolor"><td>{tr}Name{/tr}:</td><td><input type="text" name="name" value="{$name|escape}" /></td></tr>
<tr class="formcolor"><td>{tr}Type{/tr}:
{assign var=fld value="z"}
{foreach key=fk item=fi from=$field_types}
{if $fi.opt}
<span id='{$fk}' {if $type eq $fk}style="display:inline;"{else}style="display:none;"{/if}><br /><i>{$fi.help}</i></span>
{assign var=fld value=$fld|cat:$fk}
{/if}
{/foreach}
</td><td>
<select name="type" id='trkfldtype' onchange="javascript:chgTrkFld('{$fld}',this.options[selectedIndex].value);">
{foreach key=fk item=fi from=$field_types}
<option value="{$fk}" {if $type eq $fk}{if $fi.opt}{assign var=showit value=true}{/if}selected="selected"{/if}>{$fi.label}</option>
{/foreach}
</select>
<span  id='z' {if $showit}style="display:inline;"{else}style="display:none;"{/if}><br /><input type="text" name="options" value="{$options|escape}" size="50" /></span>
</td></tr>
<tr class="formcolor"><td>{tr}Is column visible when listing tracker items?{/tr}</td><td><input type="checkbox" name="isTblVisible" {if $isTblVisible eq 'y'}checked="checked"{/if} /></td></tr>
<tr class="formcolor"><td>{tr}Column links to edit/view item?{/tr}</td><td><input type="checkbox" name="isMain" {if $isMain eq 'y'}checked="checked"{/if} /></td></tr>
<tr class="formcolor"><td>{tr}Column is searchable?{/tr}</td><td><input type="checkbox" name="isSearchable" {if $isSearchable eq 'y'}checked="checked"{/if} /></td></tr>
<tr class="formcolor"><td>{tr}Field is public? (for use thru trackerlist plugin){/tr}</td><td><input type="checkbox" name="isPublic" {if $isPublic eq 'y'}checked="checked"{/if} /></td></tr>
<tr class="formcolor"><td>{tr}Field is hidden? (visible by admin only){/tr}</td><td><input type="checkbox" name="isHidden" {if $isHidden eq 'y'}checked="checked"{/if} /></td></tr>
<tr class="formcolor"><td>{tr}Field is mandatory?{/tr}</td><td><input type="checkbox" name="isMandatory" {if $isMandatory eq 'y'}checked="checked"{/if} /></td></tr>
<tr class="formcolor"><td>{tr}Order{/tr}</td><td><input type="text" size="5" name="position" value="{$position}" /></td></tr>
<tr class="formcolor"><td>&nbsp;</td><td><input type="submit" name="save" value="{tr}Save{/tr}" /></td></tr>
</table>
</form>

<!-- {$plug} -->

<h2>{tr}Tracker fields{/tr}</h2>
<div  align="center">
<table class="findtable">
<tr><td>{tr}Find{/tr}</td>
<td>
<form method="get" action="tiki-admin_tracker_fields.php">
<input type="text" name="find" value="{$find|escape}" />
<input type="submit" value="{tr}find{/tr}" name="search" />
<input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
<input type="hidden" name="trackerId" value="{$trackerId|escape}" />
&nbsp;
<input type="text" name="max" value="{$max|escape}" size="5"/>
{tr}rows{/tr}
</form>
</td>
</tr>
</table>
<table class="normal">
<tr class="heading">
<td class="heading"><a class="tableheading" href="tiki-admin_tracker_fields.php?trackerId={$trackerId}&amp;{if $max and $max ne $maxRecords}max={$max}&amp;{/if}{if $offset}offset={$offset}&amp;{/if}sort_mode={if $sort_mode eq 'position_desc'}fieldId_asc{else}fieldId_desc{/if}">{tr}Id{/tr}</a></td>
<td class="heading">&nbsp;</td>
<td class="heading"><a class="tableheading" href="tiki-admin_tracker_fields.php?trackerId={$trackerId}&amp;{if $max and $max ne $maxRecords}max={$max}&amp;{/if}{if $offset}offset={$offset}&amp;{/if}sort_mode={if $sort_mode eq 'position_desc'}position_asc{else}position_desc{/if}">{tr}position{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-admin_tracker_fields.php?trackerId={$trackerId}&amp;{if $max and $max ne $maxRecords}max={$max}&amp;{/if}{if $offset}offset={$offset}&amp;{/if}sort_mode={if $sort_mode eq 'name_desc'}name_asc{else}name_desc{/if}">{tr}name{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-admin_tracker_fields.php?trackerId={$trackerId}&amp;{if $max and $max ne $maxRecords}max={$max}&amp;{/if}{if $offset}offset={$offset}&amp;{/if}sort_mode={if $sort_mode eq 'type_desc'}type_asc{else}type_desc{/if}">{tr}type{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-admin_tracker_fields.php?trackerId={$trackerId}&amp;{if $max and $max ne $maxRecords}max={$max}&amp;{/if}{if $offset}offset={$offset}&amp;{/if}sort_mode={if $sort_mode eq 'options_desc'}options_asc{else}options_desc{/if}">{tr}options{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-admin_tracker_fields.php?trackerId={$trackerId}&amp;{if $max and $max ne $maxRecords}max={$max}&amp;{/if}{if $offset}offset={$offset}&amp;{/if}sort_mode={if $sort_mode eq 'isMain_desc'}isMain_asc{else}isMain_desc{/if}">{tr}isMain{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-admin_tracker_fields.php?trackerId={$trackerId}&amp;{if $max and $max ne $maxRecords}max={$max}&amp;{/if}{if $offset}offset={$offset}&amp;{/if}sort_mode={if $sort_mode eq 'isTblVisible_desc'}isTblVisible_asc{else}isTblVisible_desc{/if}">{tr}Tbl vis{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-admin_tracker_fields.php?trackerId={$trackerId}&amp;{if $max and $max ne $maxRecords}max={$max}&amp;{/if}{if $offset}offset={$offset}&amp;{/if}sort_mode={if $sort_mode eq 'isSearchable_desc'}isSearchable_asc{else}isSearchable_desc{/if}">{tr}Searchable{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-admin_tracker_fields.php?trackerId={$trackerId}&amp;{if $max and $max ne $maxRecords}max={$max}&amp;{/if}{if $offset}offset={$offset}&amp;{/if}sort_mode={if $sort_mode eq 'isPublic_desc'}isPublic_asc{else}isPublic_desc{/if}">{tr}Public{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-admin_tracker_fields.php?trackerId={$trackerId}&amp;{if $max and $max ne $maxRecords}max={$max}&amp;{/if}{if $offset}offset={$offset}&amp;{/if}sort_mode={if $sort_mode eq 'isHidden_desc'}isHidden_asc{else}isHidden_desc{/if}">{tr}Hidden{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-admin_tracker_fields.php?trackerId={$trackerId}&amp;{if $max and $max ne $maxRecords}max={$max}&amp;{/if}{if $offset}offset={$offset}&amp;{/if}sort_mode={if $sort_mode eq 'isMandatory_desc'}isMandatory_asc{else}isMandatory_desc{/if}">{tr}Mandatory{/tr}</a></td>
<td class="heading">&nbsp;</td>
</tr>
{cycle values="odd,even" print=false}
{section name=user loop=$channels}
<tr class="{cycle}">
<td>{$channels[user].fieldId}</td>
<td><a class="link" href="tiki-admin_tracker_fields.php?trackerId={$trackerId}{if $max and $max ne $maxRecords}&amp;max={$max}{/if}{if $offset}&amp;offset={$offset}{/if}&amp;sort_mode={$sort_mode}&amp;fieldId={$channels[user].fieldId}">{tr}edit{/tr}</a></td>
<td>{$channels[user].position}</td>
<td>{$channels[user].name}</td>
<td>{assign var=x value=$channels[user].type}{$field_types[$x].label}</td>
<td>{$channels[user].options|truncate:42:"..."}</td>
<td>{$channels[user].isMain}</td>
<td>{$channels[user].isTblVisible}</td>
<td>{$channels[user].isSearchable}</td>
<td>{$channels[user].isPublic}</td>
<td>{$channels[user].isHidden}</td>
<td>{$channels[user].isMandatory}</td>
<td><a class="link" href="tiki-admin_tracker_fields.php?trackerId={$trackerId}{if $max and $max ne $maxRecords}&amp;max={$max}{/if}{if $offset}&amp;offset={$offset}{/if}&amp;sort_mode={$sort_mode}&amp;remove={$channels[user].fieldId}">{tr}remove{/tr}</a></td>
</tr>
{/section}
</table>
<br />
<div class="mini">
{if $prev_offset >= 0}
[<a class="prevnext" href="tiki-admin_tracker_fields.php?{if $find}find={$find}&amp;{/if}trackerId={$trackerId}{if $max and $max ne $maxRecords}&amp;max={$max}{/if}&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}prev{/tr}</a>]&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;[<a class="prevnext" href="tiki-admin_tracker_fields.php?{if $find}find={$find}&amp;{/if}trackerId={$trackerId}{if $max and $max ne $maxRecords}&amp;max={$max}{/if}&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}next{/tr}</a>]
{/if}
{if $direct_pagination eq 'y'}
<br />
{section loop=$cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:$maxRecords}
<a class="prevnext" href="tiki-admin_tracker_fields.php?{if $find}find={$find}&amp;{/if}trackerId={$trackerId}{if $max and $max ne $maxRecords}&amp;max={$max}{/if}&amp;offset={$selector_offset}&amp;sort_mode={$sort_mode}">
{$smarty.section.foo.index_next}</a>&nbsp;
{/section}
{/if}

</div>
</div>

<h2>{tr}Import/export trackers fields{/tr}</h2>

<form action="tiki-admin_tracker_fields.php" method="post">
{if $find}<input type="hidden" name="find" value="{$find|escape}" />{/if}
{if $max and $max ne $maxRecords}<input type="hidden" name="max" value="{$max|escape}" />{/if}
{if $offset}<input type="hidden" name="offset" value="{$offset|escape}" />{/if}
{if $sort_mode}<input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />{/if}
<input type="hidden" name="trackerId" value="{$trackerId|escape}" />
<input type="hidden" name="import" value="1">
<textarea name="rawmeat" cols="62" rows="32" wrap="soft">
{section name=user loop=$channels}
[FIELD{$channels[user].fieldId}]
fieldId = {$channels[user].fieldId}
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

{/section}
</textarea><br />
*** Not functionnal yet ***<br>
<input type="submit" name="save" value="{tr}Import{/tr}" />
</form>

