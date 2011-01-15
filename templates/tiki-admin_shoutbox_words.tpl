{title help="Shoutbox"}{tr}Admin Shoutbox Words{/tr}{/title}

<h2>{tr}Add Banned Word{/tr}</h2>

<form method="post" action="tiki-admin_shoutbox_words.php">
	<table class="formcolor">
		<tr>
			<td>{tr}Word{/tr}</td>
			<td><input type="text" name="word" /></td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td><input type="submit" name="add" value="{tr}Add{/tr}" /></td>
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
		<tr class="{cycle}">
			<td>{$words[user].word|escape}</td>
			<td>
				&nbsp;&nbsp;
				<a class="link" href="tiki-admin_shoutbox_words.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$words[user].word|escape:"url"}" onclick="return confirmTheLink(this,"{tr}Are you sure you want to delete this word?{/tr}")" title="{tr}Delete{/tr}">{icon _id='cross' alt="{tr}Delete{/tr}"}</a>
				&nbsp;&nbsp;
			</td>
		</tr>
	{sectionelse}
		{norecords _colspan="2"}
	{/section}
</table>

{pagination_links cant=$cant_pages step=$prefs.maxRecords offset=$offset}{/pagination_links}
