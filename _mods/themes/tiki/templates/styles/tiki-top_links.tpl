{if $prefs.feature_featuredLinks eq 'y'}
<div id="toplinks">
{section name=ix loop=$featuredLinks}
{if $featuredLinks[ix].type eq 'f'}
<a href="tiki-featured_link.php?type={$featuredLinks[ix].type}&amp;url={$featuredLinks[ix].url|escape:"url"}">{$featuredLinks[ix].title}</a>
{else}
<a {if $featuredLinks[ix].type eq 'n'}target='_blank'{/if} href="{$featuredLinks[ix].url}">{$featuredLinks[ix].title}</a>
{/if}
{/section}
{assign var="feature_featuredLinks" value="n"}
</div>
{/if}
