<!-- templates/tiki-preview.tpl start -->
<h2>{tr}Preview{/tr}: {$page}</h2>
{if $feature_wiki_description eq 'y'}
<small>{$description}</small>
{/if}
<div  class="wikitext">{$parsed}</div>
{if $has_footnote}
<div  class="wikitext">{$parsed_footnote}</div>
{/if}
<!-- templates/tiki-preview.tpl end -->
