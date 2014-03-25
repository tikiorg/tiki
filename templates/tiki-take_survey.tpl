<form name="aform" formId='editpageform' action="{$form_action|default:'tiki-take_survey.php'}" method="post">
	<input type="hidden" name="surveyId" value="{$surveyId|escape}">
	<input type="hidden" name="vote" value="yes">
	{if !isset($show_name) or $show_name eq 'y'}
		{title url="tiki-take_survey.php?surveyId=$surveyId"}{$survey_info.name}{/title}
	{/if}
	{if $error_msg neq ''}
		{remarksbox type="warning" title="{tr}Warning{/tr}"}{$error_msg}{/remarksbox}
	{/if}
	<div class="description help-block">{wiki}{$survey_info.description}{/wiki}</div>
	{section name=ix loop=$questions}
		{$questionId = 'question_'|cat:$questions[ix].questionId}
		{if empty($smarty.request.$questionId)}{$answer=''}{else}{$answer = $smarty.request.$questionId}{/if}
		<div class="questionblock">
			<div class="quizquestion">{$questions[ix].question|escape|nl2br}</div>
			{if $questions[ix].type eq 'c'}
				<div class="quizoptions">
					{section name=jx loop=$questions[ix].qoptions}
						<label>
							<input type="radio" value="{$questions[ix].qoptions[jx].optionId|escape}" name="{$questionId}"
								   {if $answer eq $questions[ix].qoptions[jx].optionId} checked="checked"{/if}>
							{$questions[ix].qoptions[jx].qoption}
						</label>
					{/section}
				</div>
			{elseif $questions[ix].type eq 't'}
				<div class="quizoptions">
					{if $questions[ix].cols > 0}
						{assign var='textcols' value=$questions[ix].cols}
					{else}
						{assign var='textcols' value=80}
					{/if}
					<input type="text" size="{$textcols}" name="{$questionId}" value="{$answer}">
				</div>
			{elseif $questions[ix].type eq 'x'}
				{assign var='area' value=$questions[ix].questionId}

				{if $questions[ix].explode.0 > 0}
					{assign var='textrows' value=$questions[ix].explode.0}
				{else}
					{assign var='textrows' value=20}
				{/if}

				{if $questions[ix].explode.1 > 0}
					{assign var='textcols' value=$questions[ix].explode.1}
				{else}
					{assign var='textcols' value=80}
				{/if}
				<div class="quizoptions">
					<table class="formcolor">
						<tr>
							<td valign="top">&nbsp;

							</td>
							<td valign="top">
								{if $showToolBars}{toolbars area_id="question_$area" qtnum='2'}{/if}
								<textarea id="{$questionId}"  name="{$questionId}" rows="{$textrows}"
										  cols="{$textcols}">{$answer}</textarea>
							</td>
						</tr>
					</table>
				</div>
			{elseif $questions[ix].type eq 'm'}
				<div class="quizoptions">
					{section name=jx loop=$questions[ix].qoptions}
						<label>
							<input type="checkbox" value="{$questions[ix].qoptions[jx].optionId|escape}" name="{$questionId}[{$questions[ix].qoptions[jx].optionId}]"
									{if in_array($questions[ix].qoptions[jx].optionId, $answer)}checked="checked"{/if}>
							{$questions[ix].qoptions[jx].qoption}
						</label>
					{/section}
				</div>
			{elseif $questions[ix].type eq 'r' or $questions[ix].type eq 's'}
				<div class="quizoptions">
					{if $questions[ix].options}
						{foreach from=$questions[ix].explode key=k item=j}
							<label>
								{$k}
								<input type="radio" value="{$k}" name="{$questionId}"{if $answer eq $k} checked="checked"{/if}>
							</label>
						{/foreach}
					{elseif $questions[ix].type eq 'r'}
						1
						<input type="radio" value="1" name="{$questionId}"{if $answer eq 1} checked="checked"{/if}>
						<input type="radio" value="2" name="{$questionId}"{if $answer eq 2} checked="checked"{/if}>
						<input type="radio" value="3" name="{$questionId}"{if $answer eq 3} checked="checked"{/if}>
						<input type="radio" value="4" name="{$questionId}"{if $answer eq 4} checked="checked"{/if}>
						<input type="radio" value="5" name="{$questionId}"{if $answer eq 5} checked="checked"{/if}>
						5
					{elseif $questions[ix].type eq 's'}
						1
						<input type="radio" value="1" name="{$questionId}"{if $answer eq 1} checked="checked"{/if}>
						<input type="radio" value="2" name="{$questionId}"{if $answer eq 2} checked="checked"{/if}>
						<input type="radio" value="3" name="{$questionId}"{if $answer eq 3} checked="checked"{/if}>
						<input type="radio" value="4" name="{$questionId}"{if $answer eq 4} checked="checked"{/if}>
						<input type="radio" value="5" name="{$questionId}"{if $answer eq 5} checked="checked"{/if}>
						<input type="radio" value="6" name="{$questionId}"{if $answer eq 6} checked="checked"{/if}>
						<input type="radio" value="7" name="{$questionId}"{if $answer eq 7} checked="checked"{/if}>
						<input type="radio" value="8" name="{$questionId}"{if $answer eq 8} checked="checked"{/if}>
						<input type="radio" value="9" name="{$questionId}"{if $answer eq 9} checked="checked"{/if}>
						<input type="radio" value="10" name="{$questionId}"{if $answer eq 10} checked="checked"{/if}>
						10
					{/if}
				</div>
			{elseif $questions[ix].type eq 'g'}
				{fgal_browse _id=$questions[ix].explode.0 show_selectall='n' show_infos='n' checkbox_label="{tr}Choose{/tr}" file_checkbox_name=$questionId}
			{/if}
		</div>
	{/section}
	<input type="submit" class="btn btn-default btn-sm" value="{tr}Send Answers{/tr}" name="ans">
</form>
