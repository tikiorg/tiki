{strip}
"itemId"
{if $tracker_info.showStatus eq 'y'}
	,"status"
{/if}
{if $which eq 'all' or ($which eq 'list' and $tracker_info.showCreated eq 'y') or ($which eq 'item' and $tracker_info.showCreatedView eq 'y')}
	,"created"
{/if}
{if $which eq 'all' or ($which eq 'list' and $tracker_info.showLastModif eq 'y') or ($which eq 'item' and $tracker_info.showLastModifView eq 'y')}
	,"lastModif"
{/if}
{if !empty($listfields)}
,
{foreach item=field key=fieldId from=$listfields name=list}
	"{$field.name} -- {$fieldId}"{$field.isHidden}
	{if !$smarty.foreach.list.last},{/if}
{/foreach}
{/if}
{/strip}
{foreach from=$items item=item}
{strip}
"{$item.itemId}"
{if $tracker_info.showStatus eq 'y'}
	,"{$item.status}"
{/if}
{if $which eq 'all' or ($which eq 'list' and $tracker_info.showCreated eq 'y') or ($which eq 'item' and $tracker_info.showCreatedView eq 'y')}
	,"{$item.created|tiki_short_datetime}"
{/if}
{if $which eq 'all' or ($which eq 'list' and $tracker_info.showLastModif eq 'y') or ($which eq 'item' and $tracker_info.showLastModifView eq 'y')}
	,"{$item.lastModif|tiki_short_datetime}"
{/if}
{if !empty($listfields)}
,
{foreach item=field_value from=$item.field_values name=list}
	{if $field_value.isHidden ne 'c' or $item.creator eq $user}
		{*{capture}{include file="tracker_item_field_value.tpl"}{/capture}"{$smarty.capture.line|replace:"\r\n":"%%%"}"*}
		"{include file="tracker_item_field_value.tpl" list_mode='csv'}"
	{else}
		""
	{/if}
	{if !$smarty.foreach.list.last},{/if}
{/foreach}
{/if}
{/strip}
{/foreach}
