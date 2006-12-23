{* $Header: /cvsroot/tikiwiki/tiki/templates/module.tpl,v 1.24 2006-12-23 13:42:37 mose Exp $ *}
{* Module layout with controls *}

<div class="box box-{$module_name|escape}">
<div class="box-title">
{if $user and $user_assigned_modules == 'y' and $feature_modulecontrols eq 'y'}
<table width="100%"><tr>
<td width="11"><a title="{tr}Move module up{/tr}" href="{$current_location|escape}{$mpchar|escape}mc_up={$module_name|escape}"><img src="pics/icons/resultset_up.png" border="0" width="16" height="16" alt="[{tr}up{/tr}]" /></a></td>
<td width="11"><a title="{tr}Move module down{/tr}" href="{$current_location|escape}{$mpchar|escape}mc_down={$module_name|escape}"><img src="pics/icons/resultset_down.png" border="0" width="16" height="16" alt="[{tr}down{/tr}]" /></a></td>
<td {if $module_flip eq 'y'}ondblclick="javascript:icntoggle('mod-{$module_name|escape}','mo.png');"{/if}>
<span class="box-titletext">{$module_title}</span>
</td>
{if $module_flip eq 'y'}
<td width="11">
<a title="{tr}Hide module contents{/tr}" class="flipmodtitle" href="javascript:icntoggle('mod-{$module_name|escape}','mo.png');"><img name="mod-{$module_name|escape}icn" class="flipmodimage" src="img/icons/omo.png" border="0" alt="[{tr}hide{/tr}]" /></a>
</td>
{/if}
<td width="11"><a title="{tr}Move module to opposite side{/tr}" href="{$current_location|escape}{$mpchar|escape}mc_move={$module_name|escape}"><img src="img/icons2/admin_move.gif" border="0" alt="[{tr}opp side{/tr}]" /></a></td>
<td width="16"><a title="{tr}Unassign this module{/tr}" href="{$current_location|escape}{$mpchar|escape}mc_unassign={$module_name|escape}" onclick="return confirmTheLink(this,'{tr}Are you sure you want to unassign this module?{/tr}')"><img border="0" width="16" height="16" alt="[{tr}remove{/tr}]" src="pics/icons/cross.png" /></a></td>
</tr>
</table>
{else}
{if $module_flip eq 'y'}
<table width="100%"><tr>
<td ondblclick="javascript:icntoggle('mod-{$module_name|escape}','mo.png');">
<span class="box-titletext">
{/if}
{$module_title}
{if $module_flip eq 'y'}</span>
</td>
<td width="11">
<a title="{tr}Hide module contents{/tr}" class="flipmodtitle" href="javascript:icntoggle('mod-{$module_name|escape}','mo.png');"><img name="mod-{$module_name|escape}icn" class="flipmodimage" src="img/icons/omo.png" border="0" alt="[{tr}hide{/tr}]" /></a>
</td>
</tr>
</table>
{/if}
{/if}
</div>

<div id="mod-{$module_name|escape}" style="display: block" class="box-data">
{$module_content}
{$module_error}
{if $module_flip eq 'y'}
<script type="text/javascript">
  setsectionstate('mod-{$module_name|escape}','mo.png');
</script>
{/if}
</div>
</div>
