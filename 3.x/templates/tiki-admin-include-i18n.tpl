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
<div class="cbox">
<table class="admin"><tr><td>
<div style="padding:1em;" align="center"><input type="submit" value="{tr}Change preferences{/tr}" /></div>
<input type="hidden" name="i18nsetup" />
{if $prefs.feature_tabs eq 'y' and $prefs.feature_multilingual eq 'y' and $prefs.lang_use_db ne 'y'}
	{assign var=tabs value='y'}
{else}
	{assign var=tabs value='n'}
{/if}

{if $tabs eq 'y'}
	{tabs}{strip}{tr}Internationalization{/tr}|{tr}Babelfish links{/tr}|{tr}Customized String Translation{/tr}{/strip}{/tabs}
{/if}

<fieldset{if $tabs eq 'y'} id="content1" class="tabcontent" style="clear:both;display:block;"{/if}>
{if $tabs ne 'y'}
	<legend>{tr}Internationalization{/tr}</legend>
{/if}
<div class="adminoptionbox">
	<div class="adminoptionlabel"><label for="general-lang">{tr}Default language{/tr}:</label>
	<select name="language" id="general-lang">
					{section name=ix loop=$languages}
					<option value="{$languages[ix].value|escape}"
					{if $prefs.site_language eq $languages[ix].value}selected="selected"{/if}>{$languages[ix].name}</option>
					{/section}
	</select>
	</div>
</div>
<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" name="feature_multilingual" onclick="flip('usemultilingual');" id="feature_multilingual"
			{if $prefs.feature_multilingual eq 'y'}checked="checked"{/if}/></div>
	<div class="adminoptionlabel"><label for="feature_multilingual">{tr}Multilingual{/tr}</label>{if $prefs.feature_help eq 'y'} {help url="i18n+Admin"}{/if}
<div class="adminoptionboxchild" id="usemultilingual" style="display:{if $prefs.feature_multilingual eq 'y'}block{else}none{/if};">	

<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" name="feature_detect_language" id="feature_detect_language"
			{if $prefs.feature_detect_language eq 'y'}checked="checked"{/if}/></div>
	<div class="adminoptionlabel"><label for="feature_detect_language">{tr}Detect browser language{/tr}.</label></div>
</div>
<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" name="feature_best_language" id="feature_best_language"
			{if $prefs.feature_best_language eq 'y'}checked="checked"{/if}/></div>
	<div class="adminoptionlabel"><label for="feature_best_language">{tr}Show pages in user's preferred language{/tr}.</label>
	{if $prefs.feature_userPreferences ne 'y'}<br />{icon _id=information} <em>{tr}User preferences are disabled{/tr}. <a href="tiki-admin.php?page=features" title="{tr}Features{/tr}">{tr}Enable now{/tr}</a>. </em>{/if}
	</div>
</div>
<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" name="change_language" id="change_language"{if $prefs.change_language eq 'y'} checked="checked"{/if}></div>
	<div class="adminoptionlabel"><label for="change_language">{tr}Users can change site language{/tr}.</label></div>
</div>
<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" name="restrict_language" id="restrict_language"
				{if count($prefs.available_languages) > 0}checked="checked"{/if} 
				onclick="updateList( this.checked )"/></div>
	<div class="adminoptionlabel"><label for="restrict_language">{tr}Restrict supported languages{/tr}.</label>
	
	<div class="adminoptionboxchild" id="available_languages" {if count($prefs.available_languages) == 0}style="display:none;"{else}style="display:block;"{/if}>
					{tr}Available languages{/tr}:<br /> 
					<select name="available_languages[]" multiple="multiple" size="5" id="available_languages_select">
						{section name=ix loop=$languages}
						<option value="{$languages[ix].value|escape}"
							{if in_array($languages[ix].value, $prefs.available_languages)}selected="selected"{/if}>
							{$languages[ix].name}
						</option>
					{/section}
					</select>
					<br /><em>{tr}Use Ctrl+Click to select multiple languages{/tr}.</em>
	</div>
	
	</div>
</div>


<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" name="show_available_translations" id="show_available_translations"
			{if $prefs.show_available_translations eq 'y'}checked="checked" {/if} /></div>
	<div class="adminoptionlabel"><label for="show_available_translations">{tr}Display available translations{/tr}.</label></div>
</div>

<div class="adminoptionbox" id="langsync" style="display:{if $prefs.show_available_translations eq 'y'}block{else}none{/if};">
	<div class="adminoption"><input type="checkbox" name="feature_sync_language" id="feature_sync_language"
			{if $prefs.feature_sync_language eq 'y'}checked="checked"{/if}/></div>
	<div class="adminoptionlabel"><label for="feature_sync_language">{tr}Changing page language will also change the site language{/tr}</label></div>
</div>

<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" name="feature_translation" id="feature_translation"
			{if $prefs.feature_translation eq 'y'}checked="checked"{/if}/></div>
	<div class="adminoptionlabel"><label for="feature_translation">{tr}Translation assistant{/tr}</label>{if $prefs.feature_help eq 'y'} {help url="Translating+Tiki+content"}{/if}</div>
</div>
<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" name="feature_urgent_translation" id="feature_urgent_translation"
			{if $prefs.feature_urgent_translation eq 'y'}checked="checked"{/if}/></div>
	<div class="adminoptionlabel">
	<label for="feature_urgent_translation">{tr}Urgent translation notifications{/tr}</label>
	</div>
</div>
<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" name="quantify_changes" id="quantify_changes"
			{if $prefs.quantify_changes eq 'y'}checked="checked"{/if}/></div>
	<div class="adminoptionlabel"><label for="quantify_changes">{tr}Quantify change size{/tr}</label></div>
</div>
<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" name="feature_multilingual_structures" id="feature_multilingual_structures"
			{if $prefs.feature_multilingual_structures eq 'y'}checked="checked"{/if}/></div>
	<div class="adminoptionlabel"><label for="feature_multilingual_structures">{tr}Multilingual structures{/tr}</label>{if $prefs.feature_help eq 'y'} {help url="Structure"}{/if}
	{if $prefs.feature_wiki_structure ne 'y'}<br />{icon _id=information} <em>{tr}Structures are disabled{/tr}. <a href="tiki-admin.php?page=wiki" title="{tr}Wiki{/tr}">{tr}Enable now{/tr}</a>.</em>{/if}
	</div>
</div>

<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" name="lang_use_db" id="lang_use_db"
			{if $prefs.lang_use_db eq 'y'}checked="checked"{/if}/></div>
	<div class="adminoptionlabel"><label for="lang_use_db">{tr}Use database for translation{/tr}.</label>{if $prefs.feature_help eq 'y'} {help url="Translating+Tiki+interface"}{/if}
{if $prefs.lang_use_db eq 'y'}
<div class="adminoptionboxchild">
<div class="adminoptionbox">
	<div class="adminoptionlabel"><a class="button" href="tiki-edit_languages.php">{tr}Edit or ex/import Languages{/tr}</a></div>
</div>
<div class="adminoptionbox">
	<div class="adminoption"><input type="checkbox" name="record_untranslated" id="record_untranslated"
			{if $prefs.record_untranslated eq 'y'}checked="checked"{/if}/></div>
	<div class="adminoptionlabel"><label for="record_untranslated">{tr}Record untranslated{/tr}.</label></div>
</div>
</div>
{/if}	
	
	</div>
	<div class="adminoption"><input type="checkbox" name="feature_multilingual_one_page" id="feature_multilingual_one_page"{if $prefs.feature_multilingual_one_page eq 'y'}checked="checked"{/if}/></div>
	<div class="adminoptionlabel">
		<label for="feature_multilingual_one_page">{tr}Multilingual One Page feature{/tr}.
		{icon _id="error"} <em>{tr}Experimental{/tr}</em></label>{if $prefs.feature_help eq 'y'} {help url="Multilingual+One+Page"}{/if}</div>
</div>



</div>
	</div>
</div>

</fieldset>

{*------------------------------- Babelfish ----------------------------- *}
<fieldset{if $tabs eq 'y'} id="content2" class="tabcontent" style="clear:both;display:none;"{/if}>
{if $tabs ne 'y'}
	<legend>{tr}Babelfish links{/tr}</legend>
{/if}
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
</fieldset>

{*----------------------------------- Custom translation --------------------*}
<fieldset{if $tabs eq 'y'} id="content3" class="tabcontent" style="clear:both;display:none;"{/if}>
{if $tabs ne 'y'}
	<legend>{tr}Customized String Translation{/tr}</legend>
{/if}
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
</fieldset>

<div style="padding:1em;" align="center"><input type="submit" value="{tr}Change preferences{/tr}" /></div>
</td></tr></table>
</div>
</form>
