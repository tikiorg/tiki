<!-- templates/tiki-preview.tpl start -->
<div class="wikipreview" {if $prefs.ajax_autosave eq "y"}style="display:none;" id="autosave_preview"><div{/if}>
	{if $prefs.ajax_autosave eq "y"}
		<div class="form-group pull-right">
			<div class="pull-right" style="display:inline;margin-left: 10px;margin-top: 5px">
				{self_link _icon_name="back" _ajax="n" _class="tips" _title=":{tr}Popup preview{/tr}" _onclick="ajax_preview( 'editwiki', autoSaveId );$('#autosave_preview').hide();return false;"}
				{/self_link}
				{self_link _icon_name="remove" _ajax="n" _class="tips" _title=":{tr}Close preview{/tr}" _onclick="$('#autosave_preview').hide();return false;"}
				{/self_link}
			</div>
			<div class="pull-right" style="display:inline">
				<select name="diff_style" id="preview_diff_style" class="form-control">
					<option value="" {if empty($diff_style)}selected="selected"{/if}>{tr}Preview{/tr}</option>
					<option value="htmldiff" {if isset($diff_style) && $diff_style == "htmldiff"}selected="selected"{/if}>{tr}HTML diff{/tr}</option>
					<option value="sidediff" {if isset($diff_style) && $diff_style == "sidediff"}selected="selected"{/if}>{tr}Side-by-side diff{/tr}</option>
				</select>
			</div>
			{jq}
				$("#preview_diff_style").change(function(){
					ajaxLoadingShow($("#autosave_preview .wikitext"));
					setCookie("preview_diff_style", $(this).val(), "preview", "session");
					$.get($.service("edit", "preview"), {
						editor_id: 'editwiki',
						autoSaveId: autoSaveId,
						inPage: 1,
						{{if isset($smarty.request.hdr)}hdr: {$smarty.request.hdr},{/if}}
						diff_style: $(this).val()
					}, function(data) {
						$("#autosave_preview .wikitext").html(data);
						ajaxLoadingHide();
					});
				});
			{/jq}
		</div>
	{/if}
	{if $prefs.feature_jquery_ui eq "y"}
		{jq}
			$('#autosave_preview').resizable({
				handles:{'s':'#autosave_preview_grippy'},
				alsoResize:'#autosave_preview>div',
				resize: function(event, ui) {
					setCookie("wiki", $('#autosave_preview').height(), "preview", "session");
				}
			}).height(getCookie("wiki", "preview", ""));
			$("#autosave_preview>div").height(getCookie("wiki", "preview", ""));
		{/jq}
	{/if}
	<h2>{tr}Preview{/tr}: {$page|escape}</h2>
	{if $prefs.feature_wiki_description eq 'y'}
		<small>{$description}</small>
	{/if}
	<div align="center" class="attention" style="font-weight:bold">
		{tr}Note: Remember that this is only a preview, and has not yet been saved!{/tr}
	</div>
	<div class="preview_contents">
		<div class="wikitext">
			{$parsed}
		</div>
		{if $has_footnote and isset($parsed_footnote)}
			<div class="wikitext">{$parsed_footnote}</div>
		{/if}
	</div>
	{if $prefs.ajax_autosave eq "y"}
		</div>
		<span id="autosave_preview_grippy" class="ui-resizable-handle ui-resizable-s"> </span>
	{/if}
</div>
<hr style="clear:both; height:0px;"/> {* Information below the wiki content
must not overlap the wiki content that could contain floated elements *}
<!-- templates/tiki-preview.tpl end -->
