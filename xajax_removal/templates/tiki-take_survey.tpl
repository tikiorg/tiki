<form name="aform" formId='editpageform' action="{$form_action|default:'tiki-take_survey.php'}" method="post">
<input type="hidden" name="surveyId" value="{$surveyId|escape}" />
<input type="hidden" name="vote" value="yes" />
  {if !isset($show_name) or $show_name eq 'y'}{title}{$survey_info.name|escape}{/title}{/if}
	{if $error_msg neq ''}
		{remarksbox type="warning" title="{tr}Warning{/tr}"}{$error_msg}{/remarksbox}
	{/if}
    <div class="description">{wiki}{$survey_info.description}{/wiki}</div>
    {section name=ix loop=$questions}
    <div class="questionblock">
      <div class="quizquestion">{$questions[ix].question|escape|nl2br}</div>
      {if $questions[ix].type eq 'c'}
        <div class="quizoptions">
          {section name=jx loop=$questions[ix].qoptions}
            <input type="radio" value="{$questions[ix].qoptions[jx].optionId|escape}" name="question_{$questions[ix].questionId}" />{$questions[ix].qoptions[jx].qoption|escape}<br />
          {/section}
        </div>  
      {elseif $questions[ix].type eq 't'}
        <div class="quizoptions">
          <input type="text" name="question_{$questions[ix].questionId}" />
        </div>  
      {elseif $questions[ix].type eq 'x'}
        {assign var='area' value=$questions[ix].questionId}

				{if $questions[ix].explode.0 > 0}
					{assign var='textrows' value=$questions[ix].explode.0}
				{else}
					{assign var='textrows' value=$rows}
				{/if}

				{if $questions[ix].explode.1 > 0}
					{assign var='textcols' value=$questions[ix].explode.1}
				{else}
					{assign var='textcols' value=$cols}
				{/if}

        <div class="quizoptions">
          <table class="normal">
            <tr>
              <td valign="top">
              	&nbsp;
              </td>
              <td valign="top">
                {toolbars area_id=question_$area qtnum='2'}
                <textarea id="question_{$questions[ix].questionId}" name="question_{$questions[ix].questionId}" rows="{$textrows}" cols="{$textcols}"></textarea>
              </td>
            </tr>
          </table>
        </div>  
      {elseif $questions[ix].type eq 'm'}
        <div class="quizoptions">
          {section name=jx loop=$questions[ix].qoptions}
            <input type="checkbox" value="{$questions[ix].qoptions[jx].optionId|escape}" name="question_{$questions[ix].questionId}[{$questions[ix].qoptions[jx].optionId}]" />{$questions[ix].qoptions[jx].qoption}<br />
          {/section}
        </div>  
      {elseif $questions[ix].type eq 'r' or $questions[ix].type eq 's'}
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
      {elseif $questions[ix].type eq 'g'}
        {fgal_browse _id=$questions[ix].explode.0 show_selectall='n' show_infos='n' checkbox_label="{tr}Choose{/tr}" file_checkbox_name="question_`$questions[ix].questionId`"}
      {/if}
    </div>
  {/section}
<input type="submit" value="{tr}Send Answers{/tr}" name="ans" />
</form>
