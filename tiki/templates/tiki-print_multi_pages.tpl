{include file="header.tpl"}
{if $feature_bidi eq 'y'}
<table dir="rtl" width="100%"><tr><td>
{/if}
<h1>Wiki Pages</h1>
{section name=ix loop=$pages}
<h2>{$pages[ix].pageName}</h2>
<div class="wikitext">{$pages[ix].parsed}</div>
<hr/>
{/section}
{if $feature_bidi eq 'y'}
</td></tr></table>
{/if}
{include file="footer.tpl"}