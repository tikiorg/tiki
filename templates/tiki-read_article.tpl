<div class="articletitle">
<span class="titlea">{$title}</span><br/>
<span class="titleb">{tr}By:{/tr}{$authorName} {tr}on:{/tr}{$publishDate|date_format:"%a %d of %b, %Y [%H:%M:%S]"} ({$reads} {tr}reads{/tr})</span><br/>
</div>

{if $type eq 'Review'}
<div class="articleheading">
{tr}Rating{/tr}: 
{repeat count=$rating}
<img src="img/icons/blue.gif" />
{/repeat}
{if $rating > $entrating}
<img src="img/icons/bluehalf.gif" />
{/if}
({$rating}/10)
</div>
{/if}


<div class="articleheading">
<table width="100%" cellpadding="0" cellspacing="0">
<tr><td width="25%" valign="top">
{if $useImage eq 'y'}
{if $hasImage eq 'y'}
<img alt="theimage" border="0" src="article_image.php?id={$articleId}" {if $image_x > 0}width="{$image_x}"{/if}{if $image_y > 0 }height="{$image_y}"{/if}/>
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
{if $tiki_p_edit_article eq 'y'}
[<a class="trailer" href="tiki-edit_article.php?articleId={$articleId}">{tr}Edit{/tr}</a>] 
{/if}
{if $tiki_p_remove_article eq 'y'}
[<a class="trailer" href="tiki-list_articles.php?remove={$articleId}">{tr}Remove{/tr}</a>]
{/if}
[<a class="trailer" href="tiki-print_article.php?articleId={$articleId}">Print</a>]
)
</div>
<div class="articlebody">
{$parsed_body}
</div>
{if $feature_article_comments eq 'y'}
{include file=comments.tpl}
{/if}