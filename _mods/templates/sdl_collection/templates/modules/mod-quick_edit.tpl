{* $Header: /cvsroot/tikiwiki/_mods/templates/sdl_collection/templates/modules/mod-quick_edit.tpl,v 1.1 2004-05-09 23:09:44 damosoft Exp $ *}

{if $tiki_p_edit eq 'y'}
  {tikimodule title="{tr}Edit a Wiki Page{/tr}" name="quick_edit"}
    <form method="get" action="tiki-editpage.php">
      <input type="text" size="15" name="page" />
      <input type="submit" name="quickedit" value="{tr}Edit{/tr}" />
    </form>
  {/tikimodule}
{/if}
