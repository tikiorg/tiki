{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/module-title.tpl,v 1.7 2003-08-21 19:26:43 zaufi Exp $*}
{* Title bar for module with controls on it *}

{* Draw module controls for logged user only *}
{if $user and $user_assigned_modules == 'y' and $no_module_controls ne 'y' and $feature_modulecontrols eq 'y'}
<table width="100%">
  <tr>
    <td width="11">
      <a title="{tr}Move module up{/tr}" href="{$current_location}?mc_up={$module_name}"><img src="img/icons2/up.gif" border="0" /></a>
    </td>
    <td width="11">
      <a title="{tr}Move module down{/tr}" href="{$current_location}?mc_down={$module_name}"><img src="img/icons2/down.gif" border="0" /></a>
    </td>
    <td> {$module_title} </td>
    <td width="11">
      <a title="{tr}Move module to opposite side{/tr}" href="{$current_location}?mc_move={$module_name}"><img src="img/icons2/admin_move.gif" border="0" /></a>
    </td>
    <td width="16">
      <a title="{tr}Unassign module{/tr}" href="{$current_location}?mc_unassign={$module_name}"><img src="img/icons2/delete.gif" border="0" /></a>
    </td>
  </tr>
</table>

{else}

  {$module_title}

{/if}
