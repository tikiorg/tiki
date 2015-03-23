{title url="tiki-edit_quiz_results.php?quizId=$quizId"}{tr}Edit quiz results{/tr}{/title}

<div class="t_navbar margin-bottom-md">
	{button href="tiki-list_quizzes.php" class="btn btn-default" _text="{tr}List Quizzes{/tr}"}
	{button href="tiki-quiz_stats.php" class="btn btn-default" _text="{tr}Quiz Stats{/tr}"}
	{button href="tiki-quiz_stats_quiz.php?quizId=$quizId" class="btn btn-default" _text="{tr}This Quiz Stats{/tr}"}
	{button href="tiki-edit_quiz.php?quizId=$quizId" class="btn btn-default" _text="{tr}Edit this Quiz{/tr}"}
	{button href="tiki-edit_quiz.php" class="btn btn-default" _text="{tr}Admin Quizzes{/tr}"}
</div>

<h2>
	{tr}Create/edit questions for quiz:{/tr} <a href="tiki-edit_quiz.php?quizId={$quiz_info.quizId}" class="pageTitle">{$quiz_info.name}</a>
</h2>

<form action="tiki-edit_quiz_results.php" method="post">
	<input type="hidden" name="quizId" value="{$quizId|escape}">
	<input type="hidden" name="resultId" value="{$resultId|escape}">
	<table class="formcolor">
		<tr>
			<td>{tr}From Points:{/tr}</td>
			<td>
				<input type="text" name="fromPoints" value="{$fromPoints|escape}">
			</td>
		</tr>
		<tr>
			<td>
				{tr}To Points:{/tr}
			</td>
			<td>
				<input type="text" name="toPoints" value="{$toPoints|escape}">
			</td>
		</tr>
		<tr>
			<td>
				{tr}Answer:{/tr}
			</td>
			<td>
				<textarea name="answer" rows="10" cols="40">{$answer|escape}</textarea>
			</td>
		</tr>
		<tr>
			<td>&nbsp;

			</td>
			<td>
				<input type="submit" class="btn btn-primary btn-sm" name="save" value="{tr}Save{/tr}">
			</td>
		</tr>
	</table>
</form>

<h2>{tr}Results{/tr}</h2>

{include file='find.tpl'}

<div class="table-responsive">
<table class="table normal table-striped table-hover">
	<tr>
		<th>
			<a href="tiki-edit_quiz_results.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'fromPoints_desc'}fromPoints_asc{else}fromPoints_desc{/if}">{tr}From Points{/tr}</a>
		</th>
		<th>
			<a href="tiki-edit_quiz_results.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'toPoints_desc'}toPoints_asc{else}toPoints_desc{/if}">{tr}To Points{/tr}</a>
		</th>
		<th>
			<a href="tiki-edit_quiz_results.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'position_desc'}answer_asc{else}answer_desc{/if}">{tr}Answer{/tr}</a>
		</th>
		<th></th>
	</tr>

	{section name=user loop=$channels}
		<tr>
			<td class="integer">{$channels[user].fromPoints}</td>
			<td class="integer">{$channels[user].toPoints}</td>
			<td class="text">{$channels[user].answer|truncate:230:"(...)":true|escape|nl2br}</td>
			<td class="action">
				{capture name=quiz_results_actions}
					{strip}
						<a href="tiki-edit_quiz_results.php?quizId={$quizId}&amp;offset={$offset}&amp;sort_mode={$sort_mode}&amp;resultId={$channels[user].resultId}">
							{icon name='edit' _menu_text='y' _menu_icon='y' alt="{tr}Edit{/tr}"}
						</a>
						<a href="tiki-edit_quiz_results.php?quizId={$quizId}&amp;offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$channels[user].resultId}">
							{icon name='remove' _menu_text='y' _menu_icon='y' alt="{tr}Remove{/tr}"}
						</a>
					{/strip}
				{/capture}
				<a class="tips"
				   title="{tr}Actions{/tr}"
				   href="#" {popup trigger="click" fullhtml="1" center=true text=$smarty.capture.quiz_results_actions|escape:"javascript"|escape:"html"}
				   style="padding:0; margin:0; border:0">
					{icon name='wrench'}
				</a>
			</td>
		</tr>
	{sectionelse}
		{norecords _colspan=4}
	{/section}
</table>
</div>

{pagination_links cant=$cant_pages step=$prefs.maxRecords offset=$offset}{/pagination_links}
