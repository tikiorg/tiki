{title}{tr}Support chat transcripts{/tr}{/title}

<a class="link" href="tiki-live_support_admin.php">{tr}Back to admin{/tr}</a>

<h2>{tr}Support requests{/tr}</h2>

<form method="get" action="tiki-live_support_transcripts.php">
	<input type="hidden" name="sort_mode" value="{$sort_mode|escape}">
	<table>
		<tr>
			<td>{tr}Find{/tr}</td>
			<td>{tr}Username{/tr}</td>
			<td>{tr}operator{/tr}</td>
			<td>&nbsp;</td>
		</tr>

		<tr>
			<td><input type="text" name="find" value="{$find|escape}"></td>
			<td>
				<select name="filter_user">
					<option value="" {if $filter_user eq ''}selected="selected"{/if}>{tr}All{/tr}</option>
					{section name=ix loop=$users}
						<option value="{$users[ix]|escape}" {if $users[ix] eq $filter_user}selected="selected"{/if}>{$users[ix]|escape}</option>
					{/section}
				</select>
			</td>
			<td>
				<select name="filter_operator">
					<option value="" {if $filter_operator eq ''}selected="selected"{/if}>{tr}All{/tr}</option>
					{section name=ix loop=$operators}
						<option value="{$operators[ix]|escape}" {if $operators[ix] eq $filter_operator}selected="selected"{/if}>{$operators[ix]|escape}</option>
					{/section}
				</select>
			</td>
			<td><input type="submit" class="btn btn-default btn-sm" value="{tr}Find{/tr}" name="filter"></td>
		</tr>
	</table>
</form>

<div class="table-responsive">
	<table class="table">
		<tr>
			<th><a href="tiki-live_support_transcripts.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'chat_started_desc'}chat_started_asc{else}chat_started_desc{/if}">{tr}started{/tr}</a></th>
			<th><a href="tiki-live_support_transcripts.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'tiki_user_desc'}tiki_user_asc{else}tiki_user_desc{/if}">{tr}Username{/tr}</a></th>
			<th><a href="tiki-live_support_transcripts.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'reason_desc'}reason_asc{else}reason_desc{/if}">{tr}reason{/tr}</a></th>
			<th><a href="tiki-live_support_transcripts.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'operator_desc'}operator_asc{else}operator_desc{/if}">{tr}operator{/tr}</a></th>
			<th>{tr}msgs{/tr}</th>
		</tr>

		{section name=ix loop=$items}
			<tr>
				<td>{$items[ix].chat_started|tiki_short_datetime}</td><!--date_format:"%d %b [%H:%M]"-->
				<td>{if $items[ix].tiki_user ne ""}{$items[ix].tiki_user}{else}{$items[ix].user|escape}{/if}</td>
				<td>{$items[ix].reason}</td>
				<td>{$items[ix].operator|escape}</td>
				<td style="text-align:right;"><a class="link" href="tiki-live_support_transcripts.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;find={$find}&amp;filter_user={$filter_user}&amp;filter_operator={$filter_operator}&amp;view={$items[ix].reqId}">{$items[ix].msgs}<a>&nbsp;</td>
			</tr>
		{sectionelse}
			{norecords _colspan=5}
		{/section}
	</table>
</div>

{pagination_links cant=$cant_pages step=$prefs.maxRecords offset=$offset}{/pagination_links}

{if $smarty.request.view}
	<h3>{tr}Transcript{/tr}</h3>
	<div class="table-responsive">
		<table class="table">
			{section name=ix loop=$events}
				<tr>
					<td class="odd">
						{$events[ix].timestamp|tiki_short_time}
					</td>
					<td class="odd">
						{$events[ix].data}
					</td>
				</tr>
			{/section}
		</table>
	</div>
{/if}
