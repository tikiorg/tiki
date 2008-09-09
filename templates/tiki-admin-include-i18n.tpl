{remarksbox type="tip" title="{tr}Tip{/tr}"}<a class="rbox-link" href="http://doc.tikiwiki.org/Internationalization">{tr}Internationalization{/tr}</a>{/remarksbox}

{if $prefs.feature_tabs eq 'y' and $prefs.lang_use_db != 'y'}
	{cycle name=tabs values="1,2,3" print=false advance=false reset=true}
	<div class="tabs">
		<span	id="tab{cycle name=tabs advance=false assign=tabi}{$tabi}" 
				class="tabmark tabinactive"><a 
				href="#general"
				onclick="javascript:tikitabs({cycle name=tabs},3); return false;">{tr}General Settings{/tr}</a></span>
		<span	id="tab{cycle name=tabs advance=false assign=tabi}{$tabi}" 
				class="tabmark tabinactive"><a 
				href="#custom"
				onclick="javascript:tikitabs({cycle name=tabs},3); return false;">{tr}Overwrite Strings{/tr}</a></span>
	</div>
{/if}

{cycle name=content values="1,2,3,4,5" print=false advance=false reset=true}
<div  id="content{cycle name=content assign=focustab}{$focustab}"{if $prefs.feature_tabs eq 'y'} class="tabcontent" style="display:{if $focustab eq $cookietab}block{else}none{/if};"{/if} class="cbox">
  <div class="cbox-title">{tr}I18n setup{/tr}</div>
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
  <div class="cbox-data">
      <form action="tiki-admin.php?page=i18n" method="post">
        <table class="admin"><tr>

		
			<td class="form"><label for="general-lang">{tr}Default Language{/tr}:</label></td>
			<td>
				<select name="language" id="general-lang">
					{section name=ix loop=$languages}
					<option value="{$languages[ix].value|escape}"
					{if $prefs.site_language eq $languages[ix].value}selected="selected"{/if}>{$languages[ix].name}</option>
					{/section}
				</select>
			</td>
		</tr><tr>		

		
			<td class="form"><label for="feature_multilingual">{tr}Multilingual{/tr}:</label></td>
			<td><input type="checkbox" name="feature_multilingual" id="feature_multilingual"
			{if $prefs.feature_multilingual eq 'y'}checked="checked"{/if}/></td>
        </tr><tr>

			<td class="form"><label for="feature_multilingual">{tr}Translation Assitant{/tr}:</label></td>
			<td><input type="checkbox" name="feature_translation" id="feature_translation"
			{if $prefs.feature_translation eq 'y'}checked="checked"{/if}/></td>
        </tr><tr>		
		
			<td class="form"><label for="feature_multilingual_structures">{tr}Multilingual structures{/tr}:</label></td>
			<td><input type="checkbox" name="feature_multilingual_structures" id="feature_multilingual_structures"
			{if $prefs.feature_multilingual_structures eq 'y'}checked="checked"{/if}/></td>
        </tr><tr>
		
		
			<td class="form"><label for="feature_best_language">{tr}Show pages in user's preferred language{/tr}:</label></td>
			<td><input type="checkbox" name="feature_best_language" id="feature_best_language"
			{if $prefs.feature_best_language eq 'y'}checked="checked"{/if}/></td>
        </tr><tr>
		
			<td class="form"><label for="feature_sync_language">{tr}Page language forces to display strings in the same language{/tr}:</label></td>
			<td><input type="checkbox" name="feature_sync_language" id="feature_sync_language"
			{if $prefs.feature_sync_language eq 'y'}checked="checked"{/if}/></td>
        </tr><tr>
		
			<td class="form"><label for="feature_detect_language">{tr}Detect browser language{/tr}:</label></td>
			<td><input type="checkbox" name="feature_detect_language" id="feature_detect_language"
			{if $prefs.feature_detect_language eq 'y'}checked="checked"{/if}/></td>
		</tr><tr>
		
		
			<td class="form"><label for="restrict_language">{tr}Restrict supported languages{/tr}:</label></td>
			<td><input type="checkbox" name="restrict_language" id="restrict_language"
				{if count($prefs.available_languages) > 0}checked="checked"{/if} 
				onclick="updateList( this.checked )"/>
				<div id="available_languages" {if count($prefs.available_languages) == 0}style="display:none;"{else}style="display:block;"{/if}>
					{tr}Available languages (Ctrl+Click to select multiple languages):{/tr}<br />
					<select name="available_languages[]" multiple="multiple" size="5" id="available_languages_select">
						{section name=ix loop=$languages}
						<option value="{$languages[ix].value|escape}"
							{if in_array($languages[ix].value, $prefs.available_languages)}selected="selected"{/if}>
							{$languages[ix].name}
						</option>
					{/section}
					</select>
				</div>
			</td>
		</tr><tr>

		<td class="form"><label for="quantify_changes">{tr}Quantify change size{/tr}:</label></td>
			<td><input type="checkbox" name="quantify_changes" id="quantify_changes"
			{if $prefs.quantify_changes eq 'y'}checked="checked"{/if}/></td>
        </tr><tr>
		
		
		<td class="form"><label for="feature_user_watches_translations">{tr}User Watches Translations{/tr}:</label></td>
			<td><input type="checkbox" name="feature_user_watches_translations" id="feature_user_watches_translations"
			{if $prefs.feature_user_watches_translations eq 'y'}checked="checked"{/if}/></td>
        </tr><tr>
		

		<td class="form"><label for="lang_use_db">{tr}Use database for translation{/tr}:</label></td>
			<td><input type="checkbox" name="lang_use_db" id="lang_use_db"
			{if $prefs.lang_use_db eq 'y'}checked="checked"{/if}/></td>
			{if $prefs.lang_use_db eq 'y'}
		</tr><tr>
		
		
			<td></td>
			<td><a class="link" href="tiki-edit_languages.php">{tr}Edit or ex/import Languages{/tr}</a></td>			
		</tr><tr>
		
		
			<td class="form"><label for="record_untranslated">{tr}Record untranslated{/tr}:</label></td>
			<td><input type="checkbox" name="record_untranslated" id="record_untranslated"
			{if $prefs.record_untranslated eq 'y'}checked="checked"{/if}/></td>
			{/if}
		</tr><tr>
		
		
			<td class="form"><label for="feature_babelfish">{tr}Show Babelfish Translation URLs{/tr}:</label></td>
			<td><input type="checkbox" name="feature_babelfish" id="feature_babelfish"
			{if $prefs.feature_babelfish eq 'y'}checked="checked"{/if}/></td>
		</tr><tr>
		
		
			<td class="form"><label for="feature_babelfish_logo">{tr}Show Babelfish Translation Logo{/tr}:</label></td>		
			<td><input type="checkbox" name="feature_babelfish_logo" id="feature_babelfish_logo"
			{if $prefs.feature_babelfish_logo eq 'y'}checked="checked"{/if}/></td>
		</tr><tr>


		
          <td colspan="2" class="button"><input type="submit" name="i18nsetup" value="{tr}Save{/tr}" /></td>
		  
        </tr></table>
      </form>
  </div>
</div>

{if $prefs.lang_use_db ne 'y'}
<form action="tiki-admin.php?page=i18n" method="post">
<div id="content{cycle name=content assign=focustab}{$focustab}"{if $prefs.feature_tabs eq 'y'} class="tabcontent" style="display:{if $focustab eq $cookietab}block{else}none{/if};"{/if}>

	<select name="custom_lang">
		{foreach from=$languages key=ix item=lg}
			<option value="{$ix}"{if $custom_lang eq $ix} selected="selected"{/if}>{$lg.name|escape}</option>
		{/foreach}
	</select>
	<input type="submit" name="select" value="{tr}Select{/tr}" />
{if !empty($custom_lang)}
	<input type="hidden" name="custom_lang" value="{$custom_lang}" />
	<table class="normal">
	<tr><th>{tr}English{/tr}(en)</th><th>{tr}{$languages[$custom_lang].name|escape}{/tr}</th><th></th></tr>
	<tr>
		<td><input type="text" name="en" /></td>
		<td><input type="text" name="custom" /></td>
		<td></td>
	</tr>
	{foreach from=$custom_strings key=en item=string}
		<tr>
			<td><input type="text" name="en" value={$en}/></td>
			<td><input type="text" name="en" /></td>
		</tr>
	{/foreach}
	<tr><td colspan="2" class="button"><input type="submit" name="save_custom" value="{tr}Save{/tr}" /></td></tr>
	</table>
{/if}
</div>
</form>
{/if}

