{* $Header: /cvsroot/tikiwiki/tiki/templates/styles/musus/module.tpl,v 1.5 2004-02-01 07:43:28 musus Exp $ *}
{* Module layout with controls *}

<div class="box"><div class="box-title">
{* Draw module controls for logged user only *}
{if $user and $user_assigned_modules == 'y' and $no_module_controls ne 'y' and $feature_modulecontrols eq 'y'}
<table>
  <tr>
    <td width="11">
      <a title="{tr}Move module up{/tr}" href="{$current_location|escape}{$mpchar}mc_up={$module_name|escape}"><img src="img/icons2/up.gif" border="0" /></a>
    </td>
    <td width="11">
      <a title="{tr}Move module down{/tr}" href="{$current_location|escape}{$mpchar}mc_down={$module_name|escape}"><img src="img/icons2/down.gif" border="0" /></a>
    </td>
    <td>{$module_title}</td>
    <td width="11">
      <a title="{tr}Move module to opposite side{/tr}" href="{$current_location|escape}{$mpchar}mc_move={$module_name|escape}"><img src="img/icons2/admin_move.gif" border="0" /></a>
    </td>
    <td width="16">
      <a title="{tr}Unassign this module{/tr}" href="{$current_location|escape}{$mpchar}mc_unassign={$module_name|escape}" onclick="return confirmTheLink(this,'{tr}Are you sure you want to unassign this module?{/tr}')" title="{tr}Click here to unassign this module{/tr}"><img border="0" alt="{tr}Remove{/tr}" src="img/icons2/delete.gif" /></a>
    </td>
  </tr>
</table>
{else}
  {$module_title}
{/if}

</div><div class="box-data">
    {$module_content}
</div></div>
