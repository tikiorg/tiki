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

<h2>{tr}Create/edit trackers{/tr}</h2>
{if $individual eq 'y'}
<a class="link" href="tiki-objectpermissions.php?objectName=tracker%20{$name}&amp;objectType=Tracker&amp;permType=trackers&amp;objectId={$trackerId}">{tr}There are individual permissions set for this tracker{/tr}</a>
{/if}
<form action="tiki-admin_trackers.php" method="post">
<input type="hidden" name="trackerId" value="{$trackerId|escape}" />
<table class="normal">
<tr><td class="formcolor">{tr}Name{/tr}:</td><td class="formcolor"><input type="text" name="name" value="{$name|escape}" /></td></tr>
<tr><td class="formcolor">{tr}Description{/tr}:</td><td class="formcolor"><textarea name="description" rows="4" cols="40">{$description|escape}</textarea></td></tr>
{include file=categorize.tpl}
<tr><td class="formcolor">{tr}Show status when listing tracker items?{/tr}</td><td class="formcolor"><input type="checkbox" name="showStatus" {if $showStatus eq 'y'}checked="checked"{/if} /></td></tr>
<tr><td class="formcolor">{tr}Show creation date when listing tracker items?{/tr}</td><td class="formcolor"><input type="checkbox" name="showCreated" {if $showCreated eq 'y'}checked="checked"{/if} /></td></tr>
<tr><td class="formcolor">{tr}Show lastModif date when listing tracker items?{/tr}</td><td class="formcolor"><input type="checkbox" name="showLastModif" {if $showLastModif eq 'y'}checked="checked"{/if} /></td></tr>
<tr><td class="formcolor">{tr}Tracker items allow comments?{/tr}</td><td class="formcolor"><input type="checkbox" name="useComments" {if $useComments eq 'y'}checked="checked"{/if} />
{tr}and display comments in listing?{/tr} <input type="checkbox" name="showComments" {if $showComments eq 'y'}checked="checked"{/if} /></td></tr>
<tr><td class="formcolor">{tr}Tracker items allow attachments?{/tr}</td><td class="formcolor"><input type="checkbox" name="useAttachments" {if $useAttachments eq 'y'}checked="checked"{/if} />
{tr}and display attachments in listing?{/tr} <input type="checkbox" name="showAttachments" {if $showAttachments eq 'y'}checked="checked"{/if} /></td></tr>
</td></tr>
<tr><td  class="formcolor">&nbsp;</td><td class="formcolor"><input type="submit" name="save" value="{tr}Save{/tr}" /></td></tr>
</table>
</form>
<h2>{tr}trackers{/tr}</h2>
<div  align="center">
<table class="findtable">
<tr><td class="findtable">{tr}Find{/tr}</td>
   <td class="findtable">
   <form method="get" action="tiki-admin_trackers.php">
     <input type="text" name="find" value="{$find|escape}" />
     <input type="submit" value="{tr}find{/tr}" name="search" />
     <input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
   </form>
   </td>
</tr>
</table>
<table class="normal">
<tr>
<td class="heading"><a class="tableheading" href="tiki-admin_trackers.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'name_desc'}name_asc{else}name_desc{/if}">{tr}name{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-admin_trackers.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'description_desc'}description_asc{else}description_desc{/if}">{tr}description{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-admin_trackers.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'created_desc'}created_asc{else}created_desc{/if}">{tr}created{/tr}</a></td>
<td class="heading"><a class="tableheading" href="tiki-admin_trackers.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'lastModif_desc'}lastModif_asc{else}lastModif_desc{/if}">{tr}last modif{/tr}</a></td>
<td style="text-align:right;" class="heading"><a class="tableheading" href="tiki-admin_trackers.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'items_desc'}items_asc{else}items_desc{/if}">{tr}items{/tr}</a></td>
<td class="heading">{tr}action{/tr}</td>
</tr>
{cycle values="odd,even" print=false}
{section name=user loop=$channels}
<tr>
<td class="{cycle advance=false}">
<a class="tablename" href="tiki-view_tracker.php?trackerId={$channels[user].trackerId}">{$channels[user].name}</a>
</td>
<td class="{cycle advance=false}">{$channels[user].description}</td>
<td class="{cycle advance=false}">{$channels[user].created|tiki_short_datetime}</td>
<td class="{cycle advance=false}">{$channels[user].lastModif|tiki_short_datetime}</td>
<td style="text-align:right;" class="{cycle advance=false}">{$channels[user].items}</td>
<td  class="{cycle}">
   <a class="link" href="tiki-admin_trackers.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;trackerId={$channels[user].trackerId}"><img src='img/icons/edit.gif' alt='{tr}edit{/tr}' title='{tr}edit{/tr}' border='0' /></a>
   <a class="link" href="tiki-admin_tracker_fields.php?trackerId={$channels[user].trackerId}"><img src='img/icons/ico_table.gif' alt='{tr}fields{/tr}' title='{tr}fields{/tr}' border='0' /></a>
   {if $channels[user].individual eq 'y'}({/if}<a class="link" href="tiki-objectpermissions.php?objectName=Tracker%20{$channels[user].name}&amp;objectType=tracker&amp;permType=trackers&amp;objectId={$channels[user].trackerId}"><img src='img/icons/key.gif' border='0' alt='{tr}perms{/tr}' title='{tr}perms{/tr}' /></a>{if $channels[user].individual eq 'y'}){/if}
   <a class="link" href="tiki-admin_trackers.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$channels[user].trackerId}"><img src='img/icons2/delete.gif' alt='{tr}remove{/tr}' title='{tr}remove{/tr}' border='0' /></a>
</td>
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

