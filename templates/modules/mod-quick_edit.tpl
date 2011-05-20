{tikimodule error=$module_params.error title=$tpl_module_title name="quick_edit" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle}
<form method="post" action="{$qe_action|escape}">
<div>
{if $templateId}<input type="hidden" name="templateId" value="{$templateId|escape}" />{/if}
{if $customTip}<input type="hidden" name="customTip" value="{$customTip|escape}" />{/if}
{if $customTipTitle}<input type="hidden" name="customTipTitle" value="{$customTipTitle|escape}" />{/if}
{if $wikiHeaderTpl}<input type="hidden" name="wikiHeaderTpl" value="{$wikiHeaderTpl|escape}" />{/if}
{if $mod_quickedit_heading}<div class="box-data">{$mod_quickedit_heading|escape}</div>{/if}
<input id="{$qefield}" size="{$size}" type="text" name="page" />
{if $categId}<input type="hidden" name="categId" value="{$categId}" />{/if}
{if $addcategId}<input type="hidden" name="cat_categories[]" value="{$addcategId|escape}" />
<input type="hidden" name="cat_categorize" value="on" />{/if}
<input type="submit" name="qedit" value="{$submit|escape}" />
</div>
</form>
{if $prefs.javascript_enabled eq 'y' and $prefs.feature_jquery_autocomplete eq 'y'}
{jq}
	$("#{{$qefield}}").tiki("autocomplete", "pagename");
{/jq}
{/if}
{/tikimodule}
