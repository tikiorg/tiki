<a class="pagetitle" href="tiki-view_tracker.php?trackerId={$trackerId}">{tr}Tracker{/tr}: {$tracker_info.name}</a><br /><br />
<div>
<span class="button2"><a href="tiki-list_trackers.php" class="linkbut">{tr}List trackers{/tr}</a></span>
{if $user}
<span class="button2"><a href="tiki-view_tracker.php?trackerId={$trackerId}&amp;monitor=1" class="linkbut">{tr}{$email_mon}{/tr}</a></span>
{/if}
{if $tiki_p_admin_trackers eq 'y'}
&nbsp;&nbsp;
<span class="button2"><a href="tiki-admin_trackers.php" class="linkbut">{tr}Admin trackers{/tr}</a></span>
<span class="button2"><a href="tiki-admin_trackers.php?trackerId={$trackerId}" class="linkbut">{tr}Edit this tracker{/tr}</a></span>
<span class="button2"><a href="tiki-admin_tracker_fields.php?trackerId={$trackerId}" class="linkbut">{tr}Edit fields{/tr}</a></span>
{/if}
</div>
<br />
<div class="wikitext">{$tracker_info.description}</div>
{if $mail_msg}
<div class="wikitext">{$mail_msg}</div>
{/if}
<br />
{cycle name=tabs values="1,2,3" print=false advance=false}
<div class="tabs">
{if $tiki_p_view_trackers eq 'y'}
<span id="tab{cycle name=tabs}" class="tab tabActive">{tr}Tracker{/tr} <i>{$tracker_info.name}</i></span>
{/if}
{if $tiki_p_create_tracker_items eq 'y'}
<span id="tab{cycle name=tabs}" class="tab">{tr}Insert new item{/tr}</span>
{/if}
</div>

{cycle name=content values="1,2,3" print=false advance=false}

{* -------------------------------------------------- tab with list --- *}
{if $tiki_p_view_trackers eq 'y'}

<div id="content{cycle name=content}" class="content">

{if (($tracker_info.showStatus eq 'y' and $tracker_info.showStatusAdminOnly ne 'y') or $tiki_p_admin_trackers eq 'y') or $show_filters eq 'y'}
<form action="tiki-view_tracker.php" method="post">
<input type="hidden" name="trackerId" value="{$trackerId|escape}" />
{if $status}<input type="hidden" name="status" value="{$status}" />{/if}
{if $sort_mode}<input type="hidden" name="sort_mode" value="{$sort_mode}" />{/if}
<table class="normal"><tr>
{if ($tracker_info.showStatus eq 'y' and $tracker_info.showStatusAdminOnly ne 'y') or $tiki_p_admin_trackers eq 'y'}
{foreach key=st item=stdata from=$status_types}
<td><div class="{$stdata.class}">
<a href="tiki-view_tracker.php?trackerId={$trackerId}{if $filtervalue}&amp;filtervalue={$filtervalue|escape:"url"}{/if}{if $filterfield}&amp;filterfield={$filterfield|escape:"url"}{/if}{if $sort_mode}&amp;sort_mode={$sort_mode}{/if}&amp;status={$stdata.statuslink}" 
class="statusimg">{html_image file=$stdata.image title=$stdata.label alt=$stdata.label align=top}</a></div></td>
{/foreach}
{/if}
{if $show_filters eq 'y'}
<td class="formcolor" style="width:100%;"><input type="text" name="filtervalue" value="{$filtervalue}" /></td>
<td>
<select name="filterfield">
{foreach key=fid item=field from=$listfields}
{if $field.isSearchable eq 'y' and $field.type ne 'f' and $field.type ne 'j' and $field.type ne 'i'}
<option value="{$fid}"{if $fid eq $filterfield} selected="selected"{/if}>{$field.name|truncate:255:"..."}</option>
{/if}
{/foreach}
</select>
</td>
{/if}
<td><input type="submit" name="filter" value="{tr}filter{/tr}" /></td>
</tr></table>
</form>
{/if}

{if $cant_pages > 1 or $initial}
<div align="center">
{section name=ini loop=$initials}
{if $initial and $initials[ini] eq $initial}
<span class="button2"><span class="linkbuton">{$initials[ini]|capitalize}</span></span> . 
{else}
<a href="tiki-view_tracker.php?initial={$initials[ini]}&amp;trackerId={$trackerId}{if $sort_mode}&amp;sort_mode={$sort_mode}{/if}{if $status}&amp;status={$status|escape:"url"}{/if}" 
class="prevnext">{$initials[ini]}</a> . 
{/if}
{/section}
<a href="tiki-view_tracker.php?initial=&amp;trackerId={$trackerId}{if $sort_mode}&amp;sort_mode={$sort_mode}{/if}{if $status}&amp;status={$status|escape:"url"}{/if}" 
class="prevnext">{tr}All{/tr}</a>
</div>
{/if}

<table class="normal">
<tr>
{if $tracker_info.showStatus eq 'y' or ($tracker_info.showStatusAdminOnly eq 'y' and $tiki_p_admin_trackers eq 'y')}
<td class="heading auto" style="width:20px;">&nbsp;</td>
{/if}
{section name=ix loop=$fields}
{if $fields[ix].type eq 'l' and $fields[ix].isTblVisible eq 'y'}
<td class="heading auto">{$fields[ix].name|default:"&nbsp;"}</td>
{elseif $fields[ix].isTblVisible eq 'y' and $fields[ix].type ne 'x' and $fields[ix].type ne 'h'}
<td class="heading auto"><a class="tableheading" href="tiki-view_tracker.php?{if $status}status={$status}&amp;{/if}trackerId={$trackerId}&amp;offset={$offset}&amp;sort_mode=f_{if $sort_mode eq
'f_'|cat:$fields[ix].fieldId|cat:'_asc'}{$fields[ix].fieldId|escape:"url"}_desc{else}{$fields[ix].fieldId|escape:"url"}_asc{/if}">{$fields[ix].name|truncate:255:"..."|default:"&nbsp;"}</a></td>
{/if}
{/section}
{if $tracker_info.showCreated eq 'y'}
<td class="heading"><a class="tableheading" href="tiki-view_tracker.php?{if $status}status={$status}&amp;{/if}{if $find}find={$find}&amp;{/if}trackerId={$trackerId}&amp;offset={$offset}&amp;sort_mode={if 
$sort_mode eq 'created_desc'}created_asc{else}created_desc{/if}">{tr}created{/tr}</a></td>
{/if}
{if $tracker_info.showLastModif eq 'y'}
<td class="heading"><a class="tableheading" href="tiki-view_tracker.php?status={$status}&amp;find={$find}&amp;trackerId={$trackerId}&amp;offset={$offset}{section 
name=ix loop=$fields}{if $fields[ix].value}&amp;{$fields[ix].name}={$fields[ix].value}{/if}{/section}&amp;sort_mode={if $sort_mode eq
'lastModif_desc'}lastModif_asc{else}lastModif_desc{/if}">{tr}lastModif{/tr}</a></td>
{/if}
{if $tracker_info.useComments eq 'y' and $tracker_info.showComments eq 'y'}
<td class="heading" width="5%">{tr}coms{/tr}</td>
{/if}
{if $tracker_info.useAttachments eq 'y' and  $tracker_info.showAttachments eq 'y'}
<td class="heading" width="5%">{tr}atts{/tr}</td>
{/if}
{if $tiki_p_admin_trackers eq 'y'}
<td class="heading" width="5%">&nbsp;</td>
{/if}
</tr>
{cycle values="odd,even" print=false}
{section name=user loop=$items}
<tr class="{cycle}">
{if $tracker_info.showStatus eq 'y' or ($tracker_info.showStatusAdminOnly eq 'y' and $tiki_p_admin_trackers eq 'y')}
<td class="auto" style="width:20px;">
{assign var=ustatus value=$items[user].status|default:"c"}
{html_image file=$status_types.$ustatus.image title=$status_types.$ustatus.label alt=$status_types.$ustatus.label}
</td>
{/if}

{section name=ix loop=$items[user].field_values}

{if $items[user].field_values[ix].isTblVisible eq 'y'}
{if $items[user].field_values[ix].type eq 'l'}
<td class="auto">
{foreach key=tid item=tlabel from=$items[user].field_values[ix].links}
<div><a href="tiki-view_tracker_item.php?trackerId={$items[user].field_values[ix].trackerId}&amp;itemId={$tid}" class="link">{$tlabel|truncate:255:"..."}</a></div>
{/foreach}
</td>
{elseif $items[user].field_values[ix].isMain eq 'y' or ($items[user].field_values[ix].linkId and $items[user].field_values[ix].trackerId)}
<td class="auto">

{if $items[user].field_values[ix].linkId and $items[user].field_values[ix].trackerId}
<a href="tiki-view_tracker_item.php?trackerId={$items[user].field_values[ix].trackerId}&amp;itemId={$items[user].field_values[ix].linkId}" class="link">

{elseif $tiki_p_view_trackers eq 'y' or $tiki_p_modify_tracker_items eq 'y' or $tiki_p_comment_tracker_items eq 'y' 
 or ($tracker_info.writerCanModify eq 'y' and $user and $my eq $user) or ($tracker_info.writerCanModify eq 'y' and $group and $ours eq $group)}
<a class="tablename" href="tiki-view_tracker_item.php?trackerId={$trackerId}&amp;itemId={$items[user].itemId}&amp;show=view">
{/if}

{if  ($items[user].field_values[ix].type eq 't' or $items[user].field_values[ix].type eq 'n' or $items[user].field_values[ix].type eq 'c') 
 and $items[user].field_values[ix].options_array[2]}<span class="formunit">&nbsp;{$items[user].field_values[ix].options_array[2]}</span>{/if}

{if $items[user].field_values[ix].type eq 'f'}
{$items[user].field_values[ix].value|tiki_short_datetime|truncate:255:"..."|default:"&nbsp;"}

{elseif $items[user].field_values[ix].type eq 'c'}
{$items[user].field_values[ix].value|replace:"y":"Yes"|replace:"n":"No"}

{elseif $items[user].field_values[ix].type eq 'a'}
{$items[user].field_values[ix].pvalue}

{elseif $items[user].field_values[ix].type eq 'i'}
<img src="{$items[user].field_values[ix].value}" alt="" />

{else}
{$items[user].field_values[ix].value|truncate:255:"..."|default:"&nbsp;"}

{/if}

{if ($items[user].field_values[ix].type eq 't' or $items[user].field_values[ix].type eq 'n' or $items[user].field_values[ix].type eq 'c') 
 and $items[user].field_values[ix].options_array[3]}<span class="formunit">&nbsp;{$items[user].field_values[ix].options_array[3]}</span>{/if}

{if $tiki_p_view_trackers eq 'y' or $tiki_p_modify_tracker_items eq 'y' or $tiki_p_comment_tracker_items eq 'y' or $items[user].field_values[ix].linkId}</a>{/if}
</td>
{else}
{if $items[user].field_values[ix].type eq 'f' or $items[user].field_values[ix].type eq 'j'}
<td class="auto">
{$items[user].field_values[ix].value|tiki_short_datetime|default:"&nbsp;"}
</td>
{elseif $items[user].field_values[ix].type eq 'a'}
<td class="auto">
{$items[user].field_values[ix].pvalue}
</td>
{elseif $items[user].field_values[ix].type ne 'x' and $items[user].field_values[ix].type ne 'h'}
<td class="auto">
{$items[user].field_values[ix].value|truncate:255:"..."|default:"&nbsp;"}
</td>
{/if}
{/if}
{/if}
{/section}


{if $tracker_info.showCreated eq 'y'}
<td>{$items[user].created|tiki_short_datetime}</td>
{/if}
{if $tracker_info.showLastModif eq 'y'}
<td>{$items[user].lastModif|tiki_short_datetime}</td>
{/if}
{if $tracker_info.useComments eq 'y' and $tracker_info.showComments eq 'y'}
<td  style="text-align:center;">{$items[user].comments}</td>
{/if}
{if $tracker_info.useAttachments eq 'y' and $tracker_info.showAttachments eq 'y'}
<td  style="text-align:center;"><a href="tiki-view_tracker_item.php?trackerId={$trackerId}{section name=mix loop=$fields}{if
$fields[mix].value}&amp;{$fields[mix].name}={$fields[mix].value}{/if}{/section}&amp;itemId={$items[user].itemId}&amp;show=att" 
link="{tr}list attachments{/tr}"><img src="img/icons/folderin.gif" border="0" alt="{tr}List Attachments{/tr}" 
/></a>{$items[user].attachments}</td>
{/if}
{if $tiki_p_admin_trackers eq 'y'}
<td><a class="link" href="tiki-view_tracker.php?status={$status}&amp;trackerId={$trackerId}&amp;offset={$offset}&amp;sort_mode={$sort_mode}{section 
name=mix loop=$fields}{if $fields[mix].value}&amp;{$fields[mix].name}={$fields[mix].value}{/if}{/section}&amp;remove={$items[user].itemId}" 
title="{tr}delete{/tr}"><img border="0" alt="{tr}delete{/tr}" src="img/icons2/delete.gif" /></a></td>
{/if}
</tr>
{/section}
</table>
{include file="tiki-pagination.tpl"}
</div>
{/if}

{* --------------------------------------------------------------------------------- tab with edit --- *}
{if $tiki_p_create_tracker_items eq 'y'}
<div id="content{cycle name=content}" class="content">
<form action="tiki-view_tracker.php" method="post">
<input type="hidden" name="trackerId" value="{$trackerId|escape}" />

<h3>{tr}Insert new item{/tr}</h3>
<table class="normal">
<tr class="formcolor"><td>&nbsp;</td><td colspan="3"><input type="submit" name="save" value="{tr}save{/tr}" /></td></tr>

{if $tracker_info.showStatus eq 'y' or $tiki_p_admin_trackers eq 'y'}
<tr class="formcolor"><td>{tr}Status{/tr}</td>
<td>
<select name="status">
{foreach key=st item=stdata from=$status_types}
<option value="{$st}"{if $tracker_info.newItemStatus eq $st} selected="selected"{/if} 
style="background-image:url('{$stdata.image}');background-repeat:no-repeat;padding-left:17px;">{$stdata.label}</option>
{/foreach}
</select>
</td></tr>
{/if}


{section name=ix loop=$fields}
{assign var=fid value=$fields[ix].fieldId}

{if $fields[ix].type ne 'x' and $fields[ix].type ne 'l'}
{if $fields[ix].type eq 'h'}
</table>
<h3>{$fields[ix].name}</h3>
<table class="normal">
{else}
{if ($fields[ix].type eq 'c' or $fields[ix].type eq 't' or $fields[ix].type eq 'n') and $fields[ix].options_array[0] eq '1'}
<tr class="formcolor"><td class="formlabel">{$fields[ix].name}</td><td nowrap="nowrap">
{elseif $stick eq 'y'}
<td class="formlabel right">{$fields[ix].name}</td><td nowrap="nowrap">
{else}
<tr class="formcolor"><td class="formlabel">{$fields[ix].name}
{if $fields[ix].type eq 'a' and $fields[ix].options_array[0] eq 1}
<br />
{include file=tiki-edit_help_tool.tpl qtnum=$fid area_name=$fields[ix].ins_id}
{/if}
</td><td colspan="3" nowrap="nowrap">
{/if}
{/if}

{if $fields[ix].type eq 'u'}
{if !$fields[ix].options or ($fields[ix].options eq '1' and $tiki_p_admin_trackers eq 'y')}
<select name="{$fields[ix].ins_id}">
<option value="">{tr}None{/tr}</option>
{foreach key=id item=one from=$users}
{if $fields[ix].value}
<option value="{$one|escape}"{if $one eq $fields[ix].value} selected="selected"{/if}>{$one}</option>
{else}
<option value="{$one|escape}">{$one}</option>
{/if}
{/foreach}
</select>
{elseif $fields[ix].options eq 1 and $user}
{$user}
{/if}

{elseif $fields[ix].type eq 'g'}
{if !$fields[ix].options or ($fields[ix].options eq '1' and $tiki_p_admin_trackers eq 'y')}
<select name="{$fields[ix].ins_id}">
<option value="">{tr}None{/tr}</option>
{section name=ux loop=$groups}
<option value="{$groups[ux]|escape}">{$groups[ux]}</option>
{/section}
</select>
{elseif $fields[ix].options eq 1 and $group}
{$group}
{/if}

{elseif $fields[ix].type eq 'e'}
{assign var=fca value=$fields[ix].options}
<table width="100%"><tr>{cycle name=2_$fca values=",</tr><tr>" advance=false print=false}
{foreach key=ku item=iu from=$fields[ix].$fca}
{assign var=fcat value=$iu.categId }
<td width="50%" nowrap="nowrap"><input type="checkbox" name="ins_cat_{$ku}[]" value="{$iu.categId}">{$iu.name}</td>{cycle name=2_$fca}
{/foreach}
</table>

{elseif $fields[ix].type eq 'i'}
<input type="file" name="{$fields[ix].ins_id}"/>

{elseif $fields[ix].type eq 't'}
{if $fields[ix].options_array[2]}<span class="formunit">{$fields[ix].options_array[2]}&nbsp;</span>{/if}
<input type="text" name="{$fields[ix].ins_id}" {if $fields[ix].options_array[1]}size="{$fields[ix].options_array[1]}" maxlength="{$fields[ix].options_array[1]}"{/if} value="{$defaultvalues.$fid|escape}" />
{if $fields[ix].options_array[3]}<span class="formunit">&nbsp;{$fields[ix].options_array[3]}</span>{/if}

{elseif $fields[ix].type eq 'n'}
{if $fields[ix].options_array[2]}<span class="formunit">{$fields[ix].options_array[2]}&nbsp;</span>{/if}
<input type="text" name="{$fields[ix].ins_id}" {if $fields[ix].options_array[1]}size="{$fields[ix].options_array[1]}" maxlength="{$fields[ix].options_array[1]}"{/if} value="{$defaultvalues.$fid|escape}" />
{if $fields[ix].options_array[3]}<span class="formunit">&nbsp;{$fields[ix].options_array[3]}</span>{/if}

{elseif $fields[ix].type eq 'a'}
<textarea id="{$fields[ix].ins_id}" name="{$fields[ix].ins_id}" cols="{if $fields[ix].options_array[1] gt 1}{$fields[ix].options_array[1]}{else}50{/if}" 
rows="{if $fields[ix].options_array[2] gt 1}{$fields[ix].options_array[2]}{else}4{/if}">{$defaultvalues.$fid|escape}</textarea>

{elseif $fields[ix].type eq 'f'}
{html_select_date prefix=$fields[ix].ins_id time=$fields[ix].value end_year="+1"} {tr}at{/tr} {html_select_time prefix=$fields[ix].ins_id time=$fields[ix].value display_seconds=false}

{elseif $fields[ix].type eq 'd'}
<select name="{$fields[ix].ins_id}">
{section name=jx loop=$fields[ix].options_array}
<option value="{$fields[ix].options_array[jx]|escape}" {if $defaultvalues.$fid eq $fields[ix].options_array[jx]}selected="selected"{/if}>{$fields[ix].options_array[jx]}</option>
{/section}
</select>

{elseif $fields[ix].type eq 'c'}
<input type="checkbox" name="{$fields[ix].ins_id}" {if $defaultvalues.$fid eq 'y'}checked="checked"{/if}/>

{elseif $fields[ix].type eq 'j'}
<input type="hidden" name="ins_{$fields[ix].ins_id}" value="" id="{$fields[ix].ins_id}" />
<span id="disp_{$fields[ix].ins_id}" class="daterow">{$fields[ix].value|default:$smarty.now|tiki_long_date}</span>
<script type="text/javascript">
{literal}Calendar.setup( { {/literal}
date        : "{$fields[ix].value|default:$now|date_format:"%B %e, %Y %H:%M"}",      // initial date
inputField  : "{$fields[ix].ins_id}",      // ID of the input field
ifFormat    : "%s",    // the date format
displayArea : "disp_{$fields[ix].ins_id}",       // ID of the span where the date is to be shown
daFormat    : "{$tiki_long_date}",  // format of the displayed date
showsTime   : true,
singleClick : true,
align       : "bR"
{literal} } );{/literal}
</script>

{elseif $fields[ix].type eq 'r'}
<select name="{$fields[ix].ins_id}">
{foreach key=id item=label from=$fields[ix].list}
<option value="{$label|escape}" {if $defaultvalue eq $label}selected="selected"{/if}>{$label}</option>
{/foreach}
</select>

{/if}

{if (($fields[ix].type eq 'c' or $fields[ix].type eq 't' or $fields[ix].type eq 'n') and $fields[ix].options_array[0]) eq '1' and $stick ne 'y'}
</td>{assign var=stick value="y"}
{else}
</td></tr>{assign var=stick value="n"}
{/if}
{/if}
{/section}
<tr class="formcolor"><td>&nbsp;</td><td colspan="3"><input type="submit" name="save" value="{tr}save{/tr}" /></td></tr>
</table>
</form>
</div>
{/if}


