{* $Id$ *}

{tikimodule error=$module_params.error title=$tpl_module_title name="quick_edit" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle}
<form method="get" action="{$qe_action|escape}">
{if $categId}<input type="hidden" name="categId" value="{$categId}" />{/if}
{if $templateId}<input type="hidden" name="templateId" value="{$templateId}" />{/if}
{if $mod_quickedit_heading}<div class="box-data">{$mod_quickedit_heading}</div>{/if}
<input id="{$qefield}" type="text" size="{$size}" name="page" />
<input type="submit" name="quickedit" value="{$submit}" />
</form>
{if $prefs.javascript_enabled eq 'y' and $prefs.feature_jquery_autocomplete eq 'y'}
{jq}
	$jq("#{{$qefield}}").tiki("autocomplete", "pagename");
{/jq}
{/if}
{/tikimodule}
