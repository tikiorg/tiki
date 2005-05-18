{* $Header: /cvsroot/tikiwiki/tiki/templates/tiki-site_header.tpl,v 1.6 2005-05-18 11:03:20 mose Exp $ *}
{* Template for TikiWiki site identity header *}
{if $feature_sitemycode eq 'y' && ($sitemycode_publish eq 'y' or $tiki_p_admin eq 'y')}{eval var=$sitemycode}{* here can be custom site admin code *}{/if}
{if $feature_siteloc eq 'y' and $feature_breadcrumbs eq 'y'}
<div id="sitelocbar">
{if $feature_siteloclabel eq 'y' }{tr}Location : {/tr}{/if}
		{if $trail}{breadcrumbs type="trail" loc="site" crumbs=$trail}{breadcrumbs type="pagetitle" loc="site" crumbs=$trail} 
		{else}
                        <a title="{tr}{$crumbs[0]->description}{/tr}" href="{$crumbs[0]->url}" accesskey="1">{$crumbs[0]->title}</a>
			{if $structure eq 'y'}
				{section loop=$structure_path name=ix}
					{$site_crumb_seper|escape:"html"}
					{if $structure_path[ix].pageName ne $page or $structure_path[ix].page_alias ne $page_info.page_alias}
					<a href="tiki-index.php?page_ref_id={$structure_path[ix].page_ref_id}">
					{/if}
					{if $structure_path[ix].page_alias}
						{$structure_path[ix].page_alias}
					{else}
						{$structure_path[ix].pageName}
					{/if}
					{if $structure_path[ix].pageName ne $page or $structure_path[ix].page_alias ne $page_info.page_alias}
					</a>
					{/if}
				{/section}
			{else}
				{if $page ne ''}{$site_crumb_seper|escape:"html"} {$page}
				{elseif $title ne ''}{$site_crumb_seper|escape:"html"} {$title}
				{elseif $thread_info.title ne ''}{$site_crumb_seper|escape:"html"} {$thread_info.title}
				{elseif $forum_info.name ne ''}{$site_crumb_seper|escape:"html"} {$forum_info.name}
				{/if}
			{/if}
		{/if}
</div>{* bar with location indicator *}
{if $trail}{breadcrumbs type="desc" loc="site" crumbs=$trail}{else}{breadcrumbs type="desc" loc="site" crumbs=$crumbs}{/if}
{/if}
{if $feature_sitenav eq 'y'}
<div id="sitenavbar">
	{eval var=$sitenav_code}
</div>
{* site navigation bar *}
{/if}
{if $feature_sitelogo eq 'y'}
<div id="sitelogo"{if $sitelogo_bgcolor ne ''} style="background-color: {$sitelogo_bgcolor};"{/if}>
	<a href="./" title="{$sitelogo_title}"><img src="{$sitelogo_src}" alt="{$sitelogo_alt}" style="border: none" /></a>
</div>{* site logo *}
{/if}
{if $feature_sitead eq 'y'}
<div id="sitead" align="center">
{if $feature_sitead eq 'y' && ($sitead_publish eq 'y' or $tiki_p_admin eq 'y')}{eval var=$sitead}{/if}
</div>
{* optional ads (banners) *}
{/if}
{if $feature_sitesearch eq 'y' and $feature_search eq 'y'}
<div id="sitesearchbar">
{include
    file="tiki-searchindex.tpl"
    searchNoResults="false"
    searchStyle="menu"
    searchOrientation="horiz"
}
</div>
{* search the site *}
{/if}
