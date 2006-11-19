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
({$listpages[ix].nbreads} {tr}reads{/tr})
{/if}
</span><br />
{/if}
</div>
{if $listpages[ix].use_ratings eq 'y'}
<div class="articleheading">
{tr}Rating{/tr}: 
{repeat count=$listpages[ix].rating}
<img src="pics/icons/star.png" alt="{tr}star{/tr}" border="0" width="16" height="16" />
{/repeat}
{if $listpages[ix].rating > $listpages[ix].entrating}
<img src="pics/icons/star_half.png" alt="{tr}half star{/tr}" border="0" width="16" height="16" />
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
{if ($listpages[ix].size > 0) or (($feature_article_comments eq 'y') and ($tiki_p_read_comments eq 'y'))}
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
  {if ($feature_article_comments eq 'y')
   and ($tiki_p_read_comments eq 'y')
   and ($listpages[ix].allow_comments eq 'y')}
    <td class="articletrailer">
<a href="#comment" onclick="javascript:flip('comzone');flip('comzone_close','inline');return false;" class="linkbut">
{if $listpages[ix].comments_cant == 0 or ($tiki_p_read_comments  == 'n' and $tiki_p_post_comments  == 'y')}
{tr}add comment{/tr}
{elseif $listpages[ix].comments_cant == 1}
<span class="highlight">{tr}1 comment{/tr}</span>
{else}
<span class="highlight">{$listpages[ix].comments_cant} {tr}comments{/tr}</span>
{/if}
<span id="comzone_close" style="display:{if isset($smarty.session.tiki_cookie_jar.show_comzone) and $smarty.session.tiki_cookie_jar.show_comzone eq 'y'}inline{else}none{/if};">({tr}close{/tr})</span>
</a>
    </td>
  {/if}
{/if}
<td style="text-align:right;">
{if $tiki_p_edit_article eq 'y' or ($listpages[ix].author eq $user and $listpages[ix].creator_edit eq 'y')}
  <a class="trailer" href="tiki-edit_article.php?articleId={$listpages[ix].articleId}"><img src='pics/icons/page_edit.png' border='0' alt='{tr}Edit{/tr}' title='{tr}Edit{/tr}' width='16' height='16' /></a>
{/if}
{if $feature_cms_print eq 'y'}
  <a class="trailer" href="tiki-print_article.php?articleId={$listpages[ix].articleId}"><img src='pics/icons/printer.png' border='0' alt='{tr}Print{/tr}' title='{tr}Print{/tr}' width='16' height='16' /></a>
{/if}
{if $feature_multilingual eq 'y' and $tiki_p_edit_article eq 'y'}
<a class="trailer" href="tiki-edit_translation.php?id={$listpages[ix].articleId}&amp;type=article"><img src='pics/icons/world.png' border='0' alt='{tr}Translation{/tr}' title='{tr}Translation{/tr}' width='16' height='16' /></a>
{/if}
{if $tiki_p_remove_article eq 'y'}
  &nbsp;&nbsp;<a class="trailer" href="tiki-list_articles.php?remove={$listpages[ix].articleId}"><img src='pics/icons/cross.png' border='0' alt='{tr}Remove{/tr}' title='{tr}Remove{/tr}' width='16' height='16' /></a>
{/if}
</td>
</tr>
</table>
</div>
</div>
{/if}
{/section}

{if $cant_pages and $cant_pages ne 0}
<div align="center">
<div class="mini">
{if $prev_offset >= 0}
[<a class="artprevnext" href="tiki-view_articles.php?offset={$prev_offset}{if $find}&amp;find={$find}{/if}{if $topic}&amp;topic={$topic}{/if}{if $type}&amp;type={$type}{/if}{if $sort_mode ne 'publishDate_desc'}&amp;sort_mode={$sort_mode}{/if}">{tr}prev{/tr}</a>]&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;[<a class="artprevnext" href="tiki-view_articles.php?offset={$next_offset}{if $find}&amp;find={$find}{/if}{if $topic}&amp;topic={$topic}{/if}{if $type}&amp;type={$type}{/if}{if $sort_mode ne 'publishDate_desc'}&amp;sort_mode={$sort_mode}{/if}">{tr}next{/tr}</a>]
{/if}
{if $direct_pagination eq 'y'}
<br />
{section loop=$cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:$maxArticles}
<a class="artprevnext" href="tiki-view_articles.php?find={$find}&amp;topic={$topic}&amp;type={$type}&amp;offset={$selector_offset}&amp;sort_mode={$sort_mode}">
{$smarty.section.foo.index_next}</a>&nbsp;
{/section}
{/if}
</div>
</div>
{/if}
