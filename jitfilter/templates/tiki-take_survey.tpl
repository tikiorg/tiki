<form name="aform" formId='editpageform' action="tiki-take_survey.php" method="post">
<input type="hidden" name="surveyId" value="{$surveyId|escape}" />
<input type="hidden" name="vote" value="yes" />
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

      {if $questions[ix].type eq 'x'}
      {assign var='area' value=$questions[ix].questionId}

        <div class="quizoptions">
          <table class="normal">
            <tr>
              <td valign="top">
                {include file="textareasize.tpl" area_name='editwiki' formId='editpageform'}
                <br /><br />
                {if $prefs.quicktags_over_textarea neq 'y'}
                  {include file=tiki-edit_help_tool.tpl area_name=question_$area qtnum='2'}
                {/if}
              </td>
              <td valign="top">
                {if $prefs.quicktags_over_textarea eq 'y'}
                  {include file=tiki-edit_help_tool.tpl area_name=question_$area qtnum='2'}
                {/if}
                <textarea id='editwiki' name="question_{$questions[ix].questionId}" rows="{$rows}" cols="{$cols}"></textarea>
              </td>
            </tr>
          </table>
        </div>  
      {/if}

      {if $questions[ix].type eq 'm'}
        <div class="quizoptions">
          {section name=jx loop=$questions[ix].qoptions}
            <input type="checkbox" value="{$questions[ix].qoptions[jx].optionId|escape}" name="question_{$questions[ix].questionId}[{$questions[ix].qoptions[jx].optionId}]" />{$questions[ix].qoptions[jx].qoption}<br />
          {/section}
        </div>  
      {/if}

      {if $questions[ix].type eq 'r' or $questions[ix].type eq 's'}
        <div class="quizoptions">
          {if $questions[ix].options}
            {foreach from=$questions[ix].explode key=k item=j}
              {$k}<input type="radio" value="{$k}" name="question_{$questions[ix].questionId}" />
            {/foreach}
          {elseif $questions[ix].type eq 'r'}
            1<input type="radio" value="1" name="question_{$questions[ix].questionId}" />
            <input type="radio" value="2" name="question_{$questions[ix].questionId}" />
            <input type="radio" value="3" name="question_{$questions[ix].questionId}" />
            <input type="radio" value="4" name="question_{$questions[ix].questionId}" />
            <input type="radio" value="5" name="question_{$questions[ix].questionId}" />5
          {elseif $questions[ix].type eq 's'}
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
          {/if}
        </div>
      {/if}
    </div>
  {/section}
<input type="submit" value="{tr}Send Answers{/tr}" name="ans" />
</form>
