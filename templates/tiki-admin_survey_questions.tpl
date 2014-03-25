{title url="tiki-admin_survey_questions.php?surveyId=$surveyId"}{tr}Edit survey questions:{/tr} {$survey_info.name}{/title}

<div class="t_navbar btn-group form-group">
	{button href="tiki-admin_survey_questions.php?surveyId=$surveyId" class="btn btn-default" _text="{tr}Add a New Question{/tr}"}
	{button href="tiki-list_surveys.php" class="btn btn-default" _text="{tr}List surveys{/tr}"}
	{button href="tiki-survey_stats.php" class="btn btn-default" _text="{tr}Survey Stats{/tr}"}
	{button href="tiki-survey_stats_survey.php?surveyId=$surveyId" class="btn btn-default" _text="{tr}This survey stats{/tr}"}
	{button href="tiki-admin_surveys.php?surveyId=$surveyId" class="btn btn-default" _text="{tr}Edit this Survey{/tr}"}
	{button href="tiki-admin_surveys.php" class="btn btn-default" _text="{tr}Admin Surveys{/tr}"}
</div>

{if !empty($questionId)}{$tablabel='Edit Survey Question'|tr_if}{else}{$tablabel='Add a New Question to this survey'|tr_if}{/if}
{tabset name='tabs_adminsurveyquestions'}
	{tab name="{tr}Questions{/tr}"}
		{include file='find.tpl'}
		<table class="table normal">
			<tr>
				<th>
					{self_link _sort_arg='sort_mode' _sort_field='questionId'}{tr}ID{/tr}{/self_link}
				</th>
				<th>
					{self_link _sort_arg='sort_mode' _sort_field='position'}{tr}Position{/tr}{/self_link}
				</th>
				<th>
					{self_link _sort_arg='sort_mode' _sort_field='question'}{tr}Question{/tr}{/self_link}
				</th>
				<th>
					{self_link _sort_arg='sort_mode' _sort_field='type'}{tr}Type{/tr}{/self_link}
				</th>
				<th>
					{self_link _sort_arg='sort_mode' _sort_field='options'}{tr}Options{/tr}{/self_link}
				</th>
				<th>{tr}Action{/tr}</th>
			</tr>
			{cycle print=false values="odd,even"}
			{section name=user loop=$channels}
				<tr>
					<td class="id">{$channels[user].questionId}</td>
					<td class="integer">{$channels[user].position}</td>
					<td class="text">{self_link questionId=$channels[user].questionId}{$channels[user].question|escape|nl2br}{/self_link}</td>
					<td class="text">{$channels[user].type}</td>
					<td class="text">{$channels[user].options}</td>
					<td class="action">
						{self_link _icon='page_edit' questionId=$channels[user].questionId}{tr}Edit{/tr}{/self_link}
						{self_link _icon='cross' remove=$channels[user].questionId}{tr}Delete{/tr}{/self_link}
					</td>
				</tr>
				{sectionelse}
				{norecords _colspan=6}
			{/section}
		</table>

		{pagination_links cant=$cant_pages step=$prefs.maxRecords offset=$offset}{/pagination_links}
	{/tab}
	{tab name=$tablabel}
		<form action="tiki-admin_survey_questions.php" method="post">
			<input type="hidden" name="surveyId" value="{$surveyId|escape}">
			<input type="hidden" name="questionId" value="{$questionId|escape}">
			<table class="formcolor">
				<tr>
					<td>{tr}Question:{/tr}</td>
					<td>
						<textarea name="question" rows="5" cols="80">{$info.question|escape}</textarea>
					</td>
				</tr>
				<tr>
					<td>{tr}Position:{/tr}</td>
					<td>
						<select name="position">{html_options values=$positions output=$positions selected=$info.position}</select>
					</td>
				</tr>
				<tr>
					<td>{tr}Type:{/tr}</td>
					<td>
						<select name="type">
							<option value='c' {if $info.type eq 'c'}selected=selected{/if}>
								{tr}One choice{/tr}
							</option>
							<option value='m' {if $info.type eq 'm'}selected=selected{/if}>
								{tr}Multiple choices{/tr}
							</option>
							<option value='g' {if $info.type eq 'g'}selected=selected{/if}>
								{tr}Multiple choices of thumbnails from a file gallery{/tr}
							</option>
							<option value='t' {if $info.type eq 't'}selected=selected{/if}>
								{tr}Short text{/tr}
							</option>
							<option value='x' {if $info.type eq 'x'}selected=selected{/if}>
								{tr}Wiki textarea{/tr}</option>
							<option value='r' {if $info.type eq 'r'}selected=selected{/if}>
								{tr}Rate (1..5){/tr}
							</option>
							<option value='s' {if $info.type eq 's'}selected=selected{/if}>
								{tr}Rate (1..10){/tr}
							</option>
							<option value='r' {if $info.type eq 'r'}selected=selected{/if}>
								{tr}Rate{/tr}
							</option>
						</select>
					</td>
				</tr>
				<tr>
					<td>{tr}Answer is mandatory:{/tr}</td>
					<td>
						<input type="checkbox" name="mandatory" {if $info.mandatory eq 'y'}checked="checked"{/if}>
					</td>
				</tr>
				<tr>
					<td>{tr}Number of required answers (for multiple choices):{/tr}</td>
					<td>
						{tr}Min:{/tr}<input type="text" name="min_answers" size="4" value="{$info.min_answers}">
						{tr}Max:{/tr}<input type="text" name="max_answers" size="4" value="{$info.max_answers}">
					</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td>
						{remarksbox type="tip" title="{tr}Tip{/tr}"}
							{tr}For a multiple answer question put the answers into the following field, separated by a comma. Example: one,two,many,lots{/tr}.
							<br>
							{tr}For a rate, you can give the maximum value.{/tr}
							<br>
							{tr}For the 'multiple choices of thumbnail from a file gallery' type, options are: Gallery ID. Example: 4{/tr}
							<br>
							{tr}For the 'wiki textarea' type, options are: rows,columns. Example: 10,60{/tr}
							<br>
							{tr}For the 'short text' type, options are: columns. Example: 60{/tr}
						{/remarksbox}
					</td>
				</tr>
				<tr>
					<td>{tr}Options (if apply):{/tr}</td>
					<td><input type="text" name="options" value="{$info.options|escape}" size="80"></td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td><input type="submit" class="btn btn-default btn-sm" name="save" value="{tr}Save{/tr}"></td>
				</tr>
			</table>
		</form>
	{/tab}
{/tabset}
