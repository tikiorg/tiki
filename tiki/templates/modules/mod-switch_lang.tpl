{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-switch_lang.tpl,v 1.7 2007-02-18 11:21:17 mose Exp $ *}

{if !isset($tpl_module_title)}{assign var=tpl_module_title value="{tr}Language: {/tr} $language"}{/if}
{tikimodule title=$tpl_module_title name="switch_lang" flip=$module_params.flip decorations=$module_params.decorations}
{if $change_language ne 'n' or $user eq ''}
<form method="get" action="tiki-switch_lang.php" target="_self">
       <select name="language" size="1" onchange="this.form.submit();">
        {section name=ix loop=$languages}
	{if count($available_languages) == 0 || in_array($languages[ix].value, $available_languages)}
        <option value="{$languages[ix].value|escape}"
          {if $language eq $languages[ix].value}selected="selected"{/if}>
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
