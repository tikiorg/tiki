{remarksbox type="tip" title="{tr}Tip{/tr}"}<a class="rbox-link" href="http://doc.tikiwiki.org/Internationalization">{tr}Internationalization{/tr}</a>{/remarksbox}

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

			<td class="form"><label for="feature_translation">{tr}Translation Assitant{/tr}:</label></td>
			<td><input type="checkbox" name="feature_translation" id="feature_translation"
			{if $prefs.feature_translation eq 'y'}checked="checked"{/if}/></td>

	</tr><tr>
			<td class="form"><label for="feature_urgent_translation">{tr}Urgent Translation{/tr}:</label></td>
			<td><input type="checkbox" name="feature_urgent_translation" id="feature_translation"
			{if $prefs.feature_urgent_translation eq 'y'}checked="checked"{/if}/></td>
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

			<td class="form"><label for="show_available_translations">{tr}Display Available Translations{/tr}:</label></td>		
			<td><input type="checkbox" name="show_available_translations" id="show_available_translations"
			{if $prefs.show_available_translations eq 'y'}checked="checked"{/if}/></td>
		</tr><tr>

		
          <td colspan="2" class="button"><input type="submit" name="i18nsetup" value="{tr}Save{/tr}" /></td>
		  
        </tr></table>
      </form>
  </div>
</div>

