{title help="Quiz"}{tr}Quiz result stats{/tr}{/title}

<div class="navbar">
	{button href="tiki-list_quizzes.php" _text="{tr}List Quizzes{/tr}"}
	{button href="tiki-quiz_stats.php" _text="{tr}Quiz Stats{/tr}"}
	{button href="tiki-quiz_stats_quiz.php?quizId=$quizId" _text="{tr}This Quiz Stats{/tr}"}
	{button href="tiki-edit_quiz.php?quizId=$quizId" _text="{tr}Edit this Quiz{/tr}"}
	{button href="tiki-edit_quiz.php" _text="{tr}Admin Quizzes{/tr}"}
</div>

<table class="normal">
	<tr>
		<th colspan="2">{tr}Quiz stats{/tr}</th>
	</tr>
	<tr>
		<td class="even">{tr}Quiz{/tr}</td>
		<td class="even">{$quiz_info.name}</td>
	</tr>
	<tr>
		<td class="even">{tr}User{/tr} </td>
		<td class="even">{$ur_info.user|userlink}</td>
	</tr>
	<tr>
		<td class="even">{tr}Date{/tr}</td>
		<td class="even">{$ur_info.timestamp|tiki_short_datetime}</td>
	</tr>
	<tr>
		<td class="even">{tr}Points{/tr}</td>
		<td class="even">{$ur_info.points} / {$ur_info.maxPoints}</td>
	</tr>
	<tr>
		<td class="even">{tr}Time{/tr}</td>
		<td class="even">{$ur_info.timeTaken} secs</td>
	</tr>
</table>

<br />
{tr}Answer:{/tr}

<div class="quizanswer">{$result.answer}</div>

<h2>{tr}User answers{/tr}</h2>
<table class="normal">
	<tr>
		<th>
			<a href="tiki-quiz_result_stats.php?quizId={$quizId}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'question_desc'}question_asc{else}question_desc{/if}">{tr}Questions{/tr}</a>
		</th>
		<th>
			<a href="tiki-quiz_result_stats.php?quizId={$quizId}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'optionText_desc'}optionText_asc{else}optionText_desc{/if}">{tr}Answer{/tr}</a>
		</th>
		<th>
			<a href="tiki-quiz_result_stats.php?quizId={$quizId}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'points_desc'}points_asc{else}points_desc{/if}">{tr}Points{/tr}</a>
		</th>
		<th>
			{tr}Upload{/tr}
		</th>
	</tr>
	{cycle print=false values="odd,even"}
	{section name=ix loop=$questions}
		<tr class="{cycle}">
			<td>{$questions[ix].question}</td>
			<td>{$questions[ix].options[0].optionText}</td>
			<td>{$questions[ix].options[0].points}</td>
			{if $questions[ix].options[0].filename}
				<td>
					<a href="tiki-quiz_download_answer.php?answerUploadId={$questions[ix].options[0].answerUploadId}">{$questions[ix].options[0].filename}</a>
				</td>
			{/if}
		</tr>
	{/section}
</table>


