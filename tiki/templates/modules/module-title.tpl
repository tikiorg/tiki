{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/module-title.tpl,v 1.4 2003-08-14 00:37:58 zaufi Exp $*}
{* Title bar for module with controls on it *}

{* Draw module controls for logged user only *}
{if $user and $user_assigned_modules == 'y' and $no_module_controls ne 'y'}
<table width="100%">
  <tr>
    <td width="11">
      <a href="{$current_location}?mc_up={$module_name}"><img src="img/icons2/up.gif" border="0" /></a>
    </td>
    <td width="11">
      <a href="{$current_location}?mc_down={$module_name}"><img src="img/icons2/down.gif" border="0" /></a>
    </td>
    <td> {$module_title} </td>
    <td width="8">
      <a href="{$current_location}?mc_left={$module_name}"><img src="img/icons2/nav_dot_right.gif" border="0" /></a>
    </td>
    <td width="8">
      <a href="{$current_location}?mc_right={$module_name}"><img src="img/icons2/nav_dot_left.gif" border="0" /></a>
    </td>
    <td width="16">
      <a href="{$current_location}?mc_unassign={$module_name}"><img src="img/icons2/delete.gif" border="0" /></a>
    </td>
  </tr>
</table>

{else}

  {$module_title}

{/if}
