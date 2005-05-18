{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-comm_received_objects.tpl,v 1.7 2005-05-18 11:03:28 mose Exp $ *}

{if $feature_comm eq 'y'}
{tikimodule title="{tr}Received objects{/tr}" name="comm_received_objects" flip=$module_params.flip decorations=$module_params.decorations}
  <table  border="0" cellpadding="0" cellspacing="0">
    <tr><td valign="top" class="module">{tr}Pages:{/tr}</td><td class="module">&nbsp;{$modReceivedPages}</td></tr>
  </table>
{/tikimodule}
{/if}
