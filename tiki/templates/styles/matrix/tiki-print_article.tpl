{* $Header: /cvsroot/tikiwiki/tiki/templates/styles/matrix/tiki-print_article.tpl,v 1.4 2003-08-01 10:31:17 redflo Exp $ *}
{* Index we display a wiki page here *}

{include file="header.tpl"}
<div id="tiki-main">
<div class="articletitle">
<span class="titlea">{$title}</span><br/>
<span class="titleb">{tr}By:{/tr}{$authorName} {tr}on:{/tr}{$publishDate|tiki_short_datetime} ({$reads} {tr}reads{/tr})</span><br/>
</div>
<div class="articleheading">
<table width="100%" cellpadding="0" cellspacing="0">
<tr><td width="25%" valign="top">
{if $useImage eq 'y'}
{if $hasImage eq 'y'}
<img alt="theimage" border="0" src="article_image.php?id={$articleId}" />
{else}
<img alt="theimage" border="0" src="topic_image.php?id={$topicId}" />
{/if}
{else}
<img alt="theimage" border="0" src="topic_image.php?id={$topicId}" />
{/if}
</td><td width="75%" valign="top">
<span class="articleheading">{$parsed_heading}</span>
</td></tr>
</table>
</div>
<div class="articletrailer">
({$size} bytes
{if $tiki_p_edit_article}
[<a class="trailer" href="tiki-edit_article.php?articleId={$articleId}">{tr}Edit{/tr}</a>] 
{/if}
{if $tiki_p_remove_article}
[<a class="trailer" href="tiki-list_articles.php?remove={$articleId}">{tr}Remove{/tr}</a>]
{/if}
)
</div>
<div class="articlebody">
{$parsed_body}
</div>
</div>
{include file="footer.tpl"}
