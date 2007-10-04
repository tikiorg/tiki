{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-comm_received_objects.tpl,v 1.9 2007-10-04 22:17:46 nyloth Exp $ *}

{if $prefs.feature_comm eq 'y'}
{if !isset($tpl_module_title)}{assign var=tpl_module_title value="{tr}Received objects{/tr}"}{/if}
{tikimodule title=$tpl_module_title name="comm_received_objects" flip=$module_params.flip decorations=$module_params.decorations}
  <table  border="0" cellpadding="0" cellspacing="0">
    <tr><td valign="top" class="module">{tr}Pages:{/tr}</td><td class="module">&nbsp;{$modReceivedPages}</td></tr>
  </table>
{/tikimodule}
{/if}
