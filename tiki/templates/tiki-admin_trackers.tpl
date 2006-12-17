{* $Header: /cvsroot/tikiwiki/tiki/templates/tiki-admin_trackers.tpl,v 1.71 2006-12-17 17:43:20 fr_rodo Exp $ *}
<h1><a class="pagetitle" href="tiki-admin_trackers.php">{tr}Admin trackers{/tr}</a>
  
{if $feature_help eq 'y'}
<a href="{$helpurl}Trackers" target="tikihelp" class="tikihelp" title="{tr}Trackers{/tr}">
<img src="pics/icons/help.png" border="0" height="16" width="16" alt='{tr}help{/tr}' /></a>{/if}
{if $feature_view_tpl eq 'y'}
<a href="tiki-edit_templates.php?template=tiki-admin_trackers.tpl" target="tikihelp" class="tikihelp" title="{tr}View tpl{/tr}: {tr}admin Trackers tpl{/tr}">
<img border='0' src='pics/icons/shape_square_edit.png' alt="{tr}edit{/tr}" width='16' height='16' /></a>{/if}</h1>

<span class="button2"><a href="tiki-list_trackers.php" class="linkbut">{tr}List trackers{/tr}</a></span>
{if $trackerId}
<span class="button2"><a href="tiki-admin_tracker_fields.php?trackerId={$trackerId}" class="linkbut">{tr}Edit fields for tracker{/tr} {$name}</a></span>
<span class="button2"><a href="tiki-view_tracker.php?trackerId={$trackerId}" class="linkbut">{tr}View this tracker items{/tr}</a></span>
{/if}
<span class="button2"><a href="tiki-view_tracker.php?trackerId={$trackerId}" class="linkbut">{tr}View this tracker items{/tr}</a></span>
<br /><br />

{if $feature_tabs eq 'y'}
{cycle name=tabs values="1,2,3,4,5" print=false advance=false reset=true}
<div class="tabs">
<span id="tab{cycle name=tabs advance=false}" class="tabmark"><a href="javascript:tikitabs({cycle name=tabs},5);">{tr}Trackers{/tr}</a></span>
{if $trackerId}
<span id="tab{cycle name=tabs advance=false}" class="tabmark"><a href="javascript:tikitabs({cycle name=tabs},5);">{tr}Edit tracker{/tr} {$name} (#{$trackerId})</a></span>
{else}
<span id="tab{cycle name=tabs advance=false}" class="tabmark"><a href="javascript:tikitabs({cycle name=tabs},5);">{tr}Create trackers{/tr}</a></span>
{/if}
<span id="tab{cycle name=tabs advance=false}" class="tabmark"><a href="javascript:tikitabs({cycle name=tabs},5);">{tr}Import/export{/tr}</a></span>
<span id="tab{cycle name=tabs advance=false}" class="tabmark"><a href="javascript:tikitabs({cycle name=tabs},5);">{tr}Duplicate tracker{/tr}</a></span>
</div>
{/if}

{cycle name=content values="1,2,3,4" print=false advance=false reset=true}
{* --- tab with list --- *}
<div id="content{cycle name=content assign=focustab}{$focustab}" class="tabcontent"{if $feature_tabs eq 'y'} style="display:{if $focustab eq $cookietab}block{else}none{/if};"{/if}>
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
<td class="auto"><a title="{tr}edit{/tr}" href="tiki-admin_trackers.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;trackerId={$channels[user].trackerId}"><img src='pics/icons/page_edit.png' alt="{tr}edit{/tr}" border='0' width='16' height='16' /></a></td>
<td><a class="tablename" href="tiki-view_tracker.php?trackerId={$channels[user].trackerId}" title="{tr}view{/tr}">{$channels[user].name}</a></td>
<td nowrap="nowrap">{if $channels[user].individual eq 'y'}<a title="{tr}active permissions{/tr}" class="link" href="tiki-objectpermissions.php?objectName={$channels[user].name|escape:"url"}&amp;objectType=tracker&amp;permType=trackers&amp;objectId={$channels[user].trackerId}">
<img src='pics/icons/key_active.png' border='0' alt="{tr}active permissions{/tr}" width='16' height='16' /></a>{else}
<a title="{tr}permissions{/tr}" class="link" href="tiki-objectpermissions.php?objectName={$channels[user].name|escape:"url"}&amp;objectType=tracker&amp;permType=trackers&amp;objectId={$channels[user].trackerId}">
<img src='pics/icons/key.png' border='0' alt="{tr}permissions{/tr}" width='16' height='16' /></a>{/if}</td>
<td>{$channels[user].description}</td>
<td><a title="{tr}fields{/tr}" class="link" href="tiki-admin_tracker_fields.php?trackerId={$channels[user].trackerId}"><img src='pics/icons/table.png' alt="{tr}fields{/tr}" border='0' width='16' height='16' /></a></td>
<td>{$channels[user].created|tiki_short_date}</td>
<td>{$channels[user].lastModif|tiki_short_date}</td>
<td style="text-align:right;" >{$channels[user].items}</td>
<td  ><a title="{tr}delete{/tr}" class="link" href="tiki-admin_trackers.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$channels[user].trackerId}"><img src='pics/icons/cross.png' alt="{tr}delete{/tr}" border='0' width='16' height='16' /></a></td>
</tr>
{/section}
</table>
{include file="tiki-pagination.tpl"}
</div>
</div>

{* --- tab with form --- *}
<div id="content{cycle name=content assign=focustab}{$focustab}" class="tabcontent"{if $feature_tabs eq 'y'} style="display:{if $focustab eq $cookietab}block{else}none{/if};"{/if}>
<h2>{tr}Create/edit trackers{/tr}</h2>
{if $individual eq 'y'}
<div class="simplebox">
<a title="{tr}permissions{/tr}" class="link" href="tiki-objectpermissions.php?objectName={$name|escape:"url"}&amp;objectType=tracker&amp;permType=trackers&amp;objectId={$trackerId}">
<img src='pics/icons/key.png' border='0' alt="{tr}permissions{/tr}" width='16' height='16' />
{tr}There are individual permissions set for this tracker{/tr}</a>
</div>
{/if}
<form action="tiki-admin_trackers.php" method="post" name="editpageform" id="editpageform">
<input type="hidden" name="trackerId" value="{$trackerId|escape}" />
<table class="normal">
<tr class="formcolor"><td>{tr}Name{/tr}:</td><td><input type="text" name="name" value="{$name|escape}" /></td><td></td></tr>
<tr class="formcolor"><td>{tr}Description{/tr}:</td><td colspan="2"><textarea name="description" rows="4" cols="40">{$description|escape}</textarea></td></tr>
{assign var=cols value="2"}
{include file=categorize.tpl}
{if $feature_categories}
<tr class="formcolor"><td class="auto" colspan="2">{tr}Auto create corresponding categories{/tr}</td><td>
<input type="checkbox" name="autoCreateCategories" {if $autoCreateCategories eq 'y' }checked="checked"{/if} /></td></tr>
{/if}

{if $trk_with_mirror_tables eq 'y'}
<tr class="formcolor"><td class="auto" colspan="2">
{tr}Use "explicit" names in the mirror table{/tr}<br />
<em>{tr}tracker name must be unique, field names must be unique 
for a tracker and they must be valid in SQL{/tr}</em>
</td><td class="auto">
<input type="checkbox" name="useExplicitNames" {if $useExplicitNames eq 'y'}checked="checked"{/if} />
</td></tr>
{/if}
<tr class="formcolor"><td class="auto" colspan="2">{tr}Show status{/tr}</td><td>
<input type="checkbox" name="showStatus" {if $showStatus eq 'y'}checked="checked"{/if} /></td></tr>
<tr class="formcolor"><td class="auto" colspan="2">{tr}Default status displayed in list mode{/tr}</td><td>
{foreach key=st item=stdata from=$status_types}
<input type="checkbox" name="defaultStatus[]" value="{$st}"{if $defaultStatusList.$st} checked="checked"{/if} />{$stdata.label}<br />
{/foreach}
</td></tr>
<tr class="formcolor"><td class="auto" colspan="2">{tr}Show status to tracker admin only{/tr}</td><td>
<input type="checkbox" name="showStatusAdminOnly" {if $showStatusAdminOnly eq 'y'}checked="checked"{/if} /></td></tr>
<tr class="formcolor"><td class="auto" colspan="2">{tr}Send copies of all activity in this tracker to this e-mail address{/tr}:</td><td><input type="text" name="outboundEmail" value="{$outboundEmail|escape}" /></td></tr>
<tr class="formcolor"><td class="auto" colspan="2">{tr}Use simplified e-mail format{/tr}
<br /><i>{tr}The tracker needs a text field named Subject{/tr}</i>
</td><td>
<input type="checkbox" name="simpleEmail" {if $simpleEmail eq 'y'}checked="checked"{/if} />
</td></tr>
<tr class="formcolor"><td class="auto" colspan="2">{tr}New items are created with status{/tr}</td><td>
<select name="newItemStatus">
{foreach key=st item=stdata from=$status_types}
<option value="{$st}"{if $newItemStatus eq $st} selected="selected"{/if}>{$stdata.label}</option>
{/foreach}
</select>
</td></tr>
<tr class="formcolor"><td class="auto" colspan="2">{tr}Authoritative status for modified items{/tr}</td><td>
<select name="modItemStatus">
<option value="">{tr}No change{/tr}</option>
{foreach key=st item=stdata from=$status_types}
<option value="{$st}"{if $modItemStatus eq $st} selected="selected"{/if}>{$stdata.label}</option>
{/foreach}
</select>
</td></tr>
<tr class="formcolor"><td class="auto" colspan="2">{tr}Item creator can modify his items?{/tr}<br /><i>{tr}The tracker needs a user field with the option 1{/tr}</i></td><td><input type="checkbox" name="writerCanModify" {if $writerCanModify eq 'y'}checked="checked"{/if} /></td></tr>
<tr class="formcolor"><td class="auto" colspan="2">{tr}Only one item per user or IP{/tr}<br /><i>{tr}The tracker needs a user or IP field with the option 1{/tr}</i></td><td><input type="checkbox" name="oneUserItem" {if $oneUserItem eq 'y'}checked="checked"{/if} /></td></tr>
<tr class="formcolor"><td class="auto" colspan="2">{tr}Member of the default group of creator can modify items?{/tr}<br /><i>{tr}The tracker needs a group field with the option 1{/tr}</i></td><td><input type="checkbox" name="writerGroupCanModify" {if $writerGroupCanModify eq 'y'}checked="checked"{/if} /></td></tr>
<tr class="formcolor"><td class="auto" colspan="2">{tr}Show creation date when listing tracker items?{/tr}</td><td><input type="checkbox" name="showCreated" {if $showCreated eq 'y'}checked="checked"{/if} /></td></tr>
<tr class="formcolor"><td class="auto" colspan="2">{tr}Show lastModif date when listing tracker items?{/tr}</td><td><input type="checkbox" name="showLastModif" {if $showLastModif eq 'y'}checked="checked"{/if} /></td></tr>
<tr class="formcolor"><td class="auto" colspan="2">{tr}What field is used for default sort?{/tr}</td><td>
<select name="defaultOrderKey">
{section name=x loop=$fields}
<option value="{$fields[x].fieldId}"{if $defaultOrderKey eq $fields[x].fieldId} selected="selected"{/if}>{$fields[x].name|truncate:42:" ..."}</option>
{/section}
<option value="-1"{if $defaultOrderKey eq -1} selected="selected"{/if}>{tr}lastModif{/tr}</option>
<option value="-2"{if $defaultOrderKey eq -2} selected="selected"{/if}>{tr}created{/tr}</option>
</select>
</td></tr>
<tr class="formcolor"><td class="auto" colspan="2">{tr}What is default sort order in list?{/tr}</td><td>
<select name="defaultOrderDir">
<option value="asc" {if $defaultOrderDir eq 'asc'}selected="selected"{/if}>{tr}ascending{/tr}</option>
<option value="desc" {if $defaultOrderDir eq 'desc'}selected="selected"{/if}>{tr}descending{/tr}</option>
</select>
</td></tr>

<tr class="formcolor"><td class="auto" colspan="2">{tr}Tracker items allow ratings?{/tr}</td><td>
<input type="checkbox" name="useRatings" {if $useRatings eq 'y'}checked="checked"{/if} onclick="toggleSpan('ratingoptions');" />
<span id="ratingoptions" style="display:{if $useRatings eq 'y'}inline{else}none{/if};">
{tr}with values{/tr} <input type="text" name="ratingOptions" value="{if $ratingOptions}{$ratingOptions}{else}-2,-1,0,1,2{/if}" />
{tr}and display rating results in listing?{/tr} <input type="checkbox" name="showRatings" {if $showRatings eq 'y'}checked="checked"{/if} />
</span>
</td></tr>

<tr class="formcolor"><td class="auto" colspan="2">{tr}Tracker items allow comments?{/tr}</td><td>
<input type="checkbox" name="useComments" {if $useComments eq 'y'}checked="checked"{/if} onclick="toggleSpan('commentsoptions');" />
<span id="commentsoptions" style="display:{if $useComments eq 'y'}inline{else}none{/if};">
{tr}and display comments in listing?{/tr} <input type="checkbox" name="showComments" {if $showComments eq 'y'}checked="checked"{/if} />
</span>
</td></tr>

<tr class="formcolor"><td class="auto" colspan="2">{tr}Tracker items allow attachments?{/tr}</td><td>
<input type="checkbox" name="useAttachments" {if $useAttachments eq 'y'}checked="checked"{/if} onclick="toggleSpan('attachmentsoptions');toggleBlock('attachmentsconf');" />
<span id="attachmentsoptions" style="display:{if $useAttachments eq 'y'}inline{else}none{/if};">
{tr}and display attachments in listing?{/tr} <input type="checkbox" name="showAttachments" {if $showAttachments eq 'y'}checked="checked"{/if} />
</span>
</td></tr>
<tr class="formcolor"><td class="auto" colspan="3">
<div id="attachmentsconf" style="display:{if $useAttachments eq 'y'}block{else}none{/if};">
{tr}Attachment display options (Use numbers to order items, 0 will not be displayed, and negative values display in popups){/tr}<br />
<table width="100%"><tr>
<td>{tr}filename{/tr}</td>
<td>{tr}created{/tr}</td>
<td>{tr}downloads{/tr}</td>
<td>{tr}comment{/tr}</td>
<td>{tr}filesize{/tr}</td>
<td>{tr}version{/tr}</td>
<td>{tr}filetype{/tr}</td>
<td>{tr}longdesc{/tr}</td></tr>
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
<tr class="formcolor"><td colspan="2">{tr}Items can be created only during a certain time{/tr}</td><td>{tr}After:{/tr} <input type="checkbox" name="start"{if $info.start} checked="checked"{/if} /> {html_select_date prefix="start_" time=$info.start start_year="0" end_year="+10" field_order=$display_field_order} <span dir="ltr">{html_select_time prefix="start_" time=$info.start display_seconds=false}</span>&nbsp;{$siteTimeZone}<br />{tr}Before:{/tr}  <input type="checkbox" name="end"{if $info.end} checked="checked"{/if} /> {html_select_date prefix="end_" time=$info.end start_year="0" end_year="+10" field_order=$display_field_order} <span dir="ltr">{html_select_time prefix="end_" time=$info.end display_seconds=false}</span>
&nbsp;{$siteTimeZone}
</td></tr>

<tr class="formcolor"><td>&nbsp;</td><td colspan="2"><input type="submit" name="save" value="{tr}Save{/tr}" /></td></tr>
</table>
</form>
</div>

{* --- tab with raw form --- *}
<div id="content{cycle name=content assign=focustab}{$focustab}" class="tabcontent"{if $feature_tabs eq 'y'} style="display:{if $focustab eq $cookietab}block{else}none{/if};"{/if}>
<h2>{tr}Import/export trackers{/tr}</h2>

<form action="tiki-admin_trackers.php" method="post">
<input type="hidden" name="trackerId" value="{$trackerId|escape}" />
<input type="hidden" name="import" value="1" />
<textarea name="rawmeat" cols="62" rows="32" wrap="soft">
{if $trackerId}
[TRACKER]
trackerId = {$trackerId}
name = {$name}
description = {$description}
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
attachmentsconf = {$ui.filename|default:0},{$ui.created|default:0},{$ui.downloads|default:0},{$ui.comment|default:0},{$ui.filesize|default:0},{$ui.version|default:0},{$ui.filetype|default:0},{$ui.longdesc|default:0} 
useRatings = {$useRatings}
ratingOptions = {$ratingOptions}
categories = {$catsdump}
{/if}
</textarea><br />
<input type="submit" name="save" value="{tr}Import{/tr}" />
</form>

{if $trackerId}
<h3>{tr}Import/Export CSV Data{/tr}</h3>
<form action="tiki-export_tracker.php?trackerId={$trackerId}" method="post">
<table class="normal">
<tr class="formcolor"><td>{tr}Download CVS export{/tr}</td>
<td>{tr}File: {/tr}{tr}tracker{/tr}_{$trackerId}.csv<br />{tr}Charset encoding:{/tr} <select name="encoding"><option value="UTF-8" selected="selected">{tr}UTF-8{/tr}</option><option value="ISO-8859-1">{tr}ISO-8859-1{/tr}</option></select>
<br /><input type="radio" name="which" value="list"/> {tr}Fields searchable and visible in items list{/tr}
<br /><input type="radio" name="which" value="item"/> {tr}Fields visible in an item view{/tr} 
<br /><input type="radio" name="which" value="all" checked="checked"/> {tr}All fields{/tr}
</td></tr>
<tr class="formcolor"><td>&nbsp;</td><td><input type="submit" name="export" value="{tr}export{/tr}" /></td></tr>
</table>
</form>
<form action="tiki-import_tracker.php?trackerId={$trackerId}" method="post" enctype="multipart/form-data">
<table class="normal">
<tr class="formcolor"><td>{tr}Download CSV export{/tr}</td><td><a href="tiki-export_tracker.php?trackerId={$trackerId}" class="linkbut">{tr}tracker_{$trackerId}.csv{/tr}</a></td></tr>
<tr class="formcolor"><td>{tr}Import file{/tr}</td><td><input name="importfile" type="file" /></td></tr>
<tr class="formcolor"><td>&nbsp;</td><td><input type="submit" name="save" value="{tr}save{/tr}" /></td></tr>
</table>
</form>
{/if}

</div>

{* --- tab with raw form --- *}
<div id="content{cycle name=content assign=focustab}{$focustab}" class="tabcontent"{if $feature_tabs eq 'y'} style="display:{if $focustab eq $cookietab}block{else}none{/if};"{/if}>
<h2>{tr}Duplicate tracker{/tr}</h2>

<form action="tiki-admin_trackers.php" method="post">
<table class="normal">
<tr class="formcolor"><td>{tr}Name{/tr}</td><td><input type="text" name="name" value="{$name|escape}" /></td></tr>
<tr class="formcolor"><td>{tr}Description{/tr}</td><td><textarea name="description" rows="4" cols="40">{$description|escape}</textarea></td></tr>
<tr class="formcolor"><td>{tr}Tracker{/tr}</td>
<td>
<select name="trackerId">
{section name=ix loop=$trackers}
<option value="{$trackers[ix].trackerId}">{$trackers[ix].name|escape}</option>
{/section}
</select>
</td>
</tr>
<tr class="formcolor"><td>{tr}Duplicate categories{/tr}</td><td><input type="checkbox" name="dupCateg" /></td></tr>
<tr class="formcolor"><td>{tr}Duplicate perms{/tr}</td><td><input type="checkbox" name="dupPerms" /></td></tr>
<tr class="formcolor"><td></td><td><input type="submit" name="duplicate" value="{tr}Duplicate tracker{/tr}" /></td></tr>
</table>
</form>
</div>
