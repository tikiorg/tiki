{* $Header: /cvsroot/tikiwiki/tiki/templates/tiki-view_tracker_item.tpl,v 1.155.2.17 2007-12-03 19:40:20 nyloth Exp $ *}
<script language="JavaScript" type="text/javascript" src="lib/trackers/dynamic_list.js"></script>
<h1><a class="pagetitle" href="tiki-view_tracker_item.php?trackerId={$trackerId}&amp;itemId={$itemId}">{tr}Tracker item:{/tr} {$tracker_info.name}</a></h1>

{* --------- navigation ------ *}
<div class="navbar">
{if $prefs.feature_user_watches eq 'y' and $tiki_p_watch_trackers eq 'y'}
{if $user_watching_tracker ne 'y'}
<a href="tiki-view_tracker_item.php?trackerId={$trackerId}&amp;itemId={$itemId}&amp;watch=add" title="{tr}Monitor{/tr}"><img src="pics/icons/eye.png" width="16" height="16" border="0" align="right" hspace="5" alt="{tr}Monitor{/tr}" /></a>
{else}
<a href="tiki-view_tracker_item.php?trackerId={$trackerId}&amp;itemId={$itemId}&amp;watch=stop" title="{tr}Stop Monitor{/tr}"><img src="pics/icons/no_eye.png" width="16" height="16" border="0" align="right" hspace="5" alt="{tr}Stop Monitor{/tr}" /></a>
{/if}
{/if}
<span class="button2"><a href="tiki-view_tracker.php?trackerId={$trackerId}&amp;offset={$offset}&amp;sort_mode={$sort_mode}{foreach key=urlkey item=urlval from=$urlquery}&amp;{$urlkey}={$urlval|escape:"url"}{/foreach}" class="linkbut">{tr}items list{/tr}</a></span>
{if (isset($tiki_p_list_trackers) and $tiki_p_list_trackers eq 'y' or (!isset($tiki_p_list_trackers) and $tiki_p_view_trackers eq 'y'))}<span class="button2"><a href="tiki-list_trackers.php" class="linkbut">{tr}List trackers{/tr}</a></span>
{/if}
{if $tiki_p_view_trackers eq 'y'}
<span class="button2"><a href="tiki-view_tracker.php?trackerId={$trackerId}" class="linkbut">{tr}View this tracker items{/tr}</a></span>
{/if}
{if $tiki_p_admin_trackers eq 'y'}
&nbsp;&nbsp;
<span class="button2"><a href="tiki-admin_trackers.php" class="linkbut">{tr}Admin trackers{/tr}</a></span>
<span class="button2"><a href="tiki-admin_trackers.php?trackerId={$trackerId}" class="linkbut">{tr}Edit this tracker{/tr}</a></span>
<span class="button2"><a href="tiki-admin_tracker_fields.php?trackerId={$trackerId}" class="linkbut">{tr}Edit fields{/tr}</a></span>
{/if}
</div>

<div class="navbar" align="right">
	{if $user and $prefs.feature_user_watches eq 'y'}
		{if $category_watched eq 'y'}
			{tr}Watched by categories{/tr}:
			{section name=i loop=$watching_categories}
				<a href="tiki-browse_categories?parentId={$watching_categories[i].categId}">{$watching_categories[i].name}</a>&nbsp;
			{/section}
		{/if}	
	{/if}
</div>

{* ------- return/next/previous tab --- *}
{if $cant > 0 && $tiki_p_view_trackers eq 'y'}
  <div class="mini">
    {if $show_prev_link eq 'y'}
      [<a class="prevnext" {ajax_href template="tiki-view_tracker_item.tpl" htmlelement="tiki-center"}{$smarty.server.PHP_SELF}?trackerId={$trackerId}&amp;itemId={$itemId}&amp;offset={$offset}&amp;sort_mode={$sort_mode}{foreach key=urlkey item=urlval from=$urlquery}&amp;{$urlkey}={$urlval|escape:"url"}{/foreach}&amp;move=prev{/ajax_href}>{tr}Prev{/tr}</a>]
    {/if}
    
    {tr}Item{/tr}: {math equation="x + y + 1" x=$offset|default:0 y=$urlquery.reloff|default:0}/{$cant}
    
    {if $show_next_link eq 'y'}
      [<a class="prevnext" {ajax_href template="tiki-view_tracker_item.tpl" htmlelement="tiki-center"}{$smarty.server.PHP_SELF}?trackerId={$trackerId}&amp;itemId={$itemId}&amp;offset={$offset}&amp;sort_mode={$sort_mode}{foreach key=urlkey item=urlval from=$urlquery}&amp;{$urlkey}={$urlval|escape:"url"}{/foreach}&amp;move=next{/ajax_href}>{tr}Next{/tr}</a>]
    {/if}

    {if $prefs.direct_pagination eq 'y'}
      <br />
      {section loop=$cant name=foo}
          <a class="prevnext" {ajax_href template="tiki-view_tracker_item.tpl" htmlelement="tiki-center"}{$smarty.server.PHP_SELF}?trackerId={$trackerId}&amp;itemId={$itemId}&amp;offset={$offset}&amp;sort_mode={$sort_mode}{foreach key=urlkey item=urlval from=$urlquery}&amp;{$urlkey}={$urlval|escape:"url"}&amp;move={$smarty.section.foo.index}{/foreach}{/ajax_href}>
          {$smarty.section.foo.index_next}</a>
      {/section}
    {/if}
  </div>
{/if}


{****  Display warnings about incorrect values and missing mandatory fields ***}
{if count($err_mandatory) > 0}
<div class="simplebox highlight">
{tr}Following mandatory fields are missing{/tr}&nbsp;:<br/>
	{section name=ix loop=$err_mandatory}
{$err_mandatory[ix].name}{if !$smarty.section.ix.last},&nbsp;{/if}
	{/section}
</div><br />
{/if}
{if count($err_value) > 0}
<div class="simplebox highlight">
{tr}Following fields are incorrect{/tr}&nbsp;:<br/>
	{section name=ix loop=$err_value}
{$err_value[ix].name}{if !$smarty.section.ix.last},&nbsp;{/if}
	{/section}
</div><br />
{/if}

{if $prefs.feature_tabs eq 'y'}
{cycle name=tabs values="1,2,3,4,5" print=false advance=false reset=true}
<div id="page-bar">
<span id="tab{cycle name=tabs advance=false assign=tabi}{$tabi}" class="tabmark" style="border-color:{if $cookietab eq $tabi}black{else}white{/if};"><a href="javascript:tikitabs({cycle name=tabs},5);">{tr}View{/tr}</a></span>
{if $tracker_info.useComments eq 'y'}
<span id="tab{cycle name=tabs advance=false assign=tabi}{$tabi}" class="tabmark" style="border-color:{if $cookietab eq $tabi}black{else}white{/if};"><a href="javascript:tikitabs({cycle name=tabs},5);">{tr}Comments{/tr} ({$commentCount})</a></span>
{/if}
{if $tracker_info.useAttachments eq 'y'}
<span id="tab{cycle name=tabs advance=false assign=tabi}{$tabi}" class="tabmark" style="border-color:{if $cookietab eq $tabi}black{else}white{/if};"><a href="javascript:tikitabs({cycle name=tabs},5);">{tr}Attachments{/tr} ({$attCount})</a></span>
{/if}
{if $tiki_p_modify_tracker_items eq 'y' or $special}
<span id="tab{cycle name=tabs advance=false assign=tabi}{$tabi}" class="tabmark" style="border-color:{if $cookietab eq $tabi}black{else}white{/if};"><a href="javascript:tikitabs({cycle name=tabs},5);">{tr}Edit{/tr}</a></span>
{/if}
</div>
{/if}

{cycle name=content values="1,2,3,4,5" print=false advance=false reset=true}
{* --- tab with view ------------------------------------------------------------------------- *}
<div id="content{cycle name=content assign=focustab}{$focustab}" class="tabcontent"{if $prefs.feature_tabs eq 'y'} style="display:{if $focustab eq $cookietab}block{else}none{/if};"{/if}>
<h2>{tr}View item{/tr}</h2>
<table class="normal">
{if $tracker_info.showStatus eq 'y' and ($tracker_info.showStatusAdminOnly ne 'y' or $tiki_p_admin_trackers eq 'y')}
  {assign var=ustatus value=$info.status|default:"p"}
  <tr class="formcolor">
    <td class="formlabel">{tr}Status{/tr}</td><td>{$status_types.$ustatus.label}</td>
    <td colspan="2">{html_image file=$status_types.$ustatus.image title=$status_types.$ustatus.label alt=$status_types.$ustatus.label}</td>
  </tr>
{/if}
{assign var=stick value="n"}

{foreach from=$ins_fields key=ix item=cur_field}
  {if ($cur_field.isHidden ne 'y' or $tiki_p_admin_trackers eq 'y') and !($tracker_info.doNotShowEmptyField eq 'y' and empty($cur_field.value) and empty($cur_field.cat) and $cur_field.type ne 's')}
	{if $cur_field.type eq 'h'}
		</table>
		<h2>{$cur_field.name}</h2>
		<table class="normal">
	{elseif $cur_field.type ne 'x'}
		{if $stick ne 'y'}
			<tr class="formcolor field{$cur_field.fieldId}"><td class="formlabel" >
		{else}
			<td class="formlabel right" >
		{/if}
		{$cur_field.name}
		{if ($cur_field.type eq 'l' and $cur_field.options_array[4] eq '1')}
			<br />
			<a href="tiki-view_tracker.php?trackerId={$cur_field.options_array[0]}&amp;filterfield={$cur_field.options_array[1]}&amp;filtervalue={section name=ox loop=$ins_fields}{if $ins_fields[ox].fieldId eq $cur_field.options_array[2]}{$ins_fields[ox].value}{/if}{/section}">{tr}Filter Tracker Items{/tr}</a>
		{/if}
		</td>

		{if ($cur_field.type eq 'c' or $cur_field.type eq 't' or $cur_field.type eq 'n') and $cur_field.options_array[0] eq '1'}
			{assign var=stick value="y"}
		{else}
			{assign var=stick value="n"}
		{/if}
		{if $stick eq 'y'}<td class="formcontent">{else}<td colspan="3" class="formcontent">{/if}

		{include file="tracker_item_field_value.tpl" field_value=$cur_field list_mode=n}

		</td>
		{if $stick ne 'y'}</tr>{/if}
	{/if}
  {/if}
{/foreach}
{if $tracker_info.showCreatedView eq 'y'}<tr class="formcolor"><td class="formlabel">{tr}Created{/tr}</td><td colspan="3" class="formcontent">{$info.created|tiki_long_datetime}</td></tr>{/if}
{if $tracker_info.showLastModifView eq 'y'}<tr class="formcolor"><td class="formlabel">{tr}LastModif{/tr}</td><td colspan="3" class="formcontent">{$info.lastModif|tiki_long_datetime}</td></tr>{/if}
</table>
</div>

{* -------------------------------------------------- tab with comments --- *}
{if $tracker_info.useComments eq 'y'}
<div id="content{cycle name=content assign=focustab}{$focustab}" class="tabcontent"{if $prefs.feature_tabs eq 'y'} style="display:{if $focustab eq $cookietab}block{else}none{/if};"{/if}>
{if $tiki_p_comment_tracker_items eq 'y'}
<h2>{tr}Add a comment{/tr}</h2>
<form action="tiki-view_tracker_item.php" method="post">
<input type="hidden" name="trackerId" value="{$trackerId|escape}" />
<input type="hidden" name="itemId" value="{$itemId|escape}" />
<input type="hidden" name="commentId" value="{$commentId|escape}" />
<table class="normal">
<tr class="formcolor"><td>{tr}Title{/tr}:</td><td><input type="text" name="comment_title" value="{$comment_title|escape}"/></td></tr>
<tr class="formcolor"><td>{tr}Comment{/tr}:</td><td><textarea rows="4" cols="50" name="comment_data">{$comment_data|escape}</textarea></td></tr>
<tr class="formcolor"><td>&nbsp;</td><td><input type="submit" name="save_comment" value="{tr}Save{/tr}" /></td></tr>
</table>
</form>
{/if}
<h2>{tr}Comments{/tr}</h2>
{section name=ix loop=$comments}
<div class="commentbloc">
<b>{$comments[ix].title}</b> {if $comments[ix].user}{tr}by{/tr} {$comments[ix].user}{/if}
  {if $tiki_p_admin_trackers eq 'y'}[<a class="link" href="tiki-view_tracker_item.php?trackerId={$trackerId}&amp;itemId={$itemId}&amp;commentId={$comments[ix].commentId}" title="{tr}Edit{/tr}"><img src="pics/icons/page_edit.png" border="0" width="20" height="16"  alt='{tr}Edit{/tr}' /></a>|&nbsp;&nbsp;<a class="link" href="tiki-view_tracker_item.php?trackerId={$trackerId}&amp;itemId={$itemId}&amp;remove_comment={$comments[ix].commentId}"
title="{tr}Delete{/tr}"><img src="pics/icons/cross.png" border="0" height="16" width="16" alt='{tr}Delete{/tr}' /></a>&nbsp;&nbsp;]{/if}
<br />
<small>{tr}posted on{/tr}: {$comments[ix].posted|tiki_short_datetime}</small><br />
{$comments[ix].parsed}
<hr />
</div>
{/section}
</div>
{/if}

{* ---------------------------------------- tab with attachements --- *}
{if $tracker_info.useAttachments eq 'y'}
<div id="content{cycle name=content assign=focustab}{$focustab}" class="tabcontent"{if $prefs.feature_tabs eq 'y'} style="display:{if $focustab eq $cookietab}block{else}none{/if};" {/if}>
{if $tiki_p_attach_trackers eq 'y'}
<h2>{tr}Attach a file to this item{/tr}</h2>
<form enctype="multipart/form-data" action="tiki-view_tracker_item.php" method="post">
<input type="hidden" name="trackerId" value="{$trackerId|escape}" />
<input type="hidden" name="itemId" value="{$itemId|escape}" />
<input type="hidden" name="attId" value="{$attId|escape}" />
<table class="normal">
<tr class="formcolor"><td>{tr}Upload file{/tr}</td><td>{if $attach_file}{tr}Edit{/tr}: {/if}<input type="hidden" name="MAX_FILE_SIZE" value="1000000000" /><input name="userfile1" type="file"  />{if $attach_file}<br />{$attach_file|escape}{/if}</td></tr>
<tr class="formcolor"><td>{tr}comment{/tr}</td><td><input type="text" name="attach_comment" maxlength="250" value="{$attach_comment|escape}" /></td></tr>
<tr class="formcolor"><td>{tr}Version{/tr}</td><td><input type="text" name="attach_version" size="5" maxlength="10" value="{$attach_version|escape}" /></td></tr>
<tr class="formcolor"><td>{tr}Description{/tr}</td><td><textarea name="attach_longdesc" style="width:100%;" rows="10" >{$attach_longdesc|escape}</textarea></td></tr>

<tr class="formcolor"><td></td><td><input type="submit" name="attach" value={if $attach_file}"{tr}Edit{/tr}"{else}"{tr}attach{/tr}"{/if} /></td></tr>
</table>
</form>
{/if}
<h2>{tr}Attachments{/tr}</h2>
<table class="normal">
<tr>
<td class="heading auto">&nbsp;</td>
{section name=ix loop=$attfields}
<td class="heading auto">{tr}{$attfields[ix]}{/tr}</td>
{/section}
<td class="heading">{tr}filename{/tr}</td>
<td class="heading">{tr}comment{/tr}</td>
<td class="heading">{tr}Version{/tr}</td>
<td class="heading">{tr}Description{/tr}</td>
<td class="heading">&nbsp;</td>
</tr>
{cycle values="odd,even" print=false}
{section name=ix loop=$atts}
<tr class="{cycle}">
<td nowrap="nowrap" class="auto">
{if $attextra eq 'y'}
{assign var=link value='tiki-view_tracker_more_info.php?attId='|cat:$atts[ix].attId}
<a class="tablename" href="#" title="{tr}more info{/tr}"
onClick="javascript:window.open('{$link}','','menubar=no,toolbar=no,location=no,directories=no,status=no,scrollbars=yes,resizable=yes,width=450,height=600');"><img src="img/icons/question.gif" border="0" alt="{tr}more info{/tr}"  hspace="2" vspace="1" /></a>{/if}<a
class="tablename" href="tiki-download_item_attachment.php?attId={$atts[ix].attId}" title="{tr}Download{/tr}"><img src="img/icons/icon38.gif" border="0" alt="{tr}Download{/tr}" hspace="8" vspace="0" /></a>
</td>
{foreach key=k item=x from=$attfields}
{if $x eq 'created'}
<td>{$atts[ix].$x|tiki_short_datetime}</td>
{elseif $x eq 'filesize'}
<td nowrap="nowrap">{$atts[ix].$x|kbsize}</td>
{elseif $x eq 'filetype'}
<td>{$atts[ix].filename|iconify}</td>
{else}
<td>{$atts[ix].$x}</td>
{/if}
{/foreach}
<td>{$atts[ix].filename}</td>
<td>{$atts[ix].comment}</td>
<td>{$atts[ix].version}</td>
<td>{$atts[ix].longdesc}</td>
<td>
{if $tiki_p_wiki_admin_attachments eq 'y' or ($user and ($atts[ix].user eq $user))}
<a class="link" href="tiki-view_tracker_item.php?trackerId={$trackerId}&amp;itemId={$itemId}&amp;removeattach={$atts[ix].attId}&amp;offset={$offset}&amp;sort_mode={$sort_mode}" title="{tr}Delete{/tr}"><img
src="pics/icons/cross.png" border="0"  height="16" width="16" alt="{tr}Delete{/tr}"  hspace="2" vspace="0" /></a>
<a class="link" href="tiki-view_tracker_item.php?trackerId={$trackerId}&amp;itemId={$itemId}&amp;editattach={$atts[ix].attId}&amp;offset={$offset}&amp;sort_mode={$sort_mode}" title="{tr}Edit{/tr}"><img
src="pics/icons/page_edit.png" border="0" height="16" width="16" alt="{tr}Edit{/tr}"  hspace="2" vspace="0" /></a>
{/if}
</td>
</tr>
{sectionelse}
<tr>
 <td colspan="5" class="formcontent">{tr}No attachments for this item{/tr}</td>
</tr>
{/section}
</table>
</div>
{/if}

{* --------------------------------------------------------------- tab with edit --- *}
{if $tiki_p_modify_tracker_items eq 'y' or $special}
<div id="content{cycle name=content assign=focustab}{$focustab}" class="tabcontent nohighlight"{if $prefs.feature_tabs eq 'y'} style="display:{if $focustab eq $cookietab}block{else}none{/if};"{/if}>
<h2>{tr}Edit item{/tr}</h2>
<form enctype="multipart/form-data" action="tiki-view_tracker_item.php" method="post">
{if $special}
<input type="hidden" name="view" value=" {$special}" />
{else}
<input type="hidden" name="trackerId" value="{$trackerId|escape}" />
<input type="hidden" name="itemId" value="{$itemId|escape}" />
{/if}
{if $from}<input type="hidden" name="from" value="{$from}" />{/if}
{section name=ix loop=$fields}
{if $fields[ix].value}
<input type="hidden" name="{$fields[ix].id|escape}" value="{$fields[ix].value|escape}" />
{/if}
{/section}

<table class="normal">
<tr class="formcolor"><td class="formcontent">&nbsp;</td><td colspan="3" class="formcontent">
<input type="submit" name="save" value="{tr}Save{/tr}" />
{* --------------------------- to return to tracker list after saving --------- *}
{if $tiki_p_view_trackers eq 'y'}
<input type="submit" name="save_return" value="{tr}Save{/tr} &amp; {tr}Back{/tr} {tr}items list{/tr}" />
{if $tiki_p_admin_trackers eq 'y' or $tiki_p_modify_tracker_items eq 'y'}<a class="link" href="tiki-view_tracker.php?trackerId={$trackerId}&amp;remove={$itemId}" title="{tr}Delete{/tr}"><img src="pics/icons/cross.png" border="0" height="16" width="16" alt='{tr}Delete{/tr}' /></a>{/if}
{/if}
{* ------------------- *}
{if $tracker_info.showStatus eq 'y' or $tiki_p_admin_trackers eq 'y'}
<tr class="formcolor"><td>{tr}Status{/tr}</td>
<td>
<select name="edstatus">
{foreach key=st item=stdata from=$status_types}
<option value="{$st}"{if $item_info.status eq $st} selected="selected"{/if}
style="background-image:url('{$stdata.image}');background-repeat:no-repeat;padding-left:17px;">{$stdata.label}</option>
{/foreach}
</select>
</td></tr>
{/if}

{foreach from=$ins_fields key=ix item=cur_field}
{if $cur_field.isHidden eq 'n' or $tiki_p_admin_trackers eq 'y' or $cur_field.isHidden eq 'c'}

{if $cur_field.type eq 's' and ($cur_field.name eq "Rating" or $cur_field.name eq tra("Rating")) and ($tiki_p_tracker_view_ratings eq 'y' || $tiki_p_tracker_vote_ratings eq 'y')}
	<tr class="formcolor">
		<td>
			{$cur_field.name}
		</td>
			{if $tiki_p_tracker_view_ratings eq 'y' and $tiki_p_tracker_vote_ratings neq 'y'}
				<td>
					{$cur_field.value}
				</td>
			{elseif $tiki_p_tracker_vote_ratings eq 'y'}
				<td>
					{section name=i loop=$cur_field.options_array}
						{if $cur_field.options_array[i] eq $my_rate}
							<input name="newItemRate" checked="checked" type="radio" value="{$cur_field.options_array[i]|escape}" />{$cur_field.options_array[i]}
						{else}
							<input name="newItemRate" type="radio" value="{$cur_field.options_array[i]|escape}" />{$cur_field.options_array[i]}
						{/if}
					{/section}
				</td>
			{/if}
		</tr>
{/if}

{if $cur_field.type ne 'x' and $cur_field.type ne 's'}
{if $cur_field.type eq 'h'}
</table>
<h2>{$cur_field.name}</h2>
<table class="normal">
{else}
{if ($cur_field.type eq 'c' or $cur_field.type eq 't' or $cur_field.type eq 'n') and $cur_field.options_array[0] eq '1'}
<tr class="formcolor"><td class="formlabel" >{$cur_field.name}{if $cur_field.isMandatory eq 'y'} *{/if}</td><td >
{elseif $stick eq 'y'}
<td class="formlabel right" >{$cur_field.name}{if $cur_field.isMandatory eq 'y'} *{/if}</td><td >
{else}
<tr class="formcolor"><td class="formlabel" >{$cur_field.name}{if $cur_field.isMandatory eq 'y'} *{/if}
{if $cur_field.type eq 'a' and $cur_field.options_array[0] eq 1}
  <br />

  {if $prefs.quicktags_over_textarea neq 'y'}
    {include file=tiki-edit_help_tool.tpl qtnum=$cur_field.id area_name="area_"|cat:$cur_field.id}
  {/if}
{elseif ($cur_field.type eq 'l' and $tiki_p_create_tracker_items eq 'y')}
<br />

{* <a href="tiki-view_tracker.php?trackerId={$cur_field.trackerId}&amp;vals%5B{$cur_field.options_array[1]}%5D= *}

<a href="tiki-view_tracker.php?trackerId={$cur_field.options_array[0]}&amp;vals%5B{$cur_field.options_array[1]}%5D=
{section name=ox loop=$ins_fields}
{if $ins_fields[ox].fieldId eq $cur_field.options_array[2]}
{$ins_fields[ox].value}
{/if}
{/section}
">{tr}Insert new item{/tr}<br />
{/if}
</td><td colspan="3" class="formcontent" >
{/if}
{/if}

{if $cur_field.type eq 'u'}
{if !$cur_field.options or $tiki_p_admin_trackers eq 'y'}
<select name="ins_{$cur_field.id}" {if $cur_field.http_request}onchange="selectValues('trackerIdList={$cur_field.http_request[0]}&amp;fieldlist={$cur_field.http_request[3]}&amp;filterfield={$cur_field.http_request[1]}&amp;status={$cur_field.http_request[4]}&amp;mandatory={$cur_field.http_request[6]}&amp;filtervalue='+escape(this.value),'{$cur_field.http_request[5]}')"{/if}>
{if $cur_field.isMandatory ne 'y'}<option value="">{tr}None{/tr}</option>{/if}
{foreach key=id item=one from=$users}
{if ( ! isset($cur_field.itemChoices) || $cur_field.itemChoices|@count eq 0 || in_array($one, $cur_field.itemChoices) ) }
<option value="{$one|escape}" {if $cur_field.value eq $one or ($cur_field.isMandatory eq 'y' and empty($cur_field.value) and $one eq $user)}selected="selected"{/if}>{$one}</option>
{/if}
{/foreach}
</select>
{elseif $cur_field.options}
<a href="tiki-user_information.php?user={$cur_field.value|escape:"url"}" class="link">{$cur_field.value}</a>
{/if}

{elseif $cur_field.type eq 'I'}
{if !$cur_field.options or $tiki_p_admin_trackers eq 'y'}
<input type="text" name="ins_{$cur_field.id}" value="{$cur_field.value|escape}" />
{elseif $cur_field.options}
{$cur_field.value}
{/if}

{elseif $cur_field.type eq 'g'}
{if !$cur_field.options or $tiki_p_admin_trackers eq 'y'}
<select name="ins_{$cur_field.id}" {if $cur_field.http_request}onchange="selectValues('trackerIdList={$cur_field.http_request[0]}&amp;fieldlist={$cur_field.http_request[3]}&amp;filterfield={$cur_field.http_request[1]}&amp;status={$cur_field.http_request[4]}&amp;mandatory={$cur_field.http_request[6]}&amp;filtervalue='+escape(this.value),'{$cur_field.http_request[5]}')"{/if}>
{if $cur_field.isMandatory ne 'y'}<option value="">{tr}None{/tr}</option>{/if}
{section name=ux loop=$groups}
{if ( ! isset($cur_field.itemChoices) || $cur_field.itemChoices|@count eq 0 || in_array($groups[ux], $cur_field.itemChoices) ) }
<option value="{$groups[ux]|escape}" {if $cur_field.value|default:$cur_field.pvalue eq $groups[ux]}selected="selected"{/if}>{$groups[ux]}</option>
{/if}
{/section}
</select>
{elseif $cur_field.options}
{$cur_field.value}
{/if}

{elseif $cur_field.type eq 'l'}
{foreach key=tid item=tlabel from=$cur_field.links}
<div><a href="tiki-view_tracker_item.php?trackerId={$cur_field.trackerId}&amp;itemId={$tid}" class="link">{$tlabel}</a></div>
{/foreach}

{elseif $cur_field.type eq 'e'}
{if !empty($cur_field.options_array[2]) && ($cur_field.options_array[2] eq '1' or $cur_field.options_array[2] eq 'y')} 
<script type="text/javascript"> /* <![CDATA[ */
document.write('<div class="categSelectAll"><input type="checkbox" id="clickall" onclick="switchCheckboxes(this.form,\'ins_cat_{$cur_field.fieldId}[]\',this.checked)"/>{tr}Select All{/tr}</div>');
/* ]]> */</script>
{/if}
{assign var=fca value=$cur_field.options_array[0]}
<table width="100%"><tr>{cycle name="2_$fca" values=",</tr><tr>" advance=false}
{foreach key=ku item=iu from=$cur_field.$fca name=foreache}
{assign var=fcat value=$iu.categId }
<td width="50%" nowrap="nowrap"><input type={if $cur_field.options_array[1] eq "radio"}"radio"{else}"checkbox"{/if} name="ins_cat_{$cur_field.fieldId}[]" value="{$fcat}" {if $cur_field.cat.$fcat eq 'y'}checked="checked"{/if}/>{$iu.name}</td>
{if !$smarty.foreach.foreache.last}{cycle name="2_$fca"}{else}{if $cur_field.$fca|@count%2}<td></td>{/if}</tr>{/if}
{/foreach}
</table>

{elseif $cur_field.type eq 't' || $cur_field.type eq 'm'}

    {if $cur_field.isMultilingual ne "y"}
        {if $cur_field.options_array[2]}<span class="formunit">{$cur_field.options_array[2]}&nbsp;</span>{/if}
        <input type="text" name="ins_{$cur_field.id}" value="{$cur_field.value|escape}" {if $cur_field.options_array[1]}size="{$cur_field.options_array[1]}" maxlength="{$cur_field.options_array[1]}"{/if} />
        {if $cur_field.options_array[3]}<span class="formunit">&nbsp;{$cur_field.options_array[3]}</span>{/if}
    
    {else}
    <table>
        {foreach from=$cur_field.lingualvalue item=ling}
        <TR><TD>{$ling.lang}</td><td>
                {if $cur_field.options_array[2]}<span class="formunit">{$cur_field.options_array[2]}&nbsp;</span>{/if}
            <input type="text" name="ins_{$cur_field.id}_{$ling.lang}" value="{$ling.value|escape}" {if $cur_field.options_array[1]}size="{$cur_field.options_array[1]}" maxlength="{$cur_field.options_array[1]}"{/if} />
            {if $cur_field.options_array[3]}<span class="formunit">&nbsp;{$cur_field.options_array[3]}</span>{/if}
            </td></tr>
        {/foreach}
    </table>
    {/if}
{elseif $cur_field.type eq 'n'}
{if $cur_field.options_array[2]}<span class="formunit">{$cur_field.options_array[2]}&nbsp;</span>{/if}
<input type="text" name="ins_{$cur_field.id}" value="{$cur_field.value|escape}" {if $cur_field.options_array[1]}size="{$cur_field.options_array[1]}" maxlength="{$cur_field.options_array[1]}"{/if} />
{if $cur_field.options_array[3]}<span class="formunit">&nbsp;{$cur_field.options_array[3]}</span>{/if}

{elseif $cur_field.type eq 'q'}
<input type="hidden" name="ins_{$cur_field.id}" value="{$cur_field.value|escape}" size="6" maxlength="6" />
{$cur_field.value|escape}

{* -------------------- static text -------------------- *}
{elseif $cur_field.type eq 'S'}
{if $cur_field.description}
{wiki}{$cur_field.description|escape|nl2br}{/wiki}
{/if}

{elseif $cur_field.type eq 'a'}
{if $cur_field.description}
<em>{$cur_field.description|escape|nl2br}</em><br />
{/if}
{if $prefs.quicktags_over_textarea eq 'y' and $cur_field.options_array[0] eq 1}
      {include file=tiki-edit_help_tool.tpl qtnum=$cur_field.id area_name="area_"|cat:$cur_field.id}
{/if}
{if $cur_field.isMultilingual ne "y"}
            <textarea name="ins_{$cur_field.id}" id="area_{$cur_field.id}" cols="{if $cur_field.options_array[1] gt 1}{$cur_field.options_array[1]}{else}50{/if}" rows="{if $cur_field.options_array[2] gt 1}{$cur_field.options_array[2]}{else}4{/if}">{$cur_field.value|escape}</textarea>
        {else}
        <table>
            {foreach from=$cur_field.lingualvalue item=ling}
            <TR><TD>{$ling.lang}</td><td>
                <textarea name="ins_{$cur_field.id}_{$ling.lang}" id="area_{$cur_field.id}" cols="{if $cur_field.options_array[1] gt 1}{$cur_field.options_array[1]}{else}50{/if}" rows="{if $cur_field.options_array[2] gt 1}{$cur_field.options_array[2]}{else}4{/if}">{$ling.value|escape}</textarea></td></tr>
            {/foreach}
        </table>
        {/if}
{elseif $cur_field.type eq 'f'}
{html_select_date prefix="ins_"|cat:$cur_field.id time=$cur_field.value start_year="-4" end_year="+4" field_order=$prefs.display_field_order}{if $cur_field.options_array[0] ne 'd'} {html_select_time prefix="ins_"|cat:$cur_field.id time=$cur_field.value display_seconds=false}{/if}

{elseif $cur_field.type eq 'r'}
<select name="ins_{$cur_field.id}" {if $cur_field.http_request}onchange="selectValues('trackerIdList={$cur_field.http_request[0]}&fieldlist={$cur_field.http_request[3]}&filterfield={$cur_field.http_request[1]}&status={$cur_field.http_request[4]}&mandatory={$cur_field.http_request[6]}&filtervalue='+escape(this.value),'{$cur_field.http_request[5]}')"{/if}>
{if $cur_field.isMandatory}<option value=""></option>{/if}
{foreach key=id item=label from=$cur_field.list}
<option value="{$label|escape}" {if $cur_field.value eq $label}selected="selected"{/if}>{if $cur_field.listdisplay[$id] eq ""}{$label}{else}{$cur_field.listdisplay[$id]}{/if}</option>
{/foreach}
</select>

{elseif $cur_field.type eq 'w'}
<select name="ins_{$cur_field.id}" {if $cur_field.http_request}onchange="selectValues('trackerIdList={$cur_field.http_request[0]}&amp;fieldlist={$cur_field.http_request[3]}&amp;filterfield={$cur_field.http_request[1]}&amp;status={$cur_field.http_request[4]}&amp;mandatory={$cur_field.http_request[6]}&amp;filtervalue='+escape(this.value),'{$cur_field.http_request[5]}')"{/if}>
</select>

{elseif $cur_field.type eq 'd' or $cur_field.type eq 'D'}
<select name="ins_{$cur_field.id}" {if $cur_field.http_request}onchange="selectValues('trackerIdList={$cur_field.http_request[0]}&amp;fieldlist={$cur_field.http_request[3]}&amp;filterfield={$cur_field.http_request[1]}&amp;status={$cur_field.http_request[4]}&amp;mandatory={$cur_field.http_request[6]}&amp;filtervalue='+escape(this.value),'{$cur_field.http_request[5]}')"{/if}>
{if $cur_field.isMandatory ne 'y' || empty($cur_field.value)}<option value=""></option>{/if}{*can be empty even if mandatory when coming from a user tracker *}
{section name=jx loop=$cur_field.options_array}
<option value="{$cur_field.options_array[jx]|escape}" {if (!empty($cur_field.value) and $cur_field.value eq $cur_field.options_array[jx]) or (empty($cur_field.value) and $cur_field.options_array[jx] eq $cur_field.defaultvalue)}selected="selected"{/if}>{$cur_field.options_array[jx]|tr_if}</option>
{if !empty($cur_field.value) and $cur_field.value eq $cur_field.options_array[jx]}{assign var=otherValue value=''}{/if}
{/section}
</select>
{if $cur_field.type eq 'D'}
<br />{tr}Other:{/tr} <input type="text" name="ins_{$cur_field.id}_other" value="{$otherValue|escape}" />
{/if}

{elseif $cur_field.type eq 'R'}
{section name=jx loop=$cur_field.options_array}
<input type="radio" name="ins_{$cur_field.id}" value="{$cur_field.options_array[jx]|escape}" {if $cur_field.value eq $cur_field.options_array[jx]}checked="checked"{/if}>{$cur_field.options_array[jx]}</input>
{/section}

{elseif $cur_field.type eq 'c'}
<input type="checkbox" name="ins_{$cur_field.id}" {if $cur_field.value eq 'y'}checked="checked"{/if}/>

{elseif $cur_field.type eq 'y'}
<select name="ins_{$cur_field.id}" {if $cur_field.http_request}onchange="selectValues('trackerIdList={$cur_field.http_request[0]}&amp;fieldlist={$cur_field.http_request[3]}&amp;filterfield={$cur_field.http_request[1]}&amp;status={$cur_field.http_request[4]}&amp;mandatory={$cur_field.http_request[6]}&amp;filtervalue='+escape(this.value),'{$cur_field.http_request[5]}')"{/if}>
{if $cur_field.isMandatory ne 'y' || empty($cur_field.value)}<option value=""{if $cur_field.value eq '' or $cur_field.value eq 'None'} selected="selected"{/if}></option>{/if}
{sortlinks}
{foreach item=flag from=$cur_field.flags}
{if $flag ne 'None' and ( ! isset($cur_field.itemChoices) || $cur_field.itemChoices|@count eq 0 || in_array($flag, $cur_field.itemChoices) ) }
{capture name=flag}
{tr}{$flag}{/tr}
{/capture}
<option value="{$flag|escape}" {if $cur_field.value ne '' and $cur_field.value eq $flag}selected="selected"{/if}{if $cur_field.options_array[0] ne '1'} style="background-image:url('img/flags/{$flag}.gif');background-repeat:no-repeat;padding-left:25px;padding-bottom:3px;"{/if}>{$smarty.capture.flag|replace:'_':' '}</option>
{/if}
{/foreach}
{/sortlinks}
</select>

{elseif $cur_field.type eq 'i'}
<input type="file" name="ins_{$cur_field.id}" /><br />
{if $cur_field.value ne ''}
<img src="{$cur_field.value}" alt="n/a" width="{$cur_field.options_array[2]}" height="{$cur_field.options_array[3]}" /><br />
<a href="tiki-view_tracker_item.php?trackerId={$trackerId}&itemId={$itemId}&fieldId={$cur_field.id}&fieldName={$cur_field.name}&removeImage">{tr}Remove Image{/tr}</a>
{else}
<img border="0" src="img/icons/na_pict.gif" alt="n/a" />
{/if}

{elseif $cur_field.type eq 'M'} 
{if ($cur_field.options_array[0] > '2')}
<input type="file" name="ins_{$cur_field.id}" value="{$cur_field.value}" /><br />
{else}
<input type="text" name="ins_{$cur_field.id}" value="{$cur_field.value}" /><br />


	{assign var='Height' value=$prefs.MultimediaDefaultHeight}
	{assign var='Lenght' value=$prefs.MultimediaDefaultLength}

	{if $cur_field.value ne ''}	
	{if  $cur_field.options_array[1] ne '' } { $Lenght=$cur_field.options_array[1] }{/if}
	{if  $cur_field.options_array[2] ne '' } { $Height=$cur_field.options_array[2] }{/if}
	{if $ModeVideo eq 'y' } { assign var="Height" value=$Height+$prefs.VideoHeight}{/if}
	{include file=multiplayer.tpl url=$cur_field.value w=$Lenght h=$Height video=$ModeVideo}
{/if}
{/if}
{elseif $cur_field.type eq 'U'}
<input type="text" name="ins_{$cur_field.id}" value="{$cur_field.value}" />

{elseif $cur_field.type eq 'G'}
<input type="text" name="ins_{$cur_field.id}" value="{$cur_field.value}" />
<a href="tiki-gmap_locator.php?for=item&amp;itemId={$itemId}&amp;trackerId={$trackerId}&amp;fieldId={$cur_field.id}">{tr}Google Map Locator{/tr}</a>


{elseif $cur_field.type eq 'j'}
{if $cur_field.options_array[0] eq 'd'}
{jscalendar date=$cur_field.value|default:$smarty.now id=$cur_field.id fieldname="ins_"|cat:$cur_field.id showtime="n"}
{else}
{jscalendar date=$cur_field.value|default:$smarty.now id=$cur_field.id fieldname="ins_"|cat:$cur_field.id showtime="y"}
{/if}
{/if}

{if $cur_field.type ne 'a' and $cur_field.type ne 'S'}
{if $cur_field.description}
<br /><em>{$cur_field.description|escape}</em>
{/if}
{/if}
</td>
{if (($cur_field.type eq 'c' or $cur_field.type eq 't' or $cur_field.type eq 'n') and $cur_field.options_array[0] eq '1') and $stick ne 'y'}
{assign var=stick value="y"}
{else}
</tr>{assign var=stick value="n"}
{/if}

{elseif $cur_field.type eq 'x'}

{capture name=trkaction}
{if $cur_field.options_array[1] eq 'post'}
<form action="{$cur_field.options_array[2]}" method="post">
{else}
<form action="{$cur_field.options_array[2]}" method="get">
{/if}
{section name=tl loop=$cur_field.options_array start=3}
{assign var=valvar value=$cur_field.options_array[tl]|regex_replace:"/^[^:]*:/":""|escape}
{if $info.$valvar eq ''}
{assign var=valvar value=$cur_field.options_array[tl]|regex_replace:"/^[^\=]*\=/":""|escape}
<input type="hidden" name="{$cur_field.options_array[tl]|regex_replace:"/\=.*$/":""|escape}" value="{$valvar|escape}" />
{else}
<input type="hidden" name="{$cur_field.options_array[tl]|regex_replace:"/:.*$/":""|escape}" value="{$info.$valvar|escape}" />
{/if}
{/section}
<table class="normal">
<tr class="formcolor"><td>{$cur_field.name}</td><td><input type="submit" class="submit" name="trck_act" value="{$cur_field.options_array[0]|escape}" /></td><tr>
</table>
</form>
{/capture}
{assign var=trkact value=$trkact|cat:$smarty.capture.trkaction}
{/if}

{/if}
{/foreach}
<tr class="formcolor"><td class="formlabel">&nbsp;</td><td colspan="3" class="formcontent">
<input type="submit" name="save" value="{tr}Save{/tr}" />
{* --------------------------- to retrun to tracker list after saving --------- *}
{if $tiki_p_view_trackers eq 'y'}
<input type="submit" name="save_return" value="{tr}Save{/tr} &amp; {tr}Back{/tr} {tr}items list{/tr}" /> <span>
{/if}
</td></tr>
</table>
<input type="hidden" name="offset" value="{$offset|escape}" />
<input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
{foreach key=urlkey item=urlval from=$urlquery}
<input type="hidden" name="{$urlkey}" value="{$urlval|escape}" />
{/foreach}
{* ------------------- *}
</form>

{if $trkact}
<h2>{tr}Special Operations{/tr}</h2>
{$trkact}
{/if}
<br /><em>{tr}fields marked with a * are mandatory{/tr}</em>
</div>{*nohighlight - important comment to delimit the zone not to highlight in a search result*}
{/if}

<br /><br />

{foreach from=$ins_fields key=ix item=cur_field}
{if $cur_field.http_request}
<script language="JavaScript" type="text/javascript">
selectValues('trackerIdList={$cur_field.http_request[0]}&fieldlist={$cur_field.http_request[3]}&filterfield={$cur_field.http_request[1]}&status={$cur_field.http_request[4]}&mandatory={$cur_field.http_request[6]}&filtervalue={$cur_field.http_request[7]|escape:"url"}&selected={$cur_field.http_request[8]|escape:"url"}','{$cur_field.http_request[5]}')
</script>
{/if}
{/foreach}
