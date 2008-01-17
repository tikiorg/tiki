{* $Header: /cvsroot/tikiwiki/tiki/templates/module.tpl,v 1.38.2.3 2008-01-17 18:38:08 sylvieg Exp $ *}
{* Module layout with controls *}

{if $module_nobox neq 'y'}
<div class="box-shadow">
	<div class="box box-{$module_name|escape}">
{if $module_decorations ne 'n'}
		<div class="box-title"{if !empty($module_params.bgcolor)} style="background-color:{$module_params.bgcolor};"{/if}>
{if $user and $prefs.user_assigned_modules == 'y' and $prefs.feature_modulecontrols eq 'y'}
			<table width="100%"><tr>
				<td width="11"><a title="{tr}Move module up{/tr}" 
				href="{$current_location|escape}{$mpchar|escape}mc_up={$moduleId|escape}"><img 
				src="pics/icons/resultset_up.png" border="0" width="16" height="16" alt="[{tr}Up{/tr}]" /></a></td>
				<td width="11"><a title="{tr}Move module down{/tr}" 
				href="{$current_location|escape}{$mpchar|escape}mc_down={$moduleId|escape}"><img 
				src="pics/icons/resultset_down.png" border="0" width="16" height="16" alt="[{tr}Down{/tr}]" /></a></td>
				<td {if $module_flip eq 'y'}ondblclick="javascript:icntoggle('mod-{$module_name|cat:$module_position|cat:$module_ord|escape}','module.png');"{/if}>
<span class="box-titletext"{if !empty($module_params.color)} style="color:{$module_params.color};"{/if}>{$module_title}</span>
</td>
{if $module_flip eq 'y'}
<td width="16">
<a title="{tr}Toggle module contents{/tr}" class="flipmodtitle" href="javascript:icntoggle('mod-{$module_name|cat:$module_position|cat:$module_ord|escape}','module.png');"><img name="mod-{$module_name|cat:$module_position|cat:$module_ord|escape}icn" class="flipmodimage" src="pics/icons/module.png" border="0" width="16" height="16" alt="[{tr}toggle{/tr}]" /></a>
</td>
{/if}
<td width="16"><a title="{tr}Move module to opposite side{/tr}" href="{$current_location|escape}{$mpchar|escape}mc_move={$moduleId}"><img src="pics/icons/arrow_right-left.png" border="0" width="16" height="16" alt="[{tr}opp side{/tr}]" /></a></td>
<td width="16"><a title="{tr}Unassign this module{/tr}" href="{$current_location|escape}{$mpchar|escape}mc_unassign={$module_name|escape}" onclick="return confirmTheLink(this,'{tr}Are you sure you want to unassign this module?{/tr}')"><img border="0" width="16" height="16" alt="[{tr}Remove{/tr}]" src="pics/icons/cross.png" /></a></td>
</tr>
</table>
{else}
{if $module_flip eq 'y'}
<table width="100%"><tr>
<td ondblclick="javascript:icntoggle('mod-{$module_name|cat:$module_position|cat:$module_ord|escape}','module.png');">
<span class="box-titletext"{if !empty($module_params.color)} style="color:{$module_params.color};"{/if}>
{/if}
{$module_title}
{if $module_flip eq 'y'}</span>
</td>
<td width="16">
<a title="{tr}Toggle module contents{/tr}" class="flipmodtitle" href="javascript:icntoggle('mod-{$module_name|cat:$module_position|cat:$module_ord|escape}','module.png');"><img name="mod-{$module_name|cat:$module_position|cat:$module_ord|escape}icn" class="flipmodimage" src="pics/icons/module.png" border="0" alt="[{tr}Hide{/tr}]" /></a>
</td>
</tr>
</table>
{/if}
{/if}
		</div>
{/if}
		<div id="mod-{$module_name|cat:$module_position|cat:$module_ord|escape}" style="display: block" class="box-data">
{/if}
{$module_content}
{$module_error}
{if $module_nobox neq 'y'}
{if $module_flip eq 'y'}
			<script type="text/javascript">
				setsectionstate('mod-{$module_name|cat:$module_position|cat:$module_ord|escape}','{$module_dstate}', 'module.png');
			</script>
{/if}
		</div>
		<div class="box-footer">

		</div>
	</div>
</div>
{/if}
