{if $prefs.feature_freetags eq 'y'}
	{if $context.list_mode eq 'csv'}
		{foreach from=$field.freetags item=taginfo}
			{$taginfo|escape}&nbsp;
		{/foreach}
	{else}
		{foreach from=$field.freetags item=taginfo}
			{capture name=tagurl}{if (strstr($taginfo, ' '))}"{$taginfo}"{else}{$taginfo}{/if}{/capture}
			<a class="freetag" href="tiki-browse_freetags.php?tag={$smarty.capture.tagurl|escape:'url'}">{$taginfo|escape}</a>&nbsp; &nbsp; 
		{/foreach}		
	{/if}
{else}
	{tr}Freetags is not enabled.{/tr}
{/if}