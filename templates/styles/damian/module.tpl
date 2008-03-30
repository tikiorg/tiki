{* $Id$ *}
{* Module layout with controls *}

<div class="box box-{$module_name|escape}">
<div class="box-title">
{* Draw module controls for logged user only *}
{if $user and $prefs.user_assigned_modules == 'y' and $prefs.feature_modulecontrols eq 'y'}
<table>
  <tr>
    <td width="11">
      <a href="{$current_location|escape}{$mpchar}mc_up={$module_name|escape}"><img src="pics/icons/resultset_up.png" border="0" width="16" height="16" /></a>
    </td>
    <td width="11">
      <a href="{$current_location|escape}{$mpchar}mc_down={$module_name|escape}"><img src="pics/icons/resultset_down.png" border="0" width="16" height="16" /></a>
    </td>
    <td>
      {if $module_flip eq 'y'}
        <a href="javascript:flip('flip-{$module_name|escape}');">{$module_title}</a>
      {else}
        {$module_title}
      {/if}
    </td>
    <td width="11">
      <a href="{$current_location|escape}{$mpchar}mc_move={$module_name|escape}"><img src="img/icons2/admin_move.gif" border="0" /></a>
    </td>
    <td width="16">
      <a href="{$current_location|escape}{$mpchar}mc_unassign={$module_name|escape}" onclick="return confirmTheLink(this,'{tr}Are you sure you want to remove this module?{/tr}')"><img src="pics/icons/cross.png" border="0" width="16" height="16" alt='{tr}Remove{/tr}' /></a>
    </td>
  </tr>
</table>
{else}
  {if $module_flip eq 'y'}
    <a class="flip" href="javascript:flip('flip-{$module_name|escape}');">{$module_title}</a>
  {else}
    {$module_title}
  {/if}
{/if}

</div><div class="box-data">
{if $module_flip eq 'y'}
  <div id="flip-{$module_name|escape}" style="display: block">
    {$module_content}
  </div>
{else}
  {$module_content}
{/if}
</div></div>
