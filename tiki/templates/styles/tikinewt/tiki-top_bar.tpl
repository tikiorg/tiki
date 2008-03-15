{if $filegals_manager ne 'y' and $print_page ne 'y'}
{if $prefs.feature_sitesearch eq 'y' and $prefs.feature_search eq 'y' and $tiki_p_search eq 'y'}
		<div id="sitesearchbar">{if $prefs.feature_search_fulltext eq 'y'}
		{include file="tiki-searchresults.tpl"
									searchNoResults="false"
									searchStyle="menu"
									searchOrientation="horiz"}{else}
		{include file="tiki-searchindex.tpl"
									searchNoResults="false"
									searchStyle="menu"
									searchOrientation="horiz"}{/if}
		</div>{* search the site *}
{/if}
{/if}
{if $prefs.feature_sitemenu eq 'y'}
	{if $prefs.feature_phplayers eq 'y'}
		{phplayers id=$prefs.feature_topbar_id_menu type=horiz}
	{else}
		{menu id=$prefs.feature_topbar_id_menu type=horiz css=y}
	{/if}
{/if}
{if $prefs.feature_siteloc eq 'y' and $prefs.feature_breadcrumbs eq 'y'}
		<div id="sitelocbar">
			<small>{if $prefs.feature_siteloclabel eq 'y'}{tr}Location : {/tr}{/if}{if
	$trail}{breadcrumbs
			type="trail"
			loc="site"
			crumbs=$trail}{breadcrumbs
							type="pagetitle"
							loc="site"
							crumbs=$trail}{else}<a title="{tr}{$crumbs[0]->description}{/tr}" href="{$crumbs[0]->url}" accesskey="1">{$crumbs[0]->title}</a>
		{if $structure eq 'y'}
			{section loop=$structure_path name=ix}
				{$prefs.site_crumb_seper|escape:"html"}
				{if $structure_path[ix].pageName ne $page or $structure_path[ix].page_alias ne $page_info.page_alias}
			<a href="tiki-index.php?page_ref_id={$structure_path[ix].page_ref_id}">
				{/if}
				{if $structure_path[ix].page_alias}
					{$structure_path[ix].page_alias}
				{else}
					{if $beingStaged eq 'y' and $prefs.wikiapproval_hideprefix == 'y'}{$approvedPageName}{else}{$structure_path[ix].pageName}{/if}
				{/if}
				{if $structure_path[ix].pageName ne $page or $structure_path[ix].page_alias ne $page_info.page_alias}
					</a>
				{/if}
			{/section}
		{else}
			{if $page ne ''}{$prefs.site_crumb_seper|escape:"html"} {if $beingStaged eq 'y' and $prefs.wikiapproval_hideprefix == 'y'}{$approvedPageName}{else}{$page}{/if}
			{elseif $title ne ''}{$prefs.site_crumb_seper|escape:"html"} {$title}
			{elseif $thread_info.title ne ''}{$prefs.site_crumb_seper|escape:"html"} {$thread_info.title}
			{elseif $forum_info.name ne ''}{$prefs.site_crumb_seper|escape:"html"} {$forum_info.name}{/if}
		{/if}
	{/if}</small>
		</div>{* bar with location indicator *}
	{if $trail}{breadcrumbs	type="desc"	loc="site" crumbs=$trail}{
	 else}{breadcrumbs type="desc" loc="site" crumbs=$crumbs}{/if}
{/if}