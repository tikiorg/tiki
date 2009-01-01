{* $Id$ *}
{title help="Quiz"}{tr}Quizzes{/tr}{/title}

{if $tiki_p_view_quiz_stats eq 'y'}
	<div class="navbar">
		{button href="tiki-quiz_stats.php" _text="{tr}Quiz Stats{/tr}"}
	</div>

	{if $channels or ($find ne '')}
		<table class="findtable">
			<tr>
				<td class="findtable">{tr}Find{/tr}</td>
				<td class="findtable">
					<form method="get" action="tiki-list_quizzes.php">
						<input type="text" name="find" value="{$find|escape}" />
						<input type="submit" value="{tr}Find{/tr}" name="search" />
						<input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
						<input type="hidden" name="quizId" value="{$quizId|escape}" />
					</form>
				</td>
			</tr>
		</table>
	{/if}
{/if}

<table class="normal">
<tr>
<th>
<a href="tiki-list_quizzes.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'name_desc'}name_asc{else}name_desc{/if}">{tr}Name{/tr}</a>
</th>
<th>
<a href="tiki-list_quizzes.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'description_desc'}description_asc{else}description_desc{/if}">{tr}Description{/tr}</a>
</th>
<th>
<a href="tiki-list_quizzes.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'timeLimit_desc'}timeLimit_asc{else}timeLimit_desc{/if}">{tr}timeLimit{/tr}</a>
</th>

{* 
Why doesn't sort by questions work as well? I'm getting weird errors*
//error message: 
Warning: mysql error: Unknown column 'questionsLimit' in 'order clause' in query:
select * from `tiki_quizzes` order by `questionsLimit` desc
in /var/www/html/tikiwiki/lib/tikidblib.php on line 133

Fatal error: Call to a member function on a non-object in /var/www/html/tikiwiki/lib/tikidblib.php on line 151
// code
<th>
<a href="tiki-list_quizzes.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'questions_desc'}questions_asc{else}questionsLimit_desc{/if}">{tr}Questions{/tr}</a></th>
*}
<!-- the question heading won't sort -->
<th>{tr}Questions{/tr}</th>

</tr>
{cycle values="odd,even" print=false}
{section name=user loop=$channels}
  {if ($tiki_p_admin eq 'y') or ($channels[user].individual eq 'n' and $tiki_p_take_quiz eq 'y') or ($channels[user].individual_tiki_p_take_quiz eq 'y')}
    <tr>
      <td class="{cycle advance=false}">
        <a class="tablename" href="tiki-take_quiz.php?quizId={$channels[user].quizId}">{$channels[user].name}</a>
        {if ($tiki_p_admin eq 'y') or ($channels[user].individual eq 'n' and $tiki_p_admin_quizzes eq 'y') or ($channels[user].individual_tiki_p_admin_quizzes eq 'y')}
          <a class="link" href="tiki-edit_quiz.php?quizId={$channels[user].quizId}">{icon _id='page_edit' alt='{tr}Edit{/tr}'}</a>
        {/if}
        {if ($tiki_p_admin eq 'y') or ($channels[user].individual eq 'n' and $tiki_p_view_quiz_stats eq 'y') or ($channels[user].individual_tiki_p_view_quiz_stats eq 'y')}
          <a class="link" href="tiki-quiz_stats_quiz.php?quizId={$channels[user].quizId}">{icon _id='chart_curve' alt='{tr}Stats{/tr}'}</a>
        {/if}
        </td>
        <td class="{cycle advance=false}">{$channels[user].description}</td>
        <td class="{cycle advance=false}">{$channels[user].timeLimited} {if $channels[user].timeLimited eq 'y'}({$channels[user].timeLimit} mins){/if}</td>
        <td class="{cycle}">{$channels[user].questions}</td>
      </tr>
  {/if}
{sectionelse}
  <tr><td class="{cycle}" colspan="4">{tr}No records.{/tr}</td></tr>
{/section}
</table>

{pagination_links cant=$cant_pages step=$prefs.maxRecords offset=$offset}{/pagination_links}
