<h2>{tr}Preview{/tr}: {$page}</h2>
<div class="article">
<div class="articletitle">
<span class="titlea">{$title}</span><br />
<span class="titleb">{tr}By:{/tr} {$authorName} {tr}on:{/tr} {$publishDate|tiki_short_datetime} ({$reads} {tr}Reads{/tr})</span>
</div>

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


<div class="articleheading">
<table  cellpadding="0" cellspacing="0">
<tr><td  valign="top">
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
</td><td  valign="top">
<span class="articleheading">{$parsed_heading}</span>
</td></tr>
</table>
</div>
<div style="padding:5px;" class="articletrailer">
({$size} bytes
)
</div>

<div class="articlebody">
{$parsed_body}
</div>
</div>
