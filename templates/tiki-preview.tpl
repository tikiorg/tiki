<!-- templates/tiki-preview.tpl start -->
<div class="wikipreview" {if $prefs.ajax_autosave eq "y"}style="display:none;"{/if}>
{if $prefs.ajax_autosave eq "y"}
	<div style="float:right;">
		{self_link _icon="arrow_left" _ajax="n" _onclick="ajax_preview( 'editwiki', autoSaveId );$('.wikipreview').hide();return false;"}{tr}Popup preview{/tr}{/self_link}
		{self_link _icon="close" _ajax="n" _onclick="$('.wikipreview').hide();return false;"}{tr}Close preview{/tr}{/self_link}
	</div>
{/if}
{if $prefs.feature_jquery_ui eq "y"}{jq}$('.wikipreview').resizable({handles:'n,s'});{/jq}{/if}
<h2>{tr}Preview{/tr} {if $staging_preview eq 'y'}of current staging copy{/if}: {if $beingStaged eq 'y' and $prefs.wikiapproval_hideprefix == 'y'}{$approvedPageName|escape}{else}{$page|escape}{/if}</h2>
{if $prefs.feature_wiki_description eq 'y'}
<small>{$description}</small>
{/if}
{if $staging_preview neq 'y'}
<div align="center" class="attention" style="font-weight:bold">{tr}Note: Remember that this is only a preview, and has not yet been saved!{/tr}</div>
{/if}
<div  class="wikitext">
{$parsed}
</div>
{if $has_footnote and isset($parsed_footnote)}
<div  class="wikitext">{$parsed_footnote}</div>
{/if}
</div>
<hr style="clear:both; height:0px;"/> {* Information below the wiki content
must not overlap the wiki content that could contain floated elements *}
<!-- templates/tiki-preview.tpl end -->
