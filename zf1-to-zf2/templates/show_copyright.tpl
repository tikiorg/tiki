{if $prefs.wiki_feature_copyrights eq 'y' and $prefs.wikiLicensePage}
	{if $prefs.wikiLicensePage == $page}
		{if $tiki_p_edit_copyrights eq 'y'}
			<em>{tr}To edit the copyright notices{/tr} <a href="copyrights.php?page={$copyrightpage}">{tr}Click Here{/tr}</a>.</em>
		{/if}
	{else}
		<em>{tr}The content on this page is licensed under the terms of the{/tr} <a href="{$prefs.wikiLicensePage|sefurl:wiki:with_next}copyrightpage={$page|escape:"url"}">{$prefs.wikiLicensePage}</a>.</em>
	{/if}
{/if}
