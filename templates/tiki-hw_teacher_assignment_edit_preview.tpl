{* $Header: /cvsroot/tikiwiki/tiki/templates/tiki-hw_teacher_assignment_edit_preview.tpl,v 1.3 2004-02-06 20:37:01 ggeller Exp $ *}
{* tiki-hw_teacher_assignment_edit_preview.tpl *}
{* Adapted from tiki-preview_article.tpl *}
{* January 16, 2004 *}
{* George G. Geller *}

<h2>{tr}Preview{/tr}: {$page}</h2>
<div class="article">
<div class="articletitle">
<span class="titlea">{$title}</span><br/>
<span class="titleb">{tr}Due{/tr} {tr}on:{/tr} {$expireDate|tiki_short_datetime} </span>
</div>

{* GGG No reviews or ratings
{if $type eq 'Review'}
<div class="articleheading">
{tr}Rating{/tr}: 
{repeat count=$rating}
<img src="img/icons/blue.gif" alt=''/>
{/repeat}
{if $rating > $entrating}
<img src="img/icons/bluehalf.gif" alt=''/>
{/if}
({$rating}/10)
</div>
{/if}
GGG *}

<div class="articleheading">
<table  cellpadding="0" cellspacing="0">
<tr><td  valign="top">
{* GGG No images for assignments, maybe activate later
{if $useImage eq 'y'}
  {if $hasImage eq 'y'}
    {if $articleId gt 0}
      <img alt="{tr}Article image{/tr}" border="0" src="article_image.php?id={$articleId}" />
    {else}
      <img alt="{tr}Article image{/tr}" border="0" src="{$tempimg}" />
    {/if}
  {else}
    <img alt="{tr}Topic image{/tr}" border="0" src="topic_image.php?id={$topicId}" />
  {/if}
{else}
  <img alt="{tr}Topic image{/tr}" border="0" src="topic_image.php?id={$topicId}" />
{/if}
 GGG *}
</td><td  valign="top">
<span class="articleheading">{$parsed_heading}</span>
</td></tr>
</table>
</div>

{* GGG No byte size for assignments, maybe activate later
<div style="padding:5px;" class="articletrailer">
({$size} bytes
)
</div>
 GGG *}

<div class="articlebody">
{$parsed_body}
</div>
</div>
