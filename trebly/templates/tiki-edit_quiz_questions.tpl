{* $Id$ *}

{* Copyright (c) 2002-2008 *}
{* All Rights Reserved. See copyright.txt for details and a complete list of authors. *}
{* Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details. *}

{title help="Quiz" url="tiki-edit_quiz_questions.php?quizId=$quizId"}{tr}Edit quiz questions{/tr}{/title}

<div class="navbar">
	{button href="tiki-list_quizzes.php" _text="{tr}List Quizzes{/tr}"} 
	{button href="tiki-quiz_stats.php" _text="{tr}Quiz Stats{/tr}"}
	{button href="tiki-quiz_stats_quiz.php?quizId=$quizId" _text="{tr}This Quiz Stats{/tr}"}
	{button href="tiki-edit_quiz.php?quizId=$quizId" _text="{tr}Edit this Quiz{/tr}"} 
	{button href="tiki-edit_quiz.php" _text="{tr}Admin Quizzes{/tr}"}
</div>

<h2>{tr}Create/edit questions for quiz:{/tr} <a href="tiki-edit_quiz.php?quizId={$quiz_info.quizId}" >{$quiz_info.name|escape}</a></h2>

<form action="tiki-edit_quiz_questions.php" method="post">
	<input type="hidden" name="quizId" value="{$quizId|escape}" />
	<input type="hidden" name="questionId" value="{$questionId|escape}" />

	<table class="formcolor">
		<tr>
			<td>{tr}Question:{/tr}</td>
			<td>
				<textarea name="question" rows="5" cols="80">{$question|escape}</textarea>
			</td>
		</tr>
		<tr>
			<td>{tr}Position:{/tr}</td>
			<td>
				<select name="position">{html_options values=$positions output=$positions selected=$position}</select>
			</td>
		</tr>

		<tr>
			<td>{tr}Question Type:{/tr}</td>
			<td>
				<select name="questionType">{html_options options=$questionTypes selected=$type}</select>
			</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td><input type="submit" name="save" value="{tr}Save{/tr}" /></td>
		</tr>
	</table>
</form>

<h2>{tr}Import questions from text{/tr}
	{if $prefs.feature_help eq 'y'}
		<a href="{$prefs.helpurl}Quiz+Question+Import" target="tikihelp" class="tikihelp">
			<img src="img/icons/help.gif" alt="{tr}Help{/tr}" />
		</a>
	{/if}
</h2>

<!-- begin form area for importing questions -->
<form enctype="multipart/form-data" method="post" action="tiki-edit_quiz_questions.php?quizId={$quiz_info.quizId}">
	<table class="formcolor">
		<tr>
			<td colspan="2">
				{tr}Instructions: Type, or paste, your multiple choice questions below.  One line for the question, then start answer choices on subsequent lines.  Separate additional questions with a blank line.  Indicate correct answers by starting them with a "*" (without the quotes) character.{/tr}
			</td>
		</tr>
		<tr>
			<td>
				{tr}Input{/tr}
			</td>
			<td>
				<textarea class="wikiedit" name="input_data" rows="30" cols="80" id='subheading'></textarea>
			</td>
		</tr>
	</table>
	<div align="center">
		<input type="submit" class="wikiaction" name="import" value="Import" />
	</div>
</form>

<!-- begin form for searching questions -->
<h2>{tr}Questions{/tr}</h2>
{include file='find.tpl'}

<table class="normal">
	<tr>
		<th>
			<a href="tiki-edit_quiz_questions.php?quizId={$quizId}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'questionId_desc'}questionId_asc{else}questionId_desc{/if}">{tr}ID{/tr}</a>
		</th>
		<th>
			<a href="tiki-edit_quiz_questions.php?quizId={$quizId}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'position_desc'}position_asc{else}position_desc{/if}">{tr}Position{/tr}</a>
		</th>
		<th>
			<a href="tiki-edit_quiz_questions.php?quizId={$quizId}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'question_desc'}question_asc{else}question_desc{/if}">{tr}Question{/tr}</a>
		</th>

		<th>{tr}Options{/tr}</th>
		<th>{tr}maxScore{/tr}</th>
		<th>{tr}Action{/tr}</th>
	</tr>
	{cycle values="odd,even" print=false}
	{section name=user loop=$channels}
		<tr class="{cycle}">
			<td class="id">{$channels[user].questionId}</td>
			<td class="id">{$channels[user].position}</td>
			<td class="text">{$channels[user].question|escape}</td>
			<td class="integer">{$channels[user].options}</td>
			<td class="integer">{$channels[user].maxPoints}</td>
			<td class="action">
				<a class="link" href="tiki-edit_quiz_questions.php?quizId={$quizId}&amp;offset={$offset}&amp;sort_mode={$sort_mode}&amp;questionId={$channels[user].questionId}">{icon _id='page_edit' alt="{tr}Edit{/tr}"}</a>
				<a class="link" href="tiki-edit_question_options.php?quizId={$quizId}&amp;questionId={$channels[user].questionId}">{icon _id='bricks' alt="{tr}Options{/tr}"}</a>
				<a class="link" href="tiki-edit_quiz_questions.php?quizId={$quizId}&amp;offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$channels[user].questionId}">{icon _id='cross' alt="{tr}Remove{/tr}"}</a>
			</td>
		</tr>
	{sectionelse}
		{norecords _colpan=6}
	{/section}
</table>

{pagination_links cant=$cant_pages step=$prefs.maxRecords offset=$offset}{/pagination_links}
<!-- tiki-edit_quiz_questions.tpl end -->
