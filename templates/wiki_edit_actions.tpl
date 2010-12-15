{* $Id$ *}
<input type="hidden" name="no_bl" value="y" />
{if $prefs.ajax_autosave eq "y"}
	{button _title="{tr}Preview your changes.{/tr}" _class="wikiaction tips previewBtn" _text="{tr}Preview{/tr}" _ajax="n"}
	{jq} $(".previewBtn").click(function(){
auto_save_data['editwiki'] = "";
auto_save('editwiki', autoSaveId);
if (!ajaxPreviewWindow) {
	$('#autosave_preview').slideDown('slow', function(){ ajax_preview( 'editwiki', autoSaveId, true );});
}
return false;
});{/jq}
{else}
	<input type="submit" class="wikiaction tips" title="{tr}Edit wiki page{/tr}|{tr}Preview your changes.{/tr}" name="preview" value="{tr}Preview{/tr}" onclick="needToConfirm=false;" />
{/if}
{if $page|lower neq 'sandbox' or $tiki_p_admin eq 'y'}
	{if ! $page_badchars_display or $prefs.wiki_badchar_prevent neq 'y'}
		{if $translation_mode eq 'y'}
			<input type="hidden" name="source_page" value="{$source_page|escape}"/>
			<input type="hidden" name="target_page" value="{$target_page|escape}"/>
			<input type="submit" class="wikiaction tips" title="{tr}Edit wiki page{/tr}|{tr}Save the page as a partial translation.{/tr}" name="partial_save" value="{tr}Partial Translation{/tr}" onclick="needToConfirm=false"/>
			<input type="submit" class="wikiaction tips" title="{tr}Edit wiki page{/tr}|{tr}Save the page as a completed translation.{/tr}" name="save" value="{tr}Complete Translation{/tr}" onclick="needToConfirm=false"/>
		{else}
			{if $tiki_p_minor eq 'y' and $page|lower ne 'sandbox' and $prefs.wiki_edit_minor neq 'n'}
			<input type="submit" class="wikiaction tips" name="minor" title="{tr}Edit wiki page{/tr}|{if $prefs.wiki_watch_minor}{tr}Save the page, but do not count it as new content to be translated.{/tr}{else}{tr}Save the page, but do not send notifications and do not count it as new content to be translated.{/tr}{/if}" value="{tr}Save Minor Edit{/tr}" onclick="needToConfirm=false;" />
			{/if}
			<input type="submit" class="wikiaction tips" title="{tr}Edit wiki page{/tr}|{tr}Save the page.{/tr}" name="save" value="{tr}Save{/tr}" onclick="needToConfirm=false;" />
		{/if}
	{/if}
	{if $page|lower ne 'sandbox'}
	<input type="submit" class="wikiaction tips" title="{tr}Edit wiki page{/tr}|{tr}Cancel the edit, you will lose your changes.{/tr}" name="cancel_edit" value="{tr}Cancel Edit{/tr}" onclick="needToConfirm=false;" />
	{/if}
{/if}
