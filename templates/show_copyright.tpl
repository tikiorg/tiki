{if $prefs.feature_copyright eq 'y'}
	{if $prefs.wikiLicensePage}
		{capture name='copyright_content'}
			<span class="copyright">{tr}This content is licensed under the terms of the{/tr} <a href="{$prefs.wikiLicensePage|sefurl:wiki:with_next}copyrightpage={$page|escape:"url"}">{$prefs.wikiLicensePage}</a>.</span>
		{/capture}
		{if $prefs.wiki_feature_copyrights eq 'y' and $copyright_context eq 'wiki'}
			{$smarty.capture.copyright_content}
			{if $prefs.wikiLicensePage == $page}
				{if $tiki_p_edit_copyrights eq 'y'}
					<span class="help-block">{tr}To edit the copyright notices{/tr} <a href="copyrights.php?page={$copyrightpage}">{tr}Click Here{/tr}</a>.</span>
				{/if}
			{/if}
		{elseif $prefs.blog_feature_copyrights eq 'y' and $copyright_context eq 'blogpost'}
			{$smarty.capture.copyright_content}
		{elseif $prefs.article_feature_copyrights eq 'y' and $copyright_context eq 'article'}
			{$smarty.capture.copyright_content}
		{elseif $prefs.faq_feature_copyrights eq 'y' and $copyright_context eq 'faq' }
			{$smarty.capture.copyright_content}
		{/if}
	{else}
		{remarksbox type="warning" title="{tr}Warning{/tr}" close="n"}
		 	{tr}To edit the copyright notices{/tr} <a href="copyrights.php?page={$copyrightpage}">{tr}Click Here{/tr}</a>
		{/remarksbox}
	{/if}
{/if}