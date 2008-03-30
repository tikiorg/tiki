{* $Id$ *}

{tikimodule title="{tr}Language: {/tr}`$prefs.language`" name="switch_lang" flip=$module_params.flip decorations=$module_params.decorations}
	{if $prefs.change_language ne 'n' or $user eq ''}
	<form method="get" action="tiki-switch_lang.php">
		<select name="language" size="1" onchange="this.form.submit();">
		{section name=ix loop=$languages}
			{if count($prefs.available_languages) == 0 || in_array($languages[ix].value, $prefs.available_languages)}
			<option value="{$languages[ix].value|escape}"{if $prefs.language eq $languages[ix].value} selected="selected"{/if}>
				{$languages[ix].name}
			</option>
			{/if}
		{/section}
		</select>
		<noscript>
			<button type="submit">{tr}Switch{/tr}</button>
		</noscript>
	</form>
	{else}
		{tr}Permission denied{/tr}
	{/if}
{/tikimodule}
