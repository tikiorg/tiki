{* $Id$ *}
<script type="text/javascript" src="lib/trackers/dynamic_list.js"></script>

{title help="trackers"}{tr}Tracker Item:{/tr} {$tracker_info.name|escape}{/title}

{* --------- navigation ------ *}
<div class="navbar">
	 {if $prefs.feature_group_watches eq 'y' and ( $tiki_p_admin_users eq 'y' or $tiki_p_admin eq 'y' )}
	 	 <a href="tiki-object_watches.php?objectId={$itemId|escape:"url"}&amp;watch_event=tracker_item_modified&amp;objectType=tracker+{$trackerId}&amp;objectName={$tracker_info.name|escape:"url"}&amp;objectHref={'tiki-view_tracker_item.php?trackerId='|cat:$trackerId|cat:'&itemId='|cat:$itemId|escape:"url"}" class="icon">{icon _id='eye_group' alt="{tr}Group Monitor{/tr}" align='right' hspace='1'}</a>
	{/if}
  {if $prefs.feature_user_watches eq 'y' and $tiki_p_watch_trackers eq 'y'}
    {if $user_watching_tracker ne 'y'}
      <a href="tiki-view_tracker_item.php?trackerId={$trackerId}&amp;itemId={$itemId}&amp;watch=add" title="{tr}Monitor{/tr}">{icon _id='eye' align="right" hspace="1" alt="{tr}Monitor{/tr}"}</a>
    {else}
      <a href="tiki-view_tracker_item.php?trackerId={$trackerId}&amp;itemId={$itemId}&amp;watch=stop" title="{tr}Stop Monitor{/tr}">{icon _id='no_eye' align="right" hspace="1" alt="{tr}Stop Monitor{/tr}"}</a>
    {/if}
  {/if}
	{self_link print='y'}{icon _id='printer' align='right' hspace='1' alt="{tr}Print{/tr}"}{/self_link}

  {if $tiki_p_list_trackers eq 'y' or $tiki_p_admin_trackers eq 'y'}
		{button href="tiki-list_trackers.php" _text="{tr}List Trackers{/tr}"}
  {/if}

  {if $tiki_p_view_trackers eq 'y'}
		{button href="tiki-view_tracker.php?trackerId=$trackerId" _auto_args="status,sort_mode" _text="{tr}View This Tracker's Items{/tr}"}
  {/if}

  {if $tiki_p_admin_trackers eq 'y'}
    &nbsp;&nbsp;
		{button href="tiki-admin_trackers.php" _text="{tr}Admin Trackers{/tr}"}
		{button href="tiki-admin_trackers.php?trackerId=$trackerId&show=mod" _text="{tr}Edit This Tracker{/tr}"}
		{button href="tiki-admin_tracker_fields.php?trackerId=$trackerId" _text="{tr}Edit Fields{/tr}"}
  {/if}
</div>

<div class="categbar" align="right">
	{if $user and $prefs.feature_user_watches eq 'y'}
		{if $category_watched eq 'y'}
			{tr}Watched by categories:{/tr}
			{section name=i loop=$watching_categories}
				<a href="tiki-browse_categories.php?parentId={$watching_categories[i].categId}">{$watching_categories[i].name|escape}</a>&nbsp;
			{/section}
		{/if}
	{/if}
</div>

{* ------- return/next/previous tab --- *}
{if $tiki_p_view_trackers eq 'y'}
{pagination_links cant=$cant offset=$offset reloff=$smarty.request.reloff itemname="{tr}Item{/tr}"}
	{* Do not specify an itemId in URL used for pagination, because it will use the specified itemId instead of moving to another item *}
	{$smarty.server.php_self}?{query itemId=NULL trackerId=$trackerId}
{/pagination_links}
{/if}

{include file='tracker_error.tpl'}

{tabset name='tabs_view_tracker_item'}

{tab name="{tr}View{/tr}"}
{* --- tab with view ------------------------------------------------------------------------- *}
{if empty($tracker_info.viewItemPretty)}
<h2>{tr}View Item{/tr}</h2>
<table class="formcolor">
{if $tracker_info.showStatus eq 'y' or ($tracker_info.showStatusAdminOnly eq 'y' and $tiki_p_admin_trackers eq 'y')}
  {assign var=ustatus value=$info.status|default:"p"}
  <tr>
    <td class="formlabel">{tr}Status{/tr}</td><td>{$status_types.$ustatus.label}</td>
    <td colspan="2">{html_image file=$status_types.$ustatus.image title=$status_types.$ustatus.label alt=$status_types.$ustatus.label}</td>
  </tr>
{/if}
{assign var=stick value="n"}

{foreach from=$ins_fields key=ix item=cur_field}
  {if ($cur_field.isHidden ne 'y' or $tiki_p_admin_trackers eq 'y') and !($tracker_info.doNotShowEmptyField eq 'y' and empty($cur_field.value) and empty($cur_field.cat) and empty($cur_field.links) and $cur_field.type ne 'S' and $cur_field.type ne 's' and $cur_field.type ne 'h') and ($cur_field.type ne 'p' or $cur_field.options_array[0] ne 'password') and (empty($cur_field.visibleBy) or in_array($default_group, $cur_field.visibleBy) or $tiki_p_admin_trackers eq 'y')}
	{if $cur_field.type eq 'h'}
		</table>
		<h2>{$cur_field.name|escape}</h2>
		<table class="formcolor">
	{elseif $cur_field.type ne 'x'}
		{if $stick ne 'y'}
			<tr class="field{$cur_field.fieldId}"><td class="formlabel" >
		{else}
			<td class="formlabel right" >
		{/if}
		{$cur_field.name|escape}
		{if ($cur_field.type eq 'l' and $cur_field.options_array[4] eq '1') and $cur_field.tracker_options.oneUserItem ne 'y'}
		  {assign var="fieldopts" value="|"|explode:$cur_field.options_array[2]}
			<br />
			<a href="tiki-view_tracker.php?trackerId={$cur_field.options_array[0]}&amp;filterfield={$cur_field.options_array[1]}&amp;filtervalue={section name=ox loop=$ins_fields}{if $ins_fields[ox].fieldId eq $fieldopts[0]}{$ins_fields[ox].value}{/if}{/section}">{tr}Filter Tracker Items{/tr}</a>
		{/if}
		</td>

		{if ($cur_field.type eq 'c' or $cur_field.type eq 't' or $cur_field.type eq 'n' or $cur_field.type eq 'b') and $cur_field.options_array[0] eq '1'}
			{assign var=stick value="y"}
		{else}
			{assign var=stick value="n"}
		{/if}
		{if $stick eq 'y'}<td class="formcontent">{else}<td colspan="3" class="formcontent">{/if}

		{include file='tracker_item_field_value.tpl' field_value=$cur_field list_mode=n item=$item_info}

		</td>
		{if $stick ne 'y'}</tr>{/if}
	{/if}
  {/if}
{/foreach}
{if $tracker_info.showCreatedView eq 'y'}
	<tr>
		<td class="formlabel">{tr}Created{/tr}</td>
		<td colspan="3" class="formcontent">{$info.created|tiki_long_datetime}{if $tracker_info.showCreatedBy eq 'y'}<br />by {if $prefs.user_show_realnames eq 'y'}{if empty($info.createdBy)}Unknown{else}{$info.createdBy|username}{/if}{else}{if empty($info.createdBy)}Unknown{else}{$info.createdBy}{/if}{/if}{/if}</td>
	</tr>
{/if}
{if $tracker_info.showLastModifView eq 'y'}
	<tr>
		<td class="formlabel">{tr}LastModif{/tr}</td>
		<td colspan="3" class="formcontent">{$info.lastModif|tiki_long_datetime}{if $tracker_info.showLastModifBy eq 'y'}<br />by {if $prefs.user_show_realnames eq 'y'}{if empty($info.lastModifBy)}Unknown{else}{$info.lastModifBy|username}{/if}{else}{if empty($info.lastModifBy)}Unknown{else}{$info.lastModifBy}{/if}{/if}{/if}</td>
	</tr>
{/if}
</table>

{else}
	{include file='tracker_pretty_item.tpl' item=$item_info fields=$ins_fields wiki=$tracker_info.viewItemPretty}
{/if}
{/tab}

{* -------------------------------------------------- tab with comments --- *}
{if $tracker_info.useComments eq 'y' and ($tiki_p_tracker_view_comments ne 'n' or $tiki_p_comment_tracker_items ne 'n') }

{if $tiki_p_tracker_view_comments ne 'n'}
	{assign var=tabcomment_vtrackit value="{tr}Comments{/tr} (`$commentCount`)"}
{else}
	{assign var=tabcomment_vtrackit value="{tr}Comments{/tr}}
{/if}

{tab name=$tabcomment_vtrackit}

{if $tiki_p_comment_tracker_items eq 'y'}
<h2>{tr}Add a Comment{/tr}</h2>
<form action="tiki-view_tracker_item.php" method="post" id="commentform" name="commentform">
<input type="hidden" name="trackerId" value="{$trackerId|escape}" />
<input type="hidden" name="itemId" value="{$itemId|escape}" />
<input type="hidden" name="commentId" value="{$commentId|escape}" />
<table class="formcolor">
<tr><td>{tr}Title:{/tr}</td><td><input type="text" name="comment_title" value="{$comment_title|escape}"/></td></tr>
<tr><td>{tr}Comment:{/tr}</td>
<td><textarea rows="{if empty($rows)}4{else}{$rows}{/if}" cols="{if empty($cols)}50{else}{$cols}{/if}" name="comment_data" id="comment_data">{$comment_data|escape}</textarea>
</td></tr>
{if !$user and $prefs.feature_antibot eq 'y'}
	{include file='antibot.tpl'}
{/if}
<tr><td>&nbsp;</td><td><input type="submit" name="save_comment" value="{tr}Save{/tr}" /></td></tr>
</table>
</form>
{/if}
{if $tiki_p_tracker_view_comments ne 'n'}
<h2>{tr}Comments{/tr}</h2>
{section name=ix loop=$comments}
<div class="commentbloc">
<b>{$comments[ix].title|escape}</b> {if $comments[ix].user}{tr}by{/tr} {$comments[ix].user|userlink}{/if}
  {if $tiki_p_admin_trackers eq 'y'}[<a class="link" href="tiki-view_tracker_item.php?trackerId={$trackerId}&amp;itemId={$itemId}&amp;commentId={$comments[ix].commentId}" title="{tr}Edit{/tr}">{icon _id='page_edit'}</a>|&nbsp;&nbsp;<a class="link" href="tiki-view_tracker_item.php?trackerId={$trackerId}&amp;itemId={$itemId}&amp;remove_comment={$comments[ix].commentId}"
title="{tr}Delete{/tr}">{icon _id='cross' alt="{tr}Delete{/tr}"}</a>&nbsp;&nbsp;]{/if}
<br />
<small>{tr}posted on:{/tr} {$comments[ix].posted|tiki_short_datetime}</small><br />
{$comments[ix].parsed}
<hr />
</div>
{/section}
{/if}
{/tab}
{/if}

{* ---------------------------------------- tab with attachements --- *}
{if $tracker_info.useAttachments eq 'y'}
	{tab name="{tr}Attachments{/tr} (`$attCount`)"}
		{include file='attachments_tracker.tpl'}
	{/tab}
{/if}

{* --------------------------------------------------------------- tab with edit --- *}
{if ($tiki_p_modify_tracker_items eq 'y' and $item_info.status ne 'p' and $item_info.status ne 'c') or ($tiki_p_modify_tracker_items_pending eq 'y' and $item_info.status eq 'p') or ($tiki_p_modify_tracker_items_closed eq 'y' and $item_info.status eq 'c')or $special}
{tab name="{tr}Edit/Delete{/tr}"}
<h2>{tr}Edit Item{/tr}</h2>

{include file="tracker_validator.tpl"}

{if  $tiki_p_admin_trackers eq 'y' and !empty($trackers)}	
	<form>
	<input type="hidden" name="itemId" value="{$itemId}" />
	<select name="moveto">
		{foreach from=$trackers item=tracker}
		{if $tracker.trackerId ne $trackerId}
			<option value="{$tracker.trackerId}">{$tracker.name|escape}</option>
		{/if}
		{/foreach}
	</select>
	<input type="submit" name="go" value="{tr}Move to another tracker{/tr}" />
	</form>
{/if}

<form enctype="multipart/form-data" action="tiki-view_tracker_item.php" method="post" id="editItemForm">
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
{if $cant}<input type="hidden" name="cant" value="{$cant}" />{/if}

{remarksbox type="note"}<em class='mandatory_note'>{tr}Fields marked with a * are mandatory.{/tr}</em>{/remarksbox}
<table class="formcolor">
<tr>
<td class="formcontent">&nbsp;</td>
<td colspan="3" class="formcontent">
{if count($fields) >= 5}
<input type="submit" name="save" value="{tr}Save{/tr}" onclick="needToConfirm=false" />
{* --------------------------- to return to tracker list after saving --------- *}
{if $tiki_p_view_trackers eq 'y'}
<input type="submit" name="save_return" value="{tr}Save{/tr} &amp; {tr}Back to Items list{/tr}" onclick="needToConfirm=false" />
{if $tiki_p_admin_trackers eq 'y' or $tiki_p_modify_tracker_items eq 'y'}<a class="link" href="tiki-view_tracker.php?trackerId={$trackerId}&amp;remove={$itemId}" title="{tr}Delete{/tr}">{icon _id='cross' alt="{tr}Delete{/tr}"}</a>{/if}
{/if}
{/if}
</td></tr>
{* ------------------- *}
{if $tracker_info.showStatus eq 'y' or ($tracker_info.showStatusAdminOnly eq 'y' and $tiki_p_admin_trackers eq 'y')}
<tr>
<td class="formlabel">{tr}Status{/tr}</td>
<td class="formcontent">
{include file='tracker_status_input.tpl' item=$item_info form_status=edstatus}
</td></tr>
{/if}

{if empty($tracker_info.editItemPretty)}

{foreach from=$ins_fields key=ix item=cur_field}
{if ($cur_field.isHidden eq 'n' or $tiki_p_admin_trackers eq 'y' or $cur_field.isHidden eq 'c') and (empty($cur_field.visibleBy) or in_array($default_group, $cur_field.visibleBy) or $tiki_p_admin_trackers eq 'y')  and ($cur_field.type ne 'A' or $tiki_p_attach_trackers eq 'y') and ($cur_field.type ne '*')}

{if $cur_field.type eq 's' and ($cur_field.name eq "Rating" or $cur_field.name eq tra("Rating")) and ($tiki_p_tracker_view_ratings eq 'y' || $tiki_p_tracker_vote_ratings eq 'y') and (empty($cur_field.visibleBy) or in_array($default_group, $cur_field.visibleBy) or $tiki_p_admin_trackers eq 'y')}
	<tr>
		<td>
			{$cur_field.name}
		</td>
			{if $tiki_p_tracker_view_ratings eq 'y' and $tiki_p_tracker_vote_ratings neq 'y'}
				<td>
					{$cur_field.value}
				</td>
			{elseif $tiki_p_tracker_vote_ratings eq 'y'}
				<td>
					{include file='tracker_item_field_input.tpl' field_value=$cur_field item=$item_info}
				</td>
			{/if}
		</tr>
{/if}

{if $cur_field.type ne 'x' and $cur_field.type ne 's'}
{if $cur_field.type eq 'h'}
</table>
<h2>{$cur_field.name}</h2>
<table class="formcolor">
{else}
{if ($cur_field.type eq 'c' or $cur_field.type eq 't' or $cur_field.type eq 'n' or $cur_field.type eq 'b') and $cur_field.options_array[0] eq '1'}
<tr><td class="formlabel" >{$cur_field.name}{if $cur_field.isMandatory eq 'y'}<em class='mandatory_star'> *</em>{/if}</td><td >
{elseif $stick eq 'y'}
<td class="formlabel right" >{$cur_field.name}{if $cur_field.isMandatory eq 'y'}<em class='mandatory_star'> *</em>{/if}</td><td >
{else}
<tr><td class="formlabel" >{$cur_field.name}{if $cur_field.isMandatory eq 'y'}<em class='mandatory_star'> *</em>{/if}
</td><td colspan="3" class="formcontent" >
{/if}
{/if}

{if !empty($cur_field.editableBy) and !in_array($default_group, $cur_field.editableBy) and $tiki_p_admin_trackers ne 'y'}
	{include file='tracker_item_field_value.tpl' field_value=$cur_field}
{elseif $cur_field.type eq 'N'}
	{include file='tracker_item_field_value.tpl' field_value=$cur_field}
{elseif $cur_field.type eq 'p'}
	{if $user ne $itemUser}
		<input type="hidden" name="ins_{$cur_field.id}" value="{$cur_field.value|escape}" /> 
		{include file='tracker_item_field_value.tpl' field_value=$cur_field}
	{else}
		{include file='tracker_item_field_input.tpl' field_value=$cur_field}
		{if $cur_field.options_array[0] == 'password'}<br /><i>Let empty to not change it</i>{/if}
	{/if}
{elseif $cur_field.type eq 'A' or $cur_field.type eq 'm' or $cur_field.type eq 'L'}
	{include file='tracker_item_field_input.tpl' field_value=$cur_field}

{elseif $cur_field.type eq 'u'}
{if !$cur_field.options_array[0] or $tiki_p_admin_trackers eq 'y'}

	{if $prefs.javascript_enabled eq 'y' and $prefs.feature_jquery_autocomplete eq 'y' and $users|@count > $prefs.user_selector_threshold and $cur_field.isMandatory ne 'y'}
{* this is jquery entry field for performance enhancement - since it allows blank entry it can't be used for mandatory user selection. *}
<input id="user_selector_{$cur_field.id}" type="text" size="20" name="ins_{$cur_field.id}" value="{if $cur_field.options_array[0] eq '2'}{$user}{else}{$cur_field.value}{/if}" />
{if $prefs.user_selector_realnames_tracker == 'y'}
{jq}
$("#user_selector_{{$cur_field.id}}").tiki("autocomplete", "userrealname", {mustMatch: true});
{/jq}
{else}
{jq}
$("#user_selector_{{$cur_field.id}}").tiki("autocomplete", "username", {mustMatch: true});
{/jq}
{/if}


	{else}

<select name="ins_{$cur_field.id}" {if $cur_field.http_request}onchange="selectValues('trackerIdList={$cur_field.http_request[0]}&amp;fieldlist={$cur_field.http_request[3]}&amp;filterfield={$cur_field.http_request[1]}&amp;status={$cur_field.http_request[4]}&amp;mandatory={$cur_field.http_request[6]}&amp;filtervalue='+escape(this.value),'{$cur_field.http_request[5]}')"{/if}>
{if $cur_field.isMandatory ne 'y'}<option value="">{tr}None{/tr}</option>{/if}
{foreach key=id item=one from=$users}
{if ( ! isset($cur_field.itemChoices) || $cur_field.itemChoices|@count eq 0 || in_array($one, $cur_field.itemChoices) ) }
<option value="{$one|escape}" {if ($cur_field.options_array[0] eq '2' and $one eq $user) or ($cur_field.options_array[0] ne '2' and $cur_field.value eq $one) or ($cur_field.isMandatory eq 'y' and empty($cur_field.value) and $one eq $user)}selected="selected"{/if}>{$one|username}</option>
{/if}
{/foreach}
</select>

	{/if}

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
<div style="clear:both"><div style="float:right;text-align:right"><a href="tiki-view_tracker_item.php?trackerId={$cur_field.trackerId}&amp;itemId={$tid}" class="link" title="{tr}View Item{/tr}">{icon _id='magnifier' alt="{tr}View Item{/tr}"}</a></div>
<a href="tiki-view_tracker_item.php?trackerId={$cur_field.trackerId}&amp;itemId={$tid}" class="link" title="{tr}View Item{/tr}">{if $tlabel}{$tlabel}{else}&nbsp;{/if}</a></div>
{/foreach}
{if $tiki_p_create_tracker_items eq 'y' and !(count($cur_field.links) >= 1 and $cur_field.tracker_options.oneUserItem eq 'y')}
<div style="clear:both;text-align:right;"><a href="tiki-view_tracker.php?trackerId={$cur_field.options_array[0]}&amp;vals%5B{$cur_field.options_array[1]}%5D=
{assign var="fieldopts" value="|"|explode:$cur_field.options_array[2]}
{section name=ox loop=$ins_fields}
{if $ins_fields[ox].fieldId eq $fieldopts[0]}
{$ins_fields[ox].value}
{/if}
{/section}
">{tr}Insert New Item{/tr}
</div>
{/if}

{elseif $cur_field.type eq 'e'}
{if !empty($cur_field.options_array[2]) && ($cur_field.options_array[2] eq '1' or $cur_field.options_array[2] eq 'y')}
	{select_all checkbox_names=ins_cat_`$cur_field.fieldId`[] label="{tr}Select All{/tr}"}
{/if}
{assign var=fca value=$cur_field.options_array[0]}
{if $cur_field.options_array[1] eq 'm' or $cur_field.options_array[1] eq 'd'}
	<select name="ins_cat_{$cur_field.fieldId}[]"{if $cur_field.options_array[1] eq 'm'} multiple="multiple"{/if}>
	{if $cur_field.isMandatory ne 'y'}<option value=""></option>{/if}
	{foreach key=ku item=iu from=$cur_field.$fca name=foreache}
	{assign var=fcat value=$iu.categId }
	<option value="{$iu.categId}"{if $cur_field.cat.$fcat eq 'y'} selected="selected"{/if}>{$iu.categpath|escape}</option>
	{/foreach}
	</select>
{else}
<table width="100%"><tr>{cycle name="2_$fca" values=",</tr><tr>" advance=false}
{foreach key=ku item=iu from=$cur_field.$fca name=foreache}
{assign var=fcat value=$iu.categId }
<td width="50%" style="white_space:nowrap;"><input type={if $cur_field.options_array[1] eq "radio"}"radio"{else}"checkbox"{/if} name="ins_cat_{$cur_field.fieldId}[]" value="{$fcat}" {if $cur_field.cat.$fcat eq 'y'}checked="checked"{/if}/>{$iu.name|escape}</td>
{if !$smarty.foreach.foreache.last}{cycle name="2_$fca"}{else}{if $cur_field.$fca|@count%2}<td></td>{/if}</tr>{/if}
{/foreach}
</table>
{/if}

{elseif $cur_field.type eq 't'}
	{include file='tracker_item_field_input.tpl' field_value=$cur_field item=$item_info}

{elseif $cur_field.type eq 'k'}
        <input type="text" id="page_selector_{$cur_field.id}" name="ins_{$cur_field.id}" value="{$cur_field.value|escape}" {if $cur_field.options_array[1]}size="{$cur_field.options_array[1]}"{/if} />
        {if $prefs.javascript_enabled eq 'y' and $prefs.feature_jquery_autocomplete eq 'y' and $cur_field.isMandatory ne 'y'} {* since autocomplete allows blank entry it can't be used for mandatory selection. *}
			{jq}
			$("#page_selector_{{$cur_field.id}}").tiki("autocomplete", "pagename" );
			{/jq}
        {/if}
        
{elseif $cur_field.type eq 'n' or $cur_field.type eq 'b'}
	{if $cur_field.options_array[2]}
		<span class="formunit">{$cur_field.options_array[2]}&nbsp;</span>
	{/if}
	<input type="text" class="numeric" name="ins_{$cur_field.id}" value="{$cur_field.value|escape}" 
		{if $cur_field.options_array[1]}size="{$cur_field.options_array[1]}" maxlength="{$cur_field.options_array[1]}"{/if} />
	{if $cur_field.options_array[3]}
		<span class="formunit">&nbsp;{$cur_field.options_array[3]}</span>
	{/if}

{elseif $cur_field.type eq 'q'}
	<input type="hidden" name="ins_{$cur_field.id}" value="{$cur_field.value|escape}" size="6" maxlength="6" />
	{$cur_field.value|escape}

{* -------------------- static text -------------------- *}
{elseif $cur_field.type eq 'S'}
	{if $cur_field.description}
		{if $cur_field.options_array[0] eq 1}
			{wiki}{$cur_field.description}{/wiki}
		{else}
			{$cur_field.description|escape|nl2br}
		{/if}
	{/if}

{* -------------------- textarea -------------------- *}
{elseif $cur_field.type eq 'a'}
{include file='tracker_item_field_input.tpl' field_value=$cur_field}

{* --------------------- date ------------------------ *}
{elseif $cur_field.type eq 'f'}
{include file='tracker_item_field_input.tpl' field_value=$cur_field}

{elseif $cur_field.type eq 'r'}
{include file='tracker_item_field_input.tpl' field_value=$cur_field item=$item_info}

{elseif $cur_field.type eq 'w'}
{include file='tracker_item_field_input.tpl' field_value=$cur_field item=$item_info}

{elseif $cur_field.type eq 'd' or $cur_field.type eq 'D'
or $cur_field.type eq 'R'
or $cur_field.type eq 'i'}
{include file='tracker_item_field_input.tpl' field_value=$cur_field item=$item_info}

{elseif $cur_field.type eq 'c'}
<input type="checkbox" name="ins_{$cur_field.id}" {if $cur_field.value eq 'y'}checked="checked"{/if}/>

{* -------------------- country selector -------------------- *}
{elseif $cur_field.type eq 'y'}
{include file='tracker_item_field_input.tpl' field_value=$cur_field item=$item_info}

{* -------------------- freetags -------------------- *}
{elseif $cur_field.type eq 'F'}
{include file='tracker_item_field_input.tpl' field_value=$cur_field item=$item_info}

{* -------------------- Multimedia -------------------- *}
{elseif $cur_field.type eq 'M'}
{if ($cur_field.options_array[0] > '2')}
<input type="file" name="ins_{$cur_field.id}" value="{$cur_field.value}" /><br />
{else}
<input type="text" name="ins_{$cur_field.id}" value="{$cur_field.value}" /><br />


	{assign var='Height' value=$prefs.MultimediaDefaultHeight}
	{assign var='Length' value=$prefs.MultimediaDefaultLength}

	{if $cur_field.value ne ''}
		{if isset($cur_field.options_array[1]) and $cur_field.options_array[1] ne '' } { assign var=Length value=$cur_field.options_array[1] }{/if}
		{if isset($cur_field.options_array[2]) and $cur_field.options_array[2] ne '' } { assign var=Height value=$cur_field.options_array[2] }{/if}
		{if $ModeVideo eq 'y' } { assign var="Height" value=$Height+$prefs.VideoHeight}{/if}
		{include file='multiplayer.tpl' url=$cur_field.value w=$Length h=$Height video=$ModeVideo}
	{/if}
{/if}
{elseif $cur_field.type eq 'U'}
<input type="text" name="ins_{$cur_field.id}" value="{$cur_field.value}" />

{elseif $cur_field.type eq 'G'}
{include file='tracker_item_field_input.tpl' field_value=$cur_field}

{elseif $cur_field.type eq 'j'}
{include file='tracker_item_field_input.tpl' field_value=$cur_field}
{/if}

{if $cur_field.type ne 'S'}
{if $cur_field.description}
<br />{if $cur_field.descriptionIsParsed eq 'y'}{wiki}{$cur_field.description}{/wiki}{else}<em>{tr}{$cur_field.description|escape}{/tr}</em>{/if}
{/if}
{/if}
</td>
{if (($cur_field.type eq 'c' or $cur_field.type eq 't' or $cur_field.type eq 'n' or $cur_field.type eq 'b') and $cur_field.options_array[0] eq '1') and $stick ne 'y'}
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
<table class="formcolor">
<tr><td>{$cur_field.name}</td><td><input type="submit" name="trck_act" value="{$cur_field.options_array[0]|escape}" /></td><tr>
</table>
</form>
{/capture}
{assign var=trkact value=$trkact|cat:$smarty.capture.trkaction}
{/if}

{/if}
{/foreach}

{else}
<tr>
	<td colspan="4">
		{wikiplugin _name=tracker trackerId=$trackerId itemId=$itemId view=page wiki=$tracker_info.editItemPretty formtag='n'}{/wikiplugin}
	</td>
</tr>
{/if}

{if $groupforalert ne ''}

<tr>
<td>{tr}Choose users to alert{/tr}</td>
<td>
{section name=idx loop=$listusertoalert}
{if $showeachuser eq '' }
<input type="hidden"  name="listtoalert[]" value="{$listusertoalert[idx].user}">
{else}
<input type="checkbox" name="listtoalert[]" value="{$listusertoalert[idx].user}"> {$listusertoalert[idx].user}
{/if}
{/section}
</td>
</tr>
{/if}

{* -------------------- antibot code -------------------- *}
{if $prefs.feature_antibot eq 'y' && $user eq ''}
{include file='antibot.tpl'}
{/if}
<tr><td class="formlabel">&nbsp;</td><td colspan="3" class="formcontent">
<input type="submit" name="save" value="{tr}Save{/tr}" onclick="needToConfirm=false" />
{* --------------------------- to return to tracker list after saving --------- *}
{if $tiki_p_view_trackers eq 'y'}
<input type="submit" name="save_return" value="{tr}Save{/tr} &amp; {tr}Back to Items List{/tr}" onclick="needToConfirm=false" />
{/if}
{if $tiki_p_admin_trackers eq 'y' or $tiki_p_modify_tracker_items eq 'y'}
	<a class="link" href="tiki-view_tracker.php?trackerId={$trackerId}&amp;remove={$itemId}" title="{tr}Delete{/tr}">{icon _id='cross' alt="{tr}Delete{/tr}"}</a>
{/if}
{if $item_info.logs.cant}
	<a class="link" href="tiki-tracker_view_history.php?itemId={$itemId}" title="{tr}History{/tr}">{icon _id='database' alt="{tr}History{/tr}"}</a>
{/if}
{if $tiki_p_admin_trackers eq 'y' && empty($trackers)}
	<a class="link" href="tiki-view_tracker_item.php?itemId={$itemId}&moveto" title="{tr}Move to another tracker{/tr}">{icon _id='arrow_right' alt="{tr}Move to another tracker{/tr}"}</a>
{/if}
</td></tr>
</table>
{query _type='form_input' itemId=NULL trackerId=NULL}
{* ------------------- *}
</form>

{if $trkact}
<h2>{tr}Special Operations{/tr}</h2>
{$trkact}
{/if}
{/tab}{*nohighlight - important comment to delimit the zone not to highlight in a search result*}
{/if}

{/tabset}

<br /><br />

{foreach from=$ins_fields key=ix item=cur_field}
{if $cur_field.http_request}
{jq}
selectValues('trackerIdList={{$cur_field.http_request[0]}}&fieldlist={{$cur_field.http_request[3]}}&filterfield={{$cur_field.http_request[1]}}&status={{$cur_field.http_request[4]}}&mandatory={{$cur_field.http_request[6]}}&filtervalue={{$cur_field.http_request[7]|escape:"url"}}&selected={{$cur_field.http_request[8]|escape:"url"}}','{{$cur_field.http_request[5]}}')
{/jq}
{/if}
{/foreach}
