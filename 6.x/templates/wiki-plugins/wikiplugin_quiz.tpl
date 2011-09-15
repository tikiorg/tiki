{strip}
<!- templates/wikiplugin/wikiplugin_quiz.tpl start ->
<div id="quizplugin{$quizId|escape}">
<form enctype="multipart/form-data" action="./" name="aform" onsubmit="return submitQuiz();">
<input type="hidden" name="quizId" value="{$quizId|escape}" />
<input id='timeleft' name="timeleft" type="hidden" value={$quiz_info.timeLimitsec} />
{if $quiz_info.timeLimited eq 'y'}
{tr}Time Left{/tr}:<input id='minleft' name="minleft" type="text" size="3" value=0 />:<input size="3" id='secleft' name="secleft" type="text" value=0 />
{/if}
{if $ans eq 'n'}
{if $quiz_info.timeLimited eq 'y'}
{jq}
var itid;
function settimeleft() {
  document.getElementById('timeleft').value -= 1;
  if(document.getElementById('timeleft').value<1) {
    window.clearInterval(itid); 
    document.aform.submit();
  }
  document.getElementById('minleft').value = Math.floor(document.getElementById('timeleft').value/60);
  document.getElementById('secleft').value = document.getElementById('timeleft').value%60; 
}
itid = window.setInterval('settimeleft();',1000); 
settimeleft(itid);
{/jq}
{/if}
{/if}
{if $showtitle eq 'y'}<h2>{$quiz_info.name|escape}</h2>{/if}
{if $showdescription eq 'y'}<div class="description">{$quiz_info.description|escape}</div>{/if}
{if $ans eq 'n'}
{section name=ix loop=$questions}
<div class="questionblock">
<div class="quizquestion">{$questions[ix].question|escape}</div>
<div class="quizoptions">
  {section name=jx loop=$questions[ix].options}
  <input type="radio" value="{$questions[ix].options[jx].optionId|escape}" name="question_{$questions[ix].questionId}" />{$questions[ix].options[jx].optionText|escape}<br />
  {/section}
</div>  
{if $questions[ix].type eq "f" }
<br />
<div class="quizupload">
Supporting Documentation: <input name="question_upload_{$questions[ix].questionId}" type="file" />
</div>
{/if}
</div>  
{/section}
<input type="submit" value="{tr}Send Answers{/tr}" name="ans" />
{/if}
{if $ans eq 'y'}
<p>{tr}Result{/tr}:
{$ur_info.points}/{$ur_info.maxPoints} ({math equation="(x/y)*100" x=$ur_info.points y=$ur_info.maxPoints}%)
{if $resultlink eq 'y'}, <a href="tiki-quiz_result_stats.php?quizId={$quizId|escape}&resultId=0&userResultId={$userResultId}">{tr}view details{/tr}</a>


{jq}
$(function() {
	$('<div />').load(tiki-quiz_result_stats.php?quizId={$quizId|escape}&resultId=0&userResultId={$userResultId}, function(html) {
	$(this).dialog(html);
}
}
{/jq}

{/if}</p>
<div class="quizanswer">
{if $result.answer}
{$result.answer|escape|nl2br}
{else}
{tr}Thank you for your submission{/tr}.
{/if}
</div>
{/if}
</form>
</div>
<!- templates/wikiplugin/wikiplugin_quiz.tpl end ->
{/strip}