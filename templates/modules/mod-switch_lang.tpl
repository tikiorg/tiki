{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-switch_lang.tpl,v 1.8 2007-10-04 22:17:47 nyloth Exp $ *}

{if !isset($tpl_module_title)}{assign var=tpl_module_title value="{tr}Language: {/tr} `$prefs.language`"}{/if}
{tikimodule title=$tpl_module_title name="switch_lang" flip=$module_params.flip decorations=$module_params.decorations}
{if $prefs.change_language ne 'n' or $user eq ''}
<form method="get" action="tiki-switch_lang.php" target="_self">
       <select name="language" size="1" onchange="this.form.submit();">
        {section name=ix loop=$languages}
	{if count($prefs.available_languages) == 0 || in_array($languages[ix].value, $prefs.available_languages)}
        <option value="{$languages[ix].value|escape}"
          {if $prefs.language eq $languages[ix].value}selected="selected"{/if}>
          {$languages[ix].name}
        </option>
	{/if}
        {/section}
        </select>
</form>
{else}
{tr}Permission denied{/tr}
{/if}
{/tikimodule}
