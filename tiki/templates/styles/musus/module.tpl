{* $Header: /cvsroot/tikiwiki/tiki/templates/styles/musus/module.tpl,v 1.4 2004-01-18 00:45:14 musus Exp $ *}
{* Module layout with controls *}

<div class="module">
<div class="module-title">
{* Draw module controls for logged user only *}
{if $user and $user_assigned_modules == 'y' and $no_module_controls ne 'y' and $feature_modulecontrols eq 'y'}
<table>
  <tr>
    <td width="11">
      <a title="{tr}Move module up{/tr}" href="{$current_location|escape}{$mpchar}mc_up={$module_name|escape}"><img src="img/icons2/up.gif" /></a>
    </td>
    <td width="11">
      <a title="{tr}Move module down{/tr}" href="{$current_location|escape}{$mpchar}mc_down={$module_name|escape}"><img src="img/icons2/down.gif" /></a>
    </td>
    <td>{$module_title}</td>
    <td width="11">
      <a title="{tr}Move module to opposite side{/tr}" href="{$current_location|escape}{$mpchar}mc_move={$module_name|escape}"><img src="img/icons2/admin_move.gif" /></a>
    </td>
    <td width="16">
      &nbsp;&nbsp;<a title="{tr}Unassign this module{/tr}" href="{$current_location|escape}{$mpchar}mc_unassign={$module_name|escape}" 
onclick="return confirmTheLink(this,'{tr}Are you sure you want to unassign this module?{/tr}')" 
title="{tr}Click here to unassign this module{/tr}"><img alt="{tr}Remove{/tr}" src="img/icons2/delete2.gif" /></a>&nbsp;&nbsp;
    </td>
  </tr>
</table>
{else}
  {$module_title}
{/if}

</div> {* end module-title *}
<div class="module-content">
    {$module_content}
</div> {* end module-content *}
</div> {* end module *}
