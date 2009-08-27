{* $Id$ *}

{tikimodule error=$module_params.error title=$tpl_module_title name="quick_edit" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle}
<form method="get" action="{$qe_action|escape}">
{if $categId}<input type="hidden" name="categId" value="{$categId}" />{/if}
{if $templateId}<input type="hidden" name="templateId" value="{$templateId}" />{/if}
{if $mod_quickedit_heading}<div class="bod-data">{$mod_quickedit_heading}</div>{/if}
<input id="{$qefield}" type="text" size="{$size}" name="page" {if $prefs.feature_jquery eq 'y' and $prefs.feature_jquery_autocomplete eq 'y'}autocomplete="off"{/if} />
<input type="submit" name="quickedit" value="{$submit}" />
</form>
{if $prefs.feature_jquery eq 'y' and $prefs.feature_jquery_autocomplete eq 'y'}
<script type="text/javascript">
<!--//--><![CDATA[//><!--
{literal}
$jq(document).ready( function() {
	$jq("#{/literal}{$qefield}{literal}")
		.autocomplete('tiki-listpages.php?listonly',
			{extraParams: {'httpaccept': 'text/javascript'},
			 dataType: "json",
			 parse: parseAutoJSON,
			 formatItem: function(row) { return row; }
			});
});
{/literal}
//--><!]]>
</script>
{/if}
{/tikimodule}
