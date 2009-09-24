{remarksbox type="tip" title="{tr}Tip{/tr}"}<a class="rbox-link" href="http://doc.tikiwiki.org/Internationalization">{tr}Internationalization{/tr}</a>{/remarksbox}

  <script type="text/javascript">
	<!--//--><![CDATA[//><!--
  {literal}
	function updateList( active )
	{
  		if( active )
		{
			show('available_languages');
		}
		else
		{
			hide('available_languages');
			
			var optionList = document.getElementById( 'available_languages_select' ).options;
			for( i in optionList )
				optionList[i].selected = false;
		}
	}
  //--><!]]>
  {/literal}
  </script>

<form action="tiki-admin.php?page=i18n" method="post">
	<div class="heading input_submit_container" style="text-align: right">
		<input type="submit" value="{tr}Change preferences{/tr}" />
	</div>
<input type="hidden" name="i18nsetup" />
{tabset name="admin_i18n"}
	{tab name="{tr}Internationalization{/tr}"}

{preference name=language}
{preference name=wiki_page_regex}

<div class="adminoptionbox">
	{preference name=feature_multilingual}
<div class="adminoptionboxchild" id="usemultilingual" style="display:{if $prefs.feature_multilingual eq 'y'}block{else}none{/if};">	

	{preference name=feature_detect_language}
	{preference name=feature_best_language}
	{preference name=change_language}
	{preference name=restrict_language}
	
	<div class="adminoptionboxchild" id="available_languages" {if count($prefs.available_languages) == 0}style="display:none;"{else}style="display:block;"{/if}>
		{preference name=available_languages}
		{preference name=language_inclusion_threshold}
	</div>

	{preference name=show_available_translations}
	{preference name=feature_sync_language}
	{preference name=feature_translation}
	{preference name=feature_urgent_translation}
	{preference name=feature_multilingual_one_page}
	{preference name=quantify_changes}
	{preference name=feature_multilingual_structures}
</div>

{preference name=lang_use_db}
<div class="adminoptionlabel"><a class="button" href="tiki-edit_languages.php">{tr}Edit or export/import Languages{/tr}</a></div>

{preference name=record_untranslated}
	
{preference name=feature_machine_translation}

</div>
{/tab}

{tab name="{tr}Babelfish links{/tr}"}
{*------------------------------- Babelfish ----------------------------- *}
<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" name="feature_babelfish" id="feature_babelfish"
			{if $prefs.feature_babelfish eq 'y'}checked="checked"{/if}/></div>
	<div class="adminoptionlabel"><label for="feature_babelfish">Translation URLs</label></div>
</div>
<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" name="feature_babelfish_logo" id="feature_babelfish_logo"
			{if $prefs.feature_babelfish_logo eq 'y'}checked="checked"{/if}/></div>
	<div class="adminoptionlabel"><label for="feature_babelfish_logo">Translation logos</label></div>
</div>
{/tab}
{tab name="{tr}Customized String Translation{/tr}"}
{*----------------------------------- Custom translation --------------------*}
<div class="adminoptionbox">
	{if empty($custom_lang)}
		<select name="custom_lang" id="custom_lang_select">
			{section name=ix loop=$languages}
				<option value="{$languages[ix].value|escape}"
					{if (empty($custom_lang) && $languages[ix].value eq $prefs.site_language) || (!empty($custom_lang) && $languages[ix].value eq $custom_lang)} selected="selected"{/if}>
					{$languages[ix].name|escape}
				</option>
			{/section}
		</select>
		<input type="submit" name="custom" value="{tr}Edit{/tr}" />
	{else}
		{if !empty($custom_error)}
			{remarksbox type="error"}
				{if $custom_error eq 'param'}
					{tr}Incorrect param{/tr}
				{elseif $custom_error eq 'parse'}
					{tr}Syntax error{/tr}
				{else}
					{tr}Cannot open this file:{/tr} {$custom_file}
				{/if}
			{/remarksbox}
		{/if}
		<h2>
		{section name=ix loop=$languages}
			{if $languages[ix].value eq $custom_lang}{$languages[ix].name|escape}{/if}
		{/section}
		</h2>
		<input type="hidden" name="custom_lang" value="{$custom_lang|escape}" />
		<textarea rows="40" cols="80" name="custom_translation">{$custom_translation|escape}</textarea>
		<br />
		<input type="submit" name="custom_save" value="{tr}Save{/tr}" />
	{/if}
</div>
{/tab}
{/tabset}
	<div class="heading input_submit_container" style="text-align: center">
		<input type="submit" value="{tr}Change preferences{/tr}" />
	</div>
</form>
