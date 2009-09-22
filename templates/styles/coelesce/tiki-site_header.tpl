{* $Id$ *}
{* Template for Tikiwiki site identity header *}
<div id="header-top">
<div id="login_search">
	<div class="wrapper">
		{if $filegals_manager eq '' and $print_page ne 'y'}
		{if $prefs.feature_site_login eq 'y'}
			{include file="tiki-site_header_login.tpl"}
		{/if}
		{/if}
		<div id="login_language"> | language</div>
	</div>	
	{if $filegals_manager eq '' and $print_page ne 'y'}
	{if $prefs.feature_sitesearch eq 'y' and $prefs.feature_search eq 'y' and $tiki_p_search eq 'y'}
		<div id="sitesearchbar"{if $prefs.feature_sitemycode neq 'y' and $prefs.feature_sitelogo neq 'y' and $prefs.feature_sitead neq 'y' and $prefs.feature_fullscreen eq 'y' and $filegals_manager eq '' and $print_page ne 'y'}{if $smarty.session.fullscreen neq 'y'}style="margin-right: 80px"{/if}{/if}>
			<div class="wrapper">
		{if $prefs.feature_search_fulltext eq 'y'}
		{include file="tiki-searchresults.tpl"
									searchNoResults="false"
									searchStyle="menu"
									searchOrientation="horiz"}{else}
		{include file="tiki-searchindex.tpl"
									searchNoResults="false"
									searchStyle="menu"
									searchOrientation="horiz"}{/if}
			</div>
		</div>{* search the site *}
	{/if}
	{/if}
</div>
{if $prefs.feature_sitemycode eq 'y' && ($prefs.sitemycode_publish eq 'y' or $tiki_p_admin eq 'y')}
	{if $prefs.feature_sitead eq 'y' && ($prefs.sitead_publish eq 'y' or $tiki_p_admin eq 'y')}
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
{/if}
{* Site logo left or right, and sitead or not. *}
{if $prefs.feature_sitelogo eq 'y' and $prefs.sitelogo_align neq 'center'}
<div class="clearfix" id="sioptions">
	{if $prefs.feature_sitelogo eq 'y' and $prefs.sitelogo_align eq 'left'}
		{if $prefs.feature_sitead eq 'y' && ($prefs.sitead_publish eq 'y' or $tiki_p_admin eq 'y')}
	<div id="sitead" class="floatright">{eval var=$prefs.sitead}</div>
		{/if}
	<div id="sitelogo" class="floatleft" {if $prefs.sitelogo_bgcolor ne ''}style="background-color: {$prefs.sitelogo_bgcolor};"{/if}>
		{if $prefs.sitelogo_src}<a href="./" title="{tr}{$prefs.sitelogo_title}{/tr}"><img src="{$prefs.sitelogo_src}" alt="{tr}{$prefs.sitelogo_alt}{/tr}" style="border: none" /></a>{/if}
		<div id="sitetitles">
			<div id="sitetitle"><a href="index.php">{tr}{$prefs.sitetitle}{/tr}</a></div>
			<div id="sitesubtitle">{tr}{$prefs.sitesubtitle}{/tr}</div>
		</div>
	</div>
	{/if}
	{if $prefs.feature_sitelogo eq 'y' and $prefs.sitelogo_align eq 'right'}
		{if $prefs.feature_sitead eq 'y' && ($prefs.sitead_publish eq 'y' or $tiki_p_admin eq 'y')}
	<div id="sitead" class="floatleft">{eval var=$prefs.sitead}</div>
		{/if}
	<div id="sitelogo" class="floatright"{if $prefs.sitelogo_bgcolor ne ''} style="background-color: {$prefs.sitelogo_bgcolor};" {/if}>
		{if $prefs.sitelogo_src}<a href="./" title="{tr}{$prefs.sitelogo_title}{/tr}"><img src="{$prefs.sitelogo_src}" alt="{tr}{$prefs.sitelogo_alt}{/tr}" style="border: none" /></a>{/if}
	<div id="sitetitles">
			<div id="sitetitle"><a href="index.php">{tr}{$prefs.sitetitle}{/tr}</a></div>
			<div id="sitesubtitle">{tr}{$prefs.sitesubtitle}{/tr}</div>
		</div>
	</div>
	{/if}
</div>
{/if}

{* Sitelogo centered, and sitead: to work in small vertical space, ad (halfbanner) is floated left; a second bannerzone is floated right. *}
{if $prefs.feature_sitelogo eq 'y' and $prefs.sitelogo_align eq 'center'}
<div class="clearfix" id="sioptionscentered">
	{if $prefs.feature_sitead eq 'y' && ($prefs.sitead_publish eq 'y' or $tiki_p_admin eq 'y')}
	<div class="floatright"><div id="bannertopright">{banner zone='topright'}</div></div>{/if}
	{if $prefs.feature_sitead eq 'y' && ($prefs.sitead_publish eq 'y' or $tiki_p_admin eq 'y')}
		<div id="sitead" class="floatleft" {*style="width: 300px"*}>{eval var=$prefs.sitead}</div>
		{/if}
	<div id="sitelogo"{if $prefs.sitelogo_bgcolor ne ''} style="background-color: {$prefs.sitelogo_bgcolor};" {/if}>{if $prefs.sitelogo_src}<a href="./" title="{tr}{$prefs.sitelogo_title}{/tr}"><img src="{$prefs.sitelogo_src}" alt="{tr}{$prefs.sitelogo_alt}{/tr}" style="border: none" /></a>{/if}
	<div id="sitetitles">
			<div id="sitetitle"><a href="index.php">{tr}{$prefs.sitetitle}{/tr}</a></div>
			<div id="sitesubtitle">{tr}{$prefs.sitesubtitle}{/tr}</div>
		</div>
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
</div>{*  end div#header-top *}
{* navbar and search *}
<div class="clearfix" id="tiki-top">
	{include file="tiki-top_bar.tpl"}
</div>
{* topbar custom code *}
{if $prefs.feature_topbar_custom_code}
<div class="clearfix" id="topbar_custom_code">
	{eval var=$prefs.feature_topbar_custom_code}
</div>
{/if}
