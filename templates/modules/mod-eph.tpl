{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-eph.tpl,v 1.6 2005-03-12 16:50:59 mose Exp $ *}

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
