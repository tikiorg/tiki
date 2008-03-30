{* $Id$ *}

{if $prefs.feature_comm eq 'y'}
{if !isset($tpl_module_title)}{assign var=tpl_module_title value="{tr}Received objects{/tr}"}{/if}
{tikimodule title=$tpl_module_title name="comm_received_objects" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox}
  <table  border="0" cellpadding="0" cellspacing="0">
    <tr><td valign="top" class="module">{tr}Pages:{/tr}</td><td class="module">&nbsp;{$modReceivedPages}</td></tr>
  </table>
{/tikimodule}
{/if}
