{$menu_info.title}<br/>
<form method="post" action="{$ownurl}">
<input type="hidden" name="polls_pollId" value="{$menu_info.pollId}" />
{section name=ix loop=$channels}
  <input type="radio" name="polls_optionId" value="{$channels[ix].optionId}" />{tr}{$channels[ix].title}{/tr}<br/>
{/section}
<div align="center">
<input type="submit" name="pollVote" value="{tr}vote{/tr}" /><br/>
<a class="linkmodule" href="tiki-poll_results.php?pollId={$menu_info.pollId}">{tr}Results{/tr}</a>
</div>
</form>

