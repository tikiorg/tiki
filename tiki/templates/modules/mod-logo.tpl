{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-logo.tpl,v 1.8 2005-03-12 16:51:00 mose Exp $ *}

{tikimodule title="{tr}Tiki Logo{/tr}" name="logo" flip=$module_params.flip decorations=$module_params.decorations}
  <div align="center">
    <a href="{$tikiIndex}">
      <img src="img/tiki.jpg" align="center" alt="logo" width="150" height="100" border="0"/>
    </a>
  </div>
{/tikimodule}
