{* 
$Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-change_category.tpl,v 1.9.2.3 2007-12-04 21:14:54 sylvieg Exp $ 
parameters : id=1
id is the categId of the parent categ to list
note : lists the objects from a given category not a recursive tree
*}
{if $prefs.feature_categories eq 'y' and $page and $showmodule}
{if !isset($tpl_module_title)}{assign var=tpl_module_title value="{tr}$modcattitle{/tr}"}{/if}
{tikimodule title=$tpl_module_title name="$modname" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox}

{if $module_params.detail eq 'y'}
{cycle values="odd,even" print=false}
<table class="normal">
{foreach key=k item=i from=$modcatlist}
	{if $i.incat eq 'y'}
	<tr>
	<td class="{cycle advance=false}">{$i.categpath}</td>
	<td class="{cycle}"><a href="{$smarty.server.REQUEST_URI}{if strstr($smarty.server.REQUEST_URI, '?')}&amp;{else}?{/if}remove={$i.categId}"><img src="pics/icons/cross.png" width="16" height="16" border="0" alt="{tr}Delete{/tr}"></a></td>
	</tr>
	{/if}
{/foreach}
</table>
{/if}

<div align="center">
<form method="post" action="{$smarty.server.PHP_SELF}" target="_self">
<input type="hidden" name="page" value="{$smarty.request.page|escape}" />
<input type="hidden" name="modcatid" value="{$modcatid}" />
{if $module_params.multiple eq 'y'}
<select name="modcatchange[]" multiple="multiple">
{else}
<select name="modcatchange" size="1" onchange="this.form.submit();">
{/if}
{if $module_params.detail eq 'y'} <option value="0"><i>{if $module_params.categorize}{tr}{$module_params.categorize}{/tr}{else}{tr}Categorize{/tr}{/if}</i></option>
{elseif !isset($module_params.notop)} <option value="0"><i>{tr}None{/tr}</i></option>{/if}
{foreach key=k item=i from=$modcatlist}
	{if $module_params.detail ne 'y' or $i.incat ne 'y'}
	<option value="{$k}"{if $i.incat eq 'y'} selected="selected"{/if}>{$i.categpath}</option>
	{/if}
{/foreach}
</div>
{if $module_params.multiple eq 'y'}
<div align="center"><input type="submit" name="categorize" value="{if $module_params.categorize}{tr}{$module_params.categorize}{/tr}{else}{tr}Categorize{/tr}{/if}" /></div>
{/if}
</select>
</form>
{/tikimodule}
{/if}
