<script type="text/javascript" src="lib/trackers/dynamic_list.js"></script>

{title url="tiki-view_tracker.php?trackerId=$trackerId" adm="trackers"}{tr}Tracker:{/tr} {$tracker_info.name|escape}{/title}

<div class="navbar">
	 {if $prefs.feature_group_watches eq 'y' and ( $tiki_p_admin_users eq 'y' or $tiki_p_admin eq 'y' )}
	 	 <a href="tiki-object_watches.php?objectId={$trackerId|escape:"url"}&amp;watch_event=tracker_modified&amp;objectType=tracker&amp;objectName={$tracker_info.name|escape:"url"}&amp;objectHref={'tiki-view_tracker.php?trackerId='|cat:$trackerId|escape:"url"}" class="icon">{icon _id='eye_group' alt="{tr}Group Monitor{/tr}" align='right' hspace="1"}</a>
	{/if}
	{if $prefs.feature_user_watches eq 'y' and $tiki_p_watch_trackers eq 'y' and $user}
		{if $user_watching_tracker ne 'y'}
			<a href="tiki-view_tracker.php?trackerId={$trackerId}&amp;watch=add" title="{tr}Monitor{/tr}">{icon _id='eye' align="right" hspace="1" alt="{tr}Monitor{/tr}"}</a>
		{else}
			<a href="tiki-view_tracker.php?trackerId={$trackerId}&amp;watch=stop" title="{tr}Stop Monitor{/tr}">{icon _id='no_eye' align="right" hspace="1" alt="{tr}Stop Monitor{/tr}"}</a>
		{/if}
	{/if}

	{if $prefs.feed_tracker eq "y"}
		<a href="tiki-tracker_rss.php?trackerId={$trackerId}">{icon _id='feed' align="right" hspace="1" alt="{tr}RSS feed{/tr}"}</a>
	{/if}

	{include file="tracker_actions.tpl"}
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

{if !empty($tracker_info.description)}
	{if $tracker_info.descriptionIsParsed eq 'y'}
		<div class="description">{wiki}{$tracker_info.description}{/wiki}</div>
	{else}
		<div class="description">{$tracker_info.description|escape|nl2br}</div>
	{/if}
{/if}

{if !empty($mail_msg)}
	<div class="wikitext">{$mail_msg}</div>
{/if}

{include file='tracker_error.tpl'}

{tabset name='tabs_view_tracker'}

{if $tiki_p_view_trackers eq 'y' or (($tracker_info.writerCanModify eq 'y' or $tracker_info.writerGroupCanModify eq 'y') and $user)}
{tab name="{tr}Tracker Items{/tr}"}
{* -------------------------------------------------- tab with list --- *}

{if (($tracker_info.showStatus eq 'y' and $tracker_info.showStatusAdminOnly ne 'y') or $tiki_p_admin_trackers eq 'y') or $show_filters eq 'y'}
{include file='tracker_filter.tpl'}
{/if}

{if $cant_pages > 1 or $initial}{initials_filter_links}{/if}

<div align='left'>{tr}Items found:{/tr} {$item_count}</div>

{if $items|@count ge '1'}
{* ------- list headings --- *}
<form name="checkform" method="post" action="{$smarty.server.PHP_SELF}">
<table class="normal">
<tr>
{if $tracker_info.showStatus eq 'y' or ($tracker_info.showStatusAdminOnly eq 'y' and $tiki_p_admin_trackers eq 'y')}
	<th class="auto" style="width:20px;"></th>
{/if}

{if $tiki_p_admin_trackers eq 'y'}
	<th width="15">
		{select_all checkbox_names='action[]'}
	</th>
{/if}

{foreach from=$fields key=ix item=field_value}
{if ( $field_value.type eq 's' and ($field_value.name eq "Rating" or $field_value.name eq tra("Rating")) and $field_value.isTblVisible eq 'y' ) || ( $field_value.isTblVisible eq 'y' and $field_value.type ne 'x' and $field_value.type ne 'h' and ($field_value.isHidden eq 'n' or $field_value.isHidden eq 'p' or $tiki_p_admin_trackers eq 'y') ) and ($field_value.type ne 'p' or $field_value.options_array[0] ne 'password') and (empty($field_value.visibleBy) or in_array($default_group, $field_value.visibleBy) or $tiki_p_admin_trackers eq 'y')}
	<th class="auto">
		{self_link _sort_arg='sort_mode' _sort_field='f_'|cat:$field_value.fieldId}{$field_value.name|truncate:255:"..."|escape|default:"&nbsp;"}{/self_link}
	</th>
	{if $field_value.type eq 's' and ($field_value.name eq "Rating" or $field_value.name eq tra("Rating"))}
		{assign var=rateFieldId value=$field_value.fieldId}
	{/if}
{/if}
{/foreach}

{if $tracker_info.showCreated eq 'y'}
<th><a href="tiki-view_tracker.php?{if $status}status={$status}&amp;{/if}{if $initial}initial={$initial}&amp;{/if}{if $find}find={$find}&amp;{/if}trackerId={$trackerId}{if $offset}&amp;offset={$offset}{/if}&amp;sort_mode={if
$sort_mode eq 'created_desc'}created_asc{else}created_desc{/if}">{tr}Created{/tr}</a></th>
{/if}
{if $tracker_info.showLastModif eq 'y'}
<th><a href="tiki-view_tracker.php?status={$status}&amp;{if $initial}initial={$initial}&amp;{/if}find={$find}&amp;trackerId={$trackerId}{if $offset}&amp;offset={$offset}{/if}&amp;sort_mode={if $sort_mode eq 'lastModif_desc'}lastModif_asc{else}lastModif_desc{/if}">{tr}lastModif{/tr}</a></th>
{/if}
{if $tracker_info.useComments eq 'y' and ($tracker_info.showComments eq 'y' || $tracker_info.showLastComment eq 'y') and $tiki_p_tracker_view_comments ne 'n'}
<th{if $tracker_info.showLastComment ne 'y'} style="width:5%"{/if}>{tr}Coms{/tr}</th>
{/if}
{if ($tiki_p_tracker_view_attachments eq 'y' or $tiki_p_admin_trackers eq 'y') and $tracker_info.useAttachments eq 'y' and  $tracker_info.showAttachments eq 'y'}
<th style="width:5%">{tr}atts{/tr}</th>
{if $tiki_p_admin_trackers eq 'y'}<th style="width:5%">{tr}dls{/tr}</th>{/if}
{/if}
{if $tiki_p_admin_trackers eq 'y' or $tiki_p_remove_tracker_items eq 'y' or $tiki_p_remove_tracker_items_pending eq 'y' or $tiki_p_remove_tracker_items_closed eq 'y'}
<th style="width:20px">{tr}Action{/tr}</th>
{/if}
</tr>

{* ------- Items loop --- *}
{assign var=itemoff value=0}
{cycle values="odd,even" print=false}
{section name=user loop=$items}
<tr class="{cycle}">
{if $tracker_info.showStatus eq 'y' or ($tracker_info.showStatusAdminOnly eq 'y' and $tiki_p_admin_trackers eq 'y')}
	<td class="icon">
	{assign var=ustatus value=$items[user].status|default:"c"}
	{html_image file=$status_types.$ustatus.image title=$status_types.$ustatus.label alt=$status_types.$ustatus.label}
	</td>
{/if}
{if $tiki_p_admin_trackers eq 'y'}
  <td class="checkbox">
    <input type="checkbox" name="action[]" value='{$items[user].itemId}' style="border:1px;font-size:80%;" />
  </td>
{/if}

{* ------- list values --- *}
{foreach from=$items[user].field_values key=ix item=field_value}
	{if $field_value.isTblVisible eq 'y' and $field_value.type ne 'x' and $field_value.type ne 'h' and ($field_value.isHidden eq 'n' 
		or $field_value.isHidden eq 'p' or $tiki_p_admin_trackers eq 'y') and ($field_value.type ne 'p' or $field_value.options_array[0] ne 'password') 
		and (empty($field_value.visibleBy) or in_array($default_group, $field_value.visibleBy) or $tiki_p_admin_trackers eq 'y')}
		<td class={if $field_value.type eq 'n' or $field_value.type eq 'q' or $field_value.type eq 'b'}"numeric"{else}"auto"{/if}>
			{if $field_value.isMain eq 'y' and ($tiki_p_view_trackers eq 'y' 
			 or ($tiki_p_modify_tracker_items eq 'y' and $item.status ne 'p' and $item.status ne 'c')
			 or ($tiki_p_modify_tracker_items_pending eq 'y' and $item.status eq 'p')
			 or ($tiki_p_modify_tracker_items_closed eq 'y' and $item.status eq 'c')
			 or $tiki_p_comment_tracker_items eq 'y'
			 or ($tracker_info.writerCanModify eq 'y' and $user and $my eq $user) or ($tracker_info.writerCanModify eq 'y' and $group and $ours eq $group))}
				{if !empty($tracker_info.showPopup)}
					{capture name=popup}
						<div class="cbox">
							<table>
								{cycle values="odd,even" print=false}
								{foreach from=$items[user].field_values item=f}
									{if in_array($f.fieldId, $popupFields)}
										 <tr class="{cycle}"><th>{$f.name}</th><td>{include file='tracker_item_field_value.tpl' field_value=$f}</th></tr>
									{/if}
								{/foreach}
							</table>
						</div>
					{/capture}
					{assign var=showpopup value='y'}
				{else}
					{assign var=showpopup value='n'}
				{/if}
			{/if}
			{include file='tracker_item_field_value.tpl' field_value=$field_value list_mode="y" item=$items[user] showlinks="y" reloff=$smarty.section.user.index url=""}
		</td>
	{/if}
{/foreach}

{if $tracker_info.showCreated eq 'y'}
<td class="date">{if $tracker_info.showCreatedFormat}{$items[user].created|tiki_date_format:$tracker_info.showCreatedFormat}{else}{$items[user].created|tiki_short_datetime}{/if}</td>
{/if}
{if $tracker_info.showLastModif eq 'y'}
<td class="date">{if $tracker_info.showLastModifFormat}{$items[user].lastModif|tiki_date_format:$tracker_info.showLastModifFormat}{else}{$items[user].lastModif|tiki_short_datetime}{/if}</td>
{/if}
{if $tracker_info.useComments eq 'y' and ($tracker_info.showComments eq 'y' or $tracker_info.showLastComment eq 'y') and $tiki_p_tracker_view_comments ne 'n'}
<td  style="text-align:center;">{if $tracker_info.showComments eq 'y'}{$items[user].comments}{/if}{if $tracker_info.showComments eq 'y' and $tracker_info.showLastComment eq 'y'}<br />{/if}{if $tracker_info.showLastComment eq 'y' and !empty($items[user].lastComment)}{$items[user].lastComment.user|escape}-{$items[user].lastComment.posted|tiki_short_date}{/if}</td>
{/if}
{if ($tiki_p_tracker_view_attachments eq 'y' or $tiki_p_admin_trackers eq 'y') and $tracker_info.useAttachments eq 'y' and  $tracker_info.showAttachments eq 'y'}
<td class="icon"><a href="tiki-view_tracker_item.php?itemId={$items[user].itemId}&amp;show=att{if $offset}&amp;offset={$offset}{/if}{foreach key=urlkey item=urlval from=$urlquery}{if $urlval}&amp;{$urlkey}={$urlval|escape:"url"}{/if}{/foreach}"
link="{tr}List Attachments{/tr}"><img src="img/icons/folderin.gif" alt="{tr}List Attachments{/tr}"
/></a> {$items[user].attachments}</td>
{if $tiki_p_admin_trackers eq 'y'}<td  style="text-align:center;">{$items[user].hits}</td>{/if}
{/if}
{if $tiki_p_admin_trackers eq 'y' or ($tiki_p_remove_tracker_items eq 'y' and $items[user].status ne 'p' and $items[user].status ne 'c') or ($tiki_p_remove_tracker_items_pending eq 'y' and $items[user].status eq 'p') or ($tiki_p_remove_tracker_items_closed eq 'y' and $items[user].status eq 'c')}
  <td class="action">
    <a class="link" href="tiki-view_tracker.php?status={$status}&amp;trackerId={$trackerId}{if $offset}&amp;offset={$offset}{/if}{if $sort_mode ne ''}&amp;sort_mode={$sort_mode}{/if}&amp;remove={$items[user].itemId}" title="{tr}Delete{/tr}">{icon _id='cross' alt="{tr}Delete{/tr}"}</a>
	{if $tiki_p_admin_trackers eq 'y'}
	<a class="link" href="tiki-tracker_view_history.php?itemId={$items[user].itemId}" title="{tr}History{/tr}">{icon _id='database' alt="{tr}History{/tr}"}</a>
	{/if}
  </td>
{/if}
</tr>
{assign var=itemoff value=$itemoff+1}
{/section}
</table>

{if $tiki_p_admin_trackers eq 'y'}
<div style="text-align:left">
{tr}Perform action with checked:{/tr}
<select name="batchaction">
<option value="">{tr}...{/tr}</option>
<option value="delete">{tr}Delete{/tr}</option>
{if $tracker_info.showStatus eq 'y' or ($tracker_info.showStatusAdminOnly eq 'y' and $tiki_p_admin_trackers eq 'y')}
<option value="c">{tr}Close{/tr}</option>
<option value="o">{tr}Open{/tr}</option>
<option value="p">{tr}Pending{/tr}</option>
{/if}
</select>
<input type="hidden" name="trackerId" value="{$trackerId}" />
<input type="submit" name="act" value="{tr}OK{/tr}" />
</div>
{/if}
</form>
{pagination_links cant=$item_count step=$maxRecords offset=$offset}{/pagination_links}
{/if}
{/tab}
{/if}

{if $tiki_p_create_tracker_items eq 'y'}
{tab name="{tr}Insert New Item{/tr}"}
{* --------------------------------------------------------------------------------- tab with edit --- *}
{jq}
$("#newItemForm").validate({
	{{$validationjs}}
});
{/jq}
<form enctype="multipart/form-data" action="tiki-view_tracker.php" id="newItemForm" method="post">
<input type="hidden" name="trackerId" value="{$trackerId|escape}" />

<h2>{tr}Insert New Item{/tr}</h2>
{remarksbox type="note"}<strong class='mandatory_note'>{tr}Fields marked with a * are mandatory.{/tr}</strong>{/remarksbox}
<table class="formcolor">

{if $tracker_info.showStatus eq 'y' and ($tracker_info.showStatusAdminOnly ne 'y' or $tiki_p_admin_trackers eq 'y')}
<tr><td>{tr}Status{/tr}</td>
<td>
{include file='tracker_status_input.tpl' tracker=$tracker_info form_status=status}
</td></tr>
{/if}

{foreach from=$fields key=ix item=field_value}
{if in_array($field_value.type, TikiLib::lib('trk')->get_rendered_fields())}
	<tr>
		<td>
			{if $field_value.isMandatory eq 'y'}
				{$field_value.name}<em class='mandatory_star'>*</em>
			{else}
				{$field_value.name}
			{/if}
		</td>
		<td>
			{trackerfield field=$field_value}
		</td>
	</tr>
{else}
{assign var=fid value=$field_value.fieldId}
{* -------------------- header and others -------------------- *}
{if $field_value.isHidden eq 'n' or $field_value.isHidden eq 'c'  or $tiki_p_admin_trackers eq 'y'}
{if $field_value.type ne 'x' and $field_value.type ne 'l' and $field_value.type ne 'q' and (($field_value.type ne 'u' and $field_value.type ne 'g' and $field_value.type ne 'I') or !$field_value.options_array[0] or $tiki_p_admin_trackers eq 'y') and (empty($field_value.visibleBy) or in_array($default_group, $field_value.visibleBy) or $tiki_p_admin_trackers eq 'y')and (empty($field_value.editableBy) or in_array($default_group, $field_value.editableBy) or $tiki_p_admin_trackers eq 'y') and ($field_value.type ne 'A' or $tiki_p_attach_trackers eq 'y') and $field_value.type ne 'N' and $field_value.type ne '*' and !($field_value.type eq 's' and $field_value.name eq 'Rating') and $field_value.type ne 'usergroups'}
{if $field_value.type eq 'h'}
{include file='tracker_item_field_value.tpl' inTable='formcolor'}
{else}
{if ($field_value.type eq 'c' or $field_value.type eq 't' or $field_value.type eq 'n' or $field_value.type eq 'b') and $field_value.options_array[0] eq '1'}
<tr><td class="formlabel" >{$field_value.name|escape}{if $field_value.isMandatory eq 'y'}<strong class='mandatory_star'> *</strong>{/if}</td><td class="formcontent">
{elseif $stick eq 'y'}
<td class="formlabel right">{$field_value.name|escape}{if $field_value.isMandatory eq 'y'}<strong class='mandatory_star'> *</strong>{/if}</td><td >
{else}
<tr><td class="formlabel" >{$field_value.name|escape}{if $field_value.isMandatory eq 'y'}<strong class='mandatory_star'> *</strong>{/if}
</td><td colspan="3" class="formcontent" >
{/if}
{/if}

{if $field_value.type eq 'p'}
	{include file='tracker_item_field_input.tpl'}
	{if $field_value.type eq 'p' and $field_value.options_array[0] == 'password'}<br /><i>Leave empty if password is to remain unchanged</i>{/if}
{/if}

{* -------------------- system -------------------- *}
{if $field_value.type eq 's' and ($field_value.name eq "Rating" or $field_value.name eq tra("Rating")) and $tiki_p_tracker_vote_ratings eq 'y'}
	{include file='tracker_item_field_input.tpl'}
{/if}
{* -------------------- system -------------------- *}
{if $field_value.type eq '*'}
	{include file='tracker_item_field_input.tpl'}
{/if}

{* -------------------- user selector -------------------- *}
{if $field_value.type eq 'u'}
{if !$field_value.options_array[0] or empty($field_value.options_array[0]) or $tiki_p_admin_trackers eq 'y'}
{if $prefs.javascript_enabled eq 'y' and $prefs.feature_jquery_autocomplete eq 'y' and $users|@count > $prefs.user_selector_threshold and $field_value.isMandatory ne 'y'}
{* since autocomplete allows blank entry it can't be used for mandatory selection. *}
	<input id="user_selector_{$field_value.fieldId}" type="text" size="20" name="{$field_value.ins_id}" value="{if $field_value.options_array[0] eq '2'}{$user}{else}{$field_value.value}{/if}" />
	{jq}
		$("#user_selector_{{$field_value.fieldId}}").tiki("autocomplete", "username", {mustMatch: true});
	{/jq}
{else}
<select name="{$field_value.ins_id}" {if $listfields.$fid.http_request}onchange="selectValues('trackerIdList={$listfields.$fid.http_request[0]}&amp;fieldlist={$listfields.$fid.http_request[3]}&amp;filterfield={$listfields.$fid.http_request[1]}&amp;status={$listfields.$fid.http_request[4]}&amp;mandatory={$listfields.$fid.http_request[6]}&amp;filtervalue='+escape(this.value),'{$listfields.$fid.http_request[5]}')"{/if}>
<option value="">{tr}None{/tr}</option>
{foreach key=id item=one from=$users}
{if ( ! isset($field_value.itemChoices) || $field_value.itemChoices|@count eq 0 || in_array($one, $field_value.itemChoices) )}
{if $field_value.value}
<option value="{$one|escape}"{if $one eq $field_value.value} selected="selected"{/if}>{$one|username}</option>
{else}
<option value="{$one|escape}"{if $one eq $user and $field_value.options_array[0] ne '2'} selected="selected"{/if}>{$one|username}</option>
{/if}
{/if}
{/foreach}

</select>
{/if}
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

{* -------------------- image -------------------- *}
{elseif $field_value.type eq 'i'}
<input type="file" name="{$field_value.ins_id}" {if $input_err}value="{$field_value.value}"{/if}/>

{* -------------------- page -------------------- *}
{elseif $field_value.type eq 'k'}
{include file='tracker_item_field_input.tpl'}

{* -------------------- text field / email -------------------- *}
{elseif $field_value.type eq 't'}
{include file='tracker_item_field_input.tpl'}

{* -------------------- numeric field -------------------- *}
{elseif $field_value.type eq 'n'}
{include file='tracker_item_field_input.tpl'}

{* -------------------- currency amount -------------------- *}
{elseif $field_value.type eq 'b'}
{include file='tracker_item_field_input.tpl'}

{* -------------------- static text -------------------- *}
{elseif $field_value.type eq 'S'}
{include file='tracker_item_field_input.tpl'}

{* -------------------- email -------------------- *}
{elseif $field_value.type eq 'm'}
{include file='tracker_item_field_input.tpl'}

{* -------------------- url -------------------- *}
{elseif $field_value.type eq 'L'}
{include file='tracker_item_field_input.tpl'}

{* -------------------- textarea -------------------- *}
{elseif $field_value.type eq 'a'}
{include file='tracker_item_field_input.tpl'}

{* -------------------- date and time -------------------- *}
{elseif $field_value.type eq 'f'}
{include file='tracker_item_field_input.tpl'}

{* -------------------- drop down -------------------- *}
{elseif $field_value.type eq 'd' or $field_value.type eq 'D'}
{include file='tracker_item_field_input.tpl'}

{* -------------------- radio buttons -------------------- *}
{elseif $field_value.type eq 'R'}
{include file='tracker_item_field_input.tpl'}

{* -------------------- checkbox -------------------- *}
{elseif $field_value.type eq 'c'}
<input type="checkbox" name="{$field_value.ins_id}" {if $input_err}{if $field_value.value eq 'y'}checked="checked"{/if}{elseif $defaultvalues.$fid eq 'y'}checked="checked"{/if}/>

{* -------------------- jscalendar ------------------- *}
{elseif $field_value.type eq 'j'}
{include file='tracker_item_field_input.tpl'}

{* -------------------- item link -------------------- *}
{elseif $field_value.type eq 'r'}
{include file='tracker_item_field_input.tpl'}

{* -------------------- dynamic list -------------------- *}
{elseif $field_value.type eq 'w'}
{include file='tracker_item_field_input.tpl'}

{* -------------------- User subscription -------------------- *}
{elseif $field_value.type eq 'U'}
<input type="text" name="{$field_value.ins_id}" value="{$field_value.value}" />


{* -------------------- Google Map -------------------- *}
{elseif $field_value.type eq 'G'}
{include file='tracker_item_field_input.tpl'}

{* -------------------- freetags -------------------- *}
{elseif $field_value.type eq 'F'}
{include file='tracker_item_field_input.tpl'}

{* -------------------- country selector -------------------- *}
{elseif $field_value.type eq 'y'}
{include file='tracker_item_field_input.tpl'}
{/if}

{if $field_value.type ne 'S'}
{if $field_value.description}
<br />{if $field_value.descriptionIsParsed eq 'y'}{wiki}{$field_value.description}{/wiki}{else}<em>{$field_value.description|escape}</em>{/if}
{/if}
{/if}
</td>
{if (($field_value.type eq 'c' or $field_value.type eq 't' or $field_value.type eq 'n' or $field_value.type eq 'b') and $field_value.options_array[0]) eq '1' and $stick ne 'y'}
{assign var=stick value="y"}
{else}
</tr>{assign var=stick value="n"}
{/if}
{/if}
{/if}
{/if}
{/foreach}

{* -------------------- antibot code -------------------- *}
{if $prefs.feature_antibot eq 'y' && $user eq ''}
{include file='antibot.tpl' tr_style="formcolor" showmandatory=y}
{/if}

{if $groupforalert ne ''}
{if $showeachuser eq 'y'}
<tr>
<td>{tr}Choose users to alert{/tr}</td>
<td>
{/if}
{section name=idx loop=$listusertoalert}
{if $showeachuser eq 'n'}
<input type="hidden"  name="listtoalert[]" value="{$listusertoalert[idx].user}">
{else}
<input type="checkbox" name="listtoalert[]" value="{$listusertoalert[idx].user}"> {$listusertoalert[idx].user}
{/if}
{/section}
</td>
</tr>
{/if}

{trackerheader level=-1 title='' inTable='formcolor'}

<tr>
	<td class="formlabel">&nbsp;</td>
	<td colspan="3" class="formcontent">
		<input type="submit" name="save" value="{tr}Save{/tr}" onclick="needToConfirm = false;" /> 
		<input type="radio" name="viewitem" value="view" /> {tr}View inserted item{/tr}
		{* --------------------------- to continue inserting items after saving --------- *}
		<input type="radio" name="viewitem" value="new" checked="checked"  /> {tr}Insert new item{/tr}
	</td>
</tr>
</table>
</form>
{/tab}
{/if}

{if $tiki_p_export_tracker eq 'y'}
	{tab name="{tr}Export Tracker Items{/tr}"}
	{* -------------------------------------------------- tab with export --- *}
		{include file='tiki-export_tracker.tpl'}
	{/tab}
{/if}
{/tabset}


{foreach from=$fields key=ix item=field_value}
{assign var=fid value=$field_value.fieldId}
{if $listfields.$fid.http_request}
{jq}
selectValues('trackerIdList={{$listfields.$fid.http_request[0]}}&fieldlist={{$listfields.$fid.http_request[3]}}&filterfield={{$listfields.$fid.http_request[1]}}&status={{$listfields.$fid.http_request[4]}}&mandatory={{$listfields.$fid.http_request[6]}}','{{$listfields.$fid.http_request[5]}}','{{$field_value.ins_id}}')
{/jq}
{/if}
{/foreach}
