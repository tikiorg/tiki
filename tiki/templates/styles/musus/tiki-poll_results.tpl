<div class="pollresults">
<h3>{$poll_info.title}</h3>
<table class="pollresults">
{section name=ix loop=$options}
<tr><td class="pollr">{$options[ix].title}</td>
    <td class="pollr"><img src="img/leftbar.gif" alt="<" /><img src="img/mainbar.gif" alt="-" height="14" width="{$options[ix].width}" /><img src="img/rightbar.gif" alt=">" />  {$options[ix].percent}% ({$options[ix].votes})</td></tr>
{/section}
</table>
<br />
{tr}Total{/tr}: {$poll_info.votes} {tr}votes{/tr}<br /><br />
<a href="tiki-old_polls.php">{tr}Other Polls{/tr}</a><br /><br />
</div>
{if $feature_poll_comments eq 'y'}
{if $tiki_p_read_comments eq 'y'}
<div id="page-bar">
<table>
<tr><td>
<div class="button2">
<a href="javascript:toggle('comzone');" class="linkbut">{$comments_cant} {tr}comments{/tr}</a>
</div>
</td></tr></table>
</div>
{include file=comments.tpl}
{/if}
{/if}

