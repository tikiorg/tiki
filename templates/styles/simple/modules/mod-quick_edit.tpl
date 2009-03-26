{* $Id$ *}
{if $tiki_p_edit eq 'y'}
      {tikimodule title="{tr}Quick edit a Wiki page{/tr}" name="quick_edit"}
      <form method="get" action="tiki-editpage.php">
        <input type="text" size="15" name="page" />
        <button type="submit" name="quickedit">{tr}Edit{/tr}</button>
      </form>
      {/tikimodule}	
{/if}
