{strip}
{* $Id$ *}
{* param item, fields, wiki(wiki:page or tpl:tpl), list_mode, perms, default_group, listfields *}
{if !isset($list_mode)}{assign var=list_mode value="n"}{/if}
{foreach from=$fields item=field}
	{if $field.isPublic eq 'y'
	 and ($field.isHidden eq 'n' or ($field.isHidden eq 'c' and $item.itemUser eq $user) or $field.isHidden eq 'p' or $tiki_p_admin_trackers eq 'y')
	  and $field.type ne 'x'
	  and $field.type ne 'h'
	  and (empty($listfields) or in_array($field.fieldId, $listfields)) 
	  and ($field.type ne 'p' or $field.options_array[0] ne 'password') 
	  and (empty($field.visibleBy) or in_array($default_group, $field.visibleBy) or $tiki_p_admin_trackers eq 'y')
	  }
		{capture name=value}
			{if isset($perms)}
				{include file='tracker_item_field_value.tpl' item=$item field_value=$field list_mode=$list_mode
					tiki_p_view_trackers=$perms.tiki_p_view_trackers tiki_p_modify_tracker_items=$perms.tiki_p_modify_tracker_items tiki_p_modify_tracker_items_pending=$perms.tiki_p_modify_tracker_items_pending tiki_p_modify_tracker_items_closed=$perms.tiki_p_modify_tracker_items_closed tiki_p_comment_tracker_items=$perms.tiki_p_comment_tracker_items}
			{else}
				{include file='tracker_item_field_value.tpl' item=$item field_value=$field list_mode=$list_mode}
			{/if}
		{/capture}
		{set var=f_`$field.fieldId` value=$smarty.capture.value}
	{else}
		{set var=f_`$field.fieldId` value=''}
	{/if}
{/foreach}
{set var=f_created value=$item.created}
{set var=f_lastmodif value=$item.lastModif}
{set var=f_itemId value=$item.itemId}
{set var=f_status value=$item.status}
{set var=f_itemUser value=$item.itemUser}
{* ------------------------------------ *}
{include file="$wiki" item=$item}
{/strip}