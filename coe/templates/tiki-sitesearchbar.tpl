{* $Id$ *}
{* site header search form *}
{if $filegals_manager eq '' and $print_page ne 'y'}
	{if $prefs.feature_sitesearch eq 'y' and $prefs.feature_search eq 'y' and $tiki_p_search eq 'y'}
		<div id="sitesearchbar"{if $prefs.feature_sitemycode neq 'y' and $prefs.feature_banners eq 'y' && $prefs.feature_sitelogo neq 'y' and ($prefs.feature_banners neq 'y' or $prefs.feature_sitead neq 'y') and $prefs.feature_fullscreen eq 'y' and $filegals_manager eq '' and $print_page ne 'y'}{if $smarty.session.fullscreen neq 'y'}style="margin-right: 80px"{/if}{/if}>
		{if $prefs.feature_search_fulltext eq 'y'}
		{include file='tiki-searchresults.tpl'
									searchNoResults="false"
									searchStyle="menu"
									searchOrientation="horiz"}{else}
		{include file='tiki-searchindex.tpl'
									searchNoResults="false"
									searchStyle="menu"
									searchOrientation="horiz"}{/if}
		</div>
	{/if}
{/if}
