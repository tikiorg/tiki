{* $Header: /cvsroot/tikiwiki/tiki/templates/module.tpl,v 1.7 2004-05-31 14:36:31 sylvieg Exp $ *}
{* Module layout with controls *}

<div class="box {$module_name}"><div class="box-title">
{* Draw module controls for logged user only *}
{if $user and $user_assigned_modules == 'y' and $no_module_controls ne 'y' and $feature_modulecontrols eq 'y'}
<table>
  <tr>
    <td width="11">
      <a href="{$current_location|escape}{$mpchar}mc_up={$module_name|escape}"><img src="img/icons2/up.gif" border="0" /></a>
    </td>
    <td width="11">
      <a href="{$current_location|escape}{$mpchar}mc_down={$module_name|escape}"><img src="img/icons2/down.gif" border="0" /></a>
    </td>
    <td>{$module_title}</td>
    <td width="11">
      <a href="{$current_location|escape}{$mpchar}mc_move={$module_name|escape}"><img src="img/icons2/admin_move.gif" border="0" /></a>
    </td>
    <td width="16">
      <a href="{$current_location|escape}{$mpchar}mc_unassign={$module_name|escape}" onclick="return confirmTheLink(this,'{tr}Are you sure you want to remove this module?{/tr}')"><img border="0" alt="{tr}Remove{/tr}" src="img/icons2/delete.gif" /></a>
    </td>
  </tr>
</table>
{else}
  {$module_title}
{/if}

</div><div class="box-data">
    {$module_content}
</div></div>
