{section name=ix loop=$listpages}
{if $listpages[ix].disp_article eq 'y'}
<div class="article">
<div class="articletitle">
<span class="titlea">{$listpages[ix].title}</span><br />
{if ($listpages[ix].show_author eq 'y')
 or ($listpages[ix].show_pubdate eq 'y')
 or ($listpages[ix].show_expdate eq 'y')
 or ($listpages[ix].show_reads eq 'y')}	
<span class="titleb">
{if $listpages[ix].show_author eq 'y'}	
{tr}By:{/tr} {$listpages[ix].authorName}
{/if}
{if $listpages[ix].show_pubdate eq 'y'}
{tr}on:{/tr} {$listpages[ix].publishDate|tiki_short_datetime} 
{/if}
{if $listpages[ix].show_expdate eq 'y'}
{tr}expires:{/tr} {$listpages[ix].expireDate|tiki_short_datetime} 
{/if}
{if $listpages[ix].show_reads eq 'y'}
({$listpages[ix].reads} {tr}reads{/tr})
{/if}
</span><br />
{/if}
</div>
{if $listpages[ix].use_ratings eq 'y'}
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
{if $listpages[ix].show_image eq 'y'}
<td valign="top">
{if $listpages[ix].useImage eq 'y'}
{if $listpages[ix].hasImage eq 'y'}
<a href="tiki-read_article.php?articleId={$listpages[ix].articleId}"><img {if $listpages[ix].isfloat eq 'y'}style="margin-right:4px;float:left;"{/if} alt="{$listpages[ix].topicName}" border="0" src="article_image.php?id={$listpages[ix].articleId}" {if $listpages[ix].image_x > 0}width="{$listpages[ix].image_x}"{/if}{if $listpages[ix].image_y > 0 }height="{$listpages[ix].image_y}"{/if}/></a>
{else}
<a href="tiki-read_article.php?articleId={$listpages[ix].articleId}"><img {if $listpages[ix].isfloat eq 'y'}style="margin-right:4px;float:left;"{/if} alt="{$listpages[ix].topicName}" border="0" src="topic_image.php?id={$listpages[ix].topicId}" /></a>
{/if}
{else}
{section name=it loop=$topics}
{if ($topics[it].topicId eq $listpages[ix].topicId) and ($topics[it].image_size > 0)}
<a href="tiki-read_article.php?articleId={$listpages[ix].articleId}"><img {if $listpages[ix].isfloat eq 'y'}style="margin-right:4px;float:left;"{/if} alt="{$listpages[ix].topicName}" border="0" src="topic_image.php?id={$listpages[ix].topicId}" /></a>
{/if}
{/section}
{/if}
{if ($listpages[ix].show_avatar eq 'y')}
  <td valign="top"><a href="tiki-user_preferences.php?view_user={$listpages[ix].author}">
  <img alt="{$listpages[ix].author}" border="1" src="{$listpages[ix].avatarLibName}" />
  </a></td>
{/if}
{if $listpages[ix].isfloat eq 'n'}
</td><td  valign="top">
{/if}
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
    <td>
    <a href="tiki-read_article.php?articleId={$listpages[ix].articleId}" class="trailer">{tr}Read More{/tr}</a>
    </td>
    {if (($listpages[ix].size > 0) and ($listpages[ix].show_size eq 'y'))}
      <td>
      ({$listpages[ix].size} {tr}bytes{/tr})
      </td>
    {/if}
  {/if}
  {if ($feature_article_comments eq 'y')
   and ($tiki_p_read_comments eq 'y')
   and ($listpages[ix].allow_comments eq 'y')}
    <td>
    {if $listpages[ix].comments_cant eq 0}{tr}no comments{/tr}
    {elseif $listpages[ix].comments_cant eq 1}{tr}1 comment{/tr}
    {else}{$listpages[ix].comments_cant} {tr}comments{/tr}
    {/if}
    </td>
  {/if}
{/if}
<td style="text-align:right;">
{if $tiki_p_edit_article eq 'y'}
  <a class="trailer" href="tiki-edit_article.php?articleId={$listpages[ix].articleId}"><img src='img/icons/edit.gif' border='0' alt='{tr}Edit{/tr}' title='{tr}Edit{/tr}' /></a>
{/if}
  <a class="trailer" href="tiki-print_article.php?articleId={$listpages[ix].articleId}"><img src='img/icons/ico_print.gif' border='0' alt='{tr}Print{/tr}' title='{tr}Print{/tr}' /></a>
{if $tiki_p_remove_article eq 'y'}
  <a class="trailer" href="tiki-list_articles.php?remove={$listpages[ix].articleId}" onclick="return confirmTheLink(this,'{tr}Are you sure you want to delete this article?{/tr}')"><img src='img/icons2/delete.gif' border='0' alt='{tr}Remove{/tr}' title='{tr}Remove{/tr}' /></a>
{/if}
</td>
</tr>
</table>

</div>

</div>

{/if}
{/section}
