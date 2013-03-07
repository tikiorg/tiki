{if $excluded}
	{remarksbox type=tip title=Tip icon=help}{tr}More languages are available. To view them, update languages you can read from <a href="tiki-user_preferences.php">user preferences</a> or your browser's preferences.{/tr}{/remarksbox}
{/if}
{if $side_by_side}
	<table>
		<tr>
			{foreach item=body from=$content key=k}
				{if $k == 0 || $k == 1}
					<td width="50%">{$body}</td>
				{/if}
			{/foreach}
		</tr>
		{if $content|@count > 2}
			{foreach item=body from=$content key=k}
				{if $k > 1}
					<tr>
						<td colspan="2">{$body}</td>
					</tr>
				{/if}
			{/foreach}
		{/if}
	</table>
{else}
	{foreach item=body from=$content}
		{$body}
	{/foreach}
{/if}
