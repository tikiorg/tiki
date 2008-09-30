{* $Id: tiki-site_header.tpl 12244 2008-03-30 13:32:53Z luciash $ *}
{* Custom code ... *}
{if $prefs.feature_sitemycode eq 'y' && ($prefs.sitemycode_publish eq 'y' or $tiki_p_admin eq 'y')}
	{if $prefs.feature_sitead eq 'y' && ($prefs.sitead_publish eq 'y' or $tiki_p_admin eq 'y')}{* ... and a banner *}
		<div id="sitead" class="floatright">
		{eval var=$prefs.sitead}
		</div>
		<div id="customcodewith_ad">
		{eval var=$prefs.sitemycode}{* here will be parsed the 400px-wide custom site header code *}
		</div>
	{else}
		<div id="customcode">
		{eval var=$prefs.sitemycode}
		</div>
	{/if}
{else}
	{if $prefs.feature_sitelogo eq 'y' and $prefs.sitelogo_align neq 'center'}{* Site logo left or right, and sitead or not. *}
		<div class="clearfix" id="sioptions">
		{if $prefs.feature_sitelogo eq 'y' and $prefs.sitelogo_align eq 'left'}
			{if $prefs.feature_sitead eq 'y' && ($prefs.sitead_publish eq 'y' or $tiki_p_admin eq 'y')}
				<div id="sitead" class="floatright">{eval var=$prefs.sitead}</div>
			{/if}
			<div id="sitelogo" class="floatleft" {if $prefs.sitelogo_bgcolor ne ''}style="background-color: {$prefs.sitelogo_bgcolor};"{/if}><a href="./" title="{$prefs.sitelogo_title}"><img src="{$prefs.sitelogo_src}" alt="{$prefs.sitelogo_alt}" style="border: none" /></a>
			</div>
		{/if}
		{if $prefs.feature_sitelogo eq 'y' and $prefs.sitelogo_align eq 'right'}
			{if $prefs.feature_sitead eq 'y' && ($prefs.sitead_publish eq 'y' or $tiki_p_admin eq 'y')}
				<div id="sitead" class="floatleft">{eval var=$prefs.sitead}</div>
			{/if}
			<div id="sitelogo" class="floatright"{if $prefs.sitelogo_bgcolor ne ''} style="background-color: {$prefs.sitelogo_bgcolor};" {/if}><a href="./" title="{$prefs.sitelogo_title}"><img src="{$prefs.sitelogo_src}" alt="{$prefs.sitelogo_alt}" style="border: none" /></a>
			</div>
		{/if}
		</div>
	{/if}

	{* Sitelogo centered, and sitead: to work in small vertical space, ad (halfbanner) is floated left; a second bannerzone is floated right. *}
	{if $prefs.feature_sitelogo eq 'y' and $prefs.sitelogo_align eq 'center'}
	<div class="clearfix" id="sioptionscentered">
		{if $prefs.feature_sitead eq 'y' && ($prefs.sitead_publish eq 'y' or $tiki_p_admin eq 'y')}
		<div id="bannertopright" class="floatright"><div style="width: 234px; height: 60px; background: yellow">banner zone='topright'</div></div>
		<div id="sitead" class="floatleft" {*style="width: 300px"*}>{eval var=$prefs.sitead}</div>
		{/if}
		<div id="sitelogo"{if $prefs.sitelogo_bgcolor ne ''} style="background-color: {$prefs.sitelogo_bgcolor};" 	{/if}><a href="./" title="{$prefs.sitelogo_title}"><img src="{$prefs.sitelogo_src}" alt="{$prefs.sitelogo_alt}" style="border: none" /></a>
		</div>	
	</div>
	{/if}

	{* No sitelogo but a sitead: ad is centered. *}
	{if $prefs.feature_sitelogo eq 'n'}
		{if $prefs.feature_sitead eq 'y' && ($prefs.sitead_publish eq 'y' or $tiki_p_admin eq 'y')}
		<div align="center">
		{eval var=$prefs.sitead}</div>
		{/if}
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
{if $filegals_manager ne 'y' and $print_page ne 'y'}
	{if $prefs.feature_site_login eq 'y'}
	{include file="tiki-site_header_login.tpl"}
	{/if}
{/if}