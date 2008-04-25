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
</div>
{if $prefs.feature_poll_comments and $comments_cant and !isset($module_params)}
<br />
<a href="#" onclick="javascript:flip('comzone');flip('comzone_close','inline');return false;" class="linkbut">
{if $comments_cant == 0 or ($tiki_p_read_comments  == 'n' and $tiki_p_post_comments  == 'y')}
{tr}Add Comment{/tr}
{elseif $comments_cant == 1}
<span class="highlight">{tr}1 comment{/tr}</span>
{else}
<span class="highlight">{$comments_cant} {tr}comments{/tr}</span>
{/if}
<span id="comzone_close" style="display:{if isset($smarty.session.tiki_cookie_jar.show_comzone) and $smarty.session.tiki_cookie_jar.show_comzone eq 'y'}inline{else}none{/if};">({tr}close{/tr})</span>
</a>
</div>
{/if}
</form>
{if !$user && !isset($smarty.cookies.PHPSESSID)}<i>{tr}Cookies must be allowed to vote{/tr}</i>{/if}

