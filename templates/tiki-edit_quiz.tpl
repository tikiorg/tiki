{* $Id$ *}

{* Copyright (c) 2002-2008 *}
{* All Rights Reserved. See copyright.txt for details and a complete list of authors. *}
{* Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details. *}

{title help="Quiz"}{tr}Admin Quizzes{/tr}{/title}

<div class="t_navbar margin-bottom-md">
	{button href="tiki-list_quizzes.php" class="btn btn-default" _text="{tr}List Quizzes{/tr}"}
	{button href="tiki-quiz_stats.php" class="btn btn-default" _text="{tr}Quiz Stats{/tr}"}
</div>

{tabset}

	{tab name="{tr}Quizzes{/tr}"}
		<h2>{tr}Quizzes{/tr}</h2>
		{include file='find.tpl'}

		<div class="table-responsive">
			<table class="table normal table-striped table-hover">
				<tr>
					<th>
						{self_link _sort_arg='sort_mode' _sort_field='quizId'}{tr}ID{/tr}{/self_link}
					</th>
					<th>
						{self_link _sort_arg='sort_mode' _sort_field='name'}{tr}Quiz{/tr}{/self_link}
					</th>
					<th>
						{self_link _sort_arg='sort_mode' _sort_field='canRepeat'}{tr}canRepeat{/tr}{/self_link}
					</th>
					<th>
						{self_link _sort_arg='sort_mode' _sort_field='timeLimit'}{tr}timeLimit{/tr}{/self_link}
					</th>
					<th>{tr}Questions{/tr}</th>
					<th>{tr}Results{/tr}</th>
					<th></th>
				</tr>


				{section name=user loop=$channels}
					<tr>
						<td class="id">{$channels[user].quizId}</td>
						<td class="text">
							{$channels[user].name|escape}
							<div class="subcomment">
								{$channels[user].description|escape|nl2br}
							</div>
						</td>
						<td class="text">{$channels[user].canRepeat}</td>
						<td class="text">{$channels[user].timeLimited} {if $channels[user].timeLimited eq 'y'}({$channels[user].timeLimit} mins){/if}</td>
						<td class="integer">{$channels[user].questions}</td>
						<td class="integer">{$channels[user].results}</td>
						<td class="action">
							{capture name=edit_quiz_actions}
								{strip}
									<a href="tiki-edit_quiz_questions.php?quizId={$channels[user].quizId}">
										{icon name='help' _menu_text='y' _menu_icon='y' alt="{tr}Questions{/tr}"}
									</a>
									<a href="tiki-edit_quiz_results.php?quizId={$channels[user].quizId}">
										{icon name='view' _menu_text='y' _menu_icon='y' alt="{tr}Results{/tr}"}
									</a>
									{permission_link mode=text type=quiz permType=quizzes id=$channels[user].quizId title=$channels[user].name}
									{self_link _icon_name='edit' _menu_text='y' _menu_icon='y' cookietab='2' _anchor='anchor2' quizId=$channels[user].quizId}
										{tr}Edit{/tr}
									{/self_link}
									<a href="tiki-edit_quiz.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$channels[user].quizId}">
										{icon name='remove' _menu_text='y' _menu_icon='y' alt="{tr}Remove{/tr}"}
									</a>
								{/strip}
							{/capture}
							<a class="tips"
							   title="{tr}Actions{/tr}"
							   href="#" {popup trigger="click" fullhtml="1" center=true text=$smarty.capture.edit_quiz_actions|escape:"javascript"|escape:"html"}
							   style="padding:0; margin:0; border:0"
									>
								{icon name='wrench'}
							</a>
						</td>
					</tr>
				{sectionelse}
					{norecords _colspan=7}
				{/section}
			</table>
		</div>

		{pagination_links cant=$cant_pages step=$prefs.maxRecords offset=$offset}{/pagination_links}

	{/tab}

	{tab name="{tr}Create/edit quizzes{/tr}"}
		<h2>{tr}Create/edit quizzes{/tr}</h2>
		{if $individual eq 'y'}
			{permission_link mode=link type=quiz permType=quizzes id=$quizId title=$name label="{tr}There are individual permissions set for this quiz{/tr}"}
			<br>
			<br>
		{/if}

		<form action="tiki-edit_quiz.php" method="post">
			<input type="hidden" name="quizId" value="{$quizId|escape}">
			<table class="formcolor">
				<tr>
					<td>
						<label for="quiz-name">{tr}Name:{/tr}</label>
					</td>
					<td>
						<input type="text" size ="80" name="name" id="quiz-name" value="{$name|escape}">
					</td>
				</tr>
				<tr>
					<td>
						<label for="quiz-desc">{tr}Description:{/tr}</label>
					</td>
					<td>
						<textarea name="description" id="quiz-desc" rows="4" cols="75">{$description|escape}</textarea>
					</td>
				</tr>
				{include file='categorize.tpl'}
				<tr>
					<td>{tr}Publish Date{/tr}</td>
					<td>
						{html_select_date prefix="publish_" time=$publishDateSite start_year="-5" end_year="+10" field_order=$prefs.display_field_order}
						&nbsp;{tr}at{/tr}&nbsp;
						<span dir="ltr">
							{html_select_time prefix="publish_" time=$publishDateSite display_seconds=false use_24_hours=$use_24hr_clock}
							&nbsp;
							{$siteTimeZone}
						</span>
					</td>
				</tr>
				<tr>
					<td>{tr}Expiration Date{/tr}</td>
					<td>
						{html_select_date prefix="expire_" time=$expireDateSite start_year="-5" end_year="+10" field_order=$prefs.display_field_order}
						&nbsp;{tr}at{/tr}&nbsp;
						<span dir="ltr">
							{html_select_time prefix="expire_" time=$expireDateSite display_seconds=false use_24_hours=$use_24hr_clock} {$siteTimeZone}
						</span>
					</td>
				</tr>
				<tr>
					<td>
						<label for="quiz-repeat">{tr}Quiz can be repeated{/tr}</label>
					</td>
					<td>
						<input type="checkbox" name="canRepeat" id="quiz-repeat" {if $canRepeat eq 'y'}checked="checked"{/if}>
					</td>
				</tr>
				<tr>
					<td>
						<label for="quiz-results">{tr}Store quiz results{/tr}</label>
					</td>
					<td>
						<input type="checkbox" name="storeResults" id="quiz-results" {if $storeResults eq 'y'}checked="checked"{/if}>
					</td>
				</tr>
				{* Not implemented
				<tr>
					<td>
						<label for="immediate-feedback">{tr}Immediate feedback{/tr}</label>
					</td>
					<td>
						<input type="checkbox" name="immediateFeedback" id="immediate-feedback" {if $immediateFeedback eq 'y'}checked="checked"{/if}>
					</td>
				</tr>
				<tr>
					<td>
						<label for="show-answers">{tr}Show correct answers{/tr}</label>
					</td>
					<td>
						<input type="checkbox" name="showAnswers" id="show-answers" {if $showAnswers eq 'y'}checked="checked"{/if}>
					</td>
				</tr>
				<tr>
					<td>
						<label for="shuffle-questions">{tr}Shuffle questions{/tr}</label>
					</td>
					<td>
						<input type="checkbox" name="shuffleQuestions" id="shuffle-questions" {if $shuffleQuestions eq 'y'}checked="checked"{/if}>
					</td>
				</tr>
				<tr>
					<td>
						<label for="shuffle-answers">{tr}Shuffle answers{/tr}</label>
					</td>
					<td>
						<input type="checkbox" name="shuffleAnswers" id="shuffle-answers" {if $shuffleAnswers eq 'y'}checked="checked"{/if}>
					</td>
				</tr>*}
				<tr>
					<td>
						<label for="quiz-timelimit">{tr}Quiz is time limited{/tr}</label>
					</td>
					<td>
						<input type="checkbox" name="timeLimited" id="quiz-timelimit" {if $timeLimited eq 'y'}checked="checked"{/if}>
					</td>
				</tr>
				<tr>
					<td>
						<label for="quiz-maxtime">{tr}Maximum time{/tr}</label>
					</td>
					<td>
						<select name="timeLimit" id="quiz-maxtime">{html_options values=$mins selected=$timeLimit output=$mins}</select>
						&nbsp;{tr}minutes{/tr}
					</td>
				</tr>
				<tr>
					<td>
						<label for="quiz-passingperct">{tr}Passing Percentage{/tr}</label>
					</td>
					<td>
						<input type="text" name="passingperct" id="quiz-passingperct" size='3' maxlength='3' value="{$passingperct}">
						{tr}%{/tr}
					</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td>
						<input type="submit" class="btn btn-primary btn-sm" name="save" value="{tr}Save{/tr}">
					</td>
				</tr>
			</table>
		</form>
	{/tab}

{/tabset}
