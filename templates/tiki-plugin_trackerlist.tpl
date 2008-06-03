{strip}
{* $Id$ *}
{if !empty($popupfields)}{popup_init src="lib/overlib.js"}{/if}
{if $showtitle eq 'y'}<div class="pagetitle">{$tracker_info.name}</div>{/if}
{if $showdesc eq 'y'}<div class="wikitext">{$tracker_info.description}</div>{/if}

{if $cant_pages > 1 or $tr_initial or $showinitials eq 'y'}
<div align="center">
{section name=ini loop=$initials}
{if $tr_initial and $initials[ini] eq $tr_initial}
<span class="button2"><span class="linkbuton">{$initials[ini]|capitalize}</span></span> . 
{else}
{self_link _class="prevnext" tr_initial=$intials[ini]}{$initials[ini]}{/self_link}
{/if}
{/section}
{self_link _class="prevnext" tr_initial=''}{tr}All{/tr}{/self_link}
</div>
{/if}

{if $checkbox && $items|@count gt 0}<form method="post" action="{$checkbox.action}">{/if}

{if empty($tpl)}
<table class="normal wikiplugin_trackerlist">

{if $showfieldname ne 'n' and empty($tpl)}
<tr>
{if $checkbox}<td class="heading">{$checkbox.title}</td>{/if}
{if ($showstatus ne 'n') and ($tracker_info.showStatus eq 'y' or ($tracker_info.showStatusAdminOnly eq 'y' and $perms.tiki_p_admin_trackers eq 'y'))}
	<td class="heading auto" style="width:20px;">&nbsp;</td>
{/if}

{foreach key=jx item=ix from=$fields}
{if $ix.isPublic eq 'y' and ($ix.isHidden eq 'n' or $tiki_p_admin_trackers eq 'y') and $ix.type ne 'x' and $ix.type ne 'h' and in_array($ix.fieldId, $listfields) and ($ix.type ne 'p' or $ix.options_array[0] ne 'password')}
{if $ix.type eq 'l'}
<td class="heading auto field{$ix.fieldId}">{$ix.name|default:"&nbsp;"}</td>
{elseif $ix.type eq 's' and $ix.name eq "Rating"}
{if $perms.tiki_p_tracker_view_ratings eq 'y'}
<td class="heading auto field{$ix.fieldId}"{if $perms.tiki_p_tracker_vote_ratings eq 'y'} colspan="2"{/if}>
{self_link _class="tableheading" _sort_arg='tr_sort_mode' _sort_field='f_'|cat:$ix.fieldId}{$ix.name|default:"&nbsp;"}{/self_link}</td>
{/if}
{else}
<td class="heading auto field{$ix.fieldId}"{if $ix.type eq 's' and $ix.name eq "Rating"} colspan="2"{/if}>
{self_link _class="tableheading" _sort_arg='tr_sort_mode' _sort_field='f_'|cat:$ix.fieldId}{$ix.name|default:"&nbsp;"}{/self_link}
</td>
{/if}
{/if}
{/foreach}
{if $showcreated eq 'y'}
<td class="heading">{self_link _class="tableheading" _sort_arg='tr_sort_mode' _sort_field='created'}{tr}Created{/tr}{/self_link}</td>
{/if}
{if $showlastmodif eq 'y'}
<td class="heading">{self_link _class="tableheading" _sort_arg='tr_sort_mode' _sort_field='lastModif'}{tr}LastModif{/tr}{/self_link}</td>
{/if}
{if $tracker_info.useComments eq 'y' and $tracker_info.showComments eq 'y'}
<td class="heading" width="5%">{tr}Coms{/tr}</td>
{/if}
{if $tracker_info.useAttachments eq 'y' and  $tracker_info.showAttachments eq 'y'}
<td class="heading" width="5%">{tr}atts{/tr}</td>
{/if}
</tr>
{/if}
{/if}

{cycle values="odd,even" print=false}
{section name=user loop=$items}

{* ------- popup ---- *}
{if !empty($popupfields)}
	{capture name=popup}
	<div class="cbox">
	<table>
	{cycle values="odd,even" print=false}
	{foreach from=$items[user].field_values item=f}
		{if in_array($f.fieldId, $popupfields)}
			 <tr><th class="{cycle advance=false}">{$f.name}</th><td class="{cycle}">{include file="tracker_item_field_value.tpl" field_value=$f item=$items[user]}</td></tr>
		{/if}
	{/foreach}
	</table>
	</div>
	{/capture}
	{assign var=showpopup value='y'}
{else}
	{assign var=showpopup value='n'}
{/if}


{if empty($tpl)}

<tr class="{cycle}">
{if $checkbox}<td><input type="checkbox" name="{$checkbox.name}[]" value="{$items[user].field_values[$checkbox.ix].value}" /></td>{/if}
{if ($showstatus ne 'n') and ($tracker_info.showStatus eq 'y' or ($tracker_info.showStatusAdminOnly eq 'y' and $perms.tiki_p_admin_trackers eq 'y'))}<td class="auto" style="width:20px;">
{assign var=ustatus value=$items[user].status|default:"c"}
{html_image file=$status_types.$ustatus.image title=$status_types.$ustatus.label alt=$status_types.$ustatus.label}
</td>
{/if}

{* ------------------------------------ *}
{section name=ix loop=$items[user].field_values}
{if $items[user].field_values[ix].isPublic eq 'y' and ($items[user].field_values[ix].isHidden eq 'n' or ($items[user].field_values[ix].isHidden eq 'c' and $items[user].itemUser eq $user) or $items[user].field_values[ix].isHidden eq 'p' or $tiki_p_admin_trackers eq 'y') and $items[user].field_values[ix].type ne 'x' and $items[user].field_values[ix].type ne 'h' and in_array($items[user].field_values[ix].fieldId, $listfields) and ($items[user].field_values[ix].type ne 'p' or $items[user].field_values[ix].options_array[0] ne 'password')}
<td class="auto">
	{if isset($perms)}
		{include file="tracker_item_field_value.tpl" item=$items[user] field_value=$items[user].field_values[ix] list_mode="y"
		tiki_p_view_trackers=$perms.tiki_p_view_trackers tiki_p_modify_tracker_items=$perms.tiki_p_modify_tracker_items tiki_p_comment_tracker_items=$perms.tiki_p_comment_tracker_items}
	{else}
		{include file="tracker_item_field_value.tpl" item=$items[user] field_value=$items[user].field_values[ix] list_mode="y"}
	{/if}
</td>
{/if}
{/section}
{* ------------------------------------ *}

{if $showcreated eq 'y'}
<td>{if $tracker_info.showCreatedFormat}{$items[user].created|tiki_date_format:$tracker_info.showCreatedFormat}{else}{$items[user].created|tiki_short_datetime}{/if}</td>
{/if}
{if showlastmodif eq 'y'}
<td>{if $tracker_info.showLastModifFormat}{$items[user].lastModif|tiki_date_format:$tracker_info.showLastModifFormat}{else}{$items[user].lastModif|tiki_short_datetime}{/if}</td>
{/if}
{if $tracker_info.useComments eq 'y' and $tracker_info.showComments eq 'y'}
<td  style="text-align:center;">{$items[user].comments}</td>
{/if}
{if $tracker_info.useAttachments eq 'y' and $tracker_info.showAttachments eq 'y'}
<td  style="text-align:center;"><a href="tiki-view_tracker_item.php?trackerId={$trackerId}&amp;itemId={$items[user].itemId}&amp;show=att" 
link="{tr}List Attachments{/tr}"><img src="img/icons/folderin.gif" border="0" alt="{tr}List Attachments{/tr}" 
/></a>{$items[user].attachments}</td>
{/if}
</tr>

{else} {* a pretty tpl *}
{* ------------------------------------ *}
{section name=ix loop=$items[user].field_values}
{if $items[user].field_values[ix].isPublic eq 'y' and ($items[user].field_values[ix].isHidden eq 'n' or ($items[user].field_values[ix].isHidden eq 'c' and $items[user].itemUser eq $user) or $items[user].field_values[ix].isHidden eq 'p' or $tiki_p_admin_trackers eq 'y') and $items[user].field_values[ix].type ne 'x' and $items[user].field_values[ix].type ne 'h' and in_array($items[user].field_values[ix].fieldId, $listfields) and ($items[user].field_values[ix].type ne 'p' or $items[user].field_values[ix].options_array[0] ne 'password')}
{capture name=value}
	{if isset($perms)}
		{include file="tracker_item_field_value.tpl" item=$items[user] field_value=$items[user].field_values[ix] list_mode="y"
		tiki_p_view_trackers=$perms.tiki_p_view_trackers tiki_p_modify_tracker_items=$perms.tiki_p_modify_tracker_items tiki_p_comment_tracker_items=$perms.tiki_p_comment_tracker_items}
	{else}
		{include file="tracker_item_field_value.tpl" item=$items[user] field_value=$items[user].field_values[ix] list_mode="y"}
	{/if}
{/capture}
{set var=f_`$items[user].field_values[ix].fieldId` value=$smarty.capture.value}
{else}
{set var=f_`$items[user].field_values[ix].fieldId` value=''}
{/if}
{/section}
{* ------------------------------------ *}
{include file="$tpl" item=$items[user]}
{/if}
{/section}

{if empty($tpl)}
</table>
{if $items|@count eq 0}
{tr}No records found{/tr}
{elseif $checkbox}
<br />
{if $checkbox.tpl}{include file=$checkbox.tpl}{/if}
<input type="submit" name="{$checkbox.submit}" value="{tr}{$checkbox.title}{/tr}" /></form>
{/if}
{/if}



{if $more eq 'y'}
	<div class="more">
		 <a class="linkbut" href="{if $moreurl}{$moreurl}{else}tiki-view_tracker.php{/if}?trackerId={$trackerId}{if isset($tr_sort_mode)}&amp;sort_mode={$tr_sort_mode}{/if}">{tr}More...{/tr}</a>
	</div>
{else}
{pagination_links cant=$count_item step=$max offset=$tr_offset offset_arg=tr_offset}{/pagination_links}
{/if}
{/strip}
