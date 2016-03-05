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
			<table class="table table-striped table-hover">
				<tr>
					<th>
						{self_link _sort_arg='sort_mode' _sort_field='quizId'}{tr}ID{/tr}{/self_link}
					</th>
					<th>
						{self_link _sort_arg='sort_mode' _sort_field='name'}{tr}Quiz{/tr}{/self_link}
					</th>
					<th>
						{self_link _sort_arg='sort_mode' _sort_field='canRepeat'}{tr}Quiz can be repeated{/tr}{/self_link}
					</th>
					<th>
						{self_link _sort_arg='sort_mode' _sort_field='timeLimit'}{tr}Time Limit{/tr}{/self_link}
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
							<span class="help-block">
								{$channels[user].description|escape|nl2br}
							</span>
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
								href="#" {popup fullhtml="1" center=true text=$smarty.capture.edit_quiz_actions|escape:"javascript"|escape:"html"}
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
		<form action="tiki-edit_quiz.php" class="form-horizontal" method="post">
			<input type="hidden" name="quizId" value="{$quizId|escape}">
			<div class="form-group">
				<label class="col-md-2 control-label" for="quiz-name">
					{tr}Name{/tr}
				</label>
				<div class="col-md-10">
					<input type="text" class="form-control" name="name" id="quiz-name" value="{$name|escape}">
				</div>
			</div>
			<div class="form-group">
				<label class="col-md-2 control-label" for="quiz-desc">
					{tr}Description{/tr}
				</label>
				<div class="col-md-10">
					<textarea name="description" id="quiz-desc" class="form-control">{$description|escape}</textarea>
				</div>
			</div>
			{include file='categorize.tpl'}
			<div class="form-group">
				<label class="col-md-2 control-label">
					{tr}Publish Date{/tr}
				</label>
				<div class="col-md-5">
					{html_select_date prefix="publish_" time=$publishDateSite start_year="-5" end_year="+10" field_order=$prefs.display_field_order}
				</div>
				<div class="col-md-1 text-center">
					{tr}at{/tr}
				</div>
				<div class="col-md-4" dir="ltr">
					{html_select_time prefix="publish_" time=$publishDateSite display_seconds=false use_24_hours=$use_24hr_clock}
					{$siteTimeZone}
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-md-2">
					{tr}Expiration Date{/tr}
				</label>
				<div class="col-md-5">
					{html_select_date prefix="expire_" time=$expireDateSite start_year="-5" end_year="+10" field_order=$prefs.display_field_order}
				</div>
				<div class="col-md-1 text-center">
					{tr}at{/tr}
				</div>
				<div class="col-md-4" dir="ltr">
					{html_select_time prefix="expire_" time=$expireDateSite display_seconds=false use_24_hours=$use_24hr_clock} {$siteTimeZone}
				</div>
			</div>
			<div class="checkbox col-md-offset-2">
				<label for="quiz-repeat">
					<input type="checkbox" name="canRepeat" id="quiz-repeat" {if $canRepeat eq 'y'}checked="checked"{/if}>
					{tr}Quiz can be repeated{/tr}
				</label>
			</div>
			<div class="checkbox col-md-offset-2">
				<label for="quiz-results">
					<input type="checkbox" name="storeResults" id="quiz-results" {if $storeResults eq 'y'}checked="checked"{/if}>
					{tr}Store quiz results{/tr}
				</label>
			</div>
			{* Not implemented
			<div class="checkbox col-md-offset-2">
				<label for="immediate-feedback">
					<input type="checkbox" name="immediateFeedback" id="immediate-feedback" {if $immediateFeedback eq 'y'}checked="checked"{/if}>
					{tr}Immediate feedback{/tr}
				</label>
			</div>
			<div class="checkbox col-md-offset-2">
				<label for="show-answers">
					<input type="checkbox" name="showAnswers" id="show-answers" {if $showAnswers eq 'y'}checked="checked"{/if}>
					{tr}Show correct answers{/tr}
				</label>
			</div>
			<div class="checkbox col-md-offset-2">
				<label for="shuffle-questions">
					<input type="checkbox" name="shuffleQuestions" id="shuffle-questions" {if $shuffleQuestions eq 'y'}checked="checked"{/if}>
					{tr}Shuffle questions{/tr}
				</label>
			</div>
			<div class="checkbox col-md-offset-2">
				<label for="shuffle-answers">
					<input type="checkbox" name="shuffleAnswers" id="shuffle-answers" {if $shuffleAnswers eq 'y'}checked="checked"{/if}>
					{tr}Shuffle answers{/tr}
				</label>
			</div>
		*}
			<div class="checkbox col-md-offset-2" style="margin-bottom: 15px;">
				<label for="quiz-timelimit">
					<input type="checkbox" name="timeLimited" id="quiz-timelimit"
						{if $timeLimited eq 'y'}checked="checked"{/if}>
					{tr}Quiz is time-limited{/tr}
				</label>
			</div>
			<div class="form-group">
				<label class="control-label col-md-2" for="quiz-maxtime">
					{tr}Maximum time{/tr}
				</label>
				<div class="col-md-3">
					<select class="form-control" name="timeLimit" id="quiz-maxtime">
						{html_options values=$mins selected=$timeLimit output=$mins}
					</select>
					<div class="help-block">
						{tr}minutes{/tr}
					</div>
				</div>
			</div>
			<div class="form-group">
				<label class="col-md-2 control-label" for="quiz-passingperct">
					{tr}Passing Percentage{/tr}
				</label>
				<div class="col-md-3">
					<input type="text" name="passingperct" id="quiz-passingperct" class="form-control" maxlength='3' value="{$passingperct}">
					<div class="help-block">
						{tr}%{/tr}
					</div>
				</div>
			</div>
			<div class="text-center">
				<input type="submit" class="btn btn-primary btn-sm" name="save" value="{tr}Save{/tr}">
			</div>
		</form>
	{/tab}
{/tabset}