{if $excluded}
	{remarksbox type=tip title=Tip icon=help}{tr}More languages are available. To view them, update languages you can read from <a href="tiki-user_preferences.php">user preferences</a> or your browser's preferences.{/tr}{/remarksbox}
{/if}
{foreach item=body from=$content}
	{$body}
{/foreach}
