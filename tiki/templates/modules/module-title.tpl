{* $Header: /cvsroot/tikiwiki/tiki/templates/modules/module-title.tpl,v 1.1 2003-08-07 20:56:53 zaufi Exp $*}
{* Title bar for module with controls on it *}

<table width="100%">
  <tr>
    <td width="11">
      <a href="{$current_location}?up={$module_name}"><img src="img/icons2/up.gif" border="0" /></a>
    </td>
    <td width="11">
      <a href="{$current_location}?down={$module_name}"><img src="img/icons2/down.gif" border="0" /></a>
    </td>
    <td> {$module_title} </td>
    <td width="8">
      <a href="{$current_location}?left={$module_name}"><img src="img/icons2/nav_dot_right.gif" border="0" /></a>
    </td>
    <td width="8">
      <a href="{$current_location}?right={$module_name}"><img src="img/icons2/nav_dot_left.gif" border="0" /></a>
    </td>
    <td width="16">
      <a href="{$current_location}?unassign={$module_name}"><img src="img/icons2/delete.gif" border="0" /></a>
    </td>
  </tr>
</table>
