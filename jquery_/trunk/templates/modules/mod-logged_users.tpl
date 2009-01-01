{* $Id$ *}

{if !isset($tpl_module_title)}{assign var=tpl_module_title value="{tr}Online users{/tr}"}{/if}
{tikimodule error=$module_params.error title=$tpl_module_title name="logged_users" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle}
  <span class="user-box-text">{tr}We have{/tr} {$logged_users} {tr}online users{/tr}</span>  
{/tikimodule}

