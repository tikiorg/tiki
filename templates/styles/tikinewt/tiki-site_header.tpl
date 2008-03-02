{* $Header: /cvsroot/tikiwiki/tiki/templates/styles/tikinewt/tiki-site_header.tpl,v 1.1.2.1 2008-03-02 14:07:24 chibaguy Exp $ *}
{* Template for Tikiwiki site identity header *}{if $prefs.feature_sitemycode eq 'y' && ($prefs.sitemycode_publish eq 'y' or $tiki_p_admin eq 'y')}
		{eval var=$prefs.sitemycode}{* here will be parsed the custom site header code *}{/if}
	<ul class="clearfix" id="sioptions">
	{if $prefs.feature_sitelogo eq 'y' and $prefs.sitelogo_align eq 'left'}
		<li>
			<div id="sitelogo" style="{if $prefs.sitelogo_bgcolor ne ''}background-color: {$prefs.sitelogo_bgcolor};" {/if}>
			<a href="./" title="{$prefs.sitelogo_title}"><img src="{$prefs.sitelogo_src}" alt="{$prefs.sitelogo_alt}" style="border: none" /></a>
			</div>
		</li>
	{/if}
	{if $prefs.feature_sitead eq 'y'}
	<li class="sicenter">
		<div id="sitead">
		{if $prefs.feature_sitead eq 'y' && ($prefs.sitead_publish eq 'y' or $tiki_p_admin eq 'y')}{eval var=$prefs.sitead}{/if}
		</div>
	</li>
	{/if}
	{if $prefs.feature_sitelogo eq 'y' && $prefs.sitelogo_align eq 'center'}
	<li class="sicenter"{if $prefs.feature_sitead neq 'y' && ($prefs.sitead_publish neq 'y' or $tiki_p_admin neq 'y')}id="nositead"{/if}>
		<div id="sitelogo"{if $prefs.sitelogo_bgcolor ne ''} style="background-color: {$prefs.sitelogo_bgcolor};" {/if}>
			<a href="./" title="{$prefs.sitelogo_title}"><img src="{$prefs.sitelogo_src}" alt="{$prefs.sitelogo_alt}" style="border: none" /></a>
		</div>{* site logo *}
	</li>
	{/if}
	{if $prefs.feature_sitelogo eq 'y' and $prefs.sitelogo_align eq 'right'}
	<li class="clearfix{if $prefs.feature_sitelogo eq 'y' and $prefs.sitelogo_align eq 'right'} siright{/if}">
		<div id="sitelogo" style="{if $prefs.sitelogo_bgcolor ne ''}background-color: {$prefs.sitelogo_bgcolor};" {/if}>
			<a href="./" title="{$prefs.sitelogo_title}"><img src="{$prefs.sitelogo_src}" alt="{$prefs.sitelogo_alt}" style="border: none" /></a>
		</div>
	</li>
	{/if}
	</ul>

{if $filegals_manager ne 'y' and $print_page ne 'y'}
{if $prefs.feature_sitesearch eq 'y' and $prefs.feature_search eq 'y'}
		<div id="sitesearchbar">{if $prefs.feature_search_fulltext eq 'y'}
		{include file="tiki-searchresults.tpl"
									searchNoResults="false"
									searchStyle="menu"
									searchOrientation="horiz"}{else}
		{include file="tiki-searchindex.tpl"
									searchNoResults="false"
									searchStyle="menu"
									searchOrientation="horiz"}{/if}
		</div>
{/if}
{/if}{*
{/if}
{/if}*}
	{*{if $trail}{breadcrumbs	type="desc"	loc="site" crumbs=$trail}{
	 else}{breadcrumbs type="desc" loc="site" crumbs=$crumbs}{/if}*}

{if $prefs.feature_sitenav eq 'y'}
		<div id="sitenavbar">
			{eval var=$prefs.sitenav_code}
		</div>{* site navigation bar/phplayers menu NOT IMPLEMENTED *}
{/if}