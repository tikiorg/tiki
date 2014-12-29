{* $Id$ *}

{tikimodule error=$module_params.error title=$tpl_module_title name="quick_edit" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle}
	<form method="post" action="{$qe_action|escape}">
		<div>
			{if $templateId}
				<input type="hidden" name="templateId" value="{$templateId|escape}" />
			{/if}
			{if $customTip}
				<input type="hidden" name="customTip" value="{$customTip|escape}" />
			{/if}
			{if $customTipTitle}
				<input type="hidden" name="customTipTitle" value="{$customTipTitle|escape}" />
			{/if}
			{if $wikiHeaderTpl}
				<input type="hidden" name="wikiHeaderTpl" value="{$wikiHeaderTpl|escape}" />
			{/if}
			{if $mod_quickedit_heading}
				<div class="panel-body">{$mod_quickedit_heading|escape}</div>
			{/if}
			<div class="form-group">
				<input id="{$qefield}" class="form-control" type="text" name="page" />
				{if $addcategId}
					<input type="hidden" name="cat_categories[]" value="{$addcategId|escape}" />
					<input type="hidden" name="cat_categorize" value="on" />
				{/if}
				{if $prefs.namespace_enabled == 'y' && $prefs.namespace_default}
					<div>
						<label>
							<input type="checkbox" name="namespace" value="{$prefs.namespace_default|escape}" checked="checked" />
							{tr _0=$prefs.namespace_default}Within %0{/tr}
						</label>
					</div>
				{/if}
			</div>
			<input type="submit" class="btn btn-default btn-sm" name="qedit" value="{$submit|escape}" />
		</div>
	</form>
	{if $prefs.javascript_enabled eq 'y' and $prefs.feature_jquery_autocomplete eq 'y'}
		{jq}
			$("#{{$qefield}}").tiki("autocomplete", "pagename");
		{/jq}
	{/if}
{/tikimodule}
