{* $Header: /cvsroot/tikiwiki/_mods/modules/top_voted_comments/mod-top_voted_comments.tpl,v 1.1 2006-08-31 12:49:31 sylvieg Exp $ *}
{eval assign="ld" var="ld_voted_comments`$nb_mod_top_voted_comments`"}
{eval assign="t" var="type_voted_comments`$nb_mod_top_voted_comments`"}
{eval assign="s" var="sort_voted_comments`$nb_mod_top_voted_comments`"}

{if $nonums eq 'y'}
{eval var="{tr}`$module_rows` Top Voted Comments{/tr}" assign="title1"}
{else}
{eval var="{tr}Top Voted Comments{/tr}" assign="title1"}
{/if}
{if $type}
{eval var="<br />{tr}$type{/tr}" assign="title3"}
{else}
{eval var=" " assign="title3"}
{/if}

{if $lastdays == 1}
{eval var="<br />{tr}(This last day){/tr}" assign="title2"}
{elseif $lastdays > 1}
{eval var="<br />{tr}(These last $lastdays days){/tr}" assign="title2"}
{else}
{eval var=" " assign="title2"}
{/if}
{eval var="`$title1``$title3``$title2`" assign="tpl_module_title"} 
{tikimodule title=$tpl_module_title name="top_voted_comments"}

{if $lastdaysChoice eq 'y' or $typeChoice eq 'y' or $sortChoice eq 'y'}
<div class="box-choice">
{/if}

{if $lastdaysChoice eq 'y'}
<form method="post" action="{$url}" name="submit_{$ld}">
<input type="hidden" name="{$t}" value="{$type}" />
<input type="hidden" name="{$s}" value="{$sort}" />
{tr}Last days:{/tr}&nbsp;
<select name="{$ld}" onchange="submit_{$ld}.submit()">
<option value="" selected="selected">{tr}All{/tr}</option>
{section name=ix loop=$lastdaysList}
<option value="{$lastdaysList[ix]}" {if $lastdays eq "$lastdaysList[ix]"}selected="selected"{/if}>{$lastdaysList[ix]}</option>
{/section}
</select>
<br />
</form>
{/if}

{if $typeChoice eq 'y'}
<form method="post" action="{$url}" name="submit_{$t}">
<input type="hidden" name="{$ld}" value="{$lastdays}" />
<input type="hidden" name="{$s}" value="{$sort}" />
{tr}Type:{/tr}&nbsp;
<select name="{$t}" onchange="submit_{$t}.submit()">
<option value="" selected="selected">{tr}All{/tr}</option>
{section name=ix loop=$typeList}
<option value="{$typeList[ix]}" {if $type eq "$typeList[ix]"}selected="selected"{/if}>{$typeList[ix]}</option>
{/section}
</select>
<br />
</form>
{/if}

{if $sortChoice eq 'y'}
<form method="post" action="{$url}" name="submit_{$s}">
<input type="hidden" name="{$t}" value="{$type}" />
<input type="hidden" name="{$ld}" value="{$lastdays}" />
{tr}Sort:{/tr}&nbsp;
<select name="{$s}" onchange="submit_{$s}.submit()">
<option value="" selected="selected">{tr}Average{/tr}</option>
<option value="votes" {if $sort == "votes"}selected="selected"{/if}>{tr}Votes{/tr}</option>
<option value="points" {if $sort == "points"}selected="selected"{/if}>{tr}Points{/tr}</option>
</select>
<br />
</form>
{/if}

{if $lastdaysChoice eq 'y' or $typeChoice eq 'y' or $sortChoice eq 'y'}
</div>
{/if}

<table  border="0" cellpadding="0" cellspacing="0">
{section name=ix loop=$voted_comments}
<tr>
{if $nonums != 'y'}<td valign="top" class="module">{$smarty.section.ix.index_next})&nbsp;</td>{/if}
<td class="module">
{$voted_comments[ix].nb}# 
{if $showVotes eq 'y'}{$voted_comments[ix].votes}# {/if}

{if $voted_comments[ix].objectType eq "wiki page"}
<a class="linkmodule" href="tiki-index.php?page={$voted_comments[ix].object|escape}&amp;comzone=show#threadId{$voted_comments[ix].threadId}" title="{$voted_comments[ix].commentDate|tiki_short_datetime}, {tr}by{/tr} {$voted_comments[ix].userName}{if $type == ""}, {tr}wiki page{/tr} {$voted_comments[ix].object}{/if}">{$voted_comments[ix].title}</a>

{elseif $voted_comments[ix].objectType eq "article"}
<a class="linkmodule" href="tiki-read_article.php?articleId={$voted_comments[ix].object}&amp;comzone=show#threadId{$voted_comments[ix].threadId}" title="{$voted_comments[ix].commentDate|tiki_short_datetime}, {tr}by{/tr} {$voted_comments[ix].userName}{if $type == ""}, {tr}article{/tr}{/if}" >{$voted_comments[ix].title}</a>

{elseif $voted_comments[ix].objectType eq "forum"}
{if $comments[ix].parentId eq 0}
<a class="linkmodule" href="tiki-view_forum_thread.php?forumId={$voted_comments[ix].object}&comments_parentId={$voted_comments[ix].threadId}" title="{$voted_comments[ix].commentDate|tiki_short_datetime}, {tr}by{/tr} {$voted_comments[ix].userName}{if $type == ""}, {tr}forum{/tr}{/if}" >{$voted_comments[ix].title}</a>
{else}
<a class="linkmodule" href="tiki-view_forum_thread.php?forumId={$voted_comments[ix].object}&amp;comments_parentId={$voted_comments[ix].parentId}#threadId{$voted_comments[ix].threadId}" title="{$voted_comments[ix].commentDate|tiki_short_datetime}, {tr}by{/tr} {$voted_comments[ix].userName}{if $type == ""}, {tr}forum{/tr}"{/if}>{$voted_comments[ix].title}</a>
{/if}
{elseif $comments[ix].objectType eq "faq"}
<a class="linkmodule" href="tiki-view_faq.php?faqId={$comments[ix].faqId}&amp;comzone=show" {if $type eq ""}title="{tr}faq{/tr}"{/if}>{$comments[ix].title}</a>
{elseif $comments[ix].objectType eq "blog"}
<a class="linkmodule" href="tiki-view_blog.php?blogId={$comments[ix].blogId}&amp;comzone=show" {if $type eq ""}title="{tr}blog{/tr}"{/if}>{$comments[ix].title}</a>
{/if}
</td>
</tr>
{/section}
</table>
{/tikimodule}
