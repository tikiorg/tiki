{* $Header: /cvsroot/tikiwiki/_mods/templates/kuro/templates/styles/kuroBK/tiki-site_header.tpl,v 1.3 2005-02-14 12:06:49 michael_davey Exp $ *}
{* Template for TikiWiki site identity header *}
{if $feature_sitemycode eq 'y' && ($sitemycode_publish eq 'y' or $tiki_p_admin eq 'y')}{eval var=$sitemycode}{* here can be custom site admin code *}{/if}

{*if $feature_sitesearch eq 'y'*}
<div id="sitesearchbar">
{include
    file="tiki-searchindex.tpl"
    searchNoResults="false"
    searchStyle="menu"
    searchOrientation="horiz"
    wikiActionIcon="styles/kuroBK/but_go"
}
</div>     
{* search the site *}
{*/if*}           

{if $feature_siteloc eq 'y' or $feature_page_title eq 'y'}
<div id="sitelocbar">
{tr}Breadcrumbs : {/tr}<a href="./" accesskey="1">{$siteTitle}</a>
			{if $structure eq 'y'}
				{section loop=$structure_path name=ix}
					/
					{if $structure_path[ix].pageName ne $page or $structure_path[ix].page_alias ne $page_info.page_alias}
					<a href="tiki-index.php?page_ref_id={$structure_path[ix].page_ref_id}" class="pagedescription">
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
				{if $page ne ''}/ <a href="tiki-index.php?page={$page|escape:"url"}" class="pagedescription">{$page}</a>
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
