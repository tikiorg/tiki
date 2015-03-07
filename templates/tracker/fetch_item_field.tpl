	{if $mode eq 'output'}
		{trackeroutput field=$field}
	{else}
		{trackerinput field=$field}
	{/if}
