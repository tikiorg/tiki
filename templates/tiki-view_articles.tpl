{section name=ix loop=$listpages}
<div class="articletitle">
{if $art_view_title eq 'y'}
<span class="titlea">{$listpages[ix].title}</span><br />
{/if}
<span class="titleb">
{if $art_view_author eq 'y'}	
{tr}By:{/tr} {$listpages[ix].authorName} 
{/if}
{if $art_view_date eq 'y'}
{tr}on:{/tr} {$listpages[ix].publishDate|tiki_short_datetime} 
{/if}
{if $art_view_reads eq 'y'}
({$listpages[ix].reads} {tr}reads{/tr})
{/if}
</span><br />
</div>
{if $listpages[ix].type eq 'Review'}
<div class="articleheading">
{tr}Rating{/tr}: 
{repeat count=$listpages[ix].rating}
<img src="img/icons/blue.gif" alt=''/>
{/repeat}
{if $listpages[ix].rating > $listpages[ix].entrating}
<img src="img/icons/bluehalf.gif" alt=''/>
{/if}
({$listpages[ix].rating}/10)
</div>
{/if}
<div class="articleheading">
<table  cellpadding="0" cellspacing="0">
<tr>
{if $art_view_img eq 'y'}
<td valign="top">
{if $listpages[ix].useImage eq 'y'}
{if $listpages[ix].hasImage eq 'y'}
<img {if $listpages[ix].isfloat eq 'y'}style="margin-right:4px;float:left;"{/if} alt="{$listpages[ix].topicName}" border="0" src="article_image.php?id={$listpages[ix].articleId}" {if $listpages[ix].image_x > 0}width="{$listpages[ix].image_x}"{/if}{if $listpages[ix].image_y > 0 }height="{$listpages[ix].image_y}"{/if}/>
{else}
<img {if $listpages[ix].isfloat eq 'y'}style="margin-right:4px;float:left;"{/if} alt="{$listpages[ix].topicName}" border="0" src="topic_image.php?id={$listpages[ix].topicId}" />
{/if}
{else}
{section name=it loop=$topics}
{if ($topics[it].topicId eq $listpages[ix].topicId) and ($topics[it].image_size > 0)}
<img {if $listpages[ix].isfloat eq 'y'}style="margin-right:4px;float:left;"{/if} alt="{$listpages[ix].topicName}" border="0" src="topic_image.php?id={$listpages[ix].topicId}" />
{/if}
{/section}
{/if}
{if $listpages[ix].isfloat eq 'n'}
</td><td  valign="top">
{/if}
{/if}
<span class="articleheading">{$listpages[ix].parsed_heading}</span>
</td></tr>
</table>
</div>
<div class="articletrailer">
<table >
<tr>
<td>
{if ($listpages[ix].size > 0) or (($feature_article_comments eq 'y') and ($tiki_p_read_comments eq 'y'))}
( <a href="tiki-read_article.php?articleId={$listpages[ix].articleId}" class="trailer">{tr}Read More{/tr}</a>
{if $listpages[ix].size > 0}
- {$listpages[ix].size} {tr}bytes{/tr}
{/if}
{if ($feature_article_comments eq 'y') and ($tiki_p_read_comments eq 'y')} - 
{if $listpages[ix].comments_cant eq 1}{tr}1 comment{/tr}
{else}{$listpages[ix].comments_cant} {tr}comments{/tr}
{/if}
{/if} )
{/if}
</td>
<td style="text-align:right;">
{if $tiki_p_edit_article eq 'y'}
	<a class="trailer" href="tiki-edit_article.php?articleId={$listpages[ix].articleId}"><img src='img/icons/edit.gif' border='0' alt='{tr}Edit{/tr}' title='{tr}Edit{/tr}' /></a>
{/if}
	<a class="trailer" href="tiki-print_article.php?articleId={$listpages[ix].articleId}"><img src='img/icons/ico_print.gif' border='0' alt='{tr}Print{/tr}' title='{tr}Print{/tr}' /></a>
{if $tiki_p_remove_article eq 'y'}
	<a class="trailer" href="tiki-list_articles.php?remove={$listpages[ix].articleId}"><img src='img/icons2/delete.gif' border='0' alt='{tr}Remove{/tr}' title='{tr}Remove{/tr}' /></a>
{/if}
</td>
</tr>
</table>
</div>
{/section}

