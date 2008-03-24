{include file="header.tpl"}
{if $prefs.feature_bidi eq 'y'}
<table dir="rtl" ><tr><td>
{/if}

<div id="tiki-mid">
<div class="cbox">
<div class="cbox-title">
{tr}Error{/tr}
</div>
<div class="cbox-data">
{$msg}<br /><br />
<a href="javascript:window.close()" class="linkmenu">{tr}Close Window{/tr}</a><br /><br />
</div>
</div>
</div>
{if $prefs.feature_bidi eq 'y'}
</td></tr></table>
{/if}
{include file="footer.tpl"}
