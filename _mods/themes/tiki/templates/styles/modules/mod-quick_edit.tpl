{* $Header: /cvsroot/tikiwiki/_mods/themes/tiki/templates/styles/modules/mod-quick_edit.tpl,v 1.1 2007-11-30 20:06:27 mose Exp $ *}

{if $tiki_p_edit eq 'y'}
  {tikimodule title="{tr}Quick edit a Wiki page{/tr}" name="quick_edit"}
    <form method="get" action="tiki-editpage.php">
      <input type="text" size="15" name="page" />
      <input type="image" src="pics/icons/page_edit.png" name="quickedit" value="{tr}Edit{/tr}" width='16' height='16' />
    </form>
  {/tikimodule}
{/if}
