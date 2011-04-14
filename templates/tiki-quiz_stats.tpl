{* $Id$ *}
{title help="Quiz"}{tr}Stats for quizzes{/tr}{/title}

<div class="navbar">
	{button href="tiki-list_quizzes.php" _text="{tr}List Quizzes{/tr}"}
	{button href="tiki-edit_quiz.php" _text="{tr}Admin Quizzes{/tr}"}
</div>

<h2>{tr}Quizzes{/tr}</h2>
{if $channels}
	{include file='find.tpl'}
{/if}

<table class="normal">
	<tr>
		<th>
			<a href="tiki-quiz_stats.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'quizName_desc'}quizName_asc{else}quizName_desc{/if}">{tr}Quiz{/tr}</a>
		</th>
		<th>
			<a href="tiki-quiz_stats.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'timesTaken_desc'}timesTaken_asc{else}timesTaken_desc{/if}">{tr}taken{/tr}</a>
		</th>
		<th>
			<a href="tiki-quiz_stats.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'avgavg_desc'}avgavg_asc{else}avgavg_desc{/if}">{tr}Av score{/tr}</a>
		</th>
		<th>
			<a href="tiki-quiz_stats.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'avgtime_desc'}avgtime_asc{else}avgtime_desc{/if}">{tr}Av time{/tr}</a>
		</th>
	</tr>
	{cycle values="odd,even" print=false}
	{section name=user loop=$channels}
		{if ($tiki_p_admin eq 'y') or ($channels[user].individual eq 'n' and $tiki_p_view_quiz_stats eq 'y') or ($channels[user].individual_tiki_p_view_quiz_stats eq 'y')}
			<tr class="{cycle}">
				<td class="text"><a class="tablename" href="tiki-quiz_stats_quiz.php?quizId={$channels[user].quizId}">{$channels[user].quizName|escape}</a></td>
				<td class="date">{$channels[user].timesTaken}</td>
				<td class="integer">{$channels[user].avgavg}%</td>
				<td class="date">{$channels[user].avgtime} secs</td>
			</tr>
		{/if}
	{sectionelse}
		{norecords _colspan=4}
	{/section}
</table>

{pagination_links cant=$cant_pages step=$prefs.maxRecords offset=$offset}{/pagination_links}
