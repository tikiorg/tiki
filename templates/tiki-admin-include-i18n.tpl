<div class="rbox" name="tip">
	<div class="rbox-title" name="tip">{tr}Tip{/tr}</div>  
	<div class="rbox-data" name="tip"><a class="rbox-link" href="http://doc.tikiwiki.org/Internationalization">{tr}Internationalization{/tr}</a></div>
</div>
<br />

<div class="cbox">
  <div class="cbox-title">{tr}I18n setup{/tr}</div>
  <div class="cbox-data">
      <form action="tiki-admin.php?page=i18n" method="post">
        <table class="admin"><tr>

		
			<td class="form"><label for="general-lang">{tr}Language{/tr}:</label></td>
			<td>
				<select name="language" id="general-lang">
					{section name=ix loop=$languages}
					<option value="{$languages[ix].value|escape}"
					{if $language eq $languages[ix].value}selected="selected"{/if}>{$languages[ix].name}</option>
					{/section}
				</select>
			</td>
		</tr><tr>		

		
			<td class="form"><label for="feature_multilingual">{tr}Multilingual{/tr}:</label></td>
			<td><input type="checkbox" name="feature_multilingual" id="feature_multilingual"
			{if $feature_multilingual eq 'y'}checked="checked"{/if}/></td>
        </tr><tr>
		
		
			<td class="form"><label for="feature_best_language">{tr}Best Language{/tr}:</label></td>
			<td><input type="checkbox" name="feature_best_language" id="feature_best_language"
			{if $feature_best_language eq 'y'}checked="checked"{/if}/></td>
        </tr><tr>
		
		
			<td class="form"><label for="feature_detect_language">{tr}Detect browser language{/tr}:</label></td>
			<td><input type="checkbox" name="feature_detect_language" id="feature_detect_language"
			{if $feature_detect_language eq 'y'}checked="checked"{/if}/></td>
		</tr><tr>
		
		
			<td class="form"><label for="change_language">{tr}Reg users can change language{/tr}:</label></td>
			<td>
			<table><tr>
			<td style="width: 20px"><input type="checkbox" name="change_language" id="change_language"
			{if $change_language eq 'y'}checked="checked"{/if}/></td>
			<td>
			
			<div id="select_available_languages" {if count($available_languages) > 0}style="display:none;"{else}style="display:block;"{/if}>
				<a class="link" href="javascript:show('available_languages');hide('select_available_languages');">{tr}Restrict available languages{/tr}</a>
			</div>
			
      <div id="available_languages" {if count($available_languages) == 0}style="display:none;"{else}style="display:block;"{/if}>
        {tr}Available languages:{/tr}<br />
        <select name="available_languages[]" multiple="multiple" size="5">
          {section name=ix loop=$languages}
            <option value="{$languages[ix].value|escape}"
              {if in_array($languages[ix].value, $available_languages)}selected="selected"{/if}>
              {$languages[ix].name}
            </option>
          {/section}
        </select>
      </div>
	  
    </td>
    </tr></table>
  </td>
		</tr><tr>

		
		<td class="form"><label for="feature_user_watches_translations">{tr}User Watches Translations{/tr}:</label></td>
			<td><input type="checkbox" name="feature_user_watches_translations" id="feature_user_watches_translations"
			{if $feature_user_watches_translations eq 'y'}checked="checked"{/if}/></td>
        </tr><tr>
		

		<td class="form"><label for="lang_use_db">{tr}Use database for translation{/tr}:</label></td>
			<td><input type="checkbox" name="lang_use_db" id="lang_use_db"
			{if $lang_use_db eq 'y'}checked="checked"{/if}/></td>
			{if $lang_use_db eq 'y'}
		</tr><tr>
		
		
			<td></td>
			<td><a class="link" href="tiki-edit_languages.php">{tr}Edit or ex/import Languages{/tr}</a></td>			
		</tr><tr>
		
		
			<td class="form"><label for="record_untranslated">{tr}Record untranslated{/tr}:</label></td>
			<td><input type="checkbox" name="record_untranslated" id="record_untranslated"
			{if $record_untranslated eq 'y'}checked="checked"{/if}/></td>
			{/if}
		</tr><tr>
		
		
			<td class="form"><label for="feature_babelfish">{tr}Show Babelfish Translation URLs{/tr}:</label></td>
			<td><input type="checkbox" name="feature_babelfish" id="feature_babelfish"
			{if $feature_babelfish eq 'y'}checked="checked"{/if}/></td>
		</tr><tr>
		
		
			<td class="form"><label for="feature_babelfish_logo">{tr}Show Babelfish Translation Logo{/tr}:</label></td>		
			<td><input type="checkbox" name="feature_babelfish_logo" id="feature_babelfish_logo"
			{if $feature_babelfish_logo eq 'y'}checked="checked"{/if}/></td>
		</tr><tr>


		
          <td colspan="2" class="button"><input type="submit" name="i18nsetup" value="{tr}Save{/tr}" /></td>
		  
        </tr></table>
      </form>
  </div>
</div>

