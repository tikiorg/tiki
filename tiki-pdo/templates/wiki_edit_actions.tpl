{if $page|lower neq 'sandbox' or $tiki_p_admin eq 'y'}
	<input type="submit" class="wikiaction" onmouseover="return overlib('{tr}Preview your changes.{/tr}');" onmouseout="nd();" name="preview" value="{tr}Preview{/tr}" onclick="needToConfirm=false;" />
	{if $translation_mode eq 'y'}
		<input type="submit" class="wikiaction" onmouseover="return overlib('{tr}Save the page as a partial translation.{/tr}');" onmouseout="nd();" name="partial_save" value="{tr}Partial Translation{/tr}" onclick="needToConfirm=false"/>
		<input type="submit" class="wikiaction" onmouseover="return overlib('{tr}Save the page as a completed translation.{/tr}');" onmouseout="nd();" name="save" value="{tr}Complete Translation{/tr}" onclick="needToConfirm=false"/>
	{else}
		{if $tiki_p_minor eq 'y' and $page|lower ne 'sandbox'}
		<input type="submit" class="wikiaction" name="minor" onmouseover="return overlib('{tr}Save the page, but do not send notifications and do not count it as new content to be translated.{/tr}');" onmouseout="nd();" value="{tr}Minor Edit{/tr}" onclick="needToConfirm=false;" />
		{/if}
		<input type="submit" class="wikiaction" onmouseover="return overlib('{tr}Save the page.{/tr}');" onmouseout="nd();" name="save" value="{tr}Save{/tr}" onclick="needToConfirm=false;" />

		{if $prefs.feature_ajax eq 'y' && $prefs.feature_wiki_save_draft eq 'y'}
		<input type="submit" class="wikiaction" onmouseover="return overlib('{tr}Save the page as a draft.{/tr}');" onmouseout="nd();" value="{tr}Save Draft{/tr}" onclick="save_draft()" />
		{/if}
	{/if}

	{if $page|lower ne 'sandbox'}
	<input type="submit" class="wikiaction" onmouseover="return overlib('{tr}Cancel the edit, you will lose your changes.{/tr}');" onmouseout="nd();" name="cancel_edit" value="{tr}Cancel Edit{/tr}" onclick="needToConfirm = false;" />
	{/if}
{/if}
