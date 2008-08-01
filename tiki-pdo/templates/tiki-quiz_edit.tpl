{* $Id$ *}

{* Copyright (c) 2004 George G. Geller et. al. *}
{* All Rights Reserved. See copyright.txt for details and a complete list of authors. *}
{* Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details. *}

<!- tiki-quiz_edit.tpl start ->

<h1><a class="pagetitle" href="tiki-quiz_edit.php?quizId={$quiz->id}">{tr}Edit quiz{/tr}: {$quiz->name}</a>
{if $prefs.feature_help}
&nbsp &nbsp &nbsp<a title="{tr}Help{/tr}" href="{$prefs.helpurl}Quiz" target="help"><img src="img/icons/help.gif" border="0" height="16" width="16" alt='{tr}Help{/tr}' /></a>
{/if}
</h1>
<a class="linkbut" href="tiki-list_quizzes.php">{tr}List Quizzes{/tr}</a>
<a class="linkbut" href="tiki-quiz_stats.php">{tr}Quiz Stats{/tr}</a>
<a class="linkbut" href="tiki-quiz_stats_quiz.php?quizId={$quiz->id}">{tr}This Quiz Stats{/tr}</a>
<a class="linkbut" href="tiki-quiz_edit.php">{tr}Admin Quizzes{/tr}</a>
<br />
<br />

<form enctype="multipart/form-data" method="post" action="tiki-quiz_edit.php">
	<input type="hidden" name="quiz.id" value="{$quiz->id}" />
	<table class="normal">
		<tr class="formcolor">
			<td>{tr}Status{/tr}</td>
			<td width="85%" {if $cols} colspan="{$cols}"{/if}>
        [ <a class="link" href="javascript:show('status');">{tr}Show{/tr}</a>
 				| <a class="link" href="javascript:hide('status');">{tr}Hide{/tr}</a> ]
 				<div id="status" style="display:none;">
					<table class="normal">
						<tr>
							<td class="formcolor">
								{if $quiz->online eq 'y'}
									{html_radios name=quiz.online options=$tpl.online_choices selected=online}
								{else}
									{html_radios name=quiz.online options=$tpl.online_choices selected=offline}
								{/if}
							</td>
						</tr>
						{if $quiz->taken eq 'y'}
							<tr>
								<td class="formcolor" colspan=2>{tr}Current Version{/tr}: {$quiz->version}{if $quiz->id eq 0}, Author: {$quiz->authorLogin}, Date: {$quiz->timestamp|tiki_long_datetime}{/if}
								</td>
							</tr>
							{foreach from=$quiz->history item=history}
								<tr>
									<td class="formcolor" colspan=2>{$history}</td>
								</tr>
							{/foreach}
						{/if}
					</table>
			  </div>
			</td>
		</tr>

		<tr class="formcolor">
			<td>{tr}General Options{/tr}</td>
			<td width="85%" {if $cols} colspan="{$cols}"{/if}>
        [ <a class="link" href="javascript:show('general');">{tr}Show{/tr}</a>
 				| <a class="link" href="javascript:hide('general');">{tr}Hide{/tr}</a> ]
 				<div id="general" style="display:none;">
					<table class="normal">
						<tr class="formcolor">
							<td><label for="quiz-name">{tr}Name:{/tr}</label></td>
							<td><input type="text" name=quiz.name id="quiz-name" value="{$quiz->name|escape}" size="60" /></td>
						</tr>
						<tr class="formcolor">
							<td><label for="quiz-desc">Description:</label></td>
							<td><input type="text" name=quiz.description id="quiz-desc" value="{$quiz->description|escape}" size="60" /></td>
						</tr>
						<tr class="formcolor">
      				<td>{tr}Publication Date{/tr}</td>
      				<td>
								{html_select_date prefix="quiz_publish_" time=$quiz->datePub start_year="-5" end_year="+10" field_order=$prefs.display_field_order} {tr}at {/tr}{html_select_time prefix="quiz_publish_" time=$quiz->datePub display_seconds=false} HRS&nbsp;{$tpl.siteTimeZone} 
							</td>
						</tr>
						<tr class="formcolor">
							<td>{tr}Expiration Date{/tr}</td>
							<td>
								{html_select_date prefix="quiz_expire_" time=$quiz->dateExp start_year="-5" end_year="+10" field_order=$prefs.display_field_order} {tr}at {/tr}{html_select_time prefix="quiz_expire_" time=$quiz->dateExp display_seconds=false} HRS&nbsp;{$tpl.siteTimeZone}
							</td>
						</tr>
						<tr class="formcolor">
							<td>{tr}Questions{/tr}</td>
  						<td class="formcolor"><input type="checkbox" name=quiz.nQuestion id="nQuestion" {if $quiz->nQuestion eq 'y'}checked="checked"{/if} /><label for="nQuestions">{tr}Use {/tr}</label><select name=quiz.nQuestions id="nQuestions">{html_options values=$tpl.mins selected=$quiz->nQuestions output=$tpl.mins}</select> {tr}randomly selected questions.{/tr}</td>
						</tr>
					</table>
			  </div>
			</td>
		</tr>

		<tr class="formcolor">
			<td>{tr}Test-time Options{/tr}</td>
			<td {if $cols} colspan="{$cols}"{/if}>
				[ <a class="link" href="javascript:show('test-time');">{tr}Show{/tr}</a>
 				| <a class="link" href="javascript:hide('test-time');">{tr}Hide{/tr}</a> ]
 				<div id="test-time" style="display:none;">
					<table class="normal">
						<tr>
  						<td class="formcolor"><input type="checkbox" name=quiz.shuffleQuestions id="shuffle-questions" {if $quiz->shuffleQuestions eq 'y'}checked="checked"{/if} /><label for="shuffle-questions">{tr}Shuffle questions{/tr}</td>
						</tr>
						<tr>
  						<td class="formcolor"><input type="checkbox" name=quiz.shuffleAnswers id="shuffle-answers" {if $quiz->shuffleAnswers eq 'y'}checked="checked"{/if} /><label for="shuffle-answers">{tr}Shuffle answers{/tr}</td>
						</tr>
						<tr>
  						<td class="formcolor"><input type="checkbox" name=quiz.limitDisplay id="quiz-display-limit" {if $quiz->limitDisplay eq 'y'}checked="checked"{/if} /><label for="quiz-display-limit">{tr}Limit questions displayed per page to {/tr}</label><select name=quiz.questionsPerPage id="quiz-perpage">{html_options values=$tpl.qpp selected=$quiz->questionsPerPage output=$tpl.qpp}</select>{tr}&nbsp question(s).{/tr}</td>
						</tr>
						<tr>
  						<td class="formcolor"><input type="checkbox" name=quiz.timeLimited id="timelimit" {if $quiz->timeLimited eq 'y'}checked="checked"{/if} /><label for="timelimit">{tr}Impose a time limit of {/tr}</label><select name=quiz.timeLimit id="quiz-maxtime">{html_options values=$tpl.mins selected=$quiz->timeLimit output=$tpl.mins}</select> {tr}minutes{/tr}</td>
						</tr>
						<tr>
							<td class="formcolor"><input type="checkbox" name=quiz.multiSession id="quiz-multi-session" {if $quiz->multiSession eq 'y'}checked="checked"{/if} /><label for="quiz-multi-session">{tr}Allow students to store partial results and return to quiz.{/tr}</td>
						</tr>
						<tr>
							<td class="formcolor"><input type="checkbox" name=quiz.canRepeat id="repeat" {if $quiz->canRepeat eq 'y'}checked="checked"{/if} /><label for="repeat">{tr}Allow students to retake this quiz {/tr}</label>
							<select name=quiz.repetitions id="quiz-repeat">{html_options values=$tpl.repetitions selected=$quiz->repetitions output=$tpl.repetitions}</select> {tr}times{/tr}</td>
						</tr>
					</table>
			  </div>
			</td>
		</tr>

		<tr class="formcolor">
			<td>{tr}Grading and Feedback{/tr}</td>
			<td {if $cols} colspan="{$cols}"{/if}>
				[ <a class="link" href="javascript:show('feedback');">{tr}Show{/tr}</a>
 				| <a class="link" href="javascript:hide('feedback');">{tr}Hide{/tr}</a> ]
 				<div id="feedback" style="display:none;">
					<table class="normal">
						<tr>
							<td colspan=2 class="formcolor"><label>{tr}Grading method {/tr}</label><select name=quiz.gradingMethod" id="grading-method">{html_options values=$tpl.optionsGrading selected=$quiz->gradingMethod output=$tpl.optionsGrading}</select>
              </td>
						</tr>
						<tr>
							<td colspan=2 class="formcolor"><label>{tr}Show students their score {/tr}</label><select name=quiz.showScore id="showScore">{html_options values=$tpl.optionsShowScore selected=$quiz->showScore output=$tpl.optionsShowScore}</select>
              </td>
						</tr>
						<tr>
							<td class="formcolor"><label>{tr}Show students the correct answers {/tr}</label><select name=quiz.showCorrectAnswers>{html_options values=$tpl.optionsShowScore selected=$quiz->showCorrectAnswers output=$tpl.optionsShowScore}</select>
              </td>
						</tr>
						<tr>
							<td class="formcolor"><label>{tr}Publish statistics {/tr}</label><select name=quiz.publishStats>{html_options values=$tpl.optionsShowScore selected=$quiz->publishStats output=$tpl.optionsShowScore}</select>
						</tr>
					</table>
			  </div>
			</td>
		</tr>
		<tr class="formcolor">
			<td>{tr}Extra Options{/tr}</td>
			<td {if $cols} colspan={$cols}{/if}>
				[ <a class="link" href="javascript:show('after-test');">{tr}Show{/tr}</a>
 				| <a class="link" href="javascript:hide('after-test');">{tr}Hide{/tr}</a> ]
 				<div id="after-test" style="display:none;">
					<table class="normal">
						<tr>
							<td class="formcolor"><input type=checkbox name=quiz.additionalQuestions {if $quiz->additionalQuestions eq 'y'}checked="checked"{/if} /><label for="additional-questions">{tr}Solicit additional questions from students{/tr}</td>
						</tr>
					</table>
					<table class="normal">
						<tr>
							<td class="formcolor"><input type="checkbox" name="quiz.forum" id="forum" {if $quiz->forum eq 'y'}checked="checked"{/if} /><label>{tr}Link quiz to forum named: {/tr}</label><input type="text" name="quiz.forumName" value="{$quiz->nameForum|escape}" size="40" /></td>
						</tr>
				  </table>
			  </div>
			</td>
		</tr>
    {include file=categorize.tpl}
  </table>
	<table class="normal">
    <tr>
      <td class="formcolor">
        {tr}Prologue:{/tr}
      </td>
      <td class="formcolor">
        <textarea class="wikiedit" name="quiz.prologue" rows="8" {* rows="20" *} cols="80" id='subheading' wrap="virtual" >{$quiz->prologue}</textarea>
      </td>
    </tr>
    <tr>
      <td class="formcolor">
        {tr}Epilogue:{/tr}
      </td>
      <td class="formcolor">
        <textarea class="wikiedit" name="quiz.epilogue" rows="8" {* rows="20" *} cols="80" id='subheading' wrap="virtual" >{$quiz->epilogue}</textarea>
      </td>
    </tr>

{*  TODO: Version 2 should support preview, xml view and text view.
		<tr class="formcolor">
      <td class="formcolor">
      </td>
      <td class="formcolor">
				<input type="submit" class="wikiaction" name="preview" value="{tr}Preview{/tr}" />
				<input type="submit" class="wikiaction" name="xmlview" value="{tr}xml view{/tr}" />
				<input type="submit" class="wikiaction" name="textview" value="{tr}text view{/tr}" />
      </td>
    </tr>
*}

		<tr class="formcolor">
      <td class="formcolor">
      </td>
      <td class="formcolor">
				<input type="submit" class="wikiaction" name="save" value="{tr}Save{/tr}" /> <a class="link" href="tiki-index.php?page={$page|escape:"url"}">{tr}Cancel Edit{/tr}</a>
      </td>
    </tr>
  </table>
</form>

<!- tiki-quiz_edit end ->
