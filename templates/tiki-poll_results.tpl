{* $Id: tiki-poll_results.tpl,v 1.14 2007-07-20 18:44:27 jyhem Exp $ *}
<h1><a href="tiki-poll_results.php?pollId={$poll_info.pollId}{if !empty($list_votes)}&amp;list=y{/if}">{$poll_info.title}</a></h1>
<span class="button2"><a href="tiki-old_polls.php" class="linkbut">{tr}Other Polls{/tr}</a></span>
{if $tiki_p_admin_polls eq 'y'}<span class=button2"><a href="tiki-poll_results.php?list=y&amp;pollId={$poll_info.pollId}" class="linkbut">{tr}Votes{/tr}</a></span>{/if}
<h2>{tr}Results{/tr}</h2>
<div class="pollresults">
<table class="pollresults">
{section name=ix loop=$options}
<tr><td class="pollr">{$options[ix].title}</td>
    <td class="pollr"><img src="img/leftbar.gif" alt="&lt;" /><img src="img/mainbar.gif" alt="-" height="14" width="{$options[ix].width}" /><img src="img/rightbar.gif" alt="&gt;" />  {$options[ix].percent}% ({$options[ix].votes})</td></tr>
{/section}
</table>
<br />
{tr}Total{/tr}: {$poll_info.votes} {tr}votes{/tr}<br /><br />
{if isset($total) and $total > 0}{tr}Average:{/tr} {math equation="x/y" x=$total y=$poll_info.votes format="%.2f"}{/if}
<br />
</div>

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
<tr><td class="heading">{tr}User{/tr}</td><td class="heading">{tr}option{/tr}</td></tr>
{cycle values="odd,even" print=false}
{section name=ix loop=$list_votes}
<tr><td class="{cycle advance=false}">{$list_votes[ix].user}</td><td class="{cycle}">{$list_votes[ix].title}</td></tr>
{sectionelse}
<tr><td colspan="2">{tr}No records found{/tr}</td></tr>
{/section}
</table>	
<div class="mini" align="center">
{if $prev_offset >= 0}
[<a class="prevnext" href="tiki-poll_results.php?list=y&amp;pollId={$poll_info.pollId}&amp;find={$find}&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}Prev{/tr}</a>]&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;[<a class="prevnext" href="tiki-poll_results.php?list=y&amp;pollId={$poll_info.pollId}&amp;find={$find}&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}Next{/tr}</a>]
{/if}
{if $direct_pagination eq 'y'}
<br />
{section loop=$cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:$maxRecords}
<a class="prevnext" href="tiki-poll_results.php?list=y&amp;pollId={$poll_info.pollId}&amp;find={$find}&amp;offset={$selector_offset}&amp;sort_mode={$sort_mode}">
{$smarty.section.foo.index_next}</a>&nbsp;
{/section}
{/if}

</div>
{/if}

{if $feature_poll_comments == 'y'
&& (($tiki_p_read_comments  == 'y'
&& $comments_cant != 0)
||  $tiki_p_post_comments  == 'y'
||  $tiki_p_edit_comments  == 'y')}
<div id="page-bar">
<span class="button2">
<a href="#comments" onclick="javascript:flip('comzone');flip('comzone_close','inline');return false;" class="linkbut">
{if $comments_cant == 0 or ($tiki_p_read_comments  == 'n' and $tiki_p_post_comments  == 'y')}
{tr}Add Comment{/tr}
{elseif $comments_cant == 1}
<span class="highlight">{tr}1 comment{/tr}</span>
{else}
<span class="highlight">{$comments_cant} {tr}comments{/tr}</span>
{/if}
<span id="comzone_close" style="display:{if isset($smarty.session.tiki_cookie_jar.show_comzone) and $smarty.session.tiki_cookie_jar.show_comzone eq 'y'}inline{else}none{/if};">({tr}close{/tr})</span>
</a>
</span>
</div>
{include file=comments.tpl}
{/if}
