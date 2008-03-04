{* $Header: /cvsroot/tikiwiki/tiki/templates/tiki-view_tracker.tpl,v 1.159.2.39 2008-03-04 22:55:45 luciash Exp $ *}
<script type="text/javascript" src="lib/trackers/dynamic_list.js"></script>
{if !empty($tracker_info.showPopup)}
{popup_init src="lib/overlib.js"}
{/if}

<h1><a class="pagetitle" href="tiki-view_tracker.php?trackerId={$trackerId}">{tr}Tracker{/tr}: {$tracker_info.name}</a></h1>
<div class="navbar">
{if $prefs.feature_user_watches eq 'y' and $tiki_p_watch_trackers eq 'y' and $user}
{if $user_watching_tracker ne 'y'}
<a href="tiki-view_tracker.php?trackerId={$trackerId}&amp;watch=add" title="{tr}Monitor{/tr}">{icon _id='eye' align="right" hspace="5" alt="{tr}Monitor{/tr}"}</a>
{else}
<a href="tiki-view_tracker.php?trackerId={$trackerId}&amp;watch=stop" title="{tr}Stop Monitor{/tr}">{icon _id='no_eye' align="right" hspace="5" alt="{tr}Stop Monitor{/tr}"}</a>
{/if}
{/if}
{if $prefs.rss_tracker eq "y"}
<a href="tiki-tracker_rss.php?trackerId={$trackerId}"><img src='img/rss.png' border='0' alt='{tr}RSS feed{/tr}' title='{tr}RSS feed{/tr}'  align="right" /></a>
{/if}
{if (isset($tiki_p_list_trackers) and $tiki_p_list_trackers eq 'y') or (!isset($tiki_p_list_trackers) and $tiki_p_view_trackers eq 'y')}<span class="button2"><a href="tiki-list_trackers.php" class="linkbut">{tr}List trackers{/tr}</a></span>{/if}
{if $filtervalue}
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

{if !empty($tracker_info.description)}
<div class="wikitext">{$tracker_info.description}</div>
{/if}

{if !empty($mail_msg)}
<div class="wikitext">{$mail_msg}</div>
{/if}

{if count($err_mandatory) > 0}<div class="simplebox highlight">
{tr}Following mandatory fields are missing{/tr}&nbsp;:<br/>
	{section name=ix loop=$err_mandatory}
{$err_mandatory[ix].name}{if !$smarty.section.ix.last},&nbsp;{/if}
	{/section}
</div><br />{/if}
{if count($err_value) > 0}<div class="simplebox highlight">
{tr}Following fields are incorrect{/tr}&nbsp;:<br/>
	{section name=ix loop=$err_value}
{$err_value[ix].name}{if !$smarty.section.ix.last},&nbsp;{/if}
	{/section}
</div><br />{/if}
{if $prefs.feature_tabs eq 'y'}
{cycle name=tabs values="1,2,3" print=false advance=false reset=true}
<div class="tabs">
{if $tiki_p_view_trackers eq 'y' or ($tracker_info.writerCanModify eq 'y' and $user)}
<span id="tab{cycle name=tabs advance=false assign=tabi}{$tabi}" class="tabmark"><a href="javascript:tikitabs({cycle name=tabs},3);">{tr}Tracker{/tr} <i>{$tracker_info.name}</i></a></span>
{/if}
{if $tiki_p_create_tracker_items eq 'y'}
<span id="tab{cycle name=tabs advance=false assign=tabi}{$tabi}" class="tabmark"><a href="javascript:tikitabs({cycle name=tabs},3);">{tr}Insert new item{/tr}</a></span>
{/if}
</div>
{/if}

{cycle name=content values="1,2,3" print=false advance=false reset=true}
{* -------------------------------------------------- tab with list --- *}
{if $tiki_p_view_trackers eq 'y' or ($tracker_info.writerCanModify eq 'y' and $user)}
<div id="content{cycle name=content assign=focustab}{$focustab}" class="tabcontent">

{if (($tracker_info.showStatus eq 'y' and $tracker_info.showStatusAdminOnly ne 'y') or $tiki_p_admin_trackers eq 'y') or $show_filters eq 'y'}
{include file="tracker_filter.tpl"}
{/if}

{if $cant_pages > 1 or $initial}{initials_filter_links}{/if}

{* ------- list headings --- *}
<form name="checkform" method="post" action="{$smarty.server.PHP_SELF}">
<table class="normal">
<tr>
{if $tracker_info.showStatus eq 'y' or ($tracker_info.showStatusAdminOnly eq 'y' and $tiki_p_admin_trackers eq 'y')}
<td class="heading auto" style="width:20px;">&nbsp;</td>
{/if}

{foreach from=$fields key=ix item=field_value}
{if ( $field_value.type eq 's' and ($field_value.name eq "Rating" or $field_value.name eq tra("Rating")) and $field_value.isTblVisible eq 'y' ) || ( $field_value.isTblVisible eq 'y' and $field_value.type ne 'x' and $field_value.type ne 'h' and ($field_value.isHidden eq 'n' or $field_value.isHidden eq 'p' or $tiki_p_admin_trackers eq 'y') ) }
	<td class="heading auto">
		{self_link _class='tableheading' _sort_arg='sort_mode' _sort_field='f_'|cat:$field_value.fieldId}{$field_value.name|truncate:255:"..."|default:"&nbsp;"}{/self_link}
	</td>
	{assign var=rateFieldId value=$field_value.fieldId}
{/if}
{/foreach}

{if $tracker_info.showCreated eq 'y'}
<td class="heading"><a class="tableheading" href="tiki-view_tracker.php?{if $status}status={$status}&amp;{/if}{if $initial}initial={$initial}&amp;{/if}{if $find}find={$find}&amp;{/if}trackerId={$trackerId}{if $offset}&amp;offset={$offset}{/if}&amp;sort_mode={if 
$sort_mode eq 'created_desc'}created_asc{else}created_desc{/if}">{tr}Created{/tr}</a></td>
{/if}
{if $tracker_info.showLastModif eq 'y'}
<td class="heading"><a class="tableheading" href="tiki-view_tracker.php?status={$status}&amp;{if $initial}initial={$initial}&amp;{/if}find={$find}&amp;trackerId={$trackerId}{if $offset}&amp;offset={$offset}{/if}&amp;sort_mode={if $sort_mode eq 'lastModif_desc'}lastModif_asc{else}lastModif_desc{/if}">{tr}lastModif{/tr}</a></td>
{/if}
{if $tracker_info.useComments eq 'y' and $tracker_info.showComments eq 'y'}
<td class="heading" width="5%">{tr}Coms{/tr}</td>
{/if}
{if $tracker_info.useAttachments eq 'y' and  $tracker_info.showAttachments eq 'y'}
<td class="heading" width="5%">{tr}atts{/tr}</td>
{if $tiki_p_admin_trackers eq 'y'}<td class="heading" width="5%">{tr}dls{/tr}</td>{/if}
{/if}
{if $tiki_p_admin_trackers eq 'y'}
<td class="heading" width="5%">
<script type='text/javascript'>
document.write("<input name=\"switcher\" id=\"clickall\" type=\"checkbox\" onclick=\"switchCheckboxes(this.form,'action[]',this.checked)\"/><label for=\"clickall\">{tr}All{/tr}</label>");
</script>
</td>
{/if}
</tr>

{* ------- Items loop --- *}
{assign var=itemoff value=0}
{cycle values="odd,even" print=false}
{section name=user loop=$items}
<tr class="{cycle}">
{if $tracker_info.showStatus eq 'y' or ($tracker_info.showStatusAdminOnly eq 'y' and $tiki_p_admin_trackers eq 'y')}
	<td class="auto" style="width:20px;">
	{assign var=ustatus value=$items[user].status|default:"c"}
	{html_image file=$status_types.$ustatus.image title=$status_types.$ustatus.label alt=$status_types.$ustatus.label}
	</td>
{/if}

{* ------- list values --- *}
{foreach from=$items[user].field_values key=ix item=field_value}

{if $field_value.isTblVisible eq 'y' and $field_value.type ne 'x' and $field_value.type ne 'h' and ($field_value.isHidden eq 'n' or $field_value.isHidden eq 'p' or $tiki_p_admin_trackers eq 'y')}
<td class="auto">
{if $field_value.isMain eq 'y' and ($tiki_p_view_trackers eq 'y' or $tiki_p_modify_tracker_items eq 'y' or $tiki_p_comment_tracker_items eq 'y' 
 or ($tracker_info.writerCanModify eq 'y' and $user and $my eq $user) or ($tracker_info.writerCanModify eq 'y' and $group and $ours eq $group))}
{if !empty($tracker_info.showPopup)}
	{capture name=popup}
	<div class="cbox">
	<table>
	{cycle values="odd,even" print=false}
	{foreach from=$items[user].field_values item=f}
		{if in_array($f.fieldId, $popupFields)}
			 <tr><th class="{cycle advance=false}">{$f.name}</th><td class="{cycle}">{include file="tracker_item_field_value.tpl" field_value=$f}</td></tr>
		{/if}
	{/foreach}
	</table>
	</div>
	{/capture}
{/if}
<a class="tablename" href="tiki-view_tracker_item.php?itemId={$items[user].itemId}&amp;show=view&amp;{if $offset}offset={$offset}{/if}&amp;reloff={$smarty.section.user.index}&amp;cant={$item_count}{foreach key=urlkey item=urlval from=$urlquery}{if $urlval}&amp;{$urlkey}={$urlval|escape:"url"}{/if}{/foreach}"{if !empty($tracker_info.showPopup)} {popup text=$smarty.capture.popup|escape:"javascript"|escape:"html" fullhtml="1" hauto=true vauto=true  }{/if}>
{/if}

{include file="tracker_item_field_value.tpl" field_value=$field_value list_mode="y" item=$items[user]}

{if $field_value.isMain eq 'y' and ($tiki_p_view_trackers eq 'y' or $tiki_p_modify_tracker_items eq 'y' or $tiki_p_comment_tracker_items eq 'y' 
 or ($tracker_info.writerCanModify eq 'y' and $user and $my eq $user) or ($tracker_info.writerCanModify eq 'y' and $group and $ours eq $group))}</a>{/if}
</td>
{/if}
{/foreach}

{if $tracker_info.showCreated eq 'y'}
<td>{if $tracker_info.showCreatedFormat}{$items[user].created|tiki_date_format:$tracker_info.showCreatedFormat}{else}{$items[user].created|tiki_short_datetime}{/if}</td>
{/if}
{if $tracker_info.showLastModif eq 'y'}
<td>{if $tracker_info.showLastModifFormat}{$items[user].lastModif|tiki_date_format:$tracker_info.showLastModifFormat}{else}{$items[user].lastModif|tiki_short_datetime}{/if}</td>
{/if}
{if $tracker_info.useComments eq 'y' and $tracker_info.showComments eq 'y'}
<td  style="text-align:center;">{$items[user].comments}</td>
{/if}
{if $tracker_info.useAttachments eq 'y' and $tracker_info.showAttachments eq 'y'}
<td  style="text-align:center;"><a href="tiki-view_tracker_item.php?itemId={$items[user].itemId}&amp;show=att{if $offset}&amp;offset={$offset}{/if}{foreach key=urlkey item=urlval from=$urlquery}{if $urlval}&amp;{$urlkey}={$urlval|escape:"url"}{/if}{/foreach}{section name=mix loop=$fields}{if $fields[mix].value}&amp;{$fields[mix].name}={$fields[mix].value}{/if}{/section}" 
link="{tr}List Attachments{/tr}"><img src="img/icons/folderin.gif" border="0" alt="{tr}List Attachments{/tr}" 
/></a> {$items[user].attachments}</td>
{if $tiki_p_admin_trackers eq 'y'}<td  style="text-align:center;">{$items[user].hits}</td>{/if}
{/if}
{if $tiki_p_admin_trackers eq 'y'}
  <td>
    <input type="checkbox" name="action[]" value='{$items[user].itemId}' style="border:1px;font-size:80%;" />
    <a class="link" href="tiki-view_tracker.php?status={$status}&amp;trackerId={$trackerId}{if $offset}&amp;offset={$offset}{/if}{if $sort_mode ne ''}&amp;sort_mode={$sort_mode}{/if}&amp;remove={$items[user].itemId}" 
title="{tr}Delete{/tr}">{icon _id='cross' alt='{tr}Delete{/tr}'}</a>
  </td>
{/if}
</tr>
{assign var=itemoff value=$itemoff+1}
{/section}
</table>
{if $tiki_p_admin_trackers eq 'y'}
<div style="text-align:right;">
<script type='text/javascript'>
document.write("<input name=\"switcher\" id=\"clickall2\" type=\"checkbox\" onclick=\"switchCheckboxes(this.form,'action[]',this.checked)\"/><label for=\"clickall2\">{tr}Select All{/tr}</label>");
</script>
<select name="batchaction">
<option value="">{tr}with checked{/tr}</option>
<option value="delete">{tr}Delete{/tr}</option>
</select>
<input type="hidden" name="trackerId" value="{$trackerId}" />
<input type="submit" name="act" value="{tr}OK{/tr}" />
</div>
{/if}
</form>
{pagination_links cant=$item_count step=$maxRecords offset=$offset}{/pagination_links}
</div>
{else}<!-- {cycle name=content assign=focustab} -->
{/if}

{* --------------------------------------------------------------------------------- tab with edit --- *}
{if $tiki_p_create_tracker_items eq 'y'}
<div id="content{cycle name=content assign=focustab}{$focustab}" class="tabcontent">
<form enctype="multipart/form-data" action="tiki-view_tracker.php" method="post">
<input type="hidden" name="trackerId" value="{$trackerId|escape}" />

<h2>{tr}Insert new item{/tr}</h2>
<table class="normal">
<tr class="formcolor"><td  class="formlabel">&nbsp;</td><td colspan="3" class="formcontent">
<input type="submit" name="save" value="{tr}Save{/tr}" /></td></tr>

{if $tracker_info.showStatus eq 'y' and ($tracker_info.showStatusAdminOnly ne 'y' or $tiki_p_admin_trackers eq 'y')}
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


{foreach from=$fields key=ix item=field_value}
{assign var=fid value=$field_value.fieldId}

{* -------------------- header and others -------------------- *}
{if $field_value.isHidden eq 'n' or $field_value.isHidden eq 'c'  or $tiki_p_admin_trackers eq 'y'}
{if $field_value.type ne 'x' and $field_value.type ne 'l' and $field_value.type ne 'q' and (($field_value.type ne 'u' and $field_value.type ne 'g' and $field_value.type ne 'I') or !$field_value.options_array[0] or $tiki_p_admin_trackers eq 'y')}
{if $field_value.type eq 'h'}
</table>
<h2>{$field_value.name}</h2>
<table class="normal">
{else}
{if ($field_value.type eq 'c' or $field_value.type eq 't' or $field_value.type eq 'n') and $field_value.options_array[0] eq '1'}
<tr class="formcolor"><td class="formlabel" >{$field_value.name}{if $field_value.isMandatory eq 'y'} *{/if}</td><td class="formcontent">
{elseif $stick eq 'y'}
<td class="formlabel right">{$field_value.name}{if $field_value.isMandatory eq 'y'} *{/if}</td><td >
{else}
<tr class="formcolor"><td class="formlabel" >{$field_value.name}{if $field_value.isMandatory eq 'y'} *{/if}
{if $field_value.type eq 'a' and $field_value.options_array[0] eq 1}
{* --- display quicktags --- *}
  <br />
  {if $prefs.quicktags_over_textarea neq 'y'}
    {include file=tiki-edit_help_tool.tpl qtnum=$fid area_name=$field_value.ins_id}
  {/if}
{/if}
</td><td colspan="3" class="formcontent" >
{/if}
{/if}

{* -------------------- system -------------------- *}
{if $field_value.type eq 's' and ($field_value.name eq "Rating" or $field_value.name eq tra("Rating")) and $tiki_p_tracker_vote_ratings eq 'y'}
	{section name=i loop=$field_value.options_array}
		<input name="{$field_value.ins_id}" type="radio" value="{$field_value.options_array[i]|escape}" />{$field_value.options_array[i]}
	{/section}
{/if}

{* -------------------- user selector -------------------- *}
{if $field_value.type eq 'u'}
{if !$field_value.options_array[0] or $tiki_p_admin_trackers eq 'y'}
<select name="{$field_value.ins_id}" {if $listfields.$fid.http_request}onchange="selectValues('trackerIdList={$listfields.$fid.http_request[0]}&amp;fieldlist={$listfields.$fid.http_request[3]}&amp;filterfield={$listfields.$fid.http_request[1]}&amp;status={$listfields.$fid.http_request[4]}&amp;mandatory={$listfields.$fid.http_request[6]}&amp;filtervalue='+escape(this.value),'{$listfields.$fid.http_request[5]}')"{/if}>
<option value="">{tr}None{/tr}</option>
{foreach key=id item=one from=$users}
{if ( ! isset($field_value.itemChoices) || $field_value.itemChoices|@count eq 0 || in_array($one, $field_value.itemChoices) )}
{if $field_value.value}
<option value="{$one|escape}"{if $one eq $field_value.value} selected="selected"{/if}>{$one}</option>
{else}
<option value="{$one|escape}"{if $one eq $user and $field_value.options_array[0] ne '2'} selected="selected"{/if}>{$one}</option>
{/if}
{/if}
{/foreach}
</select>
{else}
{$user}
{/if}

{* -------------------- IP selector -------------------- *}
{elseif $field_value.type eq 'I'}
{if !$field_value.options_array[0] or $tiki_p_admin_trackers eq 'y'}
<input type="text" name="{$field_value.ins_id}" value="{if $input_err}{$field_value.value}{elseif $defaultvalues.fid}{$defaultvalues.$fid|escape}{else}{$IP}{/if}" />
{else}
{$IP}
{/if}

{* -------------------- group selector -------------------- *}
{elseif $field_value.type eq 'g'}
{if !$field_value.options_array[0] or $tiki_p_admin_trackers eq 'y'}
<select name="{$field_value.ins_id}" {if $listfields.$fid.http_request}onchange="selectValues('trackerIdList={$listfields.$fid.http_request[0]}&amp;fieldlist={$listfields.$fid.http_request[3]}&amp;filterfield={$listfields.$fid.http_request[1]}&amp;status={$listfields.$fid.http_request[4]}&amp;mandatory={$listfields.$fid.http_request[6]}&amp;filtervalue='+escape(this.value),'{$listfields.$fid.http_request[5]}')"{/if}>
<option value="">{tr}None{/tr}</option>
{section name=ux loop=$groups}
{if ( ! isset($field_value.itemChoices) || $field_value.itemChoices|@count eq 0 || in_array($groups[ux], $field_value.itemChoices) )}
<option value="{$groups[ux]|escape}" {if $input_err and $field_value.value eq $groups[ux]} selected="selected"{/if}>{$groups[ux]}</option>
{/if}
{/section}
</select>
{else}
{$group}
{/if}

{* -------------------- category -------------------- *}
{elseif $field_value.type eq 'e'}
{if !empty($field_value.options_array[2]) && ($field_value.options_array[2] eq '1' or $field_value.options_array[2] eq 'y')}
<script type="text/javascript"> /* <![CDATA[ */
document.write('<div  class="categSelectAll"><input type="checkbox" id="clickall" onclick="switchCheckboxes(this.form,\'ins_cat_{$field_value.fieldId}[]\',this.checked)"/>{tr}Select All{/tr}</div>');
/* ]]> */</script>
{/if}
{assign var=fca value=$field_value.options}
{* {assign var=onePerLine value="y"} *}
<table width="100%"><tr>{cycle name=2_$fca values=",</tr><tr>" advance=false print=false}
{foreach key=ku item=iu from=$field_value.categories name=eforeach}
{assign var=fcat value=$iu.categId }
<td{if onePerLine ne 'y'} width="50%"{/if}>
<input type={if $field_value.options_array[1] eq "radio"}"radio"{else}"checkbox"{/if} name="ins_cat_{$field_value.fieldId}[]" value="{$iu.categId}" id="cat{$iu.categId}" {if $field_value.cat.$fcat eq 'y'}checked="checked"{/if}/><label for="cat{$i.categId}">{$iu.name|escape}</label>
</td>{if $onePerLine eq 'y'}{if !$smarty.foreach.eforeach.last}</tr><tr>{/if}{elseif !$smarty.foreach.eforeach.last}{cycle name=2_$fca}{else}{if $field_value.categories|@count%2}<td></td>{/if}{/if}
{/foreach}
</tr></table>

{* -------------------- image -------------------- *}
{elseif $field_value.type eq 'i'}
<input type="file" name="{$field_value.ins_id}" {if $input_err}value="{$field_value.value}"{/if}/>

{* -------------------- multimedia -------------------- *}
{elseif $field_value.type eq 'M'}
{if ($field_value.options_array[0] > '2')}
<input type="file" name="{$field_value.ins_id}" /><br />
{else}
<input type="text" name="{$field_value.ins_id}" value="{$field_value.value}" /><br />
{/if}

{* -------------------- text field / email -------------------- *}
{elseif $field_value.type eq 't' || $field_value.type eq 'm'}
{if $field_value.isMultilingual ne "y"}
{if $field_value.options_array[2]}<span class="formunit">{$field_value.options_array[2]}&nbsp;</span>{/if}
<input type="text" name="{$field_value.ins_id}" {if $field_value.options_array[1]}size="{$field_value.options_array[1]}" maxlength="{$field_value.options_array[1]}"{/if} value="{if $input_err}{$field_value.value}{else}{$defaultvalues.$fid|escape}{/if}" />
{if $field_value.options_array[3]}<span class="formunit">&nbsp;{$field_value.options_array[3]}</span>{/if}
{else}
<table>
    {foreach from=$field_value.lingualvalue item=ling}
    <tr><td>{$ling.lang}</td><td>
            {if $field_value.options_array[2]}<span class="formunit">{$field_value.options_array[2]}&nbsp;</span>{/if}
        <input type="text" name="{$field_value.ins_id}_{$ling.lang}" value="{$ling.value|escape}" {if $field_value.options_array[1]}size="{$field_value.options_array[1]}" maxlength="{$field_value.options_array[1]}"{/if} />
        {if $field_value.options_array[3]}<span class="formunit">&nbsp;{$field_value.options_array[3]}</span>{/if}
    </td></tr>
    {/foreach}
</table>
{/if}


{* -------------------- numeric field -------------------- *}
{elseif $field_value.type eq 'n'}
{if $field_value.options_array[2]}<span class="formunit">{$field_value.options_array[2]}&nbsp;</span>{/if}
<input type="text" name="{$field_value.ins_id}" {if $field_value.options_array[1]}size="{$field_value.options_array[1]}" maxlength="{$field_value.options_array[1]}"{/if} value="{if $input_err}{$field_value.value}{else}{$defaultvalues.$fid|escape}{/if}" />
{if $field_value.options_array[3]}<span class="formunit">&nbsp;{$field_value.options_array[3]}</span>{/if}

{* -------------------- static text -------------------- *}
{elseif $field_value.type eq 'S'}
	{if $field_value.description}
    {if $field_value.options_array[0] eq 1}
      {wiki}{$field_value.description|escape}{/wiki}
    {else}
      {$field_value.description|escape|nl2br}
    {/if}
	{/if}

{* -------------------- textarea -------------------- *}
{elseif $field_value.type eq 'a'}
{if $field_value.description}
<em>{$field_value.description|escape|nl2br}</em><br />
{/if}
{if $field_value.isMultilingual ne "y"}
  {if $prefs.quicktags_over_textarea eq 'y' and $field_value.options_array[0] eq 1}
    {include file=tiki-edit_help_tool.tpl qtnum=$fid area_name=`$field_value.ins_id`}
  {/if}
<textarea id="{$field_value.ins_id}" name="{$field_value.ins_id}" cols="{if $field_value.options_array[1] gt 1}{$field_value.options_array[1]}{else}50{/if}" 
rows="{if $field_value.options_array[2] gt 1}{$field_value.options_array[2]}{else}4{/if}">{if $input_err}{$field_value.value}{else}{$defaultvalues.$fid|escape}{/if}</textarea>
{else}
<table>
{foreach from=$field_value.lingualvalue item=ling}
    <tr>
      <td>{$ling.lang}</td>
      <td>
        {if $prefs.quicktags_over_textarea eq 'y' and $field_value.options_array[0] eq 1}
          {include file=tiki-edit_help_tool.tpl qtnum=$fid area_name=ins_`$field_value.id`_`$ling.lang`}
        {/if}
        <textarea name="ins_{$field_value.id}_{$ling.lang}" id="area_{$field_value.id}" cols="{if $field_value.options_array[1] gt 1}{$field_value.options_array[1]}{else}50{/if}" rows="{if $field_value.options_array[2] gt 1}{$field_value.options_array[2]}{else}4{/if}">{$ling.value|escape}</textarea>
      </td>
    </tr>
{/foreach}
</table>
{/if}

{* -------------------- date and time -------------------- *}
{elseif $field_value.type eq 'f'}
{html_select_date prefix=$field_value.ins_id time=$field_value.value start_year=$prefs.calendar_start_year end_year=$prefs.calendar_end_year field_order=$prefs.display_field_order}{if $field_value.options_array[0] ne 'd'} {tr}at{/tr} {html_select_time prefix=$field_value.ins_id time=$field_value.value display_seconds=false}{/if}

{* -------------------- drop down -------------------- *}
{elseif $field_value.type eq 'd' or $field_value.type eq 'D'}
<select name="{$field_value.ins_id}" {if $listfields.$fid.http_request}onchange="selectValues('trackerIdList={$listfields.$fid.http_request[0]}&amp;fieldlist={$listfields.$fid.http_request[3]}&amp;filterfield={$listfields.$fid.http_request[1]}&amp;status={$listfields.$fid.http_request[4]}&amp;mandatory={$listfields.$fid.http_request[6]}&amp;filtervalue='+escape(this.value),'{$listfields.$fid.http_request[5]}')"{/if}>
{assign var=otherValue value=$field_value.value}
<option value="">&nbsp;</option>
{section name=jx loop=$field_value.options_array}
<option value="{$field_value.options_array[jx]|escape}" {if $input_err}{if $field_value.value eq $field_value.options_array[jx]}{assign var=otherValue value=''}selected="selected"{/if}{elseif $defaultvalues.$fid eq $field_value.options_array[jx] or $field_value.defaultvalue eq $field_value.options_array[jx]}selected="selected"{/if}>{$field_value.options_array[jx]|tr_if}</option>
{/section}
</select>
{if $field_value.type eq 'D'}
<br />{tr}Other:{/tr} <input type="text" name="{$field_value.ins_id}_other" value="{$otherValue|escape}" />
{/if}

{* -------------------- radio buttons -------------------- *}
{elseif $field_value.type eq 'R'}
{section name=jx loop=$field_value.options_array}
<input type="radio" name="{$field_value.ins_id}" value="{$field_value.options_array[jx]|escape}" {if $input_err}{if $field_value.value eq $field_value.options_array[jx]}checked="checked"{/if}{elseif $defaultvalues.$fid eq $field_value.options_array[jx] or $field_value.defaultvalue eq $field_value.options_array[jx]}checked="checked"{/if} />{$field_value.options_array[jx]}
{/section}

{* -------------------- checkbox -------------------- *}
{elseif $field_value.type eq 'c'}
<input type="checkbox" name="{$field_value.ins_id}" {if $input_err}{if $field_value.value eq 'y'}checked="checked"{/if}{elseif $defaultvalues.$fid eq 'y'}checked="checked"{/if}/>

{* -------------------- jscalendar ------------------- *}
{elseif $field_value.type eq 'j'}
{if $field_value.options_array[0] eq 'd'}
{jscalendar date=$now id=$field_value.ins_id fieldname=$field_value.ins_id showtime="n"}
{else}
{jscalendar date=$now id=$field_value.ins_id fieldname=$field_value.ins_id showtime="y"}
{/if}

{* -------------------- item link -------------------- *}
{elseif $field_value.type eq 'r'}
<select name="{$field_value.ins_id}" {if $listfields.$fid.http_request}onchange="selectValues('trackerIdList={$listfields.$fid.http_request[0]}&amp;fieldlist={$listfields.$fid.http_request[3]}&amp;filterfield={$listfields.$fid.http_request[1]}&amp;status={$listfields.$fid.http_request[4]}&amp;mandatory={$listfields.$fid.http_request[6]}&amp;filtervalue='+escape(this.value),'{$listfields.$fid.http_request[5]}')"{/if}>
{if $field_value.isMandatory ne 'y'}<option value=""></option>{/if}
{foreach key=id item=label from=$field_value.list}
<option value="{$label|escape}" {if $input_err}{if $field_value.value eq $label}selected="selected"{/if}{elseif $defaultvalue eq $label}selected="selected"{/if}>{if $field_value.listdisplay.$id eq ''}{$label}{else}{$field_value.listdisplay.$id}{/if}</option>
{/foreach}
</select>

{* -------------------- dynamic list -------------------- *}
{elseif $field_value.type eq 'w'}
<select name="{$field_value.ins_id}" {if $listfields.$fid.http_request}onchange="selectValues('trackerIdList={$listfields.$fid.http_request[0]}&amp;fieldlist={$listfields.$fid.http_request[3]}&amp;filterfield={$listfields.$fid.http_request[1]}&amp;status={$listfields.$fid.http_request[4]}&amp;mandatory={$listfields.$fid.http_request[6]}&amp;filtervalue='+escape(this.value),'{$listfields.$fid.http_request[5]}')"{/if}>
</select>


{* -------------------- User subscription -------------------- *}
{elseif $field_value.type eq 'U'}
<input type="text" name="{$field_value.ins_id}" value="{$field_value.value}" />


{* -------------------- Google Map -------------------- *}
{elseif $field_value.type eq 'G'}
<input type="text" name="{$field_value.ins_id}" value="{$field_value.value}" />
<br />{tr}Format : x,y,zoom - You can use Google Map Locator in the item view script.{/tr}

{* -------------------- country selector -------------------- *}
{elseif $field_value.type eq 'y'}
<select name="{$field_value.ins_id}">
<option value=""{if $field_value.value eq '' or $field_value.value eq 'None'} selected="selected"{/if}>&nbsp;</option>
{sortlinks}
{foreach item=flag from=$field_value.flags}
{if $flag ne 'None' and ( ! isset($field_value.itemChoices) || $field_value.itemChoices|@count eq 0 || in_array($flag, $field_value.itemChoices) )}
{capture name=flag}
{tr}{$flag}{/tr}
{/capture}
<option value="{$flag|escape}" {if $input_err}{if $field_value.value eq $flag}selected="selected"{/if}{elseif $flag eq $field_value.defaultvalue}selected="selected"{/if}{if $field_value.options_array[0] ne '1'} style="background-image:url('img/flags/{$flag}.gif');background-repeat:no-repeat;padding-left:25px;padding-bottom:3px;"{/if}>{$smarty.capture.flag|replace:'_':' '}</option>
{/if}
{/foreach}
{/sortlinks}
</select>

{/if}
{if $field_value.type ne 'a' and $field_value.type ne 'S'}
{if $field_value.description}
<br /><em>{$field_value.description|escape}</em>
{/if}
{/if}
</td>
{if (($field_value.type eq 'c' or $field_value.type eq 't' or $field_value.type eq 'n') and $field_value.options_array[0]) eq '1' and $stick ne 'y'}
{assign var=stick value="y"}
{else}
</tr>{assign var=stick value="n"}
{/if}
{/if}
{/if}
{/foreach}

{* -------------------- antibot code -------------------- *}
{if $prefs.feature_antibot eq 'y' && $user eq ''}
{include file="antibot.tpl"}
{/if}

<tr class="formcolor"><td class="formlabel">&nbsp;</td><td colspan="3" class="formcontent">
<input type="submit" name="save" value="{tr}Save{/tr}" /> <input type="checkbox" name="viewitem"/> {tr}View inserted item{/tr}</td></tr>
</table>
</form>
<br /><em>{tr}Fields marked with a * are mandatory.{/tr}</em>
</div>
{/if}
{foreach from=$fields key=ix item=field_value}
{assign var=fid value=$field_value.fieldId}
{if $listfields.$fid.http_request}
<script type="text/javascript">
selectValues('trackerIdList={$listfields.$fid.http_request[0]}&fieldlist={$listfields.$fid.http_request[3]}&filterfield={$listfields.$fid.http_request[1]}&status={$listfields.$fid.http_request[4]}&mandatory={$listfields.$fid.http_request[6]}','{$listfields.$fid.http_request[5]}','{$field_value.ins_id}')
</script>
{/if}
{/foreach}
