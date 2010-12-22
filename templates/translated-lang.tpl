{* displays a cell with the languages of the translation set *}
	{if isset($trads) && (count($trads) > 1 || $trads[0].langName)}
		{if $td eq 'y'}<td style="vertical-align:top;text-align: left; width:42px;">{/if}
		{if isset($verbose) && $verbose eq 'y'}{tr}The main text of this page is available in the following languages{/tr}:{/if}
			{if isset($type) && $type == 'article'}
				<form action="tiki-read_article.php" method="get">
					<div>
						<select name="articleId" onchange="this.form.submit()">
							{section name=i loop=$trads}
							<option value="{$trads[i].objId|escape}">{$trads[i].langName|escape}</option>
							{/section}
						</select>
					</div>
				</form>
			{else}
				{if $tiki_p_edit neq 'y' and $translationsCount eq '1'}
				  <span title="{tr}No translations available{/tr}">{tr}{$trads[0].langName|escape}{/tr}</span>
				{else}
			 {* get method to have the param in the url *}
				{jq notonready=true}
				{{if $beingStaged == 'y'}
					var page_to_translate = '{$approvedPageName|escape:"quotes"}';
				{else}
					var page_to_translate = '{$page|escape:"quotes"}';
				{/if}}
				function quick_switch_language( element )
				{
					var index = element.selectedIndex;
					var option = element.options[index];

					if( option.value == "-" )
						return;
					else if( option.value == "_translate_" ) {
						element.form.action = "tiki-edit_translation.php";
						element.value = page_to_translate;					
						element.form.submit();
					} else if( option.value == "_all_" ) {
						element.form.action = "tiki-all_languages.php";
						element.value = page_to_translate;
						element.form.submit();
				 	} else if (option.text.charAt(option.text.length - 1) == "*") {
				 		element.form.machine_translate_to_lang.value = element.form.page.options[element.form.page.selectedIndex].value;
						element.value = page_to_translate;
						element.form.submit();				 		
					} else
						element.form.submit();
				}
				{/jq}
				<form action="tiki-index.php" method="get">
					<div>
						{if $prefs.feature_machine_translation eq 'y'}
						<input type="hidden" name="machine_translate_to_lang" value="" />
						{/if}
						<select name="page" onchange="quick_switch_language( this )"> 
							{if $prefs.feature_machine_translation eq 'y'}
							<option value="Human Translations" disabled="disabled" style="color:black;font-weight:bold">{tr}Human Translations{/tr}</option>
							{/if}
							{section name=i loop=$trads}
							<option value="{$trads[i].objName|escape}">{tr}{$trads[i].langName|escape}{/tr}</option>
							{/section}
							{if $prefs.feature_machine_translation eq 'y'}
							<option value="Machine Translations" disabled="disabled" style="color:black;font-weight:bold">{tr}Machine Translations{/tr}</option>
							{section name=i loop=$langsCandidatesForMachineTranslation}
							<option value="{$langsCandidatesForMachineTranslation[i].lang|escape}">{tr}{$langsCandidatesForMachineTranslation[i].langName|escape}{/tr} *</option>
							{/section}
							{/if}
							{if $prefs.feature_multilingual_one_page eq 'y' and $translationsCount gt 1}
							<option value="-">---</option>
							<option value="_all_"{if basename($smarty.server.PHP_SELF) eq 'tiki-all_languages.php'} selected="selected"{/if}>{tr}All{/tr}</option>
							{/if}
							{if $tiki_p_edit eq 'y'}
							<option value="-">---</option>
							<option value="_translate_">{tr}Translate{/tr}</option>
							{/if}
						</select>
						<input type="hidden" name="no_bl" value="y" /> 
					</div>
				</form>
			  {/if}
			{/if}

		{if $td eq 'y'}</td>{/if}
	{/if}
