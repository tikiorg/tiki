{title url="tiki-edit_quiz_results.php?quizId=$quizId"}{tr}Edit quiz results{/tr}{/title}

<div class="t_navbar margin-bottom-md">
	{button href="tiki-list_quizzes.php" class="btn btn-default" _text="{tr}List Quizzes{/tr}"}
	{button href="tiki-quiz_stats.php" class="btn btn-default" _text="{tr}Quiz Stats{/tr}"}
	{button href="tiki-quiz_stats_quiz.php?quizId=$quizId" class="btn btn-default" _text="{tr}This Quiz Stats{/tr}"}
	{button href="tiki-edit_quiz.php?quizId=$quizId" class="btn btn-default" _text="{tr}Edit this Quiz{/tr}"}
	{button href="tiki-edit_quiz.php" class="btn btn-default" _text="{tr}Admin Quizzes{/tr}"}
</div>

<h2>
	{tr}Create/edit questions for quiz:{/tr} <a href="tiki-edit_quiz.php?quizId={$quiz_info.quizId}" class="pageTitle">{$quiz_info.name}</a>
</h2>

<form action="tiki-edit_quiz_results.php" method="post" class="form-horizontal">
	<input type="hidden" name="quizId" value="{$quizId|escape}">
	<input type="hidden" name="resultId" value="{$resultId|escape}">
	<div class="form-group">
		<label class="col-sm-3 control-label">{tr}From Points{/tr}</label>
		<div class="col-sm-7">
      		<input type="text" name="fromPoints" value="{$fromPoints|escape}" class="form-control">
  		</div>
    </div>
    <div class="form-group">
		<label class="col-sm-3 control-label">{tr}To Points{/tr}</label>
		<div class="col-sm-7">
      		<input type="text" name="toPoints" value="{$toPoints|escape}" class="form-control">
  		</div>
    </div>
    <div class="form-group">
		<label class="col-sm-3 control-label">{tr}Answer{/tr}</label>
		<div class="col-sm-7">
      		<textarea name="answer" rows="10" cols="40" class="form-control">{$answer|escape}</textarea>
  		</div>
    </div>
    <div class="form-group">
		<label class="col-sm-3 control-label"></label>
		<div class="col-sm-7">
      		<input type="submit" class="btn btn-primary btn-sm" name="save" value="{tr}Save{/tr}">
  		</div>
    </div>
</form>

<h2>{tr}Results{/tr}</h2>

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
			<th>
				<a href="tiki-edit_quiz_results.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'fromPoints_desc'}fromPoints_asc{else}fromPoints_desc{/if}">{tr}From Points{/tr}</a>
			</th>
			<th>
				<a href="tiki-edit_quiz_results.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'toPoints_desc'}toPoints_asc{else}toPoints_desc{/if}">{tr}To Points{/tr}</a>
			</th>
			<th>
				<a href="tiki-edit_quiz_results.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'position_desc'}answer_asc{else}answer_desc{/if}">{tr}Answer{/tr}</a>
			</th>
			<th></th>
		</tr>

		{section name=user loop=$channels}
			<tr>
				<td class="integer">{$channels[user].fromPoints}</td>
				<td class="integer">{$channels[user].toPoints}</td>
				<td class="text">{$channels[user].answer|truncate:230:"(...)":true|escape|nl2br}</td>
				<td class="action">
					{capture name=quiz_results_actions}
						{strip}
							{$libeg}<a href="tiki-edit_quiz_results.php?quizId={$quizId}&amp;offset={$offset}&amp;sort_mode={$sort_mode}&amp;resultId={$channels[user].resultId}">
								{icon name='edit' _menu_text='y' _menu_icon='y' alt="{tr}Edit{/tr}"}
							</a>{$liend}
							{$libeg}<a href="tiki-edit_quiz_results.php?quizId={$quizId}&amp;offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$channels[user].resultId}">
								{icon name='remove' _menu_text='y' _menu_icon='y' alt="{tr}Remove{/tr}"}
							</a>{$liend}
						{/strip}
					{/capture}
					{if $js === 'n'}<ul class="cssmenu_horiz"><li>{/if}
					<a
						class="tips"
						title="{tr}Actions{/tr}"
						href="#"
						{if $js === 'y'}{popup delay="0|2000" fullhtml="1" center=true text=$smarty.capture.quiz_results_actions|escape:"javascript"|escape:"html"}{/if}
						style="padding:0; margin:0; border:0"
					>
						{icon name='wrench'}
					</a>
					{if $js === 'n'}
						<ul class="dropdown-menu" role="menu">{$smarty.capture.quiz_results_actions}</ul></li></ul>
					{/if}
				</td>
			</tr>
		{sectionelse}
			{norecords _colspan=4}
		{/section}
	</table>
</div>

{pagination_links cant=$cant_pages step=$prefs.maxRecords offset=$offset}{/pagination_links}
