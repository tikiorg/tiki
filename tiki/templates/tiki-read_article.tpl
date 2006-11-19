{if $is_categorized eq 'y' and $feature_categories eq 'y' and $feature_categorypath eq 'y'}
<div align="right">{$display_catpath}</div>
{/if}

{if $show_topline eq 'y' and $topline}<div class="articletopline">{$topline}</div>{/if}
<div class="articletitle">
<span class="titlea">
{$arttitle}</span>
<br />
{if $show_subtitle eq 'y' and $subtitle}<div class="articlesubtitle">{$subtitle}</div>{/if}
<span class="titleb">
{if $show_author eq 'y' && $authorName}{tr}By:{/tr} {$authorName} {/if}
{if $show_pubdate eq 'y' && $publishDate}{tr}on:{/tr} {$publishDate|tiki_short_datetime} {/if}
{if $show_reads eq 'y'}({$reads} {tr}reads{/tr}){/if}
</span><br />
</div>

{if $type eq 'Review'}
<div class="articleheading">
{tr}Rating{/tr}: 
{repeat count=$rating}
<img src="pics/icons/star.png" alt="{tr}star{/tr}" border="0" width="16" height="16" />
{/repeat}
{if $rating > $entrating}
<img src="pics/icons/star_half.png" alt="{tr}half star{/tr}" border="0" width="16" height="16" />
{/if}
({$rating}/10)
</div>
{/if}


<div class="articleheading">
<table  cellpadding="0" cellspacing="0">
<tr>{if $isfloat eq 'n'}<td  valign="top">{else}<td valign="top">{/if}
{if $useImage eq 'y'}
{if $hasImage eq 'y'}
<a href="#" title="{if $show_image_caption and $image_caption}{$image_caption}{else}{tr}Article image{/tr}{/if}">
<img {if $isfloat eq 'y'}style="margin-right:4px;float:left;"{else}class="articleimage"{/if} 
alt="{if $show_image_caption and $image_caption}{$image_caption}{else}{tr}Article image{/tr}{/if}" 
border="0" src="article_image.php?id={$articleId}"{if $image_x > 0} width="{$image_x}"{/if}{if $image_y > 0 } height="{$image_y}"{/if} /></a>
{else}
<img {if $isfloat eq 'y'}style="margin-right:4px;float:left;"{else}class="articleimage"{/if} 
alt="{tr}Topic image{/tr}" border="0" src="topic_image.php?id={$topicId}" />
{/if}
{else}
{section name=it loop=$topics}
{if ($topics[it].topicId eq $topicId) and ($topics[it].image_size > 0)}
<img {if $isfloat eq 'y'}style="margin-right:4px;float:left;"{else}class="articleimage"{/if} alt="{$topicName}" border="0" src="topic_image.php?id={$topicId}" />
{/if}
{/section}
{/if}
{if $isfloat eq 'n'}
</td><td  valign="top">
{/if}
<div class="articleheadingtext">{$parsed_heading}</div>
</td></tr>
</table>
</div>
<div class="articletrailer">
<table class="wikitopline"><tr>
<td>{if $show_size eq 'y'}
({$size} bytes)
{/if}</td>
{if $feature_multilingual eq 'y' and $show_lang eq 'y' and $lang}{include file="translated-lang.tpl" td='y' type='article'}{/if}
<td style="text-align:right;">
{if $tiki_p_edit_article eq 'y'}
<a class="trailer" href="tiki-edit_article.php?articleId={$articleId}"><img src='pics/icons/page_edit.png' border='0' alt='{tr}Edit{/tr}' title='{tr}Edit{/tr}' width='16' height='16' /></a>
{/if}
{if $feature_cms_print eq 'y'}
<a class="trailer" href="tiki-print_article.php?articleId={$articleId}"><img src='pics/icons/printer.png' border='0' alt='{tr}Print{/tr}' title='{tr}Print{/tr}' width='16' height='16' /></a>
{/if}
{if $feature_multilingual eq 'y' and $tiki_p_edit_article eq 'y'}
<a class="trailer" href="tiki-edit_translation.php?id={$articleId}&amp;type=article"><img src='pics/icons/world.png' border='0' alt='{tr}Translation{/tr}' title='{tr}Translation{/tr}' width='16' height='16' /></a> &nbsp;
{/if}
{if $tiki_p_remove_article eq 'y'}
<a class="trailer" href="tiki-list_articles.php?remove={$articleId}"><img src='pics/icons/cross.png' border='0' alt='{tr}Remove{/tr}' title='{tr}Remove{/tr}' width='16' height='16' /></a>
{/if}
</td>
</tr>
</table>
</div>
<div class="articlebody">
{$parsed_body}
{if $pages > 1}
	<div align="center">
		<a href="tiki-read_article.php?articleId={$articleId}&amp;page={$first_page}"><img src='pics/icons/resultset_first.gif' border='0' alt='{tr}First page{/tr}' title='{tr}First page{/tr}' width='16' height='16' /></a>

		<a href="tiki-read_article.php?articleId={$articleId}&amp;page={$prev_page}"><img src='pics/icons/resultset_previous.png' border='0' alt='{tr}Previous page{/tr}' title='{tr}Previous page{/tr}' width='16' height='16' /></a>

		<small>{tr}page{/tr}:{$pagenum}/{$pages}</small>

		<a href="tiki-read_article.php?articleId={$articleId}&amp;page={$next_page}"><img src='pics/icons/resultset_next.png' border='0' alt='{tr}Next page{/tr}' title='{tr}Next page{/tr}' width='16' height='16' /></a>


		<a href="tiki-read_article.php?articleId={$articleId}&amp;page={$last_page}"><img src='üics/icons/resultset_last.png' border='0' alt='{tr}Last page{/tr}' title='{tr}Last page{/tr}' width='16' height='16' ></a>
	</div>
{/if}
</div>
{if $show_linkto eq 'y' and $linkto}
<div class="articlesource">
{tr}Source{/tr}: <a href="{$linkto}"{if $popupLinks eq 'y'} target="_blank"{/if}>{$linkto}</a>
</div>
{/if}
{if $feature_article_comments == 'y'
  && (($tiki_p_read_comments  == 'y'
  && $comments_cant != 0)
  ||  $tiki_p_post_comments  == 'y'
  ||  $tiki_p_edit_comments  == 'y')}
<div id="page-bar">
<table>
<tr><td>
<div class="button2">
<a href="#comments" onclick="javascript:flip('comzone');flip('comzone_close','inline');return false;" class="linkbut">
{if $comments_cant == 0 or ($tiki_p_read_comments  == 'n' and $tiki_p_post_comments  == 'y')}
{tr}add comment{/tr}
{elseif $comments_cant == 1}
<span class="highlight">{tr}1 comment{/tr}</span>
{else}
<span class="highlight">{$comments_cant} {tr}comments{/tr}</span>
{/if}
<span id="comzone_close" style="display:{if isset($smarty.session.tiki_cookie_jar.show_comzone) and $smarty.session.tiki_cookie_jar.show_comzone eq 'y'}inline{else}none{/if};">({tr}close{/tr})</span>
</a>
</div>
</td></tr></table>
</div>

{include file=comments.tpl}
{/if}

{if $is_categorized eq 'y' and $feature_categories eq 'y' and $feature_categoryobjects eq 'y'}
<div class="catblock">{$display_catobjects}</div>
{/if}

