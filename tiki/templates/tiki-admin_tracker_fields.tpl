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
<input type="hidden" name="fieldId" value="{$fieldId|escape}" />
<input type="hidden" name="trackerId" value="{$trackerId|escape}" />
<table class="normal">
<tr><td class="formcolor">{tr}Name{/tr}:</td><td class="formcolor"><input type="text" name="name" value="{$name|escape}" /></td></tr>
<tr><td class="formcolor">{tr}Type{/tr}:
<span  id='trkflddropdown' {if $type eq 'd'}style="display:inline;"{else}style="display:none;"{/if}><br />{tr}(Dropdown options : list of items separated with commas){/tr}:</span>
<span  id='trkfldimage' {if $type eq 'i'}style="display:inline;"{else}style="display:none;"{/if}><br />{tr}(Image options : xSize,ySize indicated in pixels){/tr}:</span>
<span  id='trkfldaction' {if $type eq 'x'}style="display:inline;"{else}style="display:none;"{/if}><br />{tr}(Action options : Label,post,tiki-index.php,page:fieldname,highlight=test){/tr}:</span>
</td><td class="formcolor">
<select name="type" id='trkfldtype' onchange="javascript:chgTrkFld();">
<option value="c" {if $type eq 'c'}selected="selected"{/if}>{tr}checkbox{/tr}</option>
<option value="t" {if $type eq 't'}selected="selected"{/if}>{tr}text field{/tr}</option>
<option value="a" {if $type eq 'a'}selected="selected"{/if}>{tr}textarea{/tr}</option>
<option value="d" {if $type eq 'd'}selected="selected"{/if}>{tr}drop down{/tr}</option>
<option value="u" {if $type eq 'u'}selected="selected"{/if}>{tr}user selector{/tr}</option>
<option value="g" {if $type eq 'g'}selected="selected"{/if}>{tr}group selector{/tr}</option>
<option value="f" {if $type eq 'f'}selected="selected"{/if}>{tr}date and time{/tr}</option>
<option value="j" {if $type eq 'j'}selected="selected"{/if}>{tr}jscalendar{/tr}</option>
<option value="i" {if $type eq 'i'}selected="selected"{/if}>{tr}image{/tr}</option>
<option value="x" {if $type eq 'x'}selected="selected"{/if}>{tr}action{/tr}</option>
<option value="h" {if $type eq 'h'}selected="selected"{/if}>{tr}header{/tr}</option>
</select>
<span  id='trkfldoptions' {if $type eq 'd' or $type eq 'i' or $type eq 'x'}style="display:inline;"{else}style="display:none;"{/if}><br /><input type="text" name="options" value="{$options|escape}" size="50" /></span>
</td></tr>
<tr><td class="formcolor">{tr}Is column visible when listing tracker items?{/tr}</td><td class="formcolor"><input type="checkbox" name="isTblVisible" {if $isTblVisible eq 'y'}checked="checked"{/if} /></td></tr>
<tr><td class="formcolor">{tr}Column links to edit/view item?{/tr}</td><td class="formcolor"><input type="checkbox" name="isMain" {if $isMain eq 'y'}checked="checked"{/if} /></td></tr>
<tr><td class="formcolor">{tr}Column is searchable?{/tr}</td><td class="formcolor"><input type="checkbox" name="isSearchable" {if $isSearchable eq 'y'}checked="checked"{/if} /></td></tr>
<tr><td class="formcolor">{tr}Order{/tr}</td><td class="formcolor"><input type="text" size="5" name="position" value="{$position}" /></td></tr>
<tr><td class="formcolor">&nbsp;</td><td class="formcolor"><input type="submit" name="save" value="{tr}Save{/tr}" /></td></tr>
</table>
</form>
<h2>{tr}Tracker fields{/tr}</h2>
<div  align="center">
<table class="findtable">
<tr><td class="findtable">{tr}Find{/tr}</td>
   <td class="findtable">
   <form method="get" action="tiki-admin_tracker_fields.php">
     <input type="text" name="find" value="{$find|escape}" />
     <input type="submit" value="{tr}find{/tr}" name="search" />
     <input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
   </form>
   </td>
</tr>
</table>
<table class="normal">
<tr>
<td class="heading"><a class="tableheading" href="tiki-admin_tracker_fields.php?trackerId={$trackerId}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'position_desc'}position_asc{else}position_desc{/if}">{tr}position{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-admin_tracker_fields.php?trackerId={$trackerId}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'name_desc'}name_asc{else}name_desc{/if}">{tr}name{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-admin_tracker_fields.php?trackerId={$trackerId}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'type_desc'}type_asc{else}type_desc{/if}">{tr}type{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-admin_tracker_fields.php?trackerId={$trackerId}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'isMain_desc'}isMain_asc{else}isMain_desc{/if}">{tr}isMain{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-admin_tracker_fields.php?trackerId={$trackerId}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'isTblVisible_desc'}isTblVisible_asc{else}isTblVisible_desc{/if}">{tr}Tbl vis{/tr}</a></td>
<td class="heading">{tr}action{/tr}</td>
</tr>
{cycle values="odd,even" print=false}
{section name=user loop=$channels}
<tr>
<td class="{cycle advance=false}">{$channels[user].position}</td>
<td class="{cycle advance=false}">{$channels[user].label}</td>
<td class="{cycle advance=false}">{$channels[user].type}</td>
<td class="{cycle advance=false}">{$channels[user].isMain}</td>
<td class="{cycle advance=false}">{$channels[user].isTblVisible}</td>
<td class="{cycle}">
   <a class="link" href="tiki-admin_tracker_fields.php?trackerId={$trackerId}&amp;offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$channels[user].fieldId}">{tr}remove{/tr}</a>
   <a class="link" href="tiki-admin_tracker_fields.php?trackerId={$trackerId}&amp;offset={$offset}&amp;sort_mode={$sort_mode}&amp;fieldId={$channels[user].fieldId}">{tr}edit{/tr}</a>
</td>
</tr>
{/section}
</table>
<br />
<div class="mini">
{if $prev_offset >= 0}
[<a class="prevnext" href="tiki-admin_tracker_fields.php?find={$find}&amp;trackerId={$trackerId}&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}prev{/tr}</a>]&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;[<a class="prevnext" href="tiki-admin_tracker_fields.php?find={$find}&amp;trackerId={$trackerId}&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}next{/tr}</a>]
{/if}
{if $direct_pagination eq 'y'}
<br />
{section loop=$cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:$maxRecords}
<a class="prevnext" href="tiki-admin_tracker_fields.php?find={$find}&amp;trackerId={$trackerId}&amp;offset={$selector_offset}&amp;sort_mode={$sort_mode}">
{$smarty.section.foo.index_next}</a>&nbsp;
{/section}
{/if}

</div>
</div>

