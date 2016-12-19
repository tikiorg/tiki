{title help="Quiz"}{tr}Quiz result stats{/tr}{/title}

<div class="t_navbar margin-bottom-md">
	{button href="tiki-list_quizzes.php" class="btn btn-default" _text="{tr}List Quizzes{/tr}"}
	{button href="tiki-quiz_stats.php" class="btn btn-default" _text="{tr}Quiz Stats{/tr}"}
	{button href="tiki-quiz_stats_quiz.php?quizId=$quizId" class="btn btn-default" _text="{tr}This Quiz Stats{/tr}"}
	{button href="tiki-edit_quiz.php?quizId=$quizId" class="btn btn-default" _text="{tr}Edit this Quiz{/tr}"}
	{button href="tiki-edit_quiz.php" class="btn btn-default" _text="{tr}Admin Quizzes{/tr}"}
</div>
<div class="table-responsive">
	<table class="table">
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
</div>

<br>
{tr}Answer:{/tr}

<div class="quizanswer">{$result.answer}</div>

<h2>{tr}User answers{/tr}</h2>
<div class="table-responsive">
<table class="table">
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
		<tr>
			<td class="text">{$questions[ix].question}</td>
			<td class="text">{$questions[ix].options[0].optionText}</td>
			<td class="integer">{$questions[ix].options[0].points}</td>
			{if $questions[ix].options[0].filename}
				<td class="action">
					<a href="tiki-quiz_download_answer.php?answerUploadId={$questions[ix].options[0].answerUploadId}">{$questions[ix].options[0].filename}</a>
				</td>
			{/if}
		</tr>
	{/section}
</table>
</div>

