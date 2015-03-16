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

<div class="table-responsive">
	<table class="table normal">
		<tr>
			<th>
				{self_link _sort_arg='sort_mode' _sort_field='name'}{tr}Name{/tr}{/self_link}
			</th>
			<th>{tr}Questions{/tr}</th>
			<th>{tr}Actions{/tr}</th>
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
						{$channels[user].questions}
					</td>
					<td class="action">
						{if ($tiki_p_admin eq 'y') or ($channels[user].individual eq 'n' and $tiki_p_admin_surveys eq 'y') or ($channels[user].individual_tiki_p_admin_surveys eq 'y')}
							<a href="tiki-admin_surveys.php?surveyId={$channels[user].surveyId}">
								{icon _id='page_edit' alt="{tr}Edit this Survey{/tr}"}
							</a>
						{/if}

						{if ($tiki_p_admin_surveys eq 'y') or ($channels[user].status eq 'o' and $channels[user].taken_survey eq 'n')}
							<a href="{$channels[user].surveyId|sefurl:survey}">
								{icon _id='control_play' alt="{tr}Take Survey{/tr}"}
							</a>
						{/if}

						{if ($tiki_p_admin eq 'y') or ($channels[user].individual eq 'n' and $tiki_p_view_survey_stats eq 'y') or ($channels[user].individual_tiki_p_view_survey_stats eq 'y')}
							<a href="tiki-survey_stats_survey.php?surveyId={$channels[user].surveyId}">
								{icon _id='chart_curve' alt="{tr}Stats{/tr}"}
							</a>
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
