{* $Id$ *}
{title help="Surveys"}{tr}Surveys{/tr}{/title}

<div class="t_navbar margin-bottom-md">
	{if $tiki_p_view_survey_stats eq 'y'}
		{button href="tiki-survey_stats.php" class="btn btn-default" _text="{tr}Survey stats{/tr}"}
	{/if}
	{if $tiki_p_admin_surveys eq 'y'}
		{button href="tiki-admin_surveys.php?cookietab=2" class="btn btn-default" _text="{tr}Create New Survey{/tr}"}
		{button href="tiki-admin_surveys.php?cookietab=1" class="btn btn-default" _text="{tr}Admin Surveys{/tr}"}
	{/if}
</div>
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
			<th>
				{self_link _sort_arg='sort_mode' _sort_field='name'}{tr}Name{/tr}{/self_link}
			</th>
			<th>{tr}Questions{/tr}</th>
			<th></th>
		</tr>

		{section name=user loop=$channels}
			{if ($tiki_p_admin eq 'y') or ($channels[user].individual eq 'n' and $tiki_p_take_survey eq 'y') or ($channels[user].individual_tiki_p_take_survey eq 'y')}
				<tr>
					<td class="text">
						{if ($tiki_p_admin_surveys eq 'y') or ($channels[user].status eq 'o' and $channels[user].taken_survey eq 'n')}
							<a class="tablename" href="{$channels[user].surveyId|sefurl:survey}">
								{$channels[user].name|escape}
							</a>
						{else}
							<a class="link" href="tiki-survey_stats_survey.php?surveyId={$channels[user].surveyId}">
								{$channels[user].name|escape}
							</a>
						{/if}
						<div class="subcomment">
							{wiki}{$channels[user].description}{/wiki}
						</div>
					</td>
					<td class="text">
						<span class="badge">{$channels[user].questions}</span>
					</td>
					<td class="action">
						{capture name=list_survey_actions}
							{strip}
								{if ($tiki_p_admin eq 'y') or ($channels[user].individual eq 'n' and $tiki_p_admin_surveys eq 'y') or ($channels[user].individual_tiki_p_admin_surveys eq 'y')}
									{$libeg}<a href="tiki-admin_surveys.php?surveyId={$channels[user].surveyId}">
										{icon name='edit' _menu_text='y' _menu_icon='y' alt="{tr}Edit{/tr}"}
									</a>{$liend}
								{/if}

								{if ($tiki_p_admin_surveys eq 'y') or ($channels[user].status eq 'o' and $channels[user].taken_survey eq 'n')}
									{$libeg}<a href="{$channels[user].surveyId|sefurl:survey}">
										{icon name='post' _menu_text='y' _menu_icon='y' alt="{tr}Take survey{/tr}"}
									</a>{$liend}
								{/if}

								{if ($tiki_p_admin eq 'y') or ($channels[user].individual eq 'n' and $tiki_p_view_survey_stats eq 'y') or ($channels[user].individual_tiki_p_view_survey_stats eq 'y')}
									{$libeg}<a href="tiki-survey_stats_survey.php?surveyId={$channels[user].surveyId}">
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
							{if $js === 'y'}{popup fullhtml="1" center=true text=$smarty.capture.list_survey_actions|escape:"javascript"|escape:"html"}{/if}
							style="padding:0; margin:0; border:0"
						>
							{icon name='wrench'}
						</a>
						{if $js === 'n'}
							<ul class="dropdown-menu" role="menu">{$smarty.capture.list_survey_actions}</ul></li></ul>
						{/if}
					</td>
				</tr>
			{/if}
			{sectionelse}
			{norecords _colspan=3}
		{/section}
	</table>
</div>

{pagination_links cant=$cant_pages step=$maxRecords offset=$offset}{/pagination_links}
