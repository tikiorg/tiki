{* $Id$ *}
{title help="Quiz"}{tr}Quizzes{/tr}{/title}

<div class="t_navbar margin-bottom-md">
	{if $tiki_p_admin_quizzes eq 'y'}
			{button href="tiki-edit_quiz.php" class="btn btn-default" _text="{tr}Admin Quizzes{/tr}"}
	{/if}
	{if $tiki_p_view_quiz_stats eq 'y'}
		{button href="tiki-quiz_stats.php" class="btn btn-default" _text="{tr}Quiz Stats{/tr}"}
	{/if}
</div>

{if $channels or ($find ne '')}
	{include file='find.tpl'}
{/if}
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

<div class="{if $js === 'y'}table-responsive{/if}"> {*the table-responsive class cuts off dropdown menus *}
	<table class="table table-striped table-hover">
		<tr>
			{assign var=numbercol value=1}
			<th>
				<a href="tiki-list_quizzes.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'name_desc'}name_asc{else}name_desc{/if}">{tr}Quiz{/tr}</a>
			</th>
			{assign var=numbercol value=$numbercol+1}
			<th>
				<a href="tiki-list_quizzes.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'timeLimit_desc'}timeLimit_asc{else}timeLimit_desc{/if}">{tr}Time Limit{/tr}</a>
			</th>
			{assign var=numbercol value=$numbercol+1}
			<th>
				{tr}Questions{/tr}
			</th>
			{if ($tiki_p_admin eq 'y' or $tiki_p_admin_quizzes eq 'y' or $tiki_p_view_quiz_stats eq 'y')}
				{assign var=numbercol value=$numbercol+1}
				<th></th>
			{/if}
		</tr>

		{section name=user loop=$channels}
			{if ($tiki_p_admin eq 'y') or ($channels[user].individual eq 'n' and $tiki_p_take_quiz eq 'y') or ($channels[user].individual_tiki_p_take_quiz eq 'y')}
				<tr>
					<td class="text">
						<a class="tablename" href="tiki-take_quiz.php?quizId={$channels[user].quizId}">{$channels[user].name|escape}</a>
						<span class="help-block">
							{$channels[user].description|escape|nl2br}
						</span>
					</td>
					<td class="integer">
						{$channels[user].timeLimited} {if $channels[user].timeLimited eq 'y'}({$channels[user].timeLimit} mins){/if}
					</td>
					<td class="integer">
						<span class="badge">{$channels[user].questions}</span>
					</td>
					{if ($tiki_p_admin eq 'y' or $tiki_p_admin_quizzes eq 'y' or $tiki_p_view_quiz_stats eq 'y')}
						<td class="action">
							{capture name=quiz_actions}
								{strip}
									{if ($tiki_p_admin eq 'y') or ($channels[user].individual eq 'n' and $tiki_p_admin_quizzes eq 'y') or ($channels[user].individual_tiki_p_admin_quizzes eq 'y')}
										{$libeg}<a href="tiki-edit_quiz.php?quizId={$channels[user].quizId}">
											{icon name='edit' _menu_text='y' _menu_icon='y' alt="{tr}Edit{/tr}"}
										</a>{$liend}
									{/if}
									{if ($tiki_p_admin eq 'y') or ($channels[user].individual eq 'n' and $tiki_p_view_quiz_stats eq 'y') or ($channels[user].individual_tiki_p_view_quiz_stats eq 'y')}
										{$libeg}<a href="tiki-quiz_stats_quiz.php?quizId={$channels[user].quizId}">
											{icon name='chart' _menu_text='y' _menu_icon='y' alt="{tr}Stats{/tr}"}
										</a>{$liend}
									{/if}
								{/strip}
							{/capture}
							{if $js === 'n'}<ul class="cssmenu_horiz"><li>{/if}
							<a
								class="tips"
								title="{tr}Actions{/tr}"
								href="#"
								{if $js === 'y'}{popup delay="0|2000" fullhtml="1" center=true text=$smarty.capture.quiz_actions|escape:"javascript"|escape:"html"}{/if}
								style="padding:0; margin:0; border:0"
							>
								{icon name='wrench'}
							</a>
							{if $js === 'n'}
								<ul class="dropdown-menu" role="menu">{$smarty.capture.quiz_actions}</ul></li></ul>
							{/if}
						</td>
					{/if}
				</tr>
			{/if}
		{sectionelse}
			{norecords _colspan=$numbercol}
		{/section}
	</table>
</div>

{pagination_links cant=$cant_pages step=$prefs.maxRecords offset=$offset}{/pagination_links}
