<form name="aform" action="tiki-take_quiz.php" method="post">
<input type="hidden" name="quizId" value="{$quizId}" />
<input id='timeleft' name="timeleft" type="hidden" value={$quiz_info.timeLimitsec} /><br/>
{if $quiz_info.timeLimited eq 'y'}
{tr}Time Left{/tr}:<input id='minleft' name="minleft" type="text" size="3" value=0 />:<input size="3" id='secleft' name="secleft" type="text" value=0 />
{/if}

{if $ans eq 'n'}
{if $quiz_info.timeLimited eq 'y'}
<script>
{literal}
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
{/literal}
</script>
{/if}
{/if}

<h2>{$quiz_info.name}</h2>
<div class="quizdescription">{$quiz_info.description}</div>
{if $ans eq 'n'}
{section name=ix loop=$questions}
<div class="questionblock">
<div class="quizquestion">{$questions[ix].question}</div>
<div class="quizoptions">
  {section name=jx loop=$questions[ix].options}
  <input type="radio" value="{$questions[ix].options[jx].optionId}" name="question_{$questions[ix].questionId}">{$questions[ix].options[jx].optionText}<br/>
  {/section}
</div>  
</div>  
{/section}
<input type="submit" value="{tr}send answers{/tr}" name="ans" />
{/if}
{if $ans eq 'y'}
{tr}Result{/tr}:
<div class="quizanswer">
{$result.answer}
</div>
{/if}
</form>