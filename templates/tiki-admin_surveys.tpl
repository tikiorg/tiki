{* $Id$ *}

{title help="Surveys"}{tr}Admin surveys{/tr}{/title}

<div class="navbar">
	{button href="tiki-list_surveys.php" _text="{tr}List Surveys{/tr}"}
	{button href="tiki-survey_stats.php" _text="{tr}Survey Stats{/tr}"}
	{button surveyId=0 cookietab=2 _auto_args="surveyId,cookietab" _text="{tr}Create Survey{/tr}"}
</div>

{tabset}

{tab name="{tr}Surveys{/tr}"}

{if $channels or ($find ne '')}
	{include file='find.tpl'}
{/if}

<table class="normal">
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
		<th style="width:120px;">{tr}Action{/tr}</th>
	</tr>
	
	{cycle values="odd,even" print=false}
	{section name=user loop=$channels}
		<tr class="{cycle}">
			<td>{$channels[user].surveyId}</td>
			<td>
				<b>{$channels[user].name|escape}</b>
				<div class="subcomment">
					{wiki}{$channels[user].description|escape}{/wiki}
				</div>
			</td>
			<td style="text-align:center;">
				{if $channels[user].status eq 'o'}
					{icon _id=ofolder alt="Open"}
				{else}
					{icon _id=folder alt="closed"}
				{/if}
			</td>
			<td style="text-align:center;">{$channels[user].questions}</td>
			<td style="text-align:right;">
				{self_link _icon='page_edit' cookietab='2' _anchor='anchor2' surveyId=$channels[user].surveyId}{tr}Edit{/tr}{/self_link}
				<a class="link" href="tiki-admin_survey_questions.php?surveyId={$channels[user].surveyId}">{icon _id='help' alt="{tr}Questions{/tr}" title="{tr}Questions{/tr}"}</a>
				<a class="link" href="tiki-admin_surveys.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$channels[user].surveyId}">{icon _id='cross' alt="{tr}Remove{/tr}"}</a>
				{if $channels[user].individual eq 'y'}
					<a class="link" href="tiki-objectpermissions.php?objectName={$channels[user].name|escape:"url"}&amp;objectType=survey&amp;permType=surveys&amp;objectId={$channels[user].surveyId}">{icon _id='key_active' alt="{tr}Active Perms{/tr}"}</a>
				{else}
					<a class="link" href="tiki-objectpermissions.php?objectName={$channels[user].name|escape:"url"}&amp;objectType=survey&amp;permType=surveys&amp;objectId={$channels[user].surveyId}">{icon _id='key' alt="{tr}Perms{/tr}"}</a>
				{/if}
				{if ($tiki_p_admin eq 'y') or ($channels[user].individual eq 'n' and $tiki_p_view_survey_stats eq 'y') or ($channels[user].individual_tiki_p_view_survey_stats eq 'y')}
					<a class="link" href="tiki-survey_stats_survey.php?surveyId={$channels[user].surveyId}">{icon _id='chart_curve' alt="{tr}Stats{/tr}"}</a>
				{/if}
			</td>
		</tr>
	{sectionelse}
		{norecords _colspan=5}
	{/section}
</table>

{pagination_links cant=$cant_pages step=$prefs.maxRecords offset=$offset}{/pagination_links}
{/tab}

{tab name="{tr}Create/Edit Surveys{/tr}"}
{if $info.surveyId > 0}
	<h2>{tr}Edit this Survey:{/tr} {$info.name}</h2>
{else}
	<h2>{tr}Create New Survey{/tr}</h2>
{/if}

{if $individual eq 'y'}
	<a class="link" href="tiki-objectpermissions.php?objectName={$info.name|escape:"url"}&amp;objectType=survey&amp;permType=surveys&amp;objectId={$info.surveyId}">{tr}There are individual permissions set for this survey{/tr}</a><br /><br />
{/if}

<form action="tiki-admin_surveys.php" method="post">
	<input type="hidden" name="surveyId" value="{$info.surveyId|escape}" />
	<table class="formcolor">
		<tr>
			<td>{tr}Name:{/tr}</td>
			<td><input type="text" name="name" size="80" value="{$info.name|escape}" /></td>
		</tr>
		<tr>
			<td>{tr}Description:{/tr}</td>
			<td>{textarea name="description" rows="6" cols="80" _toolbars='y' _zoom='n' _simple='y' comments='y'}{$info.description}{/textarea}</td>
		</tr>
		{include file='categorize.tpl'}
		<tr>
			<td>{tr}Status{/tr}</td>
			<td>
				<select name="status">
					<option value="o" {if $info.status eq 'o'}selected='selected'{/if}>{tr}Open{/tr}</option>
					<option value="c" {if $info.status eq 'c'}selected='selected'{/if}>{tr}Closed{/tr}</option>
				</select>
			</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td>
				<input type="submit" name="save" value="{tr}Save{/tr}" />
			</td>
		</tr>
	</table>
</form>
{/tab}

{/tabset}
