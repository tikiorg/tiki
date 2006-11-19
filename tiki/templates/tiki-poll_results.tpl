<div class="pollresults">
<h3>{$poll_info.title}</h3>
<table class="pollresults">
{section name=ix loop=$options}
<tr><td class="pollr">{$options[ix].title}</td>
    <td class="pollr"><img src="img/leftbar.gif" alt="&lt;" /><img src="img/mainbar.gif" alt="-" height="14" width="{$options[ix].width}" /><img src="img/rightbar.gif" alt="&gt;" />  {$options[ix].percent}% ({$options[ix].votes})</td></tr>
{/section}
</table>
<br />
{tr}Total{/tr}: {$poll_info.votes} {tr}votes{/tr}<br /><br />
<a href="tiki-old_polls.php" class="link">{tr}Other Polls{/tr}</a><br /><br />
</div>
{if $feature_poll_comments == 'y'
&& (($tiki_p_read_comments  == 'y'
&& $comments_cant != 0)
||  $tiki_p_post_comments  == 'y'
||  $tiki_p_edit_comments  == 'y')}
<div id="page-bar">
<span class="button2">
<a href="#comments" onclick="javascript:flip('comzone');flip('comzone_close','inline');return false;" class="linkbut">
{if $comments_cant == 0 or ($tiki_p_read_comments  == 'n' and $tiki_p_post_comments  == 'y')}
{tr}add comment{/tr}
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
