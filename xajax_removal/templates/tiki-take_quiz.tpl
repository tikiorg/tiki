<!- templates/tiki-take_quiz.tpl start ->
<form enctype="multipart/form-data" name="aform" action="tiki-take_quiz.php" method="post">
<input type="hidden" name="quizId" value="{$quizId|escape}" />
<input id='timeleft' name="timeleft" type="hidden" value={$quiz_info.timeLimitsec} /><br />
{if $quiz_info.timeLimited eq 'y'}
{tr}Time Left:{/tr}<input id='minleft' name="minleft" type="text" size="3" value=0 />:<input size="3" id='secleft' name="secleft" type="text" value=0 />
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

<h2>{$quiz_info.name|escape}</h2>
<div class="description">{$quiz_info.description|escape}</div>
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
{tr}Result:{/tr}
<div class="quizanswer">
{if $result.answer}
{$result.answer|escape|nl2br}
{else}
{tr}Thank you for your submission{/tr}.
{/if}
</div>
{/if}
</form>
<!- templates/tiki-take_quiz.tpl end ->
