{* $Header: /cvsroot/tikiwiki/tiki/templates/tiki-site_header.tpl,v 1.4 2005-01-22 22:56:24 mose Exp $ *}
{* Template for TikiWiki site identity header *}
{if $feature_sitemycode eq 'y' && ($sitemycode_publish eq 'y' or $tiki_p_admin eq 'y')}{eval var=$sitemycode}{* here can be custom site admin code *}{/if}
{if $feature_siteloc eq 'y'}
<div id="sitelocbar">
{tr}Location : {/tr}<a href="./" accesskey="1">{$siteTitle}</a>
			{if $structure eq 'y'}
				{section loop=$structure_path name=ix}
					/
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
				{if $page ne ''}/ {$page}
				{elseif $title ne ''}/ {$title}
				{elseif $thread_info.title ne ''}/ {$thread_info.title}
				{elseif $forum_info.name ne ''}/ {$forum_info.name}
				{/if}
			{/if}
</div>{* bar with location indicator *}
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
{if $feature_sitesearch eq 'y'}
<div id="sitesearchbar">
{tr}Search : {/tr}
</div>
{* search the site *}
{/if}
