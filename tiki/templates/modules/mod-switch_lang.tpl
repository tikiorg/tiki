{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-switch_lang.tpl,v 1.3 2003-11-23 03:53:04 zaufi Exp $ *}

{tikimodule title="{tr}Language: {/tr}{$language}" name="switch_lang"}
<form method="get" action="tiki-switch_lang.php" target="_self">
       <select name="language" size="1" onchange="this.form.submit();">
        {section name=ix loop=$languages}
        <option value="{$languages[ix].value|escape}"
          {if $language eq $languages[ix].value}selected="selected"{/if}>
          {$languages[ix].name}
        </option>
        {/section}
        </select>
</form>
{/tikimodule}
