<form name="aform" action="tiki-take_survey.php" method="post">
<input type="hidden" name="surveyId" value="{$surveyId|escape}" />
<h2>{$survey_info.name}</h2>
<div class="quizdescription">{$survey_info.description}</div>
{section name=ix loop=$questions}
<div class="questionblock">
<div class="quizquestion">{$questions[ix].question}</div>
{if $questions[ix].type eq 'c'}
<div class="quizoptions">
  {section name=jx loop=$questions[ix].qoptions}
  <input type="radio" value="{$questions[ix].qoptions[jx].optionId|escape}" name="question_{$questions[ix].questionId}" />{$questions[ix].qoptions[jx].qoption}<br />
  {/section}
</div>  
{/if}
{if $questions[ix].type eq 't'}
<div class="quizoptions">
<input type="text" name="question_{$questions[ix].questionId}" />
</div>  
{/if}
{if $questions[ix].type eq 'm'}
<div class="quizoptions">
  {section name=jx loop=$questions[ix].qoptions}
  <input type="checkbox" value="{$questions[ix].qoptions[jx].optionId|escape}" name="question_{$questions[ix].questionId}[{$questions[ix].qoptions[jx].optionId}]" />{$questions[ix].qoptions[jx].qoption}<br />
  {/section}
</div>  
{/if}
{if $questions[ix].type eq 'r'}
<div class="quizoptions">
1<input type="radio" value="1" name="question_{$questions[ix].questionId}" />
<input type="radio" value="2" name="question_{$questions[ix].questionId}" />
<input type="radio" value="3" name="question_{$questions[ix].questionId}" />
<input type="radio" value="4" name="question_{$questions[ix].questionId}" />
<input type="radio" value="5" name="question_{$questions[ix].questionId}" />5
</div>  
{/if}
{if $questions[ix].type eq 's'}
<div class="quizoptions">
1<input type="radio" value="1" name="question_{$questions[ix].questionId}" />
<input type="radio" value="2" name="question_{$questions[ix].questionId}" />
<input type="radio" value="3" name="question_{$questions[ix].questionId}" />
<input type="radio" value="4" name="question_{$questions[ix].questionId}" />
<input type="radio" value="5" name="question_{$questions[ix].questionId}" />
<input type="radio" value="6" name="question_{$questions[ix].questionId}" />
<input type="radio" value="7" name="question_{$questions[ix].questionId}" />
<input type="radio" value="8" name="question_{$questions[ix].questionId}" />
<input type="radio" value="9" name="question_{$questions[ix].questionId}" />
<input type="radio" value="10" name="question_{$questions[ix].questionId}" />10
</div>  
{/if}
</div>
{/section}
<input type="submit" value="{tr}send answers{/tr}" name="ans" />
</form>