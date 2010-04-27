{title help="Shoutbox"}{tr}Admin Shoutbox Words{/tr}{/title}

<h2>{tr}Add Banned Word{/tr}</h2>

<form method="post" action="tiki-admin_shoutbox_words.php">
	<table class="normal">
		<tr>
			<td class="formcolor">{tr}Word{/tr}</td>
			<td class="formcolor"><input type="text" name="word" /></td>
		</tr>
		<tr>
			<td class="formcolor">&nbsp;</td>
			<td class="formcolor"><input type="submit" name="add" value="{tr}Add{/tr}" /></td>
		</tr>
	</table>
</form>

{include file='find.tpl'}

<table class="normal">
	<tr>
		<th>
			<a href="tiki-admin_shoutbox_words.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'word_desc'}word_asc{else}word_desc{/if}">{tr}Word{/tr}</a>
		</th>
		<th>{tr}Action{/tr}</th>
	</tr>
	{cycle values="odd,even" print=false}
	{section name=user loop=$words}
		<tr>
			<td class="{cycle advance=false}">{$words[user].word|escape}</td>
			<td class="{cycle advance=true}">
				&nbsp;&nbsp;
				<a class="link" href="tiki-admin_shoutbox_words.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$words[user].word|escape:"url"}" onclick="return confirmTheLink(this,'{tr}Are you sure you want to delete this word?{/tr}')" title="{tr}Delete{/tr}">{icon _id='cross' alt='{tr}Delete{/tr}'}</a>
				&nbsp;&nbsp;
			</td>
		</tr>
	{sectionelse}
		<tr>
			<td colspan="2" class="odd">{tr}No records found{/tr}</td>
		</tr>
	{/section}
</table>

{pagination_links cant=$cant_pages step=$prefs.maxRecords offset=$offset}{/pagination_links}
