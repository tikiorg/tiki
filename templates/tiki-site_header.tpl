{* $Header: /cvsroot/tikiwiki/tiki/templates/tiki-site_header.tpl,v 1.1 2004-05-31 23:03:43 luciash Exp $ *}
{* Template for TikiWiki site identity header *}
{if $feature_sitenavbar eq 'y'}
<!-- site navigation bar -->
{/if}
{if $feature_sitelogo eq 'y'}
<div id="sitelogo">
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
