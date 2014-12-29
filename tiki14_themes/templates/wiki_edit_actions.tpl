{* $Id$ *}
<div class="actions">
	<input type="hidden" name="no_bl" value="y">
	<input type="submit" class="wikiaction btn btn-default" title="{tr}Preview your changes.{/tr}" name="preview" value="{tr}Preview{/tr}" onclick="needToConfirm=false;">
	{if $prefs.feature_wikilingo eq "y"}
		{if $wysiwyg eq 'y'}
			{jq}
				var wikiParserChoice = $('#wiki-parser-choice'),
					$doc = $(document);

				$('input[name=preview]').click(function(){
					$doc.trigger('previewWikiLingo', [true, $('#editwiki-ui').html(), $('#editpageform'), $('#autosave_preview').slideDown('slow')]);
					return false;
				});
				$('input.btn-primary').click(function() {
					$doc.trigger('saveWikiLingo', [true, $('#editwiki-ui').html(), $('#editpageform')]);
					return false;
				});
			{/jq}
		{else}
			{jq}
				var wikiParserChoice = $('#wiki-parser-choice'),
					$doc = $(document);

				{*the call to trigger sync is for ensuring we get the most up to date value form the editor, which could be using codemirror*}
				$('input[name=preview]').click(function(){
					if (wikiParserChoice.val() == 'wikiLingo') {
						$doc.trigger('previewWikiLingo', [false, $('#editwiki').trigger('sync').val(), $('#editpageform'), $('#autosave_preview').slideDown('slow')]);
						return false;
					}
				});
				$('input.btn-primary').click(function() {
					if (wikiParserChoice.val() == 'wikiLingo') {
						$doc.trigger('saveWikiLingo', [false, $('#editwiki').trigger('sync').val(), $('#editpageform')]);
						return false;
					}
				});
			{/jq}
		{/if}
	{else}
		{if $prefs.ajax_autosave eq "y"}
			{jq}
				$("input[name=preview]").click(function(){
					auto_save('editwiki');
					if (!ajaxPreviewWindow) {
						$('#autosave_preview').slideDown('slow', function(){ ajax_preview( 'editwiki', autoSaveId, true );});
					}
					return false;
				});
			{/jq}
		{/if}
	{/if}
	{if $page|lower neq 'sandbox' or $tiki_p_admin eq 'y'}
		{if ! isset($page_badchars_display) or $prefs.wiki_badchar_prevent neq 'y'}
			{if $translation_mode eq 'y'}
				<input type="hidden" name="source_page" value="{$source_page|escape}">
				<input type="hidden" name="target_page" value="{$target_page|escape}">
				<input type="submit" class="wikiaction tips btn btn-default" title="{tr}Edit wiki page{/tr}|{tr}Save the page as a partial translation.{/tr}" name="partial_save" value="{tr}Partial Translation{/tr}" onclick="needToConfirm=false">
				<input type="submit" class="wikiaction tips btn btn-default" title="{tr}Edit wiki page{/tr}|{tr}Save the page as a completed translation.{/tr}" name="save" value="{tr}Complete Translation{/tr}" onclick="needToConfirm=false">
			{else}
				{if $tiki_p_minor eq 'y' and $page|lower ne 'sandbox' and $prefs.wiki_edit_minor neq 'n'}
					<input type="submit" class="wikiaction tips btn btn-default" name="minor" title="{tr}Edit wiki page{/tr}|{if $prefs.wiki_watch_minor}{tr}Save the page, but do not count it as new content to be translated.{/tr}{else}{tr}Save the page, but do not send notifications and do not count it as new content to be translated.{/tr}{/if}" value="{tr}Save Minor Edit{/tr}" onclick="needToConfirm=false;">
				{/if}
				<input type="submit" class="wikiaction btn btn-primary" title="{tr}Save the page.{/tr}" name="save" value="{tr}Save{/tr}" onclick="needToConfirm=false;">
			{/if}
		{/if}
		{if $page|lower ne 'sandbox'}
			<input type="submit" class="wikiaction btn btn-default" title="{tr}Cancel the edit, you will lose your changes.{/tr}" name="cancel_edit" value="{tr}Cancel Edit{/tr}" onclick="needToConfirm=false;">
		{/if}
	{/if}
</div>
