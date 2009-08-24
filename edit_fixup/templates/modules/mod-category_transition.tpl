{if ! empty( $mod_transitions )}
	{tikimodule error=$module_params.error title=$tpl_module_title name="category_transition" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle}
	<form method="post" action="">
		{foreach from=$mod_transitions item=trans}
			<div>
				<input id="transition-{$trans.transitionId|escape}" type="radio" name="transition" value="{$trans.transitionId|escape}"/>
				<label for="transition-{$trans.transitionId|escape}">{$trans.name|escape}</label>
			</div>
		{/foreach}
		<div><input type="submit" value="{tr}Apply{/tr}"/></div>
	</form>
	{/tikimodule}
{/if}
