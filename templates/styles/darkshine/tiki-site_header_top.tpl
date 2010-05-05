{* $Id$ *}
{* wrapper for site header top items (div#header-top start/end tags, site identity options and login form) *}
{include file='tiki-site_header_top_begin.tpl'}
{include file='tiki-site_header_options.tpl'}

<div class="clearfix" id="topcontent">
	{include file='tiki-sitesearchbar.tpl'}
	{include file='tiki-site_header_login_popup.tpl'}
	{include file='tiki-secondary_sitemenu.tpl'}
</div>
	{* Top Bar Custom Code goes here *}
	{if $prefs.feature_siteidentity eq 'y' and $prefs.feature_topbar_custom_code}
		{eval var=$prefs.feature_topbar_custom_code}
	{/if}
	

{include file='tiki-site_header_top_end.tpl'}