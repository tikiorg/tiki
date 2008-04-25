<!-- templates/tiki-preview.tpl start -->
<div class="wikipreview">
<h2>{tr}Preview{/tr} {if $staging_preview eq 'y'}of current staging copy{/if}: {if $beingStaged eq 'y' and $prefs.wikiapproval_hideprefix == 'y'}{$approvedPageName|escape}{else}{$page|escape}{/if}</h2>
{if $prefs.feature_wiki_description eq 'y'}
<small>{$description}</small>
{/if}
<div  class="wikitext">
{if $staging_preview neq 'y'}
<div align="center" class="attention" style="font-weight:bold">{tr}Note: Remember that this is only a preview, and has not yet been saved!{/tr}</div>
{/if}
{$parsed}
</div>
{if $has_footnote and isset($parsed_footnote)}
<div  class="wikitext">{$parsed_footnote}</div>
{/if}
</div>
<!-- templates/tiki-preview.tpl end -->
