{* $Id$ *}{include file="header.tpl"}
{* Index we display a wiki page here *}
{if $prefs.feature_bidi eq 'y'}
<div dir="rtl">
{/if}
{if $prefs.feature_ajax eq 'y'}
{include file="tiki-ajax_header.tpl"}
{/if}
			<div id="tiki-center">
			{$mid_data}
      </div>
			
{if $prefs.feature_bidi eq 'y'}
</div>
{/if}
{include file="footer.tpl"}
