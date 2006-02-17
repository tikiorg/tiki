{* $Header: /cvsroot/tikiwiki/tiki/templates/styles/simple/modules/mod-switch_lang.tpl,v 1.2 2006-02-17 15:10:48 sylvieg Exp $ *}

{tikimodule title="{tr}Language: {/tr}`$language`" name="switch_lang" flip=$module_params.flip decorations=$module_params.decorations}
	{if $change_language ne 'n' or $user eq ''}
	<form method="get" action="tiki-switch_lang.php">
		<select name="language" size="1" onchange="this.form.submit();">
		{section name=ix loop=$languages}
			{if count($available_languages) == 0 || in_array($languages[ix].value, $available_languages)}
			<option value="{$languages[ix].value|escape}"{if $language eq $languages[ix].value} selected="selected"{/if}>
				{$languages[ix].name}
			</option>
			{/if}
		{/section}
		</select>
		<noscript>
			<button type="submit">{tr}switch{/tr}</button>
		</noscript>
	</form>
	{else}
		{tr}Permission denied{/tr}
	{/if}
{/tikimodule}
