{$menu_info.title}<br />
<form method="post" action="{$ownurl}">
<input type="hidden" name="polls_pollId" value="{$menu_info.pollId|escape}" />
{if $tiki_p_vote_poll ne 'n'}
{section name=ix loop=$channels}
  <input type="radio" name="polls_optionId" value="{$channels[ix].optionId|escape}" />{tr}{$channels[ix].title}{/tr}<br />
{/section}
{else}
  <ul>
  {section name=ix loop=$channels}
    <li>{tr}{$channels[ix].title}{/tr}</li>
  {/section}
  </ul>
{/if}
<div align="center">
{if $tiki_p_vote_poll ne 'n'}<input type="submit" name="pollVote" value="{tr}vote{/tr}" /><br />{/if}
<a class="linkmodule" href="tiki-poll_results.php?pollId={$menu_info.pollId}">{tr}View Results{/tr}</a><br />
({tr}Votes{/tr}: {$menu_info.votes})
{if $feature_poll_comments and $comments}<br />(<a href="tiki-poll_results.php?pollId={$menu_info.pollId}&amp;comzone=show#comments">{tr}Comments{/tr}: {$comments}</a>){/if}
</div>
</form>

