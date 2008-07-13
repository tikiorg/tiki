{* $Id$ *}
{* Module layout with controls *}
{if $module_nobox neq 'y'}
<div class="box-shadow">
	<div class="box box-{$module_name|escape}">
{if $module_decorations ne 'n'}
		<h3 class="box-title"{if !empty($module_params.bgcolor)} style="background-color:{$module_params.bgcolor};"{/if}>
		{if $user and $prefs.user_assigned_modules == 'y' and $prefs.feature_modulecontrols eq 'y'}
			<span class="modcontrols">
			<a title="{tr}Move module up{/tr}" 
				href="{$current_location|escape}{$mpchar|escape}mc_up={$module_name|escape}"><img 
				src="pics/icons/resultset_up.png" border="0" width="16" height="16" alt="[{tr}Up{/tr}]" /></a>
			<a title="{tr}Move module down{/tr}" 
				href="{$current_location|escape}{$mpchar|escape}mc_down={$module_name|escape}"><img 
				src="pics/icons/resultset_down.png" border="0" width="16" height="16" alt="[{tr}Down{/tr}]" /></a>
			<a title="{tr}Move module to opposite side{/tr}" href="{$current_location|escape}{$mpchar|escape}mc_move={$module_name|escape}"><img src="pics/icons/arrow_right-left.png" border="0" width="16" height="16" alt="[{tr}opp side{/tr}]" /></a>
			<a title="{tr}Unassign this module{/tr}" href="{$current_location|escape}{$mpchar|escape}mc_unassign={$module_name|escape}" onclick="return confirmTheLink(this,'{tr}Are you sure you want to unassign this module?{/tr}')"><img border="0" width="16" height="16" alt="[{tr}Remove{/tr}]" src="pics/icons/cross.png" /></a>
			</span>
		{/if}
		{$module_title}
		{if $module_flip eq 'y'}
			<span id="moduleflip-{$module_name|cat:$module_position|cat:$module_ord|escape}">
				<a title="{tr}Toggle module contents{/tr}" class="flipmodtitle" href="javascript:icntoggle('mod-{$module_name|cat:$module_position|cat:$module_ord|escape}','module.png');"><img name="mod-{$module_name|cat:$module_position|cat:$module_ord|escape}icn" class="flipmodimage" src="pics/icons/module.png" border="0" width="16" height="16" alt="[{tr}toggle{/tr}]" /></a>
			</span>
		{/if}
		</h3>
	{else}
		{if $module_flip eq 'y'}
			<h3 class="box-title" ondblclick="javascript:icntoggle('mod-{$module_name|cat:$module_position|cat:$module_ord|escape}','module.png');"{if !empty($module_params.color)} style="color:{$module_params.color};"{/if}>
		{else}
			<h3 class="box-title"{if !empty($module_params.color)} style="color:{$module_params.color};"{/if}>
		{/if}
		{$module_title}
		{if $module_flip eq 'y'}
			<span id="moduleflip-{$module_name|cat:$module_position|cat:$module_ord|escape}">
<a title="{tr}Toggle module contents{/tr}" class="flipmodtitle" href="javascript:icntoggle('mod-{$module_name|cat:$module_position|cat:$module_ord|escape}','module.png');"><img name="mod-{$module_name|cat:$module_position|cat:$module_ord|escape}icn" class="flipmodimage" src="pics/icons/module.png" border="0" alt="[{tr}Hide{/tr}]" /></a>
			</span>
		{/if}
		</h3>
	{/if}
		<div id="mod-{$module_name|cat:$module_position|cat:$module_ord|escape}" style="display: block" class="box-data">
{/if}
{$module_content}
{$module_error}
{if $module_nobox neq 'y'}
{if $module_flip eq 'y'}
			<script type="text/javascript">
<!--//--><![CDATA[//><!--
				setsectionstate('mod-{$module_name|cat:$module_position|cat:$module_ord|escape}','{$module_dstate}', 'module.png');
//--><!]]>
			</script>
{/if}
		</div>
		<div class="box-footer">

		</div>
	</div>
</div>
{/if}
