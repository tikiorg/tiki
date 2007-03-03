{* $Header: /cvsroot/tikiwiki/_mods/features/aulawiki/templates/styles/workspaces/module.tpl,v 1.3 2007-03-03 08:52:06 jreyesg Exp $ *}
{* Module layout with controls *}
<div class="box box-{$module_name|escape}">
<div class="box-title box-title-{$module_name|escape}">
{if $user and $user_assigned_modules == 'y' and $no_module_controls ne 'y' and $feature_modulecontrols eq 'y'}
<table class="box-title">
  <tr>
    <td width="11">
      <a title="{tr}Move module up{/tr}" href="{$current_location|escape}{$mpchar|escape}mc_up={$module_name|escape}"><img src="img/icons2/up.gif" border="0" alt="[{tr}up{/tr}]" /></a>
    </td>
    <td width="11">
      <a title="{tr}Move module down{/tr}" href="{$current_location|escape}{$mpchar|escape}mc_down={$module_name|escape}"><img src="img/icons2/down.gif" border="0" alt="[{tr}down{/tr}]" /></a>
    </td>
<td {if $module_flip eq 'y'}ondblclick="javascript:icntoggle('mod-{$module_name|escape}-{$moduleId}','mo.png');"{/if}>
{$module_title}
</td>
    <td width="11">
{if $module_flip eq 'y'}
<a title="{tr}Hide module contents{/tr}" class="flipmodtitle" href="javascript:icntoggle('mod-{$module_name|escape}-{$moduleId}','mo.png');"><img name="mod-{$module_name|escape}icn" class="flipmodimage" src="img/icons/omo.png" border="0" alt="[{tr}hide{/tr}]" /></a>{else}&nbsp;{/if}
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
{if $module_flip eq 'y'}
<table class="box-title">
  <tr>
    <td ondblclick="javascript:icntoggle('mod-{$module_name|escape}-{$moduleId}','mo.png');">
{/if}{$module_title}{if $module_flip eq 'y'}
    </td>
    <td width="11">
      <a title="{tr}Hide module contents{/tr}" class="flipmodtitle" href="javascript:icntoggle('mod-{$module_name|escape}-{$moduleId}','mo.png');"><img name="mod-{$module_name|escape}icn" class="flipmodimage" src="img/icons/omo.png" border="0" alt="[{tr}hide{/tr}]" /></a>
</td>
</tr>
</table>
{/if}
{/if}
</div><div id="mod-{$module_name|escape}-{$moduleId}" style="display: block" class="box-data">
{$module_content}
{$module_error}
{if $module_flip eq 'y'}
{literal}
<script language="Javascript" type="text/javascript">
  setsectionstate('mod-{/literal}{$module_name|escape}-{$moduleId}{literal}','mo.png');
</script>
{/literal}
{/if}
</div></div>
