{if $perspectives|@count gt 0}
	{tikimodule error=$module_params.error title=$tpl_module_title name="perspective" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle}
		<form method="get" action="tiki-switch_perspective.php">
			<input id="mod-switch-perspective-back" type="checkbox" name="back" value="1"/>
			<label for="mod-switch-perspective-back">{tr}Stay on this page{/tr}</label>
			<select name="perspective" onchange="this.form.submit();">
				<option>{tr}Default{/tr}</option>
				{foreach from=$perspectives item=persp}
					<option value="{$persp.perspectiveId|escape}"{if $persp.perspectiveId eq $current_perspective} selected="selected"{/if}>{$persp.name|escape}</option>
				{/foreach}
			</select>
			<noscript>
				<input type="submit" value="{tr}Go{/tr}"/>
			</noscript>
		</form>
		{if $tiki_p_perspective_admin eq 'y'}
			<div align="center">
			<a href="tiki-edit_perspective.php">{tr}Edit perspectives{/tr}</a>
			</div>
		{/if}
	{/tikimodule}
{/if}
