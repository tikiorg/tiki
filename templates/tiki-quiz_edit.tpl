{* $Header: /cvsroot/tikiwiki/tiki/templates/tiki-quiz_edit.tpl,v 1.10 2004-05-18 21:01:33 ggeller Exp $ *}

{* Copyright (c) 2004 *}
{* All Rights Reserved. See copyright.txt for details and a complete list of authors. *}
{* Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details. *}

<!- tiki-quiz_edit.tpl start ->

<a class="pagetitle" href="tiki-quiz_edit.php?quizId={$quiz.quizId}">{tr}Edit quiz{/tr}: {$quiz.name}</a>
{if $feature_help}
&nbsp &nbsp &nbsp<a title="{tr}help{/tr}" href="http://tikiwiki.org/tiki-index.php?page=QuizEdit" target="help"><img border="0" alt="{tr}Help{/tr}" src="img/icons/help.gif" /></a>
{/if}
<br /><br />
<a class="linkbut" href="tiki-list_quizzes.php">{tr}list quizzes{/tr}</a>
<a class="linkbut" href="tiki-quiz_stats.php">{tr}quiz stats{/tr}</a>
<a class="linkbut" href="tiki-quiz_stats_quiz.php?quizId={$quiz.quizId}">{tr}this quiz stats{/tr}</a>
<a class="linkbut" href="tiki-quiz_edit.php">{tr}admin quizzes{/tr}</a>
<br />
<br />

<form enctype="multipart/form-data" method="post" action="tiki-quiz_edit.php?quizId={$quiz.quizId}">
	<table class="normal">
		<tr class="formcolor">
			<td>{tr}Status{/tr}</td>
			<td width="85%" {if $cols} colspan="{$cols}"{/if}>
        [ <a class="link" href="javascript:show('status');">{tr}show{/tr}</a>
 				| <a class="link" href="javascript:hide('status');">{tr}hide{/tr}</a> ]
 				<div id="status" style="display:none;">
					<table class="normal">
						<tr>
							<td class="formcolor">
								{html_radios name="online" options=$online_choices selected=$online}
							</td>
						</tr>
						{if $quiz.taken eq 'y'}
							<tr>
								<td class="formcolor" colspan=2>{tr}Current Version{/tr}: {$quiz.version}
								</td>
							</tr>
							{foreach from=$quiz.history item=history}
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
        [ <a class="link" href="javascript:show('general');">{tr}show{/tr}</a>
 				| <a class="link" href="javascript:hide('general');">{tr}hide{/tr}</a> ]
 				<div id="general" style="display:none;">
					<table class="normal">
						<tr class="formcolor">
							<td><label for="quiz-name">Name:</label></td>
							<td><input type="text" name="name" id="quiz-name" value="{$quiz.name|escape}" size="60"></td>
						</tr>
						<tr class="formcolor">
							<td><label for="quiz-desc">Description:</label></td>
							<td><input type="text" name="description" id="quiz-desc" value="{$quiz.description|escape}" size="60"></td>
						</tr>
						<tr class="formcolor">
      				<td>{tr}Publication Date{/tr}</td>
      				<td>
								{html_select_date prefix="publish_" time=$quiz.datePub start_year="-5" end_year="+10"} {tr}at {/tr}{html_select_time prefix="publish_" time=$quiz.dateExp display_seconds=false} HRS&nbsp;{$siteTimeZone} 
							</td>
						</tr>
						<tr class="formcolor">
							<td>{tr}Expiration Date{/tr}</td>
							<td>
								{html_select_date prefix="expire_" time=$quiz.dateExp start_year="-5" end_year="+10"} {tr}at {/tr}{html_select_time prefix="expire_" time=$quiz.dateExp display_seconds=false} HRS&nbsp;{$siteTimeZone}
							</td>
						</tr>
					</table>
			  </div>
			</td>
		</tr>

		<tr class="formcolor">
			<td>{tr}Test-time Options{/tr}</td>
			<td {if $cols} colspan="{$cols}"{/if}>
				[ <a class="link" href="javascript:show('test-time');">{tr}show{/tr}</a>
 				| <a class="link" href="javascript:hide('test-time');">{tr}hide{/tr}</a> ]
 				<div id="test-time" style="display:none;">
					<table class="normal">
						<tr>
  						<td class="formcolor"><input type="checkbox" name="shuffleQuestions" id="shuffle-questions" {if $quiz.shuffleQuestions eq 'y'}checked="checked"{/if} /><label for="shuffle-questions">{tr}Shuffle questions{/tr}</td>
						</tr>
						<tr>
  						<td class="formcolor"><input type="checkbox" name="shuffleAnswers" id="shuffle-answers" {if $quiz.shuffleAnswers eq 'y'}checked="checked"{/if} /><label for="shuffle-answers">{tr}Shuffle answers{/tr}</td>
						</tr>
						<tr>
  						<td class="formcolor"><input type="checkbox" name="limitDisplay" id="quiz-display-limit" {if $limitDisplay eq 'y'}checked="checked"{/if} /><label for="quiz-display-limit">{tr}Limit questions displayed per page to {/tr}</label><select name="questionsPerPage" id="quiz-perpage">{html_options values=$qpp selected=$quiz.questionsPerPage output=$qpp}</select>{tr}&nbsp question(s).{/tr}</td>
						</tr>
						<tr>
  						<td class="formcolor"><input type="checkbox" name="timeLimited" id="timelimit" {if $timeLimited eq 'y'}checked="checked"{/if} /><label for="timelimit">{tr}Impose a time limit of {/tr}</label><select name="timeLimit" id="quiz-maxtime">{html_options values=$mins selected=$timeLimit output=$mins}</select> {tr}minutes{/tr}</td>
						</tr>
						<tr>
							<td class="formcolor"><input type="checkbox" name="multiSession" id="quiz-multi-session" {if $quiz.multiSession eq 'y'}checked="checked"{/if} /><label for="quiz-multi-session">{tr}Allow students to store partial results and return to quiz.{/tr}</td>
						</tr>
						<tr>
							<td class="formcolor"><input type="checkbox" name="canRepeat" id="repeat" {if $canRepeat eq 'y'}checked="checked"{/if} /><label for="repeat">{tr}Allow students to retake this quiz {/tr}</label>
							<select name="repetitions" id="quiz-repeat">{html_options values=$repetitions selected=$repetitionLimit output=$repetitions}</select> {tr}times{/tr}</td>
						</tr>
					</table>
			  </div>
			</td>
		</tr>

		<tr class="formcolor">
			<td>{tr}Grading and Feedback{/tr}</td>
			<td {if $cols} colspan="{$cols}"{/if}>
				[ <a class="link" href="javascript:show('feedback');">{tr}show{/tr}</a>
 				| <a class="link" href="javascript:hide('feedback');">{tr}hide{/tr}</a> ]
 				<div id="feedback" style="display:none;">
					<table class="normal">
						<tr>
							<td colspan=2 class="formcolor"><label for="grading-method">{tr}Grading method {/tr}</label><select name="grading-method" id="grading-method">{html_options values=$optionsGrading selected=$quiz.grading output=$optionsGrading}</select>
              </td>
						</tr>
						<tr>
							<td colspan=2 class="formcolor"><label for="showScore">{tr}Show students their score {/tr}</label><select name="showScore" id="showScore">{html_options values=$optionsShowScore selected=$quiz.showScore output=$optionsShowScore}</select>
              </td>
						</tr>
						<tr>
							<td colspan=2 class="formcolor"><label for="showCorrectAnswers">{tr}Show students the correct answers {/tr}</label><select name="showCorrectAnswers" id="showCorrectAnswers">{html_options values=$optionsShowScore selected=$quiz.showCorrectAnswers output=$optionsShowScore}</select>
              </td>
						</tr>
						<tr>
							<td colspan=2 class="formcolor"><label for="publishStats">{tr}Publish statistics {/tr}</label><select name="publishStats" id="publishStats">{html_options values=$optionsShowScore selected=$quiz.publishStats output=$optionsShowScore}</select>
						</tr>
					</table>
			  </div>
			</td>
		</tr>
		<tr class="formcolor">
			<td>{tr}Extra Options{/tr}</td>
			<td {if $cols} colspan="{$cols}"{/if}>
				[ <a class="link" href="javascript:show('after-test');">{tr}show{/tr}</a>
 				| <a class="link" href="javascript:hide('after-test');">{tr}hide{/tr}</a> ]
 				<div id="after-test" style="display:none;">
					<table class="normal">
						<tr>
							<td class="formcolor"><input type="checkbox" name="additionalQuestions" id="additionalQuestions" {if $additionalQuestions eq 'y'}checked="checked"{/if} /><label for="additional-questions">{tr}Solicit additional questions from students{/tr}</td>
						</tr>
					</table>
					<table class="normal">
						<tr>
							<td colspan="2" class="formcolor"><input type="checkbox" name="forum" id="forum" {if $forum eq 'y'}checked="checked"{/if} /><label for="forum">{tr}Link quiz to forum named: {/tr}</label><input type="text" name="forumName" id="forumName" value="{$quiz_info.nameForum|escape}" size="40"></td>
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
        {tr}Edit:{/tr}
      </td>
      <td class="formcolor">
        <textarea class="wikiedit" name="input_data" rows="10" {* rows="20" *} cols="80" id='subheading' wrap="virtual" ></textarea>
      </td>
    </tr>
		<tr class="formcolor">
      <td class="formcolor">
      </td>
      <td class="formcolor">
				<input type="submit" class="wikiaction" name="preview" value="{tr}preview{/tr}" />
				<input type="submit" class="wikiaction" name="xmlview" value="{tr}xml view{/tr}" />
				<input type="submit" class="wikiaction" name="textview" value="{tr}text view{/tr}" />
      </td>
    </tr>
		<tr class="formcolor">
      <td class="formcolor">
      </td>
      <td class="formcolor">
				<input type="submit" class="wikiaction" name="save" value="{tr}save{/tr}" /> <a class="link" href="tiki-index.php?page={$page|escape:"url"}">{tr}cancel edit{/tr}</a></td>
      </td>
    </tr>
  </table>
</form>

<!- tiki-quiz_edit end ->
