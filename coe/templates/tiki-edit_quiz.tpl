{* $Id$ *}
 
{* Copyright (c) 2002-2008 *}
{* All Rights Reserved. See copyright.txt for details and a complete list of authors. *}
{* Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details. *}

{title help="Quiz"}{tr}Admin Quizzes{/tr}{/title}

<div class="navbar">
	{button href="tiki-list_quizzes.php" _text="{tr}List Quizzes{/tr}"}
	{button href="tiki-quiz_stats.php" _text="{tr}Quiz Stats{/tr}"}
</div>

<h2>{tr}Create/edit quizzes{/tr}</h2>
{if $individual eq 'y'}
	<a class="link" href="tiki-objectpermissions.php?objectName={$name|escape:"url"}&amp;objectType=quiz&amp;permType=quizzes&amp;objectId={$quizId}">{tr}There are individual permissions set for this quiz{/tr}</a>
	<br />
	<br />
{/if}

<!-- begin form to create/ edit quizzes -->
<form action="tiki-edit_quiz.php" method="post">
	<input type="hidden" name="quizId" value="{$quizId|escape}" />
	<table class="normal">
		<tr>
			<td class="formcolor">
				<label for="quiz-name">{tr}Name{/tr}:</label>
			</td>
			<td class="formcolor">
				<input type="text" size ="80" name="name" id="quiz-name" value="{$name|escape}" />
			</td>
		</tr>
		<tr>
			<td class="formcolor">
				<label for="quiz-desc">{tr}Description{/tr}:</label>
			</td>
			<td class="formcolor">
				<textarea name="description" id="quiz-desc" rows="4" cols="75">{$description|escape}</textarea>
			</td>
		</tr>
		{include file=categorize.tpl}
		<tr class="formcolor">
			<td>{tr}Publish Date{/tr}</td>
			<td>
				{html_select_date prefix="publish_" time=$publishDateSite start_year="-5" end_year="+10" field_order=$prefs.display_field_order}
				&nbsp;{tr}at{/tr}&nbsp;
				<span dir="ltr">
					{html_select_time prefix="publish_" time=$publishDateSite display_seconds=false}
					&nbsp;
					{$siteTimeZone}
				</span>
			</td>
		</tr>
		<tr class="formcolor">
			<td>{tr}Expiration Date{/tr}</td>
			<td>
				{html_select_date prefix="expire_" time=$expireDateSite start_year="-5" end_year="+10" field_order=$prefs.display_field_order}
				&nbsp;{tr}at{/tr}&nbsp;
				<span dir="ltr">{html_select_time prefix="expire_" time=$expireDateSite display_seconds=false}
					&nbsp;{$siteTimeZone}
				</span>
			</td>
		</tr>
		<tr>
			<td class="formcolor">
				<label for="quiz-repeat">{tr}Quiz can be repeated{/tr}</label>
			</td>
			<td class="formcolor">
				<input type="checkbox" name="canRepeat" id="quiz-repeat" {if $canRepeat eq 'y'}checked="checked"{/if} />
			</td>
		</tr>
		<tr>
			<td class="formcolor">
				<label for="quiz-results">{tr}Store quiz results{/tr}</label>
			</td>
			<td class="formcolor">
				<input type="checkbox" name="storeResults" id="quiz-results" {if $storeResults eq 'y'}checked="checked"{/if} />
			</td>
		</tr>
		<tr>
			<td class="formcolor">
				<label for="immediate-feedback">{tr}Immediate feedback{/tr}</label>
			</td>
			<td class="formcolor">
				<input type="checkbox" name="immediateFeedback" id="immediate-feedback" {if $immediateFeedback eq 'y'}checked="checked"{/if} />
			</td>
		</tr>
		<tr>
			<td class="formcolor">
				<label for="show-answers">{tr}Show correct answers{/tr}</label>
			</td>
			<td class="formcolor">
				<input type="checkbox" name="showAnswers" id="show-answers" {if $showAnswers eq 'y'}checked="checked"{/if} />
			</td>
		</tr>
		<tr>
			<td class="formcolor">
				<label for="shuffle-questions">{tr}Shuffle questions{/tr}</label>
			</td>
			<td class="formcolor">
				<input type="checkbox" name="shuffleQuestions" id="shuffle-questions" {if $shuffleQuestions eq 'y'}checked="checked"{/if} />
			</td>
		</tr>
		<tr>
			<td class="formcolor">
				<label for="shuffle-answers">{tr}Shuffle answers{/tr}</label>
			</td>
			<td class="formcolor">
				<input type="checkbox" name="shuffleAnswers" id="shuffle-answers" {if $shuffleAnswers eq 'y'}checked="checked"{/if} />
			</td>
		</tr>
			<!--Why was this quoted out? Need to investigate
			<tr>
				<td class="formcolor">
					<label for="quiz-perpage">{tr}Questions per page{/tr}</label>
				</td>
				<td class="formcolor">
					<select name="questionsPerPage" id="quiz-perpage">{html_options values=$qpp selected=$questionsPerPage output=$qpp}</select>
				</td>
			</tr>
		-->
		<tr>
			<td class="formcolor">
				<label for="quiz-timelimit">{tr}Quiz is time limited{/tr}</label>
			</td>
			<td class="formcolor">
				<input type="checkbox" name="timeLimited" id="quiz-timelimit" {if $timeLimited eq 'y'}checked="checked"{/if} />
			</td>
		</tr>
		<tr>
			<td class="formcolor">
				<label for="quiz-maxtime">{tr}Maximum time{/tr}</label>
			</td>
			<td class="formcolor">
				<select name="timeLimit" id="quiz-maxtime">{html_options values=$mins selected=$timeLimit output=$mins}</select>
				&nbsp;{tr}minutes{/tr}
			</td>
		</tr>
		<tr>
			<td class="formcolor">
				<label for="quiz-passingperct">{tr}Passing Percentage{/tr}</label>
			</td>
			<td class="formcolor">
				<input type="text" name="passingperct" id="quiz-passingperct" size='3' maxlength='3' value="{$passingperct}" />
				{tr}%{/tr}
			</td>
		</tr>
		<tr>
			<td class="formcolor">&nbsp;</td>
			<td class="formcolor">
				<input type="submit" name="save" value="{tr}Save{/tr}" />
			</td>
		</tr>
	</table>
</form>

<!-- begin form for searching quizzes -->

<h2>{tr}Quizzes{/tr}</h2>
{include file='find.tpl'}

<!-- begin table for displaying quiz data -->
<table class="normal">
	<tr>
		<th>
			<a href="tiki-edit_quiz.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'quizId_desc'}quizId_asc{else}quizId_desc{/if}">{tr}ID{/tr}</a>
		</th>
		<th>
			<a href="tiki-edit_quiz.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'name_desc'}name_asc{else}name_desc{/if}">{tr}Quiz{/tr}</a>
		</th>
		<th>
			<a href="tiki-edit_quiz.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'canRepeat_desc'}canRepeat_asc{else}canRepeat_desc{/if}">{tr}canRepeat{/tr}</a>
		</th>
		<th>
			<a href="tiki-edit_quiz.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'timeLimit_desc'}timeLimit_asc{else}timeLimit_desc{/if}">{tr}timeLimit{/tr}</a>
		</th>

		<!-- I don't know why but these column head will not behave properly with sort -->
		<th>{tr}Questions{/tr}</th>
		<th>{tr}Results{/tr}</th>

		{* still stuck on being able to sort by number of questions and results!
			Results need to be sortable so as to give admin quick idea of user participation
			<th>
				<a href="tiki-edit_quiz.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'results_desc'}results_asc{else}results_desc{/if}">{tr}Results{/tr}</a>
			</th>
		*}

		<th>{tr}Actions{/tr}</th>
	</tr>
	<!-- end header data -->
	{cycle values="odd,even" print=false}
	{section name=user loop=$channels}
		<tr>
			<td class="{cycle advance=false}">{$channels[user].quizId}</td>
			<td class="{cycle advance=false}">
				{$channels[user].name}
				<div class="subcomment">
					{$channels[user].description}
				</div>
			</td>
			<td class="{cycle advance=false}">{$channels[user].canRepeat}</td>
			<td class="{cycle advance=false}">{$channels[user].timeLimited} {if $channels[user].timeLimited eq 'y'}({$channels[user].timeLimit} mins){/if}</td>
			<td class="{cycle advance=false}">{$channels[user].questions}</td>
			<td class="{cycle advance=false}">{$channels[user].results}</td>
			<td class="{cycle}">
				<a class="link" href="tiki-edit_quiz.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;quizId={$channels[user].quizId}">{icon _id='page_edit' alt='{tr}Edit{/tr}'}</a>
				<a class="link" href="tiki-edit_quiz_questions.php?quizId={$channels[user].quizId}">{icon _id='help' alt='{tr}Questions{/tr}' title='{tr}Questions{/tr}'}</a>
				<a class="link" href="tiki-edit_quiz_results.php?quizId={$channels[user].quizId}">{icon _id='application_form_magnify' alt='{tr}Results{/tr}' title='{tr}Results{/tr}'}</a>
				<a class="link" href="tiki-objectpermissions.php?objectName={$channels[user].name|escape:"url"}&amp;objectType=quiz&amp;permType=quizzes&amp;objectId={$channels[user].quizId}">
					{if $channels[user].individual eq 'y'}
						{icon _id='key_active' alt='{tr}Active Perms{/tr}'}
					{else}
						{icon _id='key' alt='{tr}Perms{/tr}'}
					{/if}
				</a>
				<a class="link" href="tiki-edit_quiz.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$channels[user].quizId}">{icon _id='cross' alt='{tr}Remove{/tr}'}</a>
			</td>
		</tr>
	{/section}
</table>

{pagination_links cant=$cant_pages step=$prefs.maxRecords offset=$offset}{/pagination_links}
<!-- tiki-edit_quiz.tpl end -->
