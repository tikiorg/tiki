<div class="articletitle">
<span class="titlea">{$title}</span><br />
<span class="titleb">{tr}By:{/tr} {$authorName} {tr}on:{/tr} {$publishDate|tiki_short_datetime} ({$reads} {tr}reads{/tr})</span><br />
</div>

{if $type eq 'Review'}
<div class="articleheading">
{tr}Rating{/tr}: 
{repeat count=$rating}
<img src="img/icons/blue.gif" alt=""/>
{/repeat}
{if $rating > $entrating}
<img src="img/icons/bluehalf.gif" alt=""/>
{/if}
({$rating}/10)
</div>
{/if}


<div class="articleheading">
<table  cellpadding="0" cellspacing="0">
<tr>{if $isfloat eq 'n'}<td  valign="top">{else}<td valign="top">{/if}
{if $useImage eq 'y'}
  {if $hasImage eq 'y'}
    <img {if $isfloat eq 'y'}style="margin-right:4px;float:left;"{/if} alt="{tr}Article image{/tr}" border="0" src="article_image.php?id={$articleId}" {if $image_x > 0}width="{$image_x}"{/if}{if $image_y > 0 }height="{$image_y}"{/if}/>
  {else}
    <img {if $isfloat eq 'y'}style="margin-right:4px;float:left;"{/if} alt="{tr}Topic image{/tr}" border="0" src="topic_image.php?id={$topicId}" />
  {/if}
{else}
  <img {if $isfloat eq 'y'}style="margin-right:4px;float:left;"{/if} alt="{tr}Topic image{/tr}" border="0" src="topic_image.php?id={$topicId}" />
{/if}
{if $isfloat eq 'n'}
</td><td  valign="top">
{/if}
<span class="articleheading">{$parsed_heading}</span>
</td></tr>
</table>
</div>
<div class="articletrailer">
<table ><tr><td>
({$size} bytes)
</td>
<td style="text-align:right;">
{if $tiki_p_edit_article eq 'y'}
<a class="trailer" href="tiki-edit_article.php?articleId={$articleId}"><img src='img/icons/edit.gif' border='0' alt='{tr}Edit{/tr}' title='{tr}Edit{/tr}' /></a>
{/if}
<a class="trailer" href="tiki-print_article.php?articleId={$articleId}"><img src='img/icons/ico_print.gif' border='0' alt='{tr}Print{/tr}' title='{tr}Print{/tr}' /></a>
{if $tiki_p_remove_article eq 'y'}
<a class="trailer" href="tiki-list_articles.php?remove={$articleId}"><img src='img/icons2/delete.gif' border='0' alt='{tr}Remove{/tr}' title='{tr}Remove{/tr}' /></a>
{/if}
</td>
</tr>
</table>
</div>
<div class="articlebody">
{$parsed_body}
{if $pages > 1}
	<div align="center">
		<a href="tiki-read_article.php?articleId={$articleId}&amp;page={$first_page}"><img src='img/icons2/nav_first.gif' border='0' alt='{tr}First page{/tr}' title='{tr}First page{/tr}' /></a>

		<a href="tiki-read_article.php?articleId={$articleId}&amp;page={$prev_page}"><img src='img/icons2/nav_dot_right.gif' border='0' alt='{tr}Previous page{/tr}' title='{tr}Previous page{/tr}' /></a>

		<small>{tr}page{/tr}:{$page}/{$pages}</small>

		<a href="tiki-read_article.php?articleId={$articleId}&amp;page={$next_page}"><img src='img/icons2/nav_dot_left.gif' border='0' alt='{tr}Next page{/tr}' title='{tr}Next page{/tr}' /></a>


		<a href="tiki-read_article.php?articleId={$articleId}&amp;page={$last_page}"><img src='img/icons2/nav_last.gif' border='0' alt='{tr}Last page{/tr}' title='{tr}Last page{/tr}' /></a>
	</div>
{/if}
</div>
{if $feature_article_comments eq 'y'}
{if $tiki_p_read_comments eq 'y'}
<div id="page-bar">
<table>
<tr><td>
<div class="button2"><a href="javascript:flip('comzone{if $comments_show eq 'y'}open{/if}');" class="linkbut">{if $comments_cant eq 0}{tr}comment{/tr}{elseif $comments_cant eq 1}1 {tr}comment{/tr}{else}{$comments_cant} {tr}comments{/tr}{/if}</a></div>
</td></tr></table>
</div>
{include file=comments.tpl}
{/if}
{/if}
