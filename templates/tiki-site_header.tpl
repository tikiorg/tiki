{* $Header: /cvsroot/tikiwiki/tiki/templates/tiki-site_header.tpl,v 1.2 2004-06-23 22:34:28 mose Exp $ *}
{* Template for TikiWiki site identity header *}
{if $feature_sitenavbar eq 'y'}
<!-- site navigation bar -->
{/if}
{if $feature_sitelogo eq 'y'}
<div id="sitelogo"{if $sitelogo_bgcolor ne ''} style="background-color: {$sitelogo_bgcolor}"{/if}>
	<a href="./" title="{$sitelogo_title}"><img src="{$sitelogo_src}" alt="{$sitelogo_alt}" style="border: none" /></a>
</div><!-- site logo -->
{/if}
{if $feature_sitead eq 'y'}
<!-- optional ads (banners) -->
{/if}
{if $feature_sitesearch eq 'y'}
<!-- search the site -->
{/if}
{if $feature_siteloc eq 'y'}
<!-- bar with location indicator -->
{/if}
