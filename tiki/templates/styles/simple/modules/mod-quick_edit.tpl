{* $Header: /cvsroot/tikiwiki/tiki/templates/styles/simple/modules/mod-quick_edit.tpl,v 1.3 2003-11-23 19:44:47 gmuslera Exp $ *}
{if $tiki_p_edit eq 'y'}
      {tikimodule title="{tr}Quick edit a Wiki page{/tr}" name="quick_edit"}
      <form method="get" action="tiki-editpage.php">
        <input type="text" size="15" name="page" />
        <input type="submit" name="quickedit" value="{tr}edit{/tr}" />
      </form>
      {/tikimodule}	
{/if}
