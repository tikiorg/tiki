{include file="header.tpl"}{* This must be included as the first thing in a document to be XML compliant *}
{* $Id$ *}
{* Index we display a wiki page here *}

<div id="tiki-main">
<div class="articletitle">
<span class="titlea">{$title}</span><br />
<span class="titleb">{tr}By:{/tr}{$authorName} {tr}on:{/tr}{$publishDate|tiki_short_datetime} ({$reads} {tr}Reads{/tr})</span><br />
</div>
<div class="articleheading">
<table width="100%" cellpadding="0" cellspacing="0">
<tr><td width="25%" valign="top">
{if $useImage eq 'y'}
  {if $hasImage eq 'y'}
    <img alt="{tr}Article image{/tr}" border="0" src="article_image.php?id={$articleId}" />
  {else}
    <img alt="{tr}Topic image{/tr}" border="0" src="topic_image.php?id={$topicId}" />
  {/if}
{else}
  <img alt="{tr}Topic image{/tr}" border="0" src="topic_image.php?id={$topicId}" />
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
