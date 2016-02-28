{* $Id$ *}
{jq}
	function updateList( active )
	{
		if( ! active )
		{
			var optionList = document.getElementById( 'available_languages_select' ).options;
			for( i in optionList )
				optionList[i].selected = false;
		}
	}
{/jq}
<form role="form" class="form-horizontal" action="tiki-admin.php?page=i18n" method="post">
	<input type="hidden" name="ticket" value="{$ticket|escape}">
	<input type="hidden" name="i18nsetup" />
	<div class="t_navbar margin-bottom-md clearfix">
		{if $tiki_p_edit_languages eq 'y'}
			<a class="btn btn-link tips" href="{service controller=language action=manage_custom_php_translations}" title="{tr}Customized String Translation{/tr}:{tr}Manage local translations in a custom.php file{/tr}">
				{icon name="file-code-o"} {tr}Custom Translations{/tr}
			</a>
			<a class="btn btn-link tips" href="{service controller=language action=upload language={$edit_language}}" title="{tr}Upload Translations{/tr}:{tr}Upload a file with translations for the selected language.{/tr}">
				{icon name="upload"} {tr}Upload Translations{/tr}
			</a>
			{if $prefs.lang_use_db eq "y"}
				{button _type="link" _class="tips" href="tiki-edit_languages.php" _icon_name="edit" _text="{tr}Edit languages{/tr}" _title="{tr}Edit languages{/tr}:{tr}Edit, export and import languages{/tr}"}
			{/if}
			{if $prefs.freetags_multilingual eq 'y'}
				{button _type="link" _class="tips" href="tiki-freetag_translate.php" _icon_name="tags" _text="{tr}Translate Tags{/tr}" _title=":{tr}Translate tags{/tr}"}
			{/if}
			<div class="pull-right">
				<input type="submit" class="btn btn-primary btn-sm tips" title=":{tr}Apply Changes{/tr}" value="{tr}Apply{/tr}">
			</div>
		{/if}
	</div>
	{preference name=language}
	{preference name=wiki_page_regex}
	{preference name=default_mail_charset}
	<div class="adminoptionbox">
		{preference name=feature_multilingual visible="always"}
		<div class="adminoptionboxchild" id="feature_multilingual_childcontainer">
			{preference name=feature_detect_language}
			{preference name=feature_best_language}
			{preference name=change_language}
			{preference name=restrict_language}
			<div class="adminoptionboxchild" id="restrict_language_childcontainer">
				{preference name=available_languages}
				{preference name=language_inclusion_threshold}
			</div>
			{preference name=show_available_translations}
			{preference name=feature_sync_language}
			{preference name=search_default_interface_language}
			{preference name=feature_translation}
			{preference name=feature_urgent_translation}
			{preference name=feature_translation_incomplete_notice}
			{preference name=feature_multilingual_one_page}
			{preference name=quantify_changes}
			{preference name=wiki_edit_minor}
			{preference name=feature_user_watches_translations}
			{preference name=feature_multilingual_structures}
			{preference name=freetags_multilingual}
			{preference name=category_i18n_sync}
			<div class="adminoptionboxchild category_i18n_sync_childcontainer blacklist whitelist required">
				{preference name=category_i18n_synced}
			</div>
			{preference name=wiki_dynvar_multilingual}
			{preference name=wikiplugin_tr}
			{preference name=wikiplugin_lang}
			{preference name=wikiplugin_translated}
		</div>
		{preference name=lang_use_db}
		{preference name=record_untranslated}
		{preference name=feature_machine_translation}
		<div class="adminoptionboxchild" id="feature_machine_translation_childcontainer">
			{preference name=lang_machine_translate_implementation}
			<div class="adminoptionboxchild lang_machine_translate_implementation_childcontainer google">
				{preference name=lang_google_api_key}
			</div>
			<div class="adminoptionboxchild lang_machine_translate_implementation_childcontainer bing">
				{preference name=lang_bing_api_client_id}
				{preference name=lang_bing_api_client_secret}
			</div>
			{preference name=lang_machine_translate_wiki}
		</div>
		{preference name=feature_lang_nonswitchingpages}
		<div class="adminoptionboxchild" id="feature_lang_nonswitchingpages_childcontainer">
			{preference name=feature_lang_nonswitchingpages_names}
		</div>
	</div>
	<div class="t_navbar margin-bottom-md text-center">
		<input type="submit" class="btn btn-primary btn-sm tips" title=":{tr}Apply Changes{/tr}" value="{tr}Apply{/tr}">
	</div>
</form>
