<a class="pagetitle" href="tiki-admin_trackers.php">{tr}Admin trackers{/tr}</a>
  
{if $feature_help eq 'y'}
<a href="http://tikiwiki.org/tiki-index.php?page=Trackers" target="tikihelp" class="tikihelp" title="{tr}Tikiwiki.org help{/tr}: {tr}Trackers{/tr}">
<img border='0' src='img/icons/help.gif' alt='help' /></a>{/if}

{if $feature_view_tpl eq 'y'}
<a href="tiki-edit_templates.php?template=templates/tiki-admin_trackers.tpl" target="tikihelp" class="tikihelp" title="{tr}View tpl{/tr}: {tr}admin Trackers tpl{/tr}">
<img border='0' src='img/icons/info.gif' alt='edit tpl' /></a>{/if}
<br /><br />

<div>
<span class="button2"><a href="tiki-list_trackers.php" class="linkbut">{tr}List trackers{/tr}</a></span>

{if $tiki_p_admin_trackers eq 'y'}
<span class="button2"><a href="tiki-admin_trackers.php" class="linkbut">{tr}Admin trackers{/tr}</a></span>
{/if}
</div>
<br /><br />

{cycle name=tabs values="1,2,3" print=false advance=false}
<div class="tabs">
<span id="tab{cycle name=tabs}" class="tab tabActive">{tr}Trackers{/tr}</span>
{if $trackerId}
<span id="tab{cycle name=tabs}" class="tab">{tr}Edit tracker{/tr} {$name} (#{$trackerId})</span>
{else}
<span id="tab{cycle name=tabs}" class="tab">{tr}Create trackers{/tr}</span>
{/if}
</div>

{cycle name=content values="1,2,3,4,5" print=false advance=false}
{* --- tab with list --- *}
<div id="content{cycle name=content}" class="content">
<h2>{tr}Trackers{/tr}</h2>

<div  align="center">
<form method="get" action="tiki-admin_trackers.php">
<table class="findtable">
<tr><td class="findtable">{tr}Find{/tr}</td>
<td class="findtable">
<input type="text" name="find" value="{$find|escape}" /></td>
<td class="findtable">
<input type="submit" value="{tr}find{/tr}" name="search" />
<input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
</td></tr></table>
</form>


<table class="normal">
<tr>
<td class="heading auto">&nbsp;</td>
<td class="heading"><a class="tableheading" href="tiki-admin_trackers.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'name_desc'}name_asc{else}name_desc{/if}">{tr}name{/tr}</a></td>
<td class="heading">&nbsp;</td>
<td class="heading"><a class="tableheading" href="tiki-admin_trackers.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'description_desc'}description_asc{else}description_desc{/if}">{tr}description{/tr}</a></td>
<td class="heading">&nbsp;</td>
<td class="heading"><a class="tableheading" href="tiki-admin_trackers.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'created_desc'}created_asc{else}created_desc{/if}">{tr}created{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-admin_trackers.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'lastModif_desc'}lastModif_asc{else}lastModif_desc{/if}">{tr}last modif{/tr}</a></td>
<td style="text-align:right;" class="heading"><a class="tableheading" href="tiki-admin_trackers.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'items_desc'}items_asc{else}items_desc{/if}">{tr}items{/tr}</a></td>
<td class="heading">&nbsp;</td>
</tr>
{cycle values="odd,even" print=false}
{section name=user loop=$channels}
<tr class="{cycle}">
<td class="auto"><a href="tiki-admin_trackers.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;trackerId={$channels[user].trackerId}"><img src='img/icons/edit.gif' alt='{tr}edit{/tr}' title='{tr}edit{/tr}' border='0' /></a></td>
<td><a class="tablename" href="tiki-view_tracker.php?trackerId={$channels[user].trackerId}">{$channels[user].name}</a></td>
<td nowrap="nowrap">{if $channels[user].individual eq 'y'}({/if}<a 
class="link" href="tiki-objectpermissions.php?objectName=Tracker%20{$channels[user].name}&amp;objectType=tracker&amp;permType=trackers&amp;objectId={$channels[user].trackerId}"><img 
src='img/icons/key.gif' border='0' alt='{tr}perms{/tr}' title='{tr}perms{/tr}' /></a>{if $channels[user].individual eq 'y'}){/if}</td>
<td>{$channels[user].description}</td>
<td><a class="link" href="tiki-admin_tracker_fields.php?trackerId={$channels[user].trackerId}"><img src='img/icons/ico_table.gif' alt='{tr}fields{/tr}' title='{tr}fields{/tr}' border='0' /></a></td>
<td>{$channels[user].created|tiki_short_datetime}</td>
<td>{$channels[user].lastModif|tiki_short_datetime}</td>
<td style="text-align:right;" >{$channels[user].items}</td>
<td  ><a class="link" href="tiki-admin_trackers.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$channels[user].trackerId}"><img src='img/icons2/delete.gif' alt='{tr}remove{/tr}' title='{tr}remove{/tr}' border='0' /></a></td>
</tr>
{/section}
</table>
<div class="mini">
{if $prev_offset >= 0}
[<a class="prevnext" href="tiki-admin_trackers.php?find={$find}&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}prev{/tr}</a>]&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;[<a class="prevnext" href="tiki-admin_trackers.php?find={$find}&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}next{/tr}</a>]
{/if}
{if $direct_pagination eq 'y'}
<br />
{section loop=$cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:$maxRecords}
<a class="prevnext" href="tiki-admin_trackers.php?find={$find}&amp;offset={$selector_offset}&amp;sort_mode={$sort_mode}">
{$smarty.section.foo.index_next}</a>&nbsp;
{/section}
{/if}
</div>
</div>
</div>

{* --- tab with form --- *}
<div id="content{cycle name=content}" class="content">
<h2>{tr}Create/edit trackers{/tr}</h2>
{if $individual eq 'y'}
<a class="link" href="tiki-objectpermissions.php?objectName=tracker%20{$name}&amp;objectType=Tracker&amp;permType=trackers&amp;objectId={$trackerId}">{tr}There are individual permissions set for this tracker{/tr}</a>
{/if}
<form action="tiki-admin_trackers.php" method="post">
<input type="hidden" name="trackerId" value="{$trackerId|escape}" />
<table class="normal">
<tr class="formcolor"><td>{tr}Name{/tr}:</td><td><input type="text" name="name" value="{$name|escape}" /></td></tr>
<tr class="formcolor"><td>{tr}Description{/tr}:</td><td><textarea name="description" rows="4" cols="40">{$description|escape}</textarea></td></tr>
{include file=categorize.tpl}
<tr class="formcolor"><td>{tr}Show status when listing tracker items?{/tr}</td><td>
<input type="checkbox" name="showStatus" {if $showStatus eq 'y'}checked="checked"{/if} onclick="toggleSpan('statusoptions');" />
<span id="statusoptions" style="display:{if $showStatus eq 'y'}inline{else}none{/if};">
{tr}Show status to tracker admin only (regular users only see open items){/tr} 
<input type="checkbox" name="showStatusAdminOnly" {if $showStatusAdminOnly eq 'y'}checked="checked"{/if} />
</span>
</td></tr>
<tr class="formcolor"><td>{tr}Show creation date when listing tracker items?{/tr}</td><td><input type="checkbox" name="showCreated" {if $showCreated eq 'y'}checked="checked"{/if} /></td></tr>
<tr class="formcolor"><td>{tr}Show lastModif date when listing tracker items?{/tr}</td><td><input type="checkbox" name="showLastModif" {if $showLastModif eq 'y'}checked="checked"{/if} /></td></tr>
<tr class="formcolor"><td>{tr}Tracker items allow comments?{/tr}</td><td>
<input type="checkbox" name="useComments" {if $useComments eq 'y'}checked="checked"{/if} onclick="toggleSpan('commentsoptions');" />
<span id="commentsoptions" style="display:{if $useComments eq 'y'}inline{else}none{/if};">
{tr}and display comments in listing?{/tr} <input type="checkbox" name="showComments" {if $showComments eq 'y'}checked="checked"{/if} />
</span>
</td></tr>
<tr class="formcolor"><td>{tr}Tracker items allow attachments?{/tr}</td><td>
<input type="checkbox" name="useAttachments" {if $useAttachments eq 'y'}checked="checked"{/if} onclick="toggleSpan('attachmentsoptions');toggleBlock('attachmentsconf');" />
<span id="attachmentsoptions" style="display:{if $useAttachments eq 'y'}inline{else}none{/if};">
{tr}and display attachments in listing?{/tr} <input type="checkbox" name="showAttachments" {if $showAttachments eq 'y'}checked="checked"{/if} />
</span>
</td></tr>
<tr class="formcolor"><td colspan="2">
<div id="attachmentsconf" style="display:{if $useAttachments eq 'y'}bloc{else}none{/if};">
{tr}Attachement display options (Use numbers to order items, 0 will not be displayed, and negative values will be displayed in a popup){/tr}<br />
<table width="100%"><tr>
<td>{tr}name{/tr}</td>
<td>{tr}date{/tr}</td>
<td>{tr}dls{/tr}</td>
<td>{tr}desc{/tr}</td>
<td>{tr}size{/tr}</td>
<td>{tr}version{/tr}</td>
<td>{tr}type{/tr}</td>
<td>{tr}long desc{/tr}</td></tr>
<tr>
<td><input type="text" size="2" name="ui[filename]" value="{$ui.filename}" /></td>
<td><input type="text" size="2" name="ui[created]" value="{$ui.created}" /></td>
<td><input type="text" size="2" name="ui[downloads]" value="{$ui.downloads}" /></td>
<td><input type="text" size="2" name="ui[comment]" value="{$ui.comment}" /></td>
<td><input type="text" size="2" name="ui[filesize]" value="{$ui.filesize}" /></td>
<td><input type="text" size="2" name="ui[version]" value="{$ui.version}" /></td>
<td><input type="text" size="2" name="ui[filetype]" value="{$ui.filetype}" /></td>
<td><input type="text" size="2" name="ui[longdesc]" value="{$ui.longdesc}" /></td>
</tr></table>
</div>
</td></tr>
<tr class="formcolor"><td>&nbsp;</td><td><input type="submit" name="save" value="{tr}Save{/tr}" /></td></tr>
</table>
</form>
</div>


