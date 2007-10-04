{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-logo.tpl,v 1.11 2007-10-04 22:17:47 nyloth Exp $ *}

{if !isset($tpl_module_title)}{assign var=tpl_module_title value="{tr}Tiki Logo{/tr}"}{/if}
{tikimodule title=$tpl_module_title name="logo" flip=$module_params.flip decorations=$module_params.decorations}
  <div align="center">
    <a href="{$prefs.tikiIndex}">
      <img src="img/tiki.jpg" align="center" alt="logo" width="150" height="100" border="0"/>
    </a>
  </div>
{/tikimodule}
