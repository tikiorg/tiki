{if $is_categorized eq 'y' and $prefs.feature_categories eq 'y' and $prefs.feature_categorypath eq 'y'}
<div align="right">{$display_catpath}</div>
{/if}
{if $prefs.feature_freetags eq 'y' and $tiki_p_view_freetags eq 'y' and isset($freetags.data[0])}
{include file="freetag_list.tpl"}
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
{if $show_reads eq 'y'}({$reads} {tr}Reads{/tr}){/if}
</span><br />
</div>

{if $type eq 'Review'}
<div class="articleheading">
{tr}Rating{/tr}: 
{repeat count=$rating}
{icon _id='star' alt="{tr}star{/tr}"}
{/repeat}
{if $rating > $entrating}
{icon _id='star_half' alt="{tr}half star{/tr}"}
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
{if $prefs.feature_multilingual eq 'y' and $show_lang eq 'y' and $lang}{include file="translated-lang.tpl" td='y' type='article'}{/if}
<td style="text-align:right;">
{if $tiki_p_edit_article eq 'y'}
<a class="trailer" href="tiki-edit_article.php?articleId={$articleId}">{icon _id='page_edit'}</a>
{/if}
{if $prefs.feature_cms_print eq 'y'}
<a class="trailer" href="tiki-print_article.php?articleId={$articleId}">{icon _id='printer' alt='{tr}Print{/tr}'}</a>
{/if}
{if $prefs.feature_multilingual eq 'y' and $tiki_p_edit_article eq 'y'}
<a class="trailer" href="tiki-edit_translation.php?id={$articleId}&amp;type=article">{icon _id='world' alt='{tr}Translation{/tr}'}</a> &nbsp;
{/if}
{if $tiki_p_remove_article eq 'y'}
<a class="trailer" href="tiki-list_articles.php?remove={$articleId}">{icon _id='cross' alt='{tr}Remove{/tr}'}</a>
{/if}
</td>
</tr>
</table>
</div>
<div class="articlebody">
{$parsed_body}
{if $pages > 1}
	<div align="center">
		<a href="tiki-read_article.php?articleId={$articleId}&amp;page={$first_page}"><img src='pics/icons/resultset_first.png' border='0' alt='{tr}First page{/tr}' title='{tr}First page{/tr}' width='16' height='16' /></a>

		<a href="tiki-read_article.php?articleId={$articleId}&amp;page={$prev_page}">{icon _id='resultset_previous' alt='{tr}Previous page{/tr}'}</a>

		<small>{tr}page{/tr}:{$pagenum}/{$pages}</small>

		<a href="tiki-read_article.php?articleId={$articleId}&amp;page={$next_page}">{icon _id='resultset_next' alt='{tr}Next page{/tr}'}</a>


		<a href="tiki-read_article.php?articleId={$articleId}&amp;page={$last_page}"><img src='pics/icons/resultset_last.png' border='0' alt='{tr}Last page{/tr}' title='{tr}Last page{/tr}' width='16' height='16' ></a>
	</div>
{/if}
</div>
{if $show_linkto eq 'y' and $linkto}
<div class="articlesource">
{tr}Source{/tr}: <a href="{$linkto}"{if $prefs.popupLinks eq 'y'} target="_blank"{/if}>{$linkto}</a>
</div>
{/if}

{if $prefs.articles_feature_copyrights  eq 'y' and $prefs.wikiLicensePage}
  {if $prefs.wikiLicensePage == $page}
    {if $tiki_p_edit_copyrights eq 'y'}
      <p class="editdate">{tr}To edit the copyright notices{/tr} <a href="copyrights.php?page={$copyrightpage}">{tr}Click Here{/tr}</a>.</p>
    {/if}
  {else}
    <p class="editdate">{tr}The content on this page is licensed under the terms of the{/tr} <a href="tiki-index.php?page={$prefs.wikiLicensePage}&amp;copyrightpage={$page|escape:"url"}">{$prefs.wikiLicensePage}</a>.</p>
  {/if}
{/if}


{if $prefs.feature_article_comments == 'y'
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
{tr}Add Comment{/tr}
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

{if $is_categorized eq 'y' and $prefs.feature_categories eq 'y' and $prefs.feature_categoryobjects eq 'y'}
<div class="catblock">{$display_catobjects}</div>
{/if}

