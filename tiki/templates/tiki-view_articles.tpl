{section name=ix loop=$listpages}
<div class="articletitle">
<span class="titlea">{$listpages[ix].title}</span><br/>
<span class="titleb">{tr}By:{/tr}{$listpages[ix].authorName} {tr}on:{/tr}{$listpages[ix].publishDate|date_format:"%a %d of %b, %Y [%H:%M:%S]"} ({$listpages[ix].reads} {tr}reads{/tr})</span><br/>
</div>
<div class="articleheading">
<table width="100%" cellpadding="0" cellspacing="0">
<tr><td width="25%" valign="top">
{if $listpages[ix].useImage eq 'y'}
{if $listpages[ix].hasImage eq 'y'}
<img alt="theimage" border="0" src="article_image.php?id={$listpages[ix].articleId}" {if $listpages[ix].image_x > 0}width="{$listpages[ix].image_x}"{/if}{if $listpages[ix].image_y > 0 }height="{$listpages[ix].image_y}"{/if}/>
{else}
<img alt="theimage" border="0" src="topic_image.php?id={$listpages[ix].topicId}" />
{/if}
{else}
<img alt="theimage" border="0" src="topic_image.php?id={$listpages[ix].topicId}" />
{/if}
</td><td width="75%" valign="top">
<span class="articleheading">{$listpages[ix].parsed_heading}</span>
</td></tr>
</table>
</div>
<div class="articletrailer">
(<a href="tiki-read_article.php?articleId={$listpages[ix].articleId}" class="link">{tr}Read More{/tr}</a> {$listpages[ix].size} bytes
{if $tiki_p_edit_article eq 'y'}
[<a class="trailer" href="tiki-edit_article.php?articleId={$listpages[ix].articleId}">Edit</a>] 
{/if}
{if $tiki_p_remove_article eq 'y'}
[<a class="trailer" href="tiki-list_articles.php?remove={$listpages[ix].articleId}">Remove</a>]
{/if}
[<a class="trailer" href="tiki-print_article.php?articleId={$listpages[ix].articleId}">Print</a>]
)
</div>
{/section}

