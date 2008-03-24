{* based on /cvsroot/tikiwiki/tiki/templates/modules/mod-comm_received_objects.tpl,v 1.10 2007/10/14 17:51:00 mose *}

{if $prefs.feature_comm eq 'y'}
{if !isset($tpl_module_title)}{assign var=tpl_module_title value="{tr}Received objects{/tr}"}{/if}
{tikimodule title=$tpl_module_title name="comm_received_objects" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox}
  <div>
    <span class="module">{tr}Pages:{/tr}</span><span class="module">&nbsp;{$modReceivedPages}</span>
  </div>
{/tikimodule}
{/if}
