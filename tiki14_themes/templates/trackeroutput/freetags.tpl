{if $prefs.feature_freetags eq 'y'}
	{if $context.list_mode eq 'csv'}
		{foreach from=$field.freetags item=taginfo}
			{$taginfo|escape}&nbsp;
		{/foreach}
	{else}
		{foreach from=$field.freetags item=taginfo}
			{object_link type=freetag id=$taginfo}&nbsp; &nbsp;
		{/foreach}
	{/if}
{else}
	{tr}Tags is not enabled.{/tr}
{/if}
