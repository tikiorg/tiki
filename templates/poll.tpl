{if $ratings.info.pollId and $tiki_p_wiki_view_ratings eq 'y'}
<div style="display:inline;float:right;background-color:white; padding: 1px 3px; border:1px solid #666666; -moz-border-radius : 10px;font-size:.8em;">
<div id="pollopen">
<span class="button2"><a href="#" onclick="javascript:show('pollzone');hide('polledit');hide('pollopen');" class="link" title="{tr}Click to see the ratings{/tr}">{tr}Rating{/tr}</a></span>
</div>
{if $tiki_p_wiki_vote_ratings eq 'y'}
<div id="polledit">
<div class="pollnav">
<span class="button2"><a href="#" onclick="javascript:hide('pollzone');hide('polledit');show('pollopen');" class="link">{tr}[-]{/tr}</a></span>
<span class="button2"><a href="#" onclick="javascript:show('pollzone');hide('polledit');hide('pollopen');" class="link">{tr}View{/tr}</a></span>
</div>
{if $ratings.title}<div>{$ratings.title}</div>{/if}
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
id="poll{$ratings.info.pollId}{$ratings.options[ix].optionId}" {if $user_vote eq $ratings.options[ix].optionId} checked="checked"{/if} />
</td><td valign="top" {if $user_vote eq $ratings.options[ix].optionId}class="highlight"{/if}>
<label for="poll{$ratings.info.pollId}{$ratings.options[ix].optionId}">{$ratings.options[ix].title}</label></td>
<td valign="top" {if $user_vote eq $ratings.options[ix].optionId}class="highlight"{/if}>
({$ratings.options[ix].votes})
</td></tr>
{/section}
</table>
<div align="center"><input type="submit" name="pollVote" value="{tr}vote{/tr}" style="border:1px solid #666666;background-color:white;font-size:.8em;"/></div>
</form>
</div>
<div id="pollzone">
<div class="pollnav">
<span class="button2"><a href="#" onclick="javascript:hide('pollzone');hide('polledit');show('pollopen');" class="link">{tr}[-]{/tr}</a></span>
<span class="button2"><a href="#" onclick="javascript:hide('pollzone');show('polledit');hide('pollopen');" class="link">{tr}vote{/tr}</a></span>
</div>
{if $ratings.title}<div>{$ratings.title}</div>{/if}
{section name=ix loop=$ratings.options}
<div>{$ratings.options[ix].votes} : {$ratings.options[ix].title}</div>
{/section}
</div>

{else}
<div id="pollzone">
<div class="pollnav">
<span class="button2"><a href="#" onclick="javascript:hide('pollzone');hide('polledit');show('pollopen');" class="link">{tr}[-]{/tr}</a></span>
</div>
{if $ratings.title}<div>{$ratings.title}</div>{/if}
{section name=ix loop=$ratings.options}
<div>{$ratings.options[ix].votes} : {$ratings.options[ix].title}</div>
{/section}
</div>
{/if}
</div>
{/if}
