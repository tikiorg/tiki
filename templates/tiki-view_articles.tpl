{section name=ix loop=$listpages}
{if $listpages[ix].disp_article eq 'y'}
<div class="article">
{if $listpages[ix].show_topline eq 'y' and $listpages[ix].topline}<div class="articletopline">{$listpages[ix].topline}</div>{/if}
<div class="articletitle">
<span class="titlea">{$listpages[ix].title}</span><br />
{if $listpages[ix].show_subtitle eq 'y' and $listpages[ix].subtitle}<div class="articlesubtitle">{$listpages[ix].subtitle}</div>{/if}
{if ($listpages[ix].show_author eq 'y')
 or ($listpages[ix].show_pubdate eq 'y')
 or ($listpages[ix].show_expdate eq 'y')
 or ($listpages[ix].show_reads eq 'y')}	
<span class="titleb">
{if $listpages[ix].show_author eq 'y'}	
{tr}By:{/tr} {$listpages[ix].authorName}&nbsp;
{/if}
{if $listpages[ix].show_pubdate eq 'y'}
{tr}on:{/tr} {$listpages[ix].publishDate|tiki_short_datetime}&nbsp;
{/if}
{if $listpages[ix].show_expdate eq 'y'}
{tr}expires:{/tr} {$listpages[ix].expireDate|tiki_short_datetime}&nbsp;
{/if}
{if $listpages[ix].show_reads eq 'y'}
({$listpages[ix].nbreads} {tr}Reads{/tr})
{/if}
</span><br />
{/if}
</div>
{if $listpages[ix].use_ratings eq 'y'}
<div class="articleheading">
{tr}Rating{/tr}: 
{repeat count=$listpages[ix].rating}
{icon _id='star' alt="{tr}star{/tr}"}
{/repeat}
{if $listpages[ix].rating > $listpages[ix].entrating}
{icon _id='star_half' alt="{tr}half star{/tr}"}
{/if}
({$listpages[ix].rating}/10)
</div>
{/if}
<div class="articleheading">
<table  cellpadding="0" cellspacing="0">
<tr>
<td valign="top">
{if $listpages[ix].show_image eq 'y'}
{if $listpages[ix].useImage eq 'y'}
{if $listpages[ix].hasImage eq 'y'}
<a href="tiki-read_article.php?articleId={$listpages[ix].articleId}" title="{if $listpages[ix].show_image_caption and
$listpages[ix].image_caption}{$listpages[ix].image_caption}{else}{$listpages[ix].topicName}{/if}"><img 
{if $listpages[ix].isfloat eq 'y'}style="margin-right:4px;float:left;"{else}class="articleimage"{/if} 
alt="{if $listpages[ix].show_image_caption and $listpages[ix].image_caption}{$listpages[ix].image_caption}{else}{$listpages[ix].topicName}{/if}" 
border="0" src="article_image.php?id={$listpages[ix].articleId}"
{if $listpages[ix].image_x > 0} width="{$listpages[ix].image_x}"{/if}{if $listpages[ix].image_y > 0 } height="{$listpages[ix].image_y}"{/if} /></a>
{else}
<a href="tiki-read_article.php?articleId={$listpages[ix].articleId}" title="{if $listpages[ix].show_image_caption and
$listpages[ix].image_caption}{$listpages[ix].image_caption}{else}{$listpages[ix].topicName}{/if}"><img 
{if $listpages[ix].isfloat eq 'y'}style="margin-right:4px;float:left;"{else}class="articleimage"{/if} 
alt="{if $listpages[ix].show_image_caption and $listpages[ix].image_caption}{$listpages[ix].image_caption}{else}{$listpages[ix].topicName}{/if}" 
border="0" src="topic_image.php?id={$listpages[ix].topicId}" /></a>
{/if}
{else}
{section name=it loop=$topics}
{if ($topics[it].topicId eq $listpages[ix].topicId) and ($topics[it].image_size > 0)}
<a href="tiki-read_article.php?articleId={$listpages[ix].articleId}" title="{if $listpages[ix].show_image_caption and
$listpages[ix].image_caption}{$listpages[ix].image_caption}{else}{$listpages[ix].topicName}{/if}"><img 
{if $listpages[ix].isfloat eq 'y'}style="margin-right:4px;float:left;"{else}class="articleimage"{/if} 
alt="{if $listpages[ix].show_image_caption and $listpages[ix].image_caption}{$listpages[ix].image_caption}{else}{$listpages[ix].topicName}{/if}" 
border="0" src="topic_image.php?id={$listpages[ix].topicId}" /></a>
{/if}
{/section}
{/if}
{/if}
{if ($listpages[ix].show_avatar eq 'y')}
{$listpages[ix].author|avatarize}
{/if}
{if $listpages[ix].isfloat eq 'n'}
</td><td  valign="top">
{/if}
<div class="articleheadingtext">{$listpages[ix].parsed_heading}</div>
</td></tr>
</table>
</div>
<div class="articletrailer">
<table class="wikitopline">
<tr>
{if ($listpages[ix].size > 0) or (($prefs.feature_article_comments eq 'y') and ($tiki_p_read_comments eq 'y'))}
  {if ($listpages[ix].heading_only ne 'y')}
    {if ($listpages[ix].size > 0)}
	    <td class="articletrailer">
	    <a href="tiki-read_article.php?articleId={$listpages[ix].articleId}" class="trailer">{tr}Read More{/tr}</a>
	    </td>
	    {if ($listpages[ix].show_size eq 'y')}
	      <td class="articletrailer">
	      ({$listpages[ix].size} {tr}bytes{/tr})
	      </td>
	    {/if}
    {/if}
  {/if}
  {if ($prefs.feature_article_comments eq 'y')
   and ($tiki_p_read_comments eq 'y')
   and ($listpages[ix].allow_comments eq 'y')}
    <td class="articletrailer">
<a href="tiki-read_article.php?articleId={$listpages[ix].articleId}&amp;show_comzone=y#comments" class="linkbut">
{if $listpages[ix].comments_cant == 0 or ($tiki_p_read_comments  == 'n' and $tiki_p_post_comments  == 'y')}
{tr}Add Comment{/tr}
{elseif $listpages[ix].comments_cant == 1}
<span class="highlight">{tr}1 comment{/tr}</span>
{else}
<span class="highlight">{$listpages[ix].comments_cant} {tr}comments{/tr}</span>
{/if}
</a>
    </td>
  {/if}
{/if}
<td style="text-align:right;">
{if $tiki_p_edit_article eq 'y' or ($listpages[ix].author eq $user and $listpages[ix].creator_edit eq 'y')}
  <a class="trailer" href="tiki-edit_article.php?articleId={$listpages[ix].articleId}">{icon _id='page_edit'}</a>
{/if}
{if $prefs.feature_cms_print eq 'y'}
  <a class="trailer" href="tiki-print_article.php?articleId={$listpages[ix].articleId}">{icon _id='printer' alt='{tr}Print{/tr}'}</a>
{/if}
{if $prefs.feature_multilingual eq 'y' and $tiki_p_edit_article eq 'y'}
<a class="trailer" href="tiki-edit_translation.php?id={$listpages[ix].articleId}&amp;type=article">{icon _id='world' alt='{tr}Translation{/tr}'}</a>
{/if}
{if $tiki_p_remove_article eq 'y'}
  &nbsp;&nbsp;<a class="trailer" href="tiki-list_articles.php?remove={$listpages[ix].articleId}">{icon _id='cross' alt='{tr}Remove{/tr}'}</a>
{/if}
</td>
</tr>
</table>
</div>
</div>
{/if}
{sectionelse}
{tr}No articles.{/tr}
{/section}

{pagination_links cant=$cant step=$maxArticles offset=$offset}{/pagination_links}
