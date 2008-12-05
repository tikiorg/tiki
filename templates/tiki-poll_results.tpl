{* $Id$ *}

{title help="polls" admpage="polls"}{tr}Poll Results{/tr}{/title}

<div class="navbar">
	{button href="tiki-old_polls.php" _text="{tr}Polls{/tr}"}
	{button href="tiki-poll_results.php" _text="{tr}Top Voted Polls{/tr}"}
</div>

{if empty($smarty.request.pollId) and !isset($list_votes)}
<form method="post" action="{$smarty.server.PHP_SELF}">
{if !empty($offset)}<input type="hidden" name="offset" value="{$offset}" />{/if}
{if !empty($scoresort_desc)}<input type="hidden" name="scoresort_desc" value="{$scoresort_desc}" />{/if}
{if !empty($scoresort_asc)}<input type="hidden" name="scoresort_asc" value="{$scoresort_asc}" />{/if}
<table class="findtable">
<tr>
<td class="findtitle">{if empty($what)}{tr}Find{/tr}{else}{tr}{$what}{/tr}{/if}</td>
<td class="findtitle">
	<input type="text" name="find" value="{$find|escape}" />
	{if isset($exact_match)}{tr}Exact&nbsp;match{/tr}<input type="checkbox" name="exact_match" {if $exact_match ne 'n'}checked="checked"{/if}/>{/if}
</td>
<td class="findtitle">{tr}Number of top voted polls to show{/tr}</td><td  class="findtitle"><input type="text" name="maxRecords" value="{$maxRecords|escape}" size="3" /></td>
<td class="findtitle"><input type="submit" name="search" value="{tr}Find{/tr}" /></td>
</tr>
</table>
</form>
</div>
{/if}
{section name=x loop=$poll_info_arr}
<h2><a href="tiki-poll_results.php?pollId={$poll_info_arr[x].pollId}{if !empty($list_votes)}&amp;list=y{/if}">{$poll_info_arr[x].title}</a></h2>

{if $tiki_p_admin_polls eq 'y'}
	{assign var=thispoll_info_arr value=$poll_info_arr[x].pollId}
	{button href="tiki-poll_results.php?list=y&amp;pollId=$thispoll_info_arr" _text="{tr}Votes{/tr}"}
{/if}

<div class="pollresults">
{cycle values="even,odd" print=false}
<table class="pollresults">
{section name=ix loop=$poll_info_arr[x].options}
<tr><td class="pollr {cycle advance=false}">
{if $smarty.section.x.total > 1}<a href="tiki-poll_results.php?{if !empty($scoresort_desc)}scoresort_asc{else}scoresort_desc{/if}={$smarty.section.ix.index}">{/if}
{$poll_info_arr[x].options[ix].title}
{if $smarty.section.x.total > 1}</a>{/if}

{if $prefs.feature_poll_public == 'y'}
   {tr}Users Voting For This Option{/tr}: {section name=iix loop=$poll_info_arr[x].options[ix].users}
   {if $smarty.section.iix.index >= 1}
   , 
   {/if}
   {$poll_info_arr[x].options[ix].users[iix].user}
   {/section}
{/if}
</td>
    <td class="pollr {cycle}"><img src="img/leftbar.gif" alt="&lt;" /><img src="img/mainbar.gif" alt="-" height="14" width="{$poll_info_arr[x].options[ix].width}" /><img src="img/rightbar.gif" alt="&gt;" />  {$poll_info_arr[x].options[ix].percent}% ({$poll_info_arr[x].options[ix].votes})
    </td>
    </tr>
{/section}
</table>
<br />
{tr}Total{/tr}: {$poll_info_arr[x].votes} {tr}votes{/tr}<br /><br />
{if isset($poll_info_arr[x].total) and $poll_info_arr[x].total > 0}{tr}Average:{/tr} {math equation="x/y" x=$poll_info_arr[x].total y=$poll_info_arr[x].votes format="%.2f"}{/if}
<br />
</div>
{/section}
{if isset($list_votes)}
<h2>{tr}List Votes{/tr}</h2>
<div align="center">
<table class="findtable">
<tr><td class="findtable">{tr}Find{/tr}</td>
   <td class="findtable">
   <form method="get" action="tiki-poll_results.php">
     <input type="text" name="find" value="{$find|escape}" />
     <input type="submit" value="{tr}Find{/tr}" name="search" />
     <input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
	 <input type="hidden" name="pollId" value="{$poll_info.pollId|escape}" />
	 <input type="hidden" name="list" value="y" />
   </form>
   </td>
</tr>
</table>
</div>
<table class="normal">
<tr><th>{tr}Identification{/tr}</th><th>{tr}option{/tr}</th></tr>
{cycle values="odd,even" print=false}
{section name=ix loop=$list_votes}
<tr><td class="{cycle advance=false}">{$list_votes[ix].identification}</td><td class="{cycle}">{$list_votes_options[ix]}</td></tr>
{sectionelse}
<tr><td colspan="2">{tr}No records found{/tr}</td></tr>
{/section}
</table>

{pagination_links cant=$cant_pages step=$prefs.maxRecords offset=$offset }{/pagination_links}

{/if}

{if $prefs.feature_poll_comments == 'y'
  && (($tiki_p_read_comments  == 'y'
    && $comments_cant != 0)
  ||  $tiki_p_post_comments  == 'y'
  ||  $tiki_p_edit_comments  == 'y')
}
  <div id="page-bar">
  	   {include file=comments_button.tpl}
  </div>
  {include file=comments.tpl}
{/if}
