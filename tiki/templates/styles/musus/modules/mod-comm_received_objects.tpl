{* $Header: /cvsroot/tikiwiki/tiki/templates/styles/musus/modules/mod-comm_received_objects.tpl,v 1.2 2004-01-09 15:29:32 musus Exp $ *}

{if $feature_comm eq 'y'}
{tikimodule title="{tr}Received objects{/tr}" name="comm_received_objects"}
  <table>
    <tr class="module"><td valign="top">{tr}Pages:{/tr}</td><td>&nbsp;{$modReceivedPages}</td></tr>
  </table>
{/tikimodule}
{/if}