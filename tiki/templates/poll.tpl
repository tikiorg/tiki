{if $ratings.info.pollId and $tiki_p_wiki_view_ratings eq 'y'}
<div style="float:right;background-color:white; padding:7px; border:1px solid #666666; -moz-border-radius : 10px;font-size:.8em;">
{if $tiki_p_wiki_vote_ratings eq 'y'}
<div>{$ratings.info.title}</div>
<form method="post" action="tiki-index.php">
{if $page}
<input type="hidden" name="wikipoll" value="1" />
<input type="hidden" name="page" value="{$page|escape}" />
{/if}
<input type="hidden" name="polls_pollId" value="{$ratings.info.pollId|escape}" />
<table>
{section name=ix loop=$ratings.options}
<tr><td valign="top" {if $user_vote eq $ratings.options[ix].optionId}class="highlight"{/if}>
<input type="radio" name="polls_optionId" value="{$ratings.options[ix].optionId}" 
id="{$ratings.info.pollId}{$ratings.options[ix].optionId}" {if $user_vote eq $ratings.options[ix].optionId} checked="checked"{/if} />
</td><td valign="top" {if $user_vote eq $ratings.options[ix].optionId}class="highlight"{/if}>
<label for="{$ratings.info.pollId}{$ratings.options[ix].optionId}">{$ratings.options[ix].title}</label></td>
<td valign="top" {if $user_vote eq $ratings.options[ix].optionId}class="highlight"{/if}>
({$ratings.options[ix].votes})
</td></tr>
{/section}
</table>
<input type="submit" name="pollVote" value="{tr}vote{/tr}" style="border:1px solid #666666;background-color:white;font-size:.8em;"/><br />
</form>
{else}
<div>{$ratings.info.title}</div>
{section name=ix loop=$ratings.options}
<div>{$ratings.options[ix].title} : {$ratings.options[ix].votes}</div>
{/section}
{/if}
</div>
{/if}
