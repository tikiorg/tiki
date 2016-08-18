{* $Id$ *}

{* Copyright (c) 2004 George G. Geller et. al. *}
{* All Rights Reserved. See copyright.txt for details and a complete list of authors. *}
{* Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details. *}

{title help="Quiz"}{tr}Edit quiz:{/tr} {$quiz->name}{/title}

<div class="t_navbar margin-bottom-md">
	{button href="tiki-list_quizzes.php" class="btn btn-default" _text="{tr}List Quizzes{/tr}"}
	{button href="tiki-quiz_stats.php" class="btn btn-default" _text="{tr}Quiz Stats{/tr}"}
	{button href="tiki-quiz_stats_quiz.php" _auto_args='quizId' class="btn btn-default" _text="{tr}This Quiz Stats{/tr}"}
	{button href="tiki-quiz_edit.php" class="btn btn-default" _text="{tr}Admin Quizzes{/tr}"}
</div>

<form enctype="multipart/form-data" method="post" action="tiki-quiz_edit.php" class="form-horizontal">
	<input type="hidden" name="quiz.id" value="{$quiz->id}">

	<div class="form-group">
		<label class="control-label col-sm-2 text-left">{tr}Status{/tr}</label>
		<div class="col-sm-7 form-control-static">
			[ <a class="link" href="javascript:show('status');">{tr}Show{/tr}</a>
			| <a class="link" href="javascript:hide('status');">{tr}Hide{/tr}</a> ]
			<div id="status" style="display:none;">
				<div class="table-responsive">
					<table class="table">
						<tr>
							<td>
								{if $quiz->online eq 'y'}
									{html_radios name="quiz.online" options=$tpl.online_choices selected=online}
								{else}
									{html_radios name="quiz.online" options=$tpl.online_choices selected=offline}
								{/if}
							</td>
						</tr>
						{if $quiz->taken eq 'y'}
							<tr>
								<td colspan=2>{tr}Current Version:{/tr} {$quiz->version}{if $quiz->id eq 0}, Author: {$quiz->authorLogin}, Date: {$quiz->timestamp|tiki_long_datetime}{/if}
								</td>
							</tr>
							{foreach from=$quiz->history item=history}
								<tr>
									<td colspan=2>{$history}</td>
								</tr>
							{/foreach}
						{/if}
					</table>
				</div>
			</div>
		</div>
	</div>
	<div class="form-group">
		<label class="control-label col-sm-2">{tr}General Options{/tr}</label>
		<div class="col-sm-7 form-control-static">
			[ <a class="link" href="javascript:show('general');">{tr}Show{/tr}</a>
			| <a class="link" href="javascript:hide('general');">{tr}Hide{/tr}</a> ]
			<div id="general" style="display:none;">
				<label class="control-label col-sm-3">{tr}Name:{/tr}</label>
				<div class="col-sm-9">
					<input type="text" name=quiz.name id="quiz-name" value="{$quiz->name|escape}" size="60" class="form-control margin-bottom-sm">
				</div>
				<label class="control-label col-sm-3">{tr}Description:{/tr}</label>
				<div class="col-sm-9">
					<input type="text" name=quiz.description id="quiz-desc" value="{$quiz->description|escape}" size="60" class="form-control margin-bottom-sm">
				</div>
				<label class="control-label col-sm-3">{tr}Publication Date{/tr}</label>
				<div class="col-sm-9 margin-bottom-sm">
					{html_select_date prefix="quiz_publish_" time=$quiz->datePub start_year="-5" end_year="+10" field_order=$prefs.display_field_order}
					{tr}at {/tr}{html_select_time prefix="quiz_publish_" time=$quiz->datePub display_seconds=false use_24_hours=$use_24hr_clock} HRS&nbsp;{$tpl.siteTimeZone}
				</div>
				<label class="control-label col-sm-3">{tr}Expiration Date{/tr}</label>
				<div class="col-sm-9 margin-bottom-sm">
					{html_select_date prefix="quiz_expire_" time=$quiz->dateExp start_year="-5" end_year="+10" field_order=$prefs.display_field_order}
					{tr}at {/tr}{html_select_time prefix="quiz_expire_" time=$quiz->dateExp display_seconds=false use_24_hours=$use_24hr_clock} HRS&nbsp;{$tpl.siteTimeZone}
				</div>
				<label class="control-label col-sm-3">{tr}Questions{/tr}</label>
				<div class="col-sm-9">
					<label for="nQuestion"><input type="checkbox" name=quiz.nQuestion id="nQuestion" {if $quiz->nQuestion eq 'y'}checked="checked"{/if}>{tr}Use {/tr}</label>
					<select name=quiz.nQuestions id="nQuestions" class="form-control">
						{html_options values=$tpl.mins selected=$quiz->nQuestions output=$tpl.mins}
					</select> {tr}randomly selected questions.{/tr}
				</div>
			</div>
		</div>
	</div>
	<div class="form-group">
		<label class="control-label col-sm-2 text-left">{tr}Test-time Options{/tr}</label>
		<div class="col-sm-7 form-control-static">
			[ <a class="link" href="javascript:show('test-time');">{tr}Show{/tr}</a>
			| <a class="link" href="javascript:hide('test-time');">{tr}Hide{/tr}</a> ]
			<div id="test-time" style="display:none;">
				<div class="table-responsive">
					<table class="table">
						<tr>
							<td>
								<input type="checkbox" name=quiz.shuffleQuestions id="shuffle-questions" {if $quiz->shuffleQuestions eq 'y'}checked="checked"{/if}>
								<label for="shuffle-questions">{tr}Shuffle questions{/tr}</label>
							</td>
						</tr>
						<tr>
							<td>
								<input type="checkbox" name=quiz.shuffleAnswers id="shuffle-answers" {if $quiz->shuffleAnswers eq 'y'}checked="checked"{/if}>
								<label for="shuffle-answers">{tr}Shuffle answers{/tr}</label>
							</td>
						</tr>
						<tr>
							<td>
								<div class="form-inline">
									<input type="checkbox" name=quiz.limitDisplay id="quiz-display-limit" {if $quiz->limitDisplay eq 'y'}checked="checked"{/if}>
									<label for="quiz-display-limit">{tr}Limit questions displayed per page to {/tr}</label>
									<select name=quiz.questionsPerPage id="quiz-perpage" class="form-control">
									{html_options values=$tpl.qpp selected=$quiz->questionsPerPage output=$tpl.qpp}
									</select> {tr}question(s).{/tr}
								</div>
							</td>
						</tr>
						<tr>
							<td>
								<div class="form-inline">
									<input type="checkbox" name=quiz.timeLimited id="timelimit" {if $quiz->timeLimited eq 'y'}checked="checked"{/if}>
									<label for="timelimit">{tr}Impose a time limit of {/tr}</label>
									<select name=quiz.timeLimit id="quiz-maxtime" class="form-control">{html_options values=$tpl.mins selected=$quiz->timeLimit output=$tpl.mins}</select> {tr}minutes{/tr}
								</div>
							</td>
						</tr>
						<tr>
							<td>
								<input type="checkbox" name=quiz.multiSession id="quiz-multi-session" {if $quiz->multiSession eq 'y'}checked="checked"{/if}>
								<label for="quiz-multi-session">{tr}Allow students to store partial results and return to quiz.{/tr}</label>
							</td>
						</tr>
						<tr>
							<td>
								<div class="form-inline">
									<input type="checkbox" name="quiz.canRepeat" id="repeat"{if $quiz->canRepeat eq 'y'} checked="checked"{/if}>
									<label for="repeat">{tr}Allow students to retake this quiz {/tr}</label>
									<select name=quiz.repetitions id="quiz-repeat" class="form-control">
									{html_options values=$tpl.repetitions selected=$quiz->repetitions output=$tpl.repetitions}
									</select> {tr}times{/tr}
								</div>
							</td>
						</tr>
					</table>
				</div>
			</div>
		</div>
	</div>

	<div class="form-group">
		<label class="control-label col-sm-2 text-left">{tr}Grading and Feedback{/tr}</label>
		<div class="col-sm-7 form-control-static">
			[ <a class="link" href="javascript:show('feedback');">{tr}Show{/tr}</a>
			| <a class="link" href="javascript:hide('feedback');">{tr}Hide{/tr}</a> ]
			<div id="feedback" style="display:none;">
				<div class="table-responsive">
					<table class="table">
						<tr>
							<td colspan=2>
								<div class="form-inline">
									<label>{tr}Grading method {/tr}</label>
									<select name=quiz.gradingMethod id="grading-method" class="form-control">
										{html_options values=$tpl.optionsGrading selected=$quiz->gradingMethod output=$tpl.optionsGrading}
									</select>
								</div>
							</td>
						</tr>
						<tr>
							<td colspan=2>
								<div class="form-inline">
									<label>{tr}Show students their score {/tr}</label>
									<select name=quiz.showScore id="showScore" class="form-control">
										{html_options values=$tpl.optionsShowScore selected=$quiz->showScore output=$tpl.optionsShowScore}
									</select>
								</div>
							</td>
						</tr>
						<tr>
							<td>
								<div class="form-inline">
									<label>{tr}Show students the correct answers{/tr}</label>
									<select name=quiz.showCorrectAnswers class="form-control">
										{html_options values=$tpl.optionsShowScore selected=$quiz->showCorrectAnswers output=$tpl.optionsShowScore}
									</select>
								</div>
							</td>
						</tr>
						<tr>
							<td>
								<div class="form-inline">
									<label>{tr}Publish statistics {/tr}</label>
									<select name=quiz.publishStats class="form-control">
										{html_options values=$tpl.optionsShowScore selected=$quiz->publishStats output=$tpl.optionsShowScore}
									</select>
								</div>
							</td>
						</tr>
					</table>
				</div>
			</div>
		</div>
	</div>

	<div class="form-group">
		<label class="control-label col-sm-2 text-left">{tr}Extra Options{/tr}</label>
		<div class="col-sm-7 form-control-static">
			[ <a class="link" href="javascript:show('after-test');">{tr}Show{/tr}</a>
			| <a class="link" href="javascript:hide('after-test');">{tr}Hide{/tr}</a> ]
			<div id="after-test" style="display:none;">
				<label class="control-label col-sm-3">{tr}Questions{/tr}</label>
				<div class="col-sm-9">
					<label><input type='checkbox' name='quiz.additionalQuestions' {if $quiz->additionalQuestions eq 'y'}checked="checked"{/if}> {tr}Solicit additional questions from students{/tr}</label>
				</div>
				<label class="control-label col-sm-3">{tr}Link quiz to forum named: {/tr}</label>
				<div class="col-sm-9">
					<div class="form-inline">
						<label><input type="checkbox" name="quiz.forum" id="forum" {if $quiz->forum eq 'y'}checked="checked"{/if}> {tr}Yes{/tr}
						</label>
					 	<input type="text" name="quiz.forumName" value="{$quiz->nameForum|escape}" size="40" class="form-control">
				 	</div>
				</div>
			</div>
		</div>
	</div>
	{include file='categorize.tpl'}

	<div class="table-responsive">
		<table class="table">
			<tr>
				<td>
					{tr}Prologue:{/tr}
				</td>
				<td>
					<textarea class="wikiedit form-control" name="quiz.prologue" rows="8" {* rows="20" *} cols="80" id='subheading' wrap="virtual" >{$quiz->prologue}</textarea>
				</td>
			</tr>
			<tr>
				<td>
					{tr}Epilogue:{/tr}
				</td>
				<td>
					<textarea class="wikiedit form-control" name="quiz.epilogue" rows="8" {* rows="20" *} cols="80" id='subheading' wrap="virtual" >{$quiz->epilogue}</textarea>
				</td>
			</tr>

			<tr>
				<td>
				</td>
				<td>
					<input type="submit" class="wikiaction btn btn-default" name="save" value="{tr}Save{/tr}">
					<a class="btn btn-default" href="tiki-index.php?page={$page|escape:"url"}">{tr}Cancel Edit{/tr}</a>
				</td>
			</tr>
		</table>
	</div>
</form>

<!- tiki-quiz_edit end ->
