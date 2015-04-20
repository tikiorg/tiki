{title url="tiki-edit_question_options.php?questionId=$questionId"}{tr}Edit question options{/tr}{/title}

<div class="t_navbar margin-bottom-md">
	{button href="tiki-list_quizzes.php" class="btn btn-default" _text="{tr}List Quizzes{/tr}"}
	{button href="tiki-quiz_stats.php" class="btn btn-default" _text="{tr}Quiz Stats{/tr}"}
	{button href="tiki-quiz_stats_quiz.php?quizId=$quizId" class="btn btn-default" _text="{tr}This Quiz Stats{/tr}"}
	{button href="tiki-edit_quiz.php?quizId=$quizId" class="btn btn-default" _text="{tr}Edit this Quiz{/tr}"}
	{button href="tiki-edit_quiz.php" class="btn btn-default" _text="{tr}Admin Quizzes{/tr}"}
</div>

<h2>{tr}Create/edit options for question:{/tr} <a href="tiki-edit_quiz_questions.php?quizId={$question_info.quizId}&amp;questionId={$question_info.questionId}">{$question_info.question|escape}</a></h2>
<form action="tiki-edit_question_options.php" method="post">
	<input type="hidden" name="optionId" value="{$optionId|escape}">
	<input type="hidden" name="questionId" value="{$questionId|escape}">
	<table class="formcolor">
		<tr><td>{tr}Option:{/tr}</td><td><textarea name="optionText" rows="5" cols="40">{$optionText|escape}</textarea></td></tr>
		<tr><td>{tr}Points:{/tr}</td><td><input type="text" name="points" value="{$points|escape}"></td></tr>
		<tr><td >&nbsp;</td><td><input type="submit" class="btn btn-primary btn-sm" name="save" value="{tr}Save{/tr}"></td></tr>
	</table>
</form>

<h2>Options</h2>

{include file='find.tpl'}

<div class="table-responsive">
	<table class="table normal table-striped table-hover">
		<tr>
			<th><a href="tiki-edit_question_options.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'optionId_desc'}optionId_asc{else}optionId_desc{/if}">{tr}ID{/tr}</a></th>
			<th><a href="tiki-edit_question_options.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'optionText_desc'}optionText_asc{else}optionText_desc{/if}">{tr}text{/tr}</a></th>
			<th><a href="tiki-edit_question_options.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'points_desc'}points_asc{else}points_desc{/if}">{tr}points{/tr}</a></th>
			<th></th>
		</tr>


		{section name=user loop=$channels}
			<tr>
				<td class="id">{$channels[user].optionId}</td>
				<td class="text">{$channels[user].optionText|escape}</td>
				<td class="integer">{$channels[user].points}</td>
				<td class="action">
					{capture name=question_option_actions}
						{strip}
							<a href="tiki-edit_question_options.php?questionId={$questionId}&amp;offset={$offset}&amp;sort_mode={$sort_mode}&amp;optionId={$channels[user].optionId}">
								{icon name='edit' _menu_text='y' _menu_icon='y' alt="{tr}Edit{/tr}"}
							</a>
							<a href="tiki-edit_question_options.php?questionId={$questionId}&amp;offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$channels[user].optionId}">
								{icon name='remove' _menu_text='y' _menu_icon='y' alt="{tr}Remove{/tr}"}
							</a>
						{/strip}
					{/capture}
					<a class="tips"
					   title="{tr}Actions{/tr}"
					   href="#" {popup delay="0|2000" fullhtml="1" center=true text=$smarty.capture.question_option_actions|escape:"javascript"|escape:"html"}
					   style="padding:0; margin:0; border:0"
							>
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
