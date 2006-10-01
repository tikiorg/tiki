{* $Header: /cvsroot/tikiwiki/tiki/templates/styles/tiki/modules/mod-quick_edit.tpl,v 1.4 2006-10-01 14:18:28 ohertel Exp $ *}

{if $tiki_p_edit eq 'y'}
  {tikimodule title="{tr}Quick edit a Wiki page{/tr}" name="quick_edit"}
    <form method="get" action="tiki-editpage.php">
      <input type="text" size="15" name="page" />
      <input type="image" src="pics/icons/page_edit.png" name="quickedit" value="{tr}edit{/tr}" width='16' height='16' />
    </form>
  {/tikimodule}
{/if}
