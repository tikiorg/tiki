{* $Header: /cvsroot/tikiwiki/tiki/templates/styles/tiki/modules/mod-quick_edit.tpl,v 1.2 2004-09-08 19:53:08 mose Exp $ *}

{if $tiki_p_edit eq 'y'}
  {tikimodule title="{tr}Quick edit a Wiki page{/tr}" name="quick_edit"}
    <form method="get" action="tiki-editpage.php">
      <input type="text" size="15" name="page" />
      <input type="image" src="img/icons/edit.gif" name="quickedit" value="{tr}edit{/tr}" />
    </form>
  {/tikimodule}
{/if}
