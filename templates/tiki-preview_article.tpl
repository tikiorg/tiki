<h2>{tr}Preview{/tr}: {$page}</h2>
<div class="articletitle">
<span class="titlea">{$title}</span><br/>
<span class="titleb">{tr}By:{/tr}{$authorName} {tr}on:{/tr}{$publishDate|date_format:"%a %d of %b, %Y [%H:%M:%S]"} ({$reads} {tr}reads{/tr})</span>
</div>

{if $type eq 'Review'}
<div class="articleheading">
{tr}Rating{/tr}: 
{repeat count=$rating}
<img src="img/icons/blue.gif" />
{/repeat}
{if $rating > $entrating}
<img src="img/icons/bluehalf.gif" />
{/if}
({$rating}/10)
</div>
{/if}


<div class="articleheading">
<table width="100%" cellpadding="0" cellspacing="0">
<tr><td width="25%" valign="top">
{if $useImage eq 'y'}
{if $hasImage eq 'y'}
<img alt="theimage" border="0" src="{$tempimg}" />
{else}
<img alt="theimage" border="0" src="topic_image.php?id={$topicId}" />
{/if}
{else}
<img alt="theimage" border="0" src="topic_image.php?id={$topicId}" />
{/if}
</td><td width="75%" valign="top">
<span class="articleheading">{$parsed_heading}</span>
</td></tr>
</table>
</div>
<div class="articletrailer">
({$size} bytes
)
</div>

<div class="articlebody">
{$parsed_body}
</div>
