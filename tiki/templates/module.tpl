{* $Header: /cvsroot/tikiwiki/tiki/templates/module.tpl,v 1.15 2005-01-22 22:56:18 mose Exp $ *}
{* Module layout with controls *}

<div class="box {$module_name}">
<div class="box-title">{if $user and $user_assigned_modules == 'y' and $no_module_controls ne 'y' and $feature_modulecontrols eq 'y'}
<table>
  <tr>
    <td width="11">
      <a title="{tr}Move module up{/tr}" href="{$current_location|escape}{$mpchar|escape}mc_up={$module_name|escape}"><img src="img/icons2/up.gif" border="0" alt="[{tr}up{/tr}]" /></a>
    </td>
    <td width="11">
      <a title="{tr}Move module down{/tr}" href="{$current_location|escape}{$mpchar|escape}mc_down={$module_name|escape}"><img src="img/icons2/down.gif" border="0" alt="[{tr}down{/tr}]" /></a>
    </td>
<td>
{if $module_flip eq 'y'}<a class="flip" href="javascript:flip('flip-{$module_name|escape}');">{$module_title}</a>{else}{$module_title}{/if}
</td>
<td width="11">
<a title="{tr}Move module to opposite side{/tr}" href="{$current_location|escape}{$mpchar|escape}mc_move={$module_name|escape}"><img src="img/icons2/admin_move.gif" border="0" alt="[{tr}opp side{/tr}]" /></a>
</td>
<td width="16">
<a title="{tr}Unassign this module{/tr}" href="{$current_location|escape}{$mpchar|escape}mc_unassign={$module_name|escape}" onclick="return confirmTheLink(this,'{tr}Are you sure you want to unassign this module?{/tr}')"><img border="0" alt="[{tr}remove{/tr}]" src="img/icons2/delete.gif" /></a>
</td>
</tr>
</table>
{else}
{if $module_flip eq 'y'}<a class="flip" href="javascript:flip('flip-{$module_name|escape}');">{$module_title}</a>{else}{$module_title}{/if}
{/if}
</div><div class="box-data">
{if $module_flip eq 'y'}
  <div id="flip-{$module_name|escape}" style="display: block">
    {$module_content}
  </div>
{else}
  {$module_content}
{/if}
{$module_error}
</div></div>
