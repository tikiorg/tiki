{* based on /cvsroot/tikiwiki/tiki/templates/modules/mod-switch_lang2.tpl,v 1.10 2007/10/14 17:51:01 mose *}

{if !isset($tpl_module_title)}{assign var=tpl_module_title value="{tr}Language :{/tr} `$prefs.language`"}{/if}
{tikimodule title=$tpl_module_title name="switch_lang2" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox}
{section name=ix loop=$languages}
  <li>
    <a title="{$languages[ix].name|escape}" class="linkmodule" href="tiki-switch_lang.php?language={$languages[ix].value|escape}">
      {$languages[ix].display|escape}
    </a>
  </li>
{/section}
</ul>
{/tikimodule}
