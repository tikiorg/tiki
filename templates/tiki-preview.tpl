<!-- templates/tiki-preview.tpl start -->
<h2>{tr}Preview{/tr}: {$page}</h2>
{if $feature_wiki_description eq 'y'}
<small>{$description}</small>
{/if}
<div  class="wikitext"><div align="center" class="attention" style="font-weight:bold">{tr}Note: Remember that this is only a preview, and has not yet been saved!{/tr}</div>{$parsed}</div>
{if $has_footnote}
<div  class="wikitext">{$parsed_footnote}</div>
{/if}
<!-- templates/tiki-preview.tpl end -->
