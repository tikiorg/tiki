{* $Header: /cvsroot/tikiwiki/tiki/templates/styles/musus/modules/mod-comm_received_objects.tpl,v 1.1 2004-01-07 04:31:24 musus Exp $ *}

{if $feature_comm eq 'y'}
{tikimodule title="{tr}Received objects{/tr}" name="comm_received_objects"}
  <table  border="0" cellpadding="0" cellspacing="0">
    <tr><td valign="top" class="module">{tr}Pages:{/tr}</td><td class="module">&nbsp;{$modReceivedPages}</td></tr>
  </table>
{/tikimodule}
{/if}
