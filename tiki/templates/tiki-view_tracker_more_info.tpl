{include file="header.tpl"}
{if $feature_bidi eq 'y'}
<table dir="rtl" ><tr><td>
{/if}
<div id="tiki-main">
<h3>{tr}Details{/tr}</h3>
<table class="normal">
{foreach key=l item=v from=$info}
<tr><td class="formcolor">{$l}</td>
<td class="formcolor">
{$v}
</td>
</tr>
{/section}
</table>
<div class="cbox">
<a href="#" onclick="javascript:window.close();" class="link">{tr}close{/tr}</a>
</div>
</div>
{if $feature_bidi eq 'y'}
</td></tr></table>
{/if}
{include file="footer.tpl"}
