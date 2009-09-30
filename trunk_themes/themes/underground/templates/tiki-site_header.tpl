{* $Id$ *}
<div id="header-top">
	{*if $filegals_manager ne 'y' and $print_page ne 'y'*}
		{if $prefs.feature_sitemycode eq 'y' && ($prefs.sitemycode_publish eq 'y' or $tiki_p_admin eq 'y')}
			{eval var=$prefs.sitemycode}
		{/if}
	{*/if*}
	{include file='tiki-top_bar.tpl'}
</div>
<div class="clearfix" id="header-bottom">
	{include file='tiki-sitesearchbar.tpl'}
	<div id="sitesubtitle">	{tr}{$prefs.sitesubtitle}{/tr}</div>
	<div id="sitetitle"><a href="index.php">{tr}{$prefs.sitetitle}{/tr}</a></div>
</div>
{if $prefs.feature_topbar_custom_code}
	<div class="clearfix" id="topbar_custom_code">
		{eval var=$prefs.feature_topbar_custom_code}
	</div>
{/if}