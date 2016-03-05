{title url="tiki-edit_question_options.php?questionId=$questionId"}{tr}Edit question options{/tr}{/title}

<div class="t_navbar margin-bottom-md">
	{button href="tiki-list_quizzes.php" class="btn btn-default" _text="{tr}List Quizzes{/tr}"}
	{button href="tiki-quiz_stats.php" class="btn btn-default" _text="{tr}Quiz Stats{/tr}"}
	{button href="tiki-quiz_stats_quiz.php?quizId=$quizId" class="btn btn-default" _text="{tr}This Quiz Stats{/tr}"}
	{button href="tiki-edit_quiz.php?quizId=$quizId" class="btn btn-default" _text="{tr}Edit this Quiz{/tr}"}
	{button href="tiki-edit_quiz.php" class="btn btn-default" _text="{tr}Admin Quizzes{/tr}"}
</div>

<h2>{tr}Create/edit options for question:{/tr} <a href="tiki-edit_quiz_questions.php?quizId={$question_info.quizId}&amp;questionId={$question_info.questionId}">{$question_info.question|escape}</a></h2>
<form action="tiki-edit_question_options.php" method="post" class="form-horizontal">
	<input type="hidden" name="optionId" value="{$optionId|escape}">
	<input type="hidden" name="questionId" value="{$questionId|escape}">

    <div class="form-group">
		<label class="col-sm-3 control-label">{tr}Option{/tr}</label>
		<div class="col-sm-7">
	      	<textarea name="optionText" rows="5" cols="40" class="form-control">{$optionText|escape}</textarea>
	    </div>
    </div>
    <div class="form-group">
		<label class="col-sm-3 control-label">{tr}Points{/tr}</label>
		<div class="col-sm-7">
	      	<input type="text" name="points" value="{$points|escape}" class="form-control">
	    </div>
    </div>
    <div class="form-group">
		<label class="col-sm-3 control-label"></label>
		<div class="col-sm-7">
	      	<input type="submit" class="btn btn-primary btn-sm" name="save" value="{tr}Save{/tr}">
	    </div>
    </div>
</form>

<h2>Options</h2>

{include file='find.tpl'}

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
<div class="{if $js === 'y'}table-responsive{/if}"> {* table-responsive class cuts off css drop-down menus *}
	<table class="table table-striped table-hover">
		<tr>
			<th><a href="tiki-edit_question_options.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'optionId_desc'}optionId_asc{else}optionId_desc{/if}">{tr}ID{/tr}</a></th>
			<th><a href="tiki-edit_question_options.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'optionText_desc'}optionText_asc{else}optionText_desc{/if}">{tr}text{/tr}</a></th>
			<th><a href="tiki-edit_question_options.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'points_desc'}points_asc{else}points_desc{/if}">{tr}points{/tr}</a></th>
			<th></th>
		</tr>


		{section name=user loop=$channels}
			<tr>
				<td class="id">{$channels[user].optionId}</td>
				<td class="text">{$channels[user].optionText|escape}</td>
				<td class="integer">{$channels[user].points}</td>
				<td class="action">
					{capture name=question_option_actions}
						{strip}
							{$libeg}<a href="tiki-edit_question_options.php?questionId={$questionId}&amp;offset={$offset}&amp;sort_mode={$sort_mode}&amp;optionId={$channels[user].optionId}">
								{icon name='edit' _menu_text='y' _menu_icon='y' alt="{tr}Edit{/tr}"}
							</a>{$liend}
							{$libeg}<a href="tiki-edit_question_options.php?questionId={$questionId}&amp;offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$channels[user].optionId}">
								{icon name='remove' _menu_text='y' _menu_icon='y' alt="{tr}Remove{/tr}"}
							</a>{$liend}
						{/strip}
					{/capture}
					{if $js === 'n'}<ul class="cssmenu_horiz"><li>{/if}
					<a
						class="tips"
						title="{tr}Actions{/tr}"
						href="#"
						{if $js === 'y'}{popup fullhtml="1" center=true text=$smarty.capture.question_option_actions|escape:"javascript"|escape:"html"}{/if}
						style="padding:0; margin:0; border:0"
					>
						{icon name='wrench'}
					</a>
					{if $js === 'n'}
						<ul class="dropdown-menu" role="menu">{$smarty.capture.question_option_actions}</ul></li></ul>
					{/if}
				</td>
			</tr>
		{sectionelse}
			{norecords _colspan=4}
		{/section}
	</table>
</div>

{pagination_links cant=$cant_pages step=$prefs.maxRecords offset=$offset}{/pagination_links}
