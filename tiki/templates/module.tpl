{* $Header: /cvsroot/tikiwiki/tiki/templates/module.tpl,v 1.1 2003-11-23 03:22:35 zaufi Exp $ *}
{* Module layout with controls *}

<div class="box" id="{$module_name|escape}"><div class="box-title">
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
      <a title="{tr}Unassign module{/tr}" href="{$current_location|escape}{$mpchar}mc_unassign={$module_name|escape}"><img src="img/icons2/delete.gif" border="0" /></a>
    </td>
  </tr>
</table>
{else}
  {$module_title}
{/if}

</div><div class="box-data">
    {$module_content}
</div></div>
