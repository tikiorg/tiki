{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-eph.tpl,v 1.5 2003-11-23 03:15:07 zaufi Exp $ *}

{tikimodule title="<a class=\"cboxtlink\" href=\"tiki-eph.php\">{tr}Ephemerides{/tr}</a>" name="eph"}
{if $modephdata}
  <table>
  {if $modephdata.filesize}
    <tr>
      <td text-align="center" class="module"><img alt="image" src="tiki-view_eph.php?ephId={$modephdata.ephId}" /></td>
    </tr>
  {/if}
  <tr>
    <td class="module">{$modephdata.textdata}</td>
  </tr>
  </table>
{/if}
{/tikimodule}
