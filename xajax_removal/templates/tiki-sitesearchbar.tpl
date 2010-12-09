{* $Id$ *}
{* site header search form *}
{if empty($filegals_manager) or !isset($print_page) or $print_page ne 'y'}
	{if $prefs.feature_sitesearch eq 'y' and $tiki_p_search eq 'y'}
	{if $prefs.feature_search eq 'y' or $prefs.feature_search_fulltext eq 'y'}
		<div id="sitesearchbar"{if $prefs.feature_sitemycode neq 'y' and $prefs.feature_banners eq 'y' && $prefs.feature_sitelogo neq 'y' and ($prefs.feature_banners neq 'y' or $prefs.feature_sitead neq 'y') and $prefs.feature_fullscreen eq 'y' and $filegals_manager eq '' and $print_page ne 'y'}{if $smarty.session.fullscreen neq 'y'}style="margin-right: 80px"{/if}{/if}>
		{if $prefs.feature_search_fulltext eq 'y'}
		{include file='tiki-searchresults.tpl'
									where=$prefs.search_default_where
									searchNoResults="false"
									searchStyle="menu"
									iSearch=1
									searchOrientation="horiz"}
		{else}
			<form action="tiki-searchindex.php" method="get" id="search-form" class="findtable">
				<label class="findtitle">
					{tr}Find{/tr} <input id="highlight2" name="filter[content]" size="14" type="text" accesskey="s" value="{$words}" />
				</label>
				{jq}
					$("#highlight2").tiki("autocomplete", "pagename");
				{/jq}
			</form>
		{/if}
		</div>
	{/if}
	{/if}
{/if}
