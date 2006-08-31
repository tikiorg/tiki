{* $Header: /cvsroot/tikiwiki/_mods/modules/most_commented/mod-most_commented.tpl,v 1.1 2006-08-31 12:49:30 sylvieg Exp $ *}
{eval assign="ld" var="ld_commented`$nb_mod_most_commented`"}
{eval assign="t" var="type_commented`$nb_mod_most_commented`"}

{if $nonums eq 'y'}
{eval var="{tr}`$module_rows` Most Commented{/tr}" assign="title1"}
{else}
{eval var="{tr}Most Commented{/tr}" assign="title1"}
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
{tikimodule title=$tpl_module_title name="most_commented"}

{if $lastdaysChoice eq 'y' or $typeChoice eq 'y'}
<div class="box-choice">
{/if}

{if $lastdaysChoice eq 'y'}
<form method="post" action="{$url}" name="submit_{$ld}">
<input type="hidden" name="{$t}" value="{$type}" />
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

{if $lastdaysChoice eq 'y' or $typeChoice eq 'y'}
</div>
{/if}

<table  border="0" cellpadding="0" cellspacing="0">
{section name=ix loop=$comments}
<tr>
{if $nonums != 'y'}<td valign="top" class="module">{$smarty.section.ix.index_next})&nbsp;</td>{/if}
<td class="module">
{if $comments[ix].objectType eq "wiki page"}
<a class="linkmodule" href="tiki-index.php?page={$comments[ix].object|escape}&amp;comzone=show" {if $type eq ""}title="{tr}wiki page{/tr}"{/if}>{$comments[ix].object}</a>

{elseif $comments[ix].objectType eq "article"}
<a class="linkmodule" href="tiki-read_article.php?articleId={$comments[ix].articleId}&amp;comzone=show" {if $type eq ""}title="{tr}article{/tr}"{/if}>{$comments[ix].title}</a>

{elseif $comments[ix].objectType eq "forum"}
<a class="linkmodule" href="tiki-view_forum_thread.php?forumId={$comments[ix].object}&comments_parentId={$comments[ix].threadId}" {if $type eq ""}title="{tr}forum{/tr}"{/if}>{$comments[ix].title}</a>

{elseif $comments[ix].objectType eq "faq"}
<a class="linkmodule" href="tiki-view_faq.php?faqId={$comments[ix].faqId}&amp;comzone=show" {if $type eq ""}title="{tr}faq{/tr}"{/if}>{$comments[ix].title}</a>

{elseif $comments[ix].objectType eq "blog"}
<a class="linkmodule" href="tiki-view_blog.php?blogId={$comments[ix].blogId}&amp;comzone=show" {if $type eq ""}title="{tr}blog{/tr}"{/if}>{$comments[ix].title}</a>
{/if} <span class="module_text_small">- {$comments[ix].nb} comments</span>
</td>
</tr>
{/section}
</table>
{/tikimodule}
