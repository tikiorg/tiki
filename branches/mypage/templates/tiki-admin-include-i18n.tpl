<div class="rbox" name="tip">
	<div class="rbox-title" name="tip">{tr}Tip{/tr}</div>  
	<div class="rbox-data" name="tip"><a class="rbox-link" href="http://doc.tikiwiki.org/Internationalization">{tr}Internationalization{/tr}</a></div>
</div>
<br />

<div class="cbox">
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
		
		
			<td class="form"><label for="feature_multilingual_structures">{tr}Multilingual structures{/tr}:</label></td>
			<td><input type="checkbox" name="feature_multilingual_structures" id="feature_multilingual_structures"
			{if $prefs.feature_multilingual_structures eq 'y'}checked="checked"{/if}/></td>
        </tr><tr>
		
		
			<td class="form"><label for="feature_best_language">{tr}Show pages in user's preferred language{/tr}:</label></td>
			<td><input type="checkbox" name="feature_best_language" id="feature_best_language"
			{if $prefs.feature_best_language eq 'y'}checked="checked"{/if}/></td>
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

