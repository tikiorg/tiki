<a class="pagetitle" href="tiki-view_tracker_item.php?trackerId={$trackerId}&amp;itemId={$itemId}">{tr}Editing tracker item{/tr} {$tracker_info.name}</a>
<br /><br />
<div>
<span class="button2"><a href="tiki-list_trackers.php" class="linkbut">{tr}List trackers{/tr}</a></span>
<span class="button2"><a href="tiki-view_tracker.php?trackerId={$trackerId}" class="linkbut">{tr}View this tracker items{/tr}</a></span>
{if $user}
<span class="button2"><a href="tiki-view_tracker_item.php?itemId={$itemId}&amp;trackerId={$trackerId}&amp;monitor=1" class="linkbut">{tr}{$email_mon}{/tr}</a></span>
{/if}
{if $tiki_p_admin_trackers eq 'y'}
&nbsp;&nbsp;
<span class="button2"><a href="tiki-admin_trackers.php" class="linkbut">{tr}Admin trackers{/tr}</a></span>
<span class="button2"><a href="tiki-admin_trackers.php?trackerId={$trackerId}" class="linkbut">{tr}Edit this tracker{/tr}</a></span>
<span class="button2"><a href="tiki-admin_tracker_fields.php?trackerId={$trackerId}" class="linkbut">{tr}Edit fields{/tr}</a></span>
{/if}
</div>
<br /><br />

{if $feature_tabs eq 'y'}
{cycle name=tabs values="1,2,3,4,5" print=false advance=false}
<div id="page-bar">
<span id="tab{cycle name=tabs advance=false}" class="button3"><a href="javascript:tikitabs({cycle name=tabs},5);" class="linkbut">{tr}View{/tr}</a></span>
{if $tracker_info.useComments eq 'y'}
<span id="tab{cycle name=tabs advance=false}" class="button3"><a href="javascript:tikitabs({cycle name=tabs},5);" class="linkbut">{tr}Comments{/tr}</a></span>
{/if}
{if $tracker_info.useAttachments eq 'y'}
<span id="tab{cycle name=tabs advance=false}" class="button3"><a href="javascript:tikitabs({cycle name=tabs},5);" class="linkbut">{tr}Attachments{/tr}</a></span>
{/if}
{if $tiki_p_modify_tracker_items eq 'y'}
<span id="tab{cycle name=tabs advance=false}" class="button3"><a href="javascript:tikitabs({cycle name=tabs},5);" class="linkbut">{tr}Edit{/tr}</a></span>
{/if}
</div>
{/if}

{cycle name=content values="1,2,3,4,5" print=false advance=false}
{* --- tab with view --- *}
<div id="content{cycle name=content assign=focustab}{$focustab}" class="wikitext"{if $feature_tabs eq 'y'} style="display:{if $focustab eq $smarty.cookies.tab}block{else}none{/if};"{/if}>
<h3>{tr}View item{/tr}</h3>
<table class="normal">
{if $tracker_info.showStatus eq 'y' and ($tracker_info.showStatusAdminOnly ne 'y' or $tiki_p_admin_trackers eq 'y')}
{assign var=ustatus value=$info.status|default:"p"}
<tr class="formcolor"><td>{tr}Status{/tr}</td><td>{$status_types.$ustatus.label}</td>
<td colspan="2">{html_image file=$status_types.$ustatus.image title=$status_types.$ustatus.label alt=$status_types.$ustatus.label}</td></tr>
{/if}
{section name=ix loop=$ins_fields}
{if $ins_fields[ix].isPublic eq 'y' or $tiki_p_admin_trackers eq 'y'}
{if $ins_fields[ix].type eq 'h'}
</table>
<h3>{$ins_fields[ix].name}</h3>
<table class="normal">

{elseif $ins_fields[ix].type ne 'x'}
{if ($ins_fields[ix].type eq 'c' or $fields[ix].type eq 't' or $fields[ix].type eq 'n') and $fields[ix].options_array[0] eq '1'}
<tr class="formcolor"><td class="formlabel">{$ins_fields[ix].name}</td><td>
{elseif $stick eq 'y'}
<td class="formlabel right">{$ins_fields[ix].name}</td><td>
{else}
<tr class="formcolor"><td>{$ins_fields[ix].name}</td>
<td colspan="3">
{/if}
{if $ins_fields[ix].type eq 'f' or $ins_fields[ix].type eq 'j'}
{$ins_fields[ix].value|tiki_long_date}</td></tr>

{elseif $ins_fields[ix].type eq 'l'}
{foreach key=tid item=tlabel from=$ins_fields[ix].links}
<div><a href="tiki-view_tracker_item.php?trackerId={$ins_fields[ix].trackerId}&amp;itemId={$tid}" class="link">{$tlabel}</a></div>
{/foreach}

{elseif $ins_fields[ix].type eq 'u'}
<a href="tiki-user_information.php?view_user={$ins_fields[ix].value|escape:"url"}">{$ins_fields[ix].value}</a>

{elseif $ins_fields[ix].type eq 'a'}
{$ins_fields[ix].pvalue|default:"&nbsp;"}

{elseif $ins_fields[ix].type eq 'e'}
{assign var=fca value=$fields[ix].options}
<table width="100%"><tr>{cycle name=$fca values=",</tr><tr>" advance=false print=false}
{foreach key=ku item=iu from=$fields[ix].$fca}
{assign var=fcat value=$iu.categId }
<td width="50%" nowrap="nowrap">
{if $ins_fields[ix].cat.$fcat eq 'y'}
<tt>X&nbsp;</tt><b>{$iu.name}</b></td>
{else}
<tt>&nbsp;&nbsp;</tt><s>{$iu.name}</s></td>
{/if}
{cycle name=$fca}
{/foreach}
</tr></table></td></tr>

{elseif $ins_fields[ix].type eq 'c'}
{if $ins_fields[ix].value eq 'y'}{tr}Yes{/tr}
{else}{tr}No{/tr}
{/if}
{if $ins_fields[ix].options_array[0] eq '1' and $stick ne 'y'}
</td>
{assign var=stick value="y"}
{else}
</td></tr>
{assign var=stick value="n"}
{/if}

{elseif $ins_fields[ix].type eq 't' or $ins_fields[ix].type eq 'r' or $ins_fields[ix].type eq 'n'}
{if $ins_fields[ix].options_array[2]}<span class="formunit">{$ins_fields[ix].options_array[2]|escape}&nbsp;</span>{/if}
{if $ins_fields[ix].linkId}
<a href="tiki-view_tracker_item.php?trackerId={$ins_fields[ix].options_array[0]}&amp;itemId={$ins_fields[ix].linkId}" class="link">{$ins_fields[ix].value|default:"&nbsp;"}</a>
{else}
{$ins_fields[ix].value|default:"&nbsp;"}
{/if}
{if $ins_fields[ix].options_array[3]}<span class="formunit">&nbsp;{$ins_fields[ix].options_array[3]|escape}</span>{/if}

{if $ins_fields[ix].options_array[0] eq '1' and $stick ne 'y'}
</td>
{assign var=stick value="y"}
{else}
</td></tr>
{assign var=stick value="n"}
{/if}

{else}
{$ins_fields[ix].value|default:"&nbsp;"}
</td></tr>
{assign var=stick value="n"}
{/if}
{/if}
{/if}
{/section}
</table>
</div>

{* -------------------------------------------------- tab with comments --- *}
{if $tracker_info.useComments eq 'y'}
<div id="content{cycle name=content assign=focustab}{$focustab}" class="wikitext"{if $feature_tabs eq 'y'} style="display:{if $focustab eq $smarty.cookies.tab}block{else}none{/if};"{/if}>
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
  {if $tiki_p_admin_trackers eq 'y'}[<a class="link" href="tiki-view_tracker_item.php?trackerId={$trackerId}&amp;itemId={$itemId}&amp;commentId={$comments[ix].commentId}" title="{tr}edit{/tr}"><img border="0" alt="{tr}edit{/tr}" src="img/icons/edit.gif" /></a>|&nbsp;&nbsp;<a class="link" href="tiki-view_tracker_item.php?trackerId={$trackerId}&amp;itemId={$itemId}&amp;remove_comment={$comments[ix].commentId}" 
title="{tr}delete{/tr}"><img border="0" alt="{tr}delete{/tr}" src="img/icons2/delete.gif" /></a>&nbsp;&nbsp;]{/if}
<br />
<small>{tr}posted on{/tr}: {$comments[ix].posted|tiki_short_datetime}</small><br />
{$comments[ix].parsed}
<hr/>
{/section}
</div>
{/if}

{* ---------------------------------------- tab with attachements --- *}
{if $tracker_info.useAttachments eq 'y'}
<div id="content{cycle name=content assign=focustab}{$focustab}" class="wikitext"{if $feature_tabs eq 'y'} style="display:{if $focustab eq $smarty.cookies.tab}block{else}none{/if};" {/if}>
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
<td class="heading auto">&nbsp;</td>
{section name=ix loop=$attfields}
<td class="heading auto">{tr}{$attfields[ix]}{/tr}</td>
{/section}
<td class="heading">&nbsp;</td>
</tr>
{cycle values="odd,even" print=false}
{section name=ix loop=$atts}
<tr class="{cycle}">
<td nowrap="nowrap" class="auto">
{if $attextra eq 'y'}
{assign var=link value='tiki-view_tracker_more_info.php?attId='|cat:$atts[ix].attId}
<a class="tablename" href="#" title="{tr}more info{/tr}"
onClick="javascript:window.open('{$link}','','menubar=no,toolbar=no,location=no,directories=no,status=no,scrollbars=yes,resizable=yes,width=450,height=600');"><img src="img/icons/question.gif" border="0" alt="{tr}question{/tr}"  hspace="2" vspace="1" /></a>{/if}<a 
class="tablename" href="tiki-download_item_attachment.php?attId={$atts[ix].attId}" title="{tr}download{/tr}"><img src="img/icons/icon38.gif" border="0" alt="{tr}download{/tr}" hspace="8" vspace="O" /></a>
</td>
{foreach key=k item=x from=$attfields}
{if $x eq 'created'}
<td>{$atts[ix].$x|tiki_short_datetime}</td>
{elseif $x eq 'filesize'}
<td nowrap="nowrap">{$atts[ix].$x|kbsize}</td>
{elseif $x eq 'filetype'}
<td>{$atts[ix].$x|iconify}</td>
{else}
<td>{$atts[ix].$x}</td>
{/if}
{/foreach}
<td>
{if $tiki_p_wiki_admin_attachments eq 'y' or ($user and ($atts[ix].user eq $user))}
<a class="link" href="tiki-view_tracker_item.php?trackerId={$trackerId}&amp;itemId={$itemId}&amp;removeattach={$atts[ix].attId}&amp;offset={$offset}&amp;sort_mode={$sort_mode}" title="{tr}delete{/tr}"><img
src="img/icons2/delete.gif" border="0" alt="{tr}delete{/tr}"  hspace="2" vspace="0" /></a>
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

{* --------------------------------------------------------------- tab with edit --- *}
{if $tiki_p_modify_tracker_items eq 'y'}
<div id="content{cycle name=content assign=focustab}{$focustab}" class="wikitext"{if $feature_tabs eq 'y'} style="display:{if $focustab eq $smarty.cookies.tab}block{else}none{/if};"{/if}>
<h3>{tr}Edit item{/tr}</h3>
<form action="tiki-view_tracker_item.php" method="post">
<input type="hidden" name="trackerId" value="{$trackerId|escape}" />
<input type="hidden" name="itemId" value="{$itemId|escape}" />
{section name=ix loop=$fields}
<input type="hidden" name="{$fields[ix].id|escape}" value="{$fields[ix].value|escape}" />
{/section}

<table class="normal">
<tr class="formcolor"><td>&nbsp;</td><td colspan="3"><input type="submit" name="save" value="{tr}save{/tr}" />
{if $tracker_info.showStatus eq 'y' or $tiki_p_admin_trackers eq 'y'}
<tr class="formcolor"><td>{tr}Status{/tr}</td>
<td>
<select name="status">
{foreach key=st item=stdata from=$status_types}
<option value="{$st}"{if $item_info.status eq $st} selected="selected"{/if} 
style="background-image:url('{$stdata.image}');background-repeat:no-repeat;padding-left:17px;">{$stdata.label}</option>
{/foreach}
</select>
</td></tr>
{/if}

{section name=ix loop=$ins_fields}
{if $ins_fields[ix].isPublic eq 'y' or $tiki_p_admin_trackers eq 'y'}
{if $ins_fields[ix].type ne 'x'}
{if $ins_fields[ix].type eq 'h'}
</table>
<h3>{$ins_fields[ix].name}</h3>
<table class="normal">
{else}
{if ($ins_fields[ix].type eq 'c' or $fields[ix].type eq 't' or $fields[ix].type eq 'n') and $fields[ix].options_array[0] eq '1'}
<tr class="formcolor"><td class="formlabel">{$ins_fields[ix].name}</td><td nowrap="nowrap">
{elseif $stick eq 'y'}
<td class="formlabel right">{$ins_fields[ix].name}</td><td nowrap="nowrap">
{else}
<tr class="formcolor"><td class="formlabel">{$ins_fields[ix].name}
{if $ins_fields[ix].type eq 'a' and $ins_fields[ix].options_array[0] eq 1}
<br />
{include file=tiki-edit_help_tool.tpl qtnum=$ins_fields[ix].id area_name="area_"|cat:$ins_fields[ix].id}
{/if}
</td><td colspan="3" nowrap="nowrap">
{/if}
{/if}

{if $ins_fields[ix].type eq 'u'}
{if !$fields[ix].options or $tiki_p_admin_trackers eq 'y'}
<select name="ins_{$ins_fields[ix].id}">
<option value="">{tr}None{/tr}</option>
{foreach key=id item=one from=$users}
<option value="{$one|escape}" {if $ins_fields[ix].value eq $one}selected="selected"{/if}>{$one}</option>
{/foreach}
</select>
{elseif $ins_fields[ix].options}
<a href="tiki-user_information.php?user={$ins_fields[ix].value|escape:"url"}" class="link">{$ins_fields[ix].value}</a>
{/if}

{elseif $ins_fields[ix].type eq 'g'}
{if !$fields[ix].options or $tiki_p_admin_trackers eq 'y'}
<select name="ins_{$ins_fields[ix].id}">
<option value="">{tr}None{/tr}</option>
{section name=ux loop=$groups}
<option value="{$groups[ux]|escape}" {if $ins_fields[ix].value|default:$ins_fields[ix].pvalue eq $groups[ux]}selected="selected"{/if}>{$groups[ux]}</option>
{/section}
</select>
{elseif $ins_fields[ix].options}
{$ins_fields[ix].value}
{/if}

{elseif $ins_fields[ix].type eq 'l'}
{foreach key=tid item=tlabel from=$ins_fields[ix].links}
<div><a href="tiki-view_tracker_item.php?trackerId={$ins_fields[ix].trackerId}&amp;itemId={$tid}" class="link">{$tlabel}</a></div>
{/foreach}

{elseif $ins_fields[ix].type eq 'e'}
{assign var=fca value=$ins_fields[ix].options}
<table width="100%"><tr>{cycle name="2_$fca" values=",</tr><tr>" advance=false print=false}
{foreach key=ku item=iu from=$fields[ix].$fca}
{assign var=fcat value=$iu.categId }
<td width="50%" nowrap="nowrap"><input type="checkbox" name="ins_cat_{$fca}[]" value="{$fcat}" {if $ins_fields[ix].cat.$fcat eq 'y'}checked="checked"{/if}/>{$iu.name}</td>{cycle name="2_$fca"}
{/foreach}
</table>

{elseif $ins_fields[ix].type eq 't'}
{if $ins_fields[ix].options_array[2]}<span class="formunit">{$ins_fields[ix].options_array[2]}&nbsp;</span>{/if}
<input type="text" name="ins_{$ins_fields[ix].id}" value="{$ins_fields[ix].value|escape}" {if $ins_fields[ix].options_array[1]}size="{$ins_fields[ix].options_array[1]}" maxlength="{$ins_fields[ix].options_array[1]}"{/if} />
{if $ins_fields[ix].options_array[3]}<span class="formunit">&nbsp;{$ins_fields[ix].options_array[3]}</span>{/if}

{elseif $ins_fields[ix].type eq 'n'}
{if $ins_fields[ix].options_array[2]}<span class="formunit">{$ins_fields[ix].options_array[2]}&nbsp;</span>{/if}
<input type="text" name="ins_{$ins_fields[ix].id}" value="{$ins_fields[ix].value|escape}" {if $ins_fields[ix].options_array[1]}size="{$ins_fields[ix].options_array[1]}" maxlength="{$ins_fields[ix].options_array[1]}"{/if} />
{if $ins_fields[ix].options_array[3]}<span class="formunit">&nbsp;{$ins_fields[ix].options_array[3]}</span>{/if}

{elseif $ins_fields[ix].type eq 'a'}
<textarea name="ins_{$ins_fields[ix].id}" id="area_{$ins_fields[ix].id}" cols="{if $fields[ix].options_array[1] gt 1}{$fields[ix].options_array[1]}{else}50{/if}" 
rows="{if $fields[ix].options_array[2] gt 1}{$fields[ix].options_array[2]}{else}4{/if}">{$ins_fields[ix].value|escape}</textarea>

{elseif $ins_fields[ix].type eq 'f'}
{html_select_date prefix="ins_"|cat:$ins_fields[ix].id time=$ins_fields[ix].value start_year="-4" end_year="+4"} {html_select_time prefix="ins_"|cat:$ins_fields[ix].id time=$ins_fields[ix].value display_seconds=false}

{elseif $ins_fields[ix].type eq 'r'}
<select name="ins_{$ins_fields[ix].id}">
{foreach key=id item=label from=$ins_fields[ix].list}
<option value="{$label|escape}" {if $ins_fields[ix].value eq $label}selected="selected"{/if}>{$label}</option>
{/foreach}
</select>

{elseif $ins_fields[ix].type eq 'd'}
<select name="ins_{$ins_fields[ix].id}">
{section name=jx loop=$ins_fields[ix].options_array}
<option value="{$ins_fields[ix].options_array[jx]|escape}" {if $ins_fields[ix].value eq $ins_fields[ix].options_array[jx]}selected="selected"{/if}>{$fields[ix].options_array[jx]}</option>
{/section}
</select>

{elseif $ins_fields[ix].type eq 'c'}
<input type="checkbox" name="ins_{$ins_fields[ix].id}" {if $ins_fields[ix].value eq 'y'}checked="checked"{/if}/>

{elseif $ins_fields[ix].type eq 'j'}
<input type="hidden" name="ins_{$ins_fields[ix].id}" value="{$ins_fields[ix].value|default:$smarty.now}" id="ins_{$ins_fields[ix].id}" />
<span id="disp_{$ins_fields[ix].id}" class="daterow">{$ins_fields[ix].value|default:$smarty.now|tiki_long_datetime}</span>
<script type="text/javascript">
{literal}Calendar.setup( { {/literal}
date        : "{$ins_fields[ix].value|default:$smarty.now|date_format:"%B %e, %Y %H:%M"}",      // initial date
inputField  : "ins_{$ins_fields[ix].id}",      // ID of the input field
ifFormat    : "%s",    // the date format
displayArea : "disp_{$ins_fields[ix].id}",       // ID of the span where the date is to be shown
daFormat    : "{$long_date_format}",  // format of the displayed date
showsTime   : true,
singleClick : true,
align       : "bR"
{literal} } );{/literal}
</script>
{/if}
{if (($ins_fields[ix].type eq 'c' or $fields[ix].type eq 't' or $fields[ix].type eq 'n') and $fields[ix].options_array[0] eq '1')
 and $stick ne 'y'}
</td>{assign var=stick value="y"}
{else}
</td></tr>{assign var=stick value="n"}
{/if}

{else}

{capture name=trkaction}
{if $ins_fields[ix].options_array[1] eq 'post'}
<form action="{$ins_fields[ix].options_array[2]}" method="post">
{else}
<form action="{$ins_fields[ix].options_array[2]}" method="get">
{/if}
{section name=tl loop=$ins_fields[ix].options_array start=3}
{assign var=valvar value=$ins_fields[ix].options_array[tl]|regex_replace:"/^[^:]*:/":""|escape}
{if $info.$valvar eq ''}
{assign var=valvar value=$ins_fields[ix].options_array[tl]|regex_replace:"/^[^\=]*\=/":""|escape}
<input type="hidden" name="{$ins_fields[ix].options_array[tl]|regex_replace:"/\=.*$/":""|escape}" value="{$valvar|escape}" />
{else}
<input type="hidden" name="{$ins_fields[ix].options_array[tl]|regex_replace:"/:.*$/":""|escape}" value="{$info.$valvar|escape}" />
{/if}
{/section}
<table class="normal">
<tr class="formcolor"><td>{$ins_fields[ix].name}</td><td><input type="submit" class="submit" name="trck_act" value="{$ins_fields[ix].options_array[0]|escape}" /></td><tr>
</table>
</form>
{/capture}
{assign var=trkact value=$trkact|cat:$smarty.capture.trkaction}
{/if}

{/if}
{/section}
<tr class="formcolor"><td>&nbsp;</td><td colspan="3"><input type="submit" name="save" value="{tr}save{/tr}" />
</td></tr>
</table>
</form>
{if $trkact}
<h3>{tr}Special Operations{/tr}</h3>
{$trkact}
{/if}
</div>
{/if}

<br /><br />

