{strip}
{if $heading ne 'n'}
{if $showItemId ne 'n'}
{assign var='comma' value='y'}
"itemId"
{/if}
{if $showStatus ne 'n'}
	{if $comma eq 'y'},{else}{assign var='comma' value='y'}{/if}
	"status"
{/if}
{if $showCreated ne 'n'}
	{if $comma eq 'y'},{else}{assign var='comma' value='y'}{/if}
	"created"
{/if}
{if $showLastModif ne 'n'}
	{if $comma eq 'y'},{else}{assign var='comma' value='y'}{/if}
	"lastModif"
{/if}
{if !empty($listfields)}
	{if $comma eq 'y'},{else}{assign var='comma' value='y'}{/if}
{foreach item=field key=fieldId from=$listfields name=list}
	"{$field.name} -- {$fieldId}"
	{if !$smarty.foreach.list.last},{/if}
{/foreach}
{/if}
{assign var='comma' value='n'}
{/strip}
{strip}
{/if}
{foreach from=$items item=item}
{if $showItemId ne 'n'}
{assign var='comma' value='y'}
"{$item.itemId}"
{/if}
{if $showStatus eq 'y'}
	{if $comma eq 'y'},{else}{assign var='comma' value='y'}{/if}
	"{$item.status}"
{/if}
{if $showCreated ne 'n'}
	{if $comma eq 'y'},{else}{assign var='comma' value='y'}{/if}
	"{$item.created|tiki_short_datetime}"
{/if}
{if $showLastModif ne 'n'}
	{if $comma eq 'y'},{else}{assign var='comma' value='y'}{/if}
	"{$item.lastModif|tiki_short_datetime}"
{/if}
{if !empty($listfields)}
{if $comma eq 'y'},{else}{assign var='comma' value='y'}{/if}
{foreach item=field_value from=$item.field_values name=list}
	{if $field_value.isHidden ne 'c' or $item.creator eq $user or $tiki_p_admin_trackers eq 'y'}
		{capture name="line"}{include file="tracker_item_field_value.tpl" list_mode='csv' showlinks='n'}{/capture}"{$smarty.capture.line|replace:"\r\n":"%%%"|replace:"\n":"%%%"|replace:"<br />":"%%%"|replace:'"':'""'}"
	{else}
		""
	{/if}
	{if !$smarty.foreach.list.last},{/if}
{/foreach}
{/if}
{/strip}
{/foreach}