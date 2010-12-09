{* $Id$ *}
{* wrapper for site header top items (div#header-top start/end tags, site identity options. login form is custom in this theme.) *}
{include file='tiki-site_header_top_begin.tpl'}
{include file='tiki-secondary_sitemenu.tpl'}
<div id="login_search">
	<div class="wrapper">
		{include file='tiki-site_header_login.tpl'}
		{include file='tiki-sitesearchbar.tpl'}
	</div>
</div>
{include file='tiki-site_header_options.tpl'}
{include file='tiki-site_header_top_end.tpl'}