<!-- START of {$smarty.template} -->{* $Id$ *}

{tikimodule title="{tr}Language{/tr}: `$prefs.language`" name="switch_lang2"}
<ul class='floatlist'>
{section name=ix loop=$languages}
  <li>
    <a title="{$languages[ix].name|escape}" class="linkmodule" href="tiki-switch_lang.php?language={$languages[ix].value|escape}">
      {$languages[ix].display|escape}
    </a>
  </li>
{/section}
</ul>
{/tikimodule}
