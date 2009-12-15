{* $Id$ *}

{if !isset($tpl_module_title)}{assign var=tpl_module_title value="{tr}Tiki Logo{/tr}"}{/if}
{tikimodule error=$module_params.error title=$tpl_module_title name="logo" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle}
  <div align="center">
    <a href="{$prefs.tikiIndex}">
      <img src="img/tiki.jpg" align="center" alt="logo" width="150" height="100" />
    </a>
  </div>
{/tikimodule}
