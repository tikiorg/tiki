{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-logged_users.tpl,v 1.10 2007-10-14 17:51:01 mose Exp $ *}

{if !isset($tpl_module_title)}{assign var=tpl_module_title value="{tr}Online users{/tr}"}{/if}
{tikimodule title=$tpl_module_title name="logged_users" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox}
  <span class="user-box-text">{tr}We have{/tr} {$logged_users} {tr}online users{/tr}</span>  
{/tikimodule}

