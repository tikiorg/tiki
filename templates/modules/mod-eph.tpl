{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-eph.tpl,v 1.7 2005-05-18 11:03:29 mose Exp $ *}

{tikimodule title="<a class=\"cboxtlink\" href=\"tiki-eph.php\">{tr}Ephemerides{/tr}</a>" name="eph" flip=$module_params.flip decorations=$module_params.decorations}
{if $modephdata}
  <table>
  {if $modephdata.filesize}
    <tr>
      <td text-align="center" class="module"><img src="tiki-view_eph.php?ephId={$modephdata.ephId}" alt="{tr}image{/tr}" /></td>
    </tr>
  {/if}
  <tr>
    <td class="module">{$modephdata.textdata}</td>
  </tr>
  </table>
{/if}
{/tikimodule}
