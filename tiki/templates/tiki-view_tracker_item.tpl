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
<tr><td class="formcolor">{$ins_fields[ix].name}</td>
<td class="formcolor">
{$ins_fields[ix].value}
</td>
</tr>
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
<tr><td class="formcolor">{tr}Title{/tr}:</td><td class="formcolor"><input type="text" name="comment_title" value="{$comment_title|escape}"/></td></tr>
<tr><td class="formcolor">{tr}Comment{/tr}:</td><td class="formcolor"><textarea rows="4" cols="50" name="comment_data">{$comment_data|escape}</textarea></td></tr>
<tr><td class="formcolor">&nbsp;</td><td class="formcolor"><input type="submit" name="save_comment" value="{tr}save{/tr}" /></td></tr>
</table>
</form>
{/if}
<h3>{tr}Comments{/tr}</h3>
{section name=ix loop=$comments}
<b>{$comments[ix].title}</b> {if $comments[ix].user}{tr}by{/tr} {$comments[ix].user}{/if}
  {if $tiki_p_admin_trackers eq 'y'}[<a class="link" href="tiki-view_tracker_item.php?trackerId={$trackerId}&amp;itemId={$itemId}&amp;commentId={$comments[ix].commentId}">edit</a>|<a class="link" href="tiki-view_tracker_item.php?trackerId={$trackerId}&amp;itemId={$itemId}&amp;remove_comment={$comments[ix].commentId}">remove</a>]{/if}
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
<tr>
 <td class="formcolor">{tr}Upload file{/tr}:<input type="hidden" name="MAX_FILE_SIZE" value="1000000000" /><input name="userfile1" type="file" />
 {tr}comment{/tr}: <input type="text" name="attach_comment" maxlength="250" />
 <input type="submit" name="attach" value="{tr}attach{/tr}" />
 </td>
</tr>
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
{$atts[ix].$x}
</td>
{/foreach}
<td class="{cycle}" align="right" nowrap="nowrap">
{if $attextra eq 'y'}
<a class="tablename" href="#" onclick="javascript:window.open('tiki-view_tracker_more_info.php?attId={$atts[ix].attId}','_blank','menubar=no,toolbar=no,location=no,directories=no,fullscreen=no,titlebar=no,hotkeys=no,status=no,scrollbars=yes,resizable=yes,width=350,height=500');">more</a>
{/if}
<a class="tablename" href="tiki-download_item_attachment.php?attId={$atts[ix].attId}">{$atts[ix].filename|iconify}</a>
{if $tiki_p_wiki_admin_attachments eq 'y' or ($user and ($atts[ix].user eq $user))}
&nbsp;&nbsp;<a class="link" href="tiki-view_tracker_item.php?trackerId={$trackerId}&amp;itemId={$itemId}&amp;removeattach={$atts[ix].attId}&amp;offset={$offset}&amp;sort_mode={$sort_mode}">[x]</a>
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
<table class="normal">
<tr><td class="formcolor">{tr}Status{/tr}</td>
<td class="formcolor">
<select name="status">
<option value="o" {if $item_info.status eq 'o'}selected="selected"{/if}>{tr}open{/tr}</option>
<option value="c" {if $item_info.status eq 'c'}selected="selected"{/if}>{tr}closed{/tr}</option>
</select>
</td></tr>
{section name=ix loop=$ins_fields}
<tr><td class="formcolor">{$ins_fields[ix].name}</td>
<td class="formcolor">
{if $ins_fields[ix].type eq 'u'}
<select name="ins_{$ins_fields[ix].name}">
<option value="">{tr}None{/tr}</option>
{section name=ux loop=$users}
<option value="{$users[ux]|escape}" {if $ins_fields[ix].value eq $users[ux]}selected="selected"{/if}>{$users[ux]}</option>
{/section}
</select>
{/if}
{if $ins_fields[ix].type eq 'g'}
<select name="ins_{$ins_fields[ix].name}">
<option value="">{tr}None{/tr}</option>
{section name=ux loop=$groups}
<option value="{$groups[ux].groupName|escape}" {if $ins_fields[ix].value eq $groups[ux].groupName}selected="selected"{/if}>{$groups[ux].groupName}</option>
{/section}
</select>
{/if}
{if $ins_fields[ix].type eq 't'}
<input type="text" name="ins_{$ins_fields[ix].name}" value="{$ins_fields[ix].value|escape}" />
{/if}
{if $ins_fields[ix].type eq 'a'}
<textarea name="ins_{$ins_fields[ix].name}" rows="4" cols="50">{$ins_fields[ix].value|escape}</textarea>
{/if}
{if $ins_fields[ix].type eq 'f'}
{html_select_date prefix=$ins_fields[ix].ins_name time=$ins_fields[ix].value end_year="+1"} at {html_select_time prefix=$ins_fields[ix].ins_name time=$ins_fields[ix].value display_seconds=false}
{/if}
{if $ins_fields[ix].type eq 'd'}
<select name="ins_{$ins_fields[ix].name}">
{section name=jx loop=$ins_fields[ix].options_array}
<option value="{$ins_fields[ix].options_array[jx]|escape}" {if $ins_fields[ix].value eq $ins_fields[ix].options_array[jx]}selected="selected"{/if}>{$fields[ix].options_array[jx]}</option>
{/section}
</select>
{/if}
{if $ins_fields[ix].type eq 'c'}
<input type="checkbox" name="ins_{$ins_fields[ix].name}" {if $ins_fields[ix].value eq 'y'}checked="checked"{/if}/>
{/if}
</td>
</tr>
{/section}
<tr><td class="formcolor">&nbsp;</td><td class="formcolor"><input type="submit" name="save" value="{tr}save{/tr}" /></td></tr>
</table>
</form>
</div>
{/if}

<br/><br/>

