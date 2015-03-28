{* $Id$ *}

{title url='tiki-admin_surveys.php' help="Surveys"}{tr}Admin surveys{/tr}{/title}

<div class="t_navbar btn-group form-group">
	{button href="tiki-list_surveys.php" class="btn btn-default" _icon_name="list" _text="{tr}List{/tr}"}
	{button href="tiki-survey_stats.php" class="btn btn-default" _icon_name="chart" _text="{tr}Stats{/tr}"}
	{button surveyId=0 _anchor='content_admin_surveys1-2' _auto_args="surveyId" class="btn btn-default" _icon_name="create" _text="{tr}Create{/tr}"}
</div>

{tabset}

	{tab name="{tr}Surveys{/tr}"}
		<h2>{tr}Surveys{/tr}</h2>
		{if $channels or ($find ne '')}
			{include file='find.tpl'}
		{/if}

		<div class="table-responsive">
			<table class="table normal table-striped table-hover">
				<tr>
					<th>
						{self_link _sort_arg='sort_mode' _sort_field='surveyId'}{tr}ID{/tr}{/self_link}
					</th>
					<th>
						{self_link _sort_arg='sort_mode' _sort_field='name'}{tr}Survey{/tr}{/self_link}
					</th>
					<th>
						{self_link _sort_arg='sort_mode' _sort_field='status'}{tr}Status{/tr}{/self_link}
					</th>
					<th>{tr}Questions{/tr}</th>
					<th></th>
				</tr>

				{section name=user loop=$channels}
					<tr>
						<td class="id">{$channels[user].surveyId}</td>
						<td class="text">
							<b>{$channels[user].name|escape}</b>
							<div class="subcomment">
								{wiki}{$channels[user].description}{/wiki}
							</div>
						</td>
						<td class="icon">
							{if $channels[user].status eq 'o'}
								{icon name='unlock' class='tips' title=":{tr}Open{/tr}"}
							{else}
								{icon name='lock' class='tips' title=":{tr}Closed{/tr}"}
							{/if}
						</td>
						<td class="integer"><span class="badge">{$channels[user].questions}</span></td>
						<td class="action">
							{capture name=survey_actions}
								{strip}
									<a href="tiki-admin_survey_questions.php?surveyId={$channels[user].surveyId}">
										{icon name='list' _menu_text='y' _menu_icon='y' alt="{tr}Questions{/tr}"}
									</a>
									{permission_link mode=text type=survey permType=surveys id=$channels[user].surveyId title=$channels[user].name}
									{if ($tiki_p_admin eq 'y') or ($channels[user].individual eq 'n' and $tiki_p_view_survey_stats eq 'y') or ($channels[user].individual_tiki_p_view_survey_stats eq 'y')}
										<a href="tiki-survey_stats_survey.php?surveyId={$channels[user].surveyId}">
											{icon name='chart' _menu_text='y' _menu_icon='y' alt="{tr}Stats{/tr}"}
										</a>
									{/if}
									{self_link _icon_name='edit' _anchor='content_admin_surveys1-2' _menu_text='y' _menu_icon='y' surveyId=$channels[user].surveyId}
										{tr}Edit{/tr}
									{/self_link}
									<a href="tiki-admin_surveys.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$channels[user].surveyId}">
										{icon name='remove' _menu_text='y' _menu_icon='y' alt="{tr}Remove{/tr}"}
									</a>
								{/strip}
							{/capture}
							<a class="tips"
							   title="{tr}Actions{/tr}"
							   href="#" {popup delay="0|2000" fullhtml="1" center=true text=$smarty.capture.survey_actions|escape:"javascript"|escape:"html"}
							   style="padding:0; margin:0; border:0"
									>
								{icon name='wrench'}
							</a>
						</td>
					</tr>
				{sectionelse}
					{norecords _colspan=5}
				{/section}
			</table>
		</div>

		{pagination_links cant=$cant_pages step=$prefs.maxRecords offset=$offset}{/pagination_links}
	{/tab}

	{tab name="{tr}Create/Edit Surveys{/tr}"}
		{if $info.surveyId > 0}
			<h2>{tr}Edit this Survey:{/tr} {$info.name}</h2>
		{else}
			<h2>{tr}Create New Survey{/tr}</h2>
		{/if}

		{if $individual eq 'y'}
			{permission_link mode=link type=survey permType=surveys id=$info.surveyId title=$info.name label="{tr}There are individual permissions set for this survey{/tr}"}
		{/if}

		<form action="tiki-admin_surveys.php" method="post" class="form-horizontal" role="form">
			<div class="form-group">
				<input type="hidden" name="surveyId" value="{$info.surveyId|escape}">
				<label for="name" class="col-sm-2 control-label">{tr}Name{/tr}</label>
				<div class="col-sm-10">
					<input type="text" name="name" id="name" class="form-control" value="{$info.name|escape}">
				</div>
			</div>
			<div class="form-group">
				<label for="description" class="col-sm-2 control-label">{tr}Description{/tr}</label>
				<div class="col-sm-10">
					{textarea name="description" rows="6" id="description" class="form-control" _toolbars='y' _simple='y' comments='y'}{$info.description}{/textarea}
				</div>
			</div>
			<div class="margin-bottom-md">
				{include file='categorize.tpl' labelcol='2' inputcol='10'}
			</div>
			<div class="form-group">
				<label for="status" class="col-sm-2 control-label">{tr}Status{/tr}</label>
				<div class="col-sm-10">
					<select name="status" class="form-control">
						<option value="o" {if $info.status eq 'o'}selected='selected'{/if}>{tr}Open{/tr}</option>
						<option value="c" {if $info.status eq 'c'}selected='selected'{/if}>{tr}Closed{/tr}</option>
					</select>
				</div>
			</div>
			<div class="form-group">
				<div class="col-sm-10 col-sm-offset-2">
					<input type="submit" class="btn btn-primary" name="save" value="{tr}Save{/tr}">
				</div>
			</div>
		</form>
	{/tab}

{/tabset}
