<a class="pagetitle" href="tiki-view_tracker_item.php?trackerId={$trackerId}&amp;itemId={$itemId}">{tr}Editing tracker item{/tr}</a><br/><br/>
<div>
<span class="button2"><a href="tiki-list_trackers.php" class="linkbut">{tr}List trackers{/tr}</a></span>
{if $tiki_p_admin_trackers eq 'y'}
<span class="button2"><a href="tiki-admin_trackers.php" class="linkbut">{tr}Admin trackers{/tr}</a></span>
<span class="button2"><a href="tiki-admin_trackers.php?trackerId={$trackerId}" class="linkbut">{tr}Edit this tracker{/tr}</a></span>
{if $user}
<span class="button2"><a href="tiki-view_tracker_item.php?itemId={$itemId}&amp;trackerId={$trackerId}&amp;monitor=1" class="linkbut">{tr}{$email_mon}{/tr}</a></span>
{/if}
{/if}
<span class="button2"><a href="tiki-view_tracker.php?trackerId={$trackerId}" class="linkbut">{tr}View this tracker items{/tr}</a></span>
</div>
<br/><br/>


{cycle name=tabs values="1,2,3,4,5" print=false advance=false}
<div class="tabs">
<span id="tab{cycle name=tabs}" class="tab tabActive">{tr}View{/tr}</span>
{if $tracker_info.useComments eq 'y'}
<span id="tab{cycle name=tabs}" class="tab">{tr}Comments{/tr}</span>
{/if}
{if $tracker_info.useAttachments eq 'y'}
<span id="tab{cycle name=tabs}" class="tab">{tr}Attachements{/tr}</span>
{/if}
{if $tiki_p_modify_tracker_items eq 'y'}
<span id="tab{cycle name=tabs}" class="tab">{tr}Edit{/tr}</span>
{/if}
</div>

{cycle name=content values="1,2,3,4,5" print=false advance=false}
{* --- tab with view --- *}
<div id="content{cycle name=content}" class="content">
<h3>{tr}View item{/tr}</h3>
<table class="normal">
{section name=ix loop=$ins_fields}
{if $ins_fields[ix].type eq 'h'}
</table>
<h3>{$ins_fields[ix].label}</h3>
<table class="normal">
{elseif $ins_fields[ix].type ne 'x'}
<tr class="formcolor"><td>{$ins_fields[ix].label}</td>
<td>
{if $ins_fields[ix].type eq 'f' or $ins_fields[ix].type eq 'j'}
{$ins_fields[ix].value|date_format:$daformat}
{elseif $ins_fields[ix].type eq 'a'}
{$ins_fields[ix].pvalue}
{else}
{$ins_fields[ix].value}
{/if}
</td>
</tr>
{/if}
{/section}
</table>
</div>

{* --- tab with comments --- *}
{if $tracker_info.useComments eq 'y'}
<div id="content{cycle name=content}" class="content">
{if $tiki_p_comment_tracker_items eq 'y'}
<h3>{tr}Add a comment{/tr}</h3>
<form action="tiki-view_tracker_item.php" method="post">
<input type="hidden" name="trackerId" value="{$trackerId|escape}" />
<input type="hidden" name="itemId" value="{$itemId|escape}" />
<input type="hidden" name="commentId" value="{$commentId|escape}" />
<table class="normal">
<tr class="formcolor"><td>{tr}Title{/tr}:</td><td><input type="text" name="comment_title" value="{$comment_title|escape}"/></td></tr>
<tr class="formcolor"><td>{tr}Comment{/tr}:</td><td><textarea rows="4" cols="50" name="comment_data">{$comment_data|escape}</textarea></td></tr>
<tr class="formcolor"><td>&nbsp;</td><td><input type="submit" name="save_comment" value="{tr}save{/tr}" /></td></tr>
</table>
</form>
{/if}
<h3>{tr}Comments{/tr}</h3>
{section name=ix loop=$comments}
<b>{$comments[ix].title}</b> {if $comments[ix].user}{tr}by{/tr} {$comments[ix].user}{/if}
  {if $tiki_p_admin_trackers eq 'y'}[<a class="link" href="tiki-view_tracker_item.php?trackerId={$trackerId}&amp;itemId={$itemId}&amp;commentId={$comments[ix].commentId}" title="{tr}Click here to edit this comment{/tr}"><img border="0" alt="{tr}Edit{/tr}" src="img/icons/edit.gif" /></a>|&nbsp;&nbsp;<a class="link" href="tiki-view_tracker_item.php?trackerId={$trackerId}&amp;itemId={$itemId}&amp;remove_comment={$comments[ix].commentId}" 
onclick="return confirmTheLink(this,'{tr}Are you sure you want to delete this comment?{/tr}')" 
title="{tr}Click here to delete this comment{/tr}"><img border="0" alt="{tr}Remove{/tr}" src="img/icons2/delete.gif" /></a>&nbsp;&nbsp;]{/if}
<br/>
<small>{tr}posted on{/tr}: {$comments[ix].posted|tiki_short_datetime}</small><br/>
{$comments[ix].parsed}
<hr/>
{/section}
</div>
{/if}

{* --- tab with attachements --- *}
{if $tracker_info.useAttachments eq 'y'}
<div id="content{cycle name=content}" class="content">
{if $tiki_p_attach_trackers eq 'y'}
<h3>{tr}Attach a file to this item{/tr}</h3>
<form enctype="multipart/form-data" action="tiki-view_tracker_item.php" method="post">
<input type="hidden" name="trackerId" value="{$trackerId|escape}" />
<input type="hidden" name="itemId" value="{$itemId|escape}" />
<input type="hidden" name="commentId" value="{$commentId|escape}" />
<table class="normal">
<tr class="formcolor"><td>{tr}Upload file{/tr}</td><td><input type="hidden" name="MAX_FILE_SIZE" value="1000000000" /><input name="userfile1" type="file" /></td></tr>
<tr class="formcolor"><td>{tr}comment{/tr}</td><td><input type="text" name="attach_comment" maxlength="250" /></td></tr>
<tr class="formcolor"><td>{tr}version{/tr}</td><td><input type="text" name="attach_version" size="5" maxlength="10" /></td></tr>
<tr class="formcolor"><td>{tr}description{/tr}</td><td><textarea name="attach_longdesc" style="width:100%;" rows="10"></textarea></td></tr>

<tr class="formcolor"><td></td><td><input type="submit" name="attach" value="{tr}attach{/tr}" /></td></tr>
</table>
</form>
{/if}
<h3>{tr}Attachments{/tr}</h3>
<table class="normal">

<tr> 
{section name=ix loop=$attfields}
<td class="heading">{tr}{$attfields[ix]}{/tr}</td>
{/section}
<td class="heading">{tr}action{/tr}</td>
</tr>
{cycle values="odd,even" print=false}
{section name=ix loop=$atts}
<tr>
{foreach key=k item=x from=$attfields}
<td class="{cycle advance=false}">
{if $x eq 'created'}
{$atts[ix].$x|tiki_short_datetime}
{else}
{$atts[ix].$x}
{/if}
</td>
{/foreach}
<td class="{cycle}" align="right" nowrap="nowrap">
{if $attextra eq 'y'}
{assign var=link value='tiki-view_tracker_more_info.php?attId='|cat:$atts[ix].attId}
<a class="tablename" href="#" onClick="window.open('http://{$http_domain}{$http_prefix}{$link|escape:"javascript"}','newin','menubar=no,toolbar=no,location=no,directories=no,status=no,scrollbars=yes,resizable=yes,width=450,height=600');return true;">{tr}More Info{/tr}</a>
{/if}
<a class="tablename" href="tiki-download_item_attachment.php?attId={$atts[ix].attId}">{tr}Download{/tr}</a>
{if $tiki_p_wiki_admin_attachments eq 'y' or ($user and ($atts[ix].user eq $user))}
&nbsp;&nbsp;<a class="link" href="tiki-view_tracker_item.php?trackerId={$trackerId}&amp;itemId={$itemId}&amp;removeattach={$atts[ix].attId}&amp;offset={$offset}&amp;sort_mode={$sort_mode}">[{tr}erase{/tr}]</a>
{/if}
</td>
</tr>
{sectionelse}
<tr>
 <td colspan="5">{tr}No attachments for this item{/tr}</td>
</tr>
{/section}
</table>
</div>
{/if}

{* --- tab with edit --- *}
{if $tiki_p_modify_tracker_items eq 'y'}
<div id="content{cycle name=content}" class="content">
<h3>{tr}Edit item{/tr}</h3>
<form action="tiki-view_tracker_item.php" method="post">
<input type="hidden" name="trackerId" value="{$trackerId|escape}" />
<input type="hidden" name="itemId" value="{$itemId|escape}" />
{section name=ix loop=$fields}
<input type="hidden" name="{$fields[ix].name|escape}" value="{$fields[ix].value|escape}" />
{/section}
<table>
<tr><td>{tr}Status{/tr}</td>
<td>
<select name="status">
<option value="o" {if $item_info.status eq 'o'}selected="selected"{/if}>{tr}open{/tr}</option>
<option value="c" {if $item_info.status eq 'c'}selected="selected"{/if}>{tr}closed{/tr}</option>
</select>
</td></tr>
</table>
<table class="normal">
{section name=ix loop=$ins_fields}

{if $ins_fields[ix].type ne 'x'}
{if $ins_fields[ix].type eq 'h'}
</table>
<h3>{$ins_fields[ix].label}</h3>
<table class="normal">
{else}
<tr class="formcolor"><td>{$ins_fields[ix].label}</td><td>
{/if}

{if $ins_fields[ix].type eq 'u'}
<select name="ins_{$ins_fields[ix].name}">
<option value="">{tr}None{/tr}</option>
{section name=ux loop=$users}
<option value="{$users[ux]|escape}" {if $ins_fields[ix].value eq $users[ux]}selected="selected"{/if}>{$users[ux]}</option>
{/section}
</select>

{elseif $ins_fields[ix].type eq 'g'}
<select name="ins_{$ins_fields[ix].name}">
<option value="">{tr}None{/tr}</option>
{section name=ux loop=$groups}
<option value="{$groups[ux].groupName|escape}" {if $ins_fields[ix].value eq $groups[ux].groupName}selected="selected"{/if}>{$groups[ux].groupName}</option>
{/section}
</select>

{elseif $ins_fields[ix].type eq 't'}
<input type="text" name="ins_{$ins_fields[ix].name}" value="{$ins_fields[ix].value|escape}" />

{elseif $ins_fields[ix].type eq 'a'}
<textarea name="ins_{$ins_fields[ix].name}" rows="4" cols="50">{$ins_fields[ix].value|escape}</textarea>

{elseif $ins_fields[ix].type eq 'f'}
{html_select_date prefix=$ins_fields[ix].ins_name time=$ins_fields[ix].value end_year="+1"} at {html_select_time prefix=$ins_fields[ix].ins_name time=$ins_fields[ix].value display_seconds=false}

{elseif $ins_fields[ix].type eq 'd'}
<select name="ins_{$ins_fields[ix].name}">
{section name=jx loop=$ins_fields[ix].options_array}
<option value="{$ins_fields[ix].options_array[jx]|escape}" {if $ins_fields[ix].value eq $ins_fields[ix].options_array[jx]}selected="selected"{/if}>{$fields[ix].options_array[jx]}</option>
{/section}
</select>

{elseif $ins_fields[ix].type eq 'c'}
<input type="checkbox" name="ins_{$ins_fields[ix].name}" {if $ins_fields[ix].value eq 'y'}checked="checked"{/if}/>

{elseif $ins_fields[ix].type eq 'j'}
<input type="hidden" name="ins_{$ins_fields[ix].name}" value="{$ins_fields[ix].value|default:$smarty.now}" id="ins_{$ins_fields[ix].name}" />
<span id="disp_{$ins_fields[ix].name}" class="daterow">{$ins_fields[ix].value|default:$smarty.now|date_format:$daformat}</span>
<script type="text/javascript">
{literal}Calendar.setup( { {/literal}
date        : "{$ins_fields[ix].value|default:$smarty.now|date_format:"%B %e, %Y %H:%M"}",      // initial date
inputField  : "ins_{$ins_fields[ix].name}",      // ID of the input field
ifFormat    : "%s",    // the date format
displayArea : "disp_{$ins_fields[ix].name}",       // ID of the span where the date is to be shown
daFormat    : "{$daformat}",  // format of the displayed date
showsTime   : true,
singleClick : true,
align       : "bR"
{literal} } );{/literal}
</script>
{/if}

</td></tr>

{else}

{capture name=trkaction}
{if $ins_fields[ix].options_array[1] eq 'post'}
<form action="{$ins_fields[ix].options_array[2]}" method="post" target="_blank">
{else}
<form action="{$ins_fields[ix].options_array[2]}" method="get" target="_blank">
{/if}
<table class="normal">
{section name=tl loop=$ins_fields[ix].options_array start=3}
{assign var=valvar value=$ins_fields[ix].options_array[tl]|regex_replace:"/^[^:]*:/":""|escape}
{if $item_info.$valvar eq ''}
{assign var=valvar value=$ins_fields[ix].options_array[tl]|regex_replace:"/^[^\=]*\=/":""|escape}
<input type="hidden" name="{$ins_fields[ix].options_array[tl]|regex_replace:"/\=.*$/":""|escape}" value="{$valvar|escape}" />
{else}
<input type="hidden" name="{$ins_fields[ix].options_array[tl]|regex_replace:"/:.*$/":""|escape}" value="{$item_info.$valvar|escape}" />
{/if}
{/section}
<tr class="formcolor"><td>{$ins_fields[ix].label}</td><td><input type="submit" class="submit" name="trck_act" value="{$ins_fields[ix].options_array[0]|escape}" /></td><tr>
</table>
</form>
{/capture}
{assign var=trkact value=$trkact|cat:$smarty.capture.trkaction}
{/if}

{/section}
<tr class="formcolor"><td>&nbsp;</td><td><input type="submit" name="save" value="{tr}save{/tr}" />
</td></tr>
</table>
</form>
{if $trkact}
<h3>{tr}Special Operations{/tr}</h3>
{$trkact}
{/if}
</div>
{/if}

<br/><br/>

