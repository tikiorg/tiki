{title url="tiki-admin_survey_questions.php?surveyId=$surveyId"}{tr}Edit survey questions:{/tr} {$survey_info.name}{/title}

<div class="t_navbar btn-group form-group">
	{button href="tiki-admin_survey_questions.php?surveyId=$surveyId" class="btn btn-default" _icon_name='create' _text="{tr}Add a New Question{/tr}"}
	{button href="tiki-list_surveys.php" class="btn btn-default" _icon_name='list' _text="{tr}List Surveys{/tr}"}
	{button href="tiki-survey_stats.php" class="btn btn-default" _icon_name='chart' _text="{tr}Survey Stats{/tr}"}
	{button href="tiki-survey_stats_survey.php?surveyId=$surveyId" class="btn btn-default" _icon_name='chart' _text="{tr}This survey stats{/tr}"}
	{button href="tiki-admin_surveys.php?surveyId=$surveyId" class="btn btn-default" _icon_name='edit' _text="{tr}Edit this Survey{/tr}"}
	{button href="tiki-admin_surveys.php" class="btn btn-default" _icon_name='cog' _text="{tr}Admin Surveys{/tr}"}
</div>

{if !empty($questionId)}{$tablabel='Edit Survey Question'|tr_if}{else}{$tablabel='Add a New Question to this survey'|tr_if}{/if}
{tabset name='tabs_adminsurveyquestions'}
	{tab name="{tr}Questions{/tr}"}
		{include file='find.tpl' types='0'}
		{button _text="{tr}Save{/tr}" _style="display:none;" _class="save_list" _ajax="n" _auto_args="save_list"}
		{* Use css menus as fallback for item dropdown action menu if javascript is not being used *}
		{if $prefs.javascript_enabled !== 'y'}
			{$js = 'n'}
			{$libeg = '<li>'}
			{$liend = '</li>'}
		{else}
			{$js = 'y'}
			{$libeg = ''}
			{$liend = ''}
		{/if}
		<form action="tiki-admin_survey_questions.php" method="post" id="reorderForm">
			<input type="hidden" name="surveyId" value="{$surveyId|escape}">
			<input type="hidden" name="questionIds" value="">
		</form>
		<table class="table surveyquestions table-striped table-hover">
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
				<th></th>
			</tr>
			{cycle print=false values="odd,even"}
			{section name=user loop=$channels}
				<tr>
					<td class="id">{$channels[user].questionId}</td>
					<td class="integer">{$channels[user].position}</td>
					<td class="text">{self_link questionId=$channels[user].questionId}{$channels[user].question|escape|nl2br}{/self_link}</td>
					<td class="text">{$types[$channels[user].type]}</td>
					<td class="text">{$channels[user].options}</td>
					<td class="action">
						{capture name=question_actions}
							{strip}
								{$libeg}{self_link _icon_name='edit' _menu_text='y' _menu_icon='y' questionId=$channels[user].questionId}
									{tr}Edit{/tr}
								{/self_link}{$liend}
								{$libeg}{self_link _icon_name='remove' _menu_text='y' _menu_icon='y' remove=$channels[user].questionId}
									{tr}Delete{/tr}
								{/self_link}{$liend}
							{/strip}
						{/capture}
						{if $js === 'n'}<ul class="cssmenu_horiz"><li>{/if}
						<a
							class="tips"
							title="{tr}Actions{/tr}"
							href="#"
							{if $js === 'y'}{popup fullhtml="1" center=true text=$smarty.capture.question_actions|escape:"javascript"|escape:"html"}{/if}
							style="padding:0; margin:0; border:0"
						>
							{icon name='wrench'}
						</a>
						{if $js === 'n'}
							<ul class="dropdown-menu" role="menu">{$smarty.capture.question_actions}</ul></li></ul>
						{/if}
					</td>
				</tr>
				{sectionelse}
				{norecords _colspan=6}
			{/section}
		</table>

		{pagination_links cant=$cant_pages step=$prefs.maxRecords offset=$offset}{/pagination_links}

		{button _text="{tr}Save{/tr}" _style="display:none;" _class="save_list" _ajax="n" _auto_args="save_list"}
	{/tab}
	{tab name=$tablabel}
		<form action="tiki-admin_survey_questions.php" method="post" class="form-horizontal">
			<input type="hidden" name="surveyId" value="{$surveyId|escape}">
			<input type="hidden" name="questionId" value="{$questionId|escape}">
            </br>
            <div class="form-group">
                <label class="col-sm-3 control-label">{tr}Question{/tr}</label>
                <div class="col-sm-7 col-sm-offset-1">
                    <textarea name="question"  class="form-control">{$info.question|escape}</textarea>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label">{tr}Answer is mandatory{/tr}</label>
                <div class="col-sm-7 col-sm-offset-1">
                    <input type="checkbox" name="mandatory" {if $info.mandatory eq 'y'}checked="checked"{/if}>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label">{tr}Position{/tr}</label>
                <div class="col-sm-7 col-sm-offset-1">
                    <select name="position" class="form-control">{html_options values=$positions output=$positions selected=$info.position}</select>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label">{tr}Type{/tr}</label>
                <div class="col-sm-7 col-sm-offset-1">
                    <select name="type" class="form-control">
                        {foreach $types as $initial => $label}
                            <option value="{$initial}"{if $info.type eq $initial} selected=selected{/if}>{$label}</option>
                        {/foreach}
                    </select>
                </div>
            </div>
            <div class="form-group type_option type_m type_g">
                <label class="col-sm-3 control-label">{tr}Required answers{/tr}</label>
                <div class="col-sm-7 col-sm-offset-1">
                    <div class="col-sm-6">
                        <div class="col-sm-3">
                            <label class="control-label">{tr}Min{/tr}</label>
                        </div>
                        <div class="col-sm-9">
                            <input type="text" name="min_answers" maxlength="4" value="{$info.min_answers}" class="form-control">
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="col-sm-3">
                            <label class="control-label">{tr}Maximum{/tr}</label>
                        </div>
                        <div class="col-sm-9">
                            <input type="text" name="max_answers" maxlength="4" value="{$info.max_answers}" class="form-control">
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-9 col-sm-offset-2">
                {remarksbox type="tip" title="{tr}Tip{/tr}"}
                    <p class="type_option type_c"><strong>{$types.c}:</strong> {tr}Single choice using radio buttons.{/tr}<br>{tr}Example: "one, two, many, lots".{/tr}<br>{tr}(Use "\," to include a comma.{/tr})</p>
                    <p class="type_option type_m"><strong>{$types.m}:</strong> {tr}Multiple choice using checkboxes.{/tr}<br>{tr}Example: "one, two, many, lots".{/tr}<br>{tr}(Use "\," to include a comma.{/tr})</p>
                    <p class="type_option type_r type_s"><strong>{$types.r}:</strong> {tr}For a rate, you can give the maximum value.{/tr}</p>
                    <p class="type_option type_g"><strong>{$types.g}:</strong> {tr}Multiple choices of thumbnail from a file gallery, options contains Gallery ID.{/tr}<br>{tr}Example: 4{/tr}</p>
                    <p class="type_option type_x"><strong>{$types.x}:</strong> {tr}Options are: rows,columns,toolbars.{/tr}<br>{tr}Example: 10,60,n (toolbar can be "y", "n" or "c" for comments toolbar){/tr}</p>
                    <p class="type_option type_t"><strong>{$types.t}:</strong> {tr}For the 'short text' type, options are: columns.{/tr}<br>{tr}Example: 60{/tr}</p>
                    <p class="type_option type_h"><strong>{$types.h}:</strong> {tr}A heading to go between questions. Options are newpage,tag{/tr}<br>{tr}Example: y,h4{/tr}</p>
                {/remarksbox}
                {jq}
                    $("select[name=type]").change(function () {
                    $(".type_option").hide();
                    $(".type_option.type_" + $(this).val()).show();
                    }).change();
                {/jq}
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label">{tr}Options (if apply):{/tr}</label>
                <div class="col-sm-7 col-sm-offset-1">
                    <input type="text" name="options" value="{$info.options|escape}" maxlength="80" class="form-control">
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label"></label>
                <div class="col-sm-7 col-sm-offset-1">
                    <input type="submit" class="btn btn-primary btn-sm" name="save" value="{tr}Save{/tr}">
                </div>
            </div>
		</form>
	{/tab}
{/tabset}
