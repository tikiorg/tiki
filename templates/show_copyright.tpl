{if $prefs.feature_copyright eq 'y'}
	{if $prefs.wikiLicensePage}
		{capture name='copyright_content'}
			<div class="copyright">{tr}This content is licensed under the terms of the{/tr} <a href="{$prefs.wikiLicensePage|sefurl:wiki:with_next}copyrightpage={$page|escape:"url"}">{$prefs.wikiLicensePage}</a>.</div>
		{/capture}
		{if $prefs.wiki_feature_copyrights eq 'y' and $copyright_context eq 'wiki'}
			{if $prefs.wikiLicensePage == $page and $tiki_p_edit_copyrights eq 'y'}
				<div class="help-block">{tr}To edit the copyright notices{/tr} <a href="copyrights.php?page={$copyrightpage}">{tr}Click Here{/tr}</a>.</div>
			{else}
				{$smarty.capture.copyright_content}
			{/if}
		{elseif $prefs.blog_feature_copyrights eq 'y' and $copyright_context eq 'blogpost'}
			{$smarty.capture.copyright_content}
		{elseif $prefs.article_feature_copyrights eq 'y' and $copyright_context eq 'article'}
			{$smarty.capture.copyright_content}
		{elseif $prefs.faq_feature_copyrights eq 'y' and $copyright_context eq 'faq' }
			{$smarty.capture.copyright_content}
		{/if}
	{/if}
{/if}