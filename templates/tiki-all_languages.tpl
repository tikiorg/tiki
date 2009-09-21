{if $excluded}
	{remarksbox type=tip title=Tip icon=help}{tr}More languages are available. To view them, update languages you can read from <a href="tiki-user_preferences.php">user preferences</a> or your browser's preferences.{/tr}{/remarksbox}
{/if}
{if $side_by_side}
	<table>
		<tr>
			{foreach item=body from=$content}
				<td>{$body}</td>
			{/foreach}
		</tr>
	</table>
{else}
	{foreach item=body from=$content}
		{$body}
	{/foreach}
{/if}
