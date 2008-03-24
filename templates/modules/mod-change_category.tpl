{* 
$Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-change_category.tpl,v 1.9.2.10 2008-02-27 14:47:06 sylvieg Exp $ 
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
	<td class="{cycle advance=false}">{if $module_params.path eq 'n'}{$i.name}{else}{$i.categpath}{/if}</td>
	{if $module_params.del ne 'n'}
	<td class="{cycle}"><a href="{$smarty.server.REQUEST_URI}{if strstr($smarty.server.REQUEST_URI, '?')}&amp;{else}?{/if}remove={$i.categId}"><img src="pics/icons/cross.png" width="16" height="16" border="0" alt="{tr}Delete{/tr}" /></a></td>
	{/if}
	</tr>
	{/if}
{/foreach}
</table>
{/if}

{if $module_params.detail ne 'y' or ($module_params.add ne 'n' and $remainCateg)}
<div align="center">
<form method="post" action="{$smarty.server.REQUEST_URI}" target="_self">
<input type="hidden" name="page" value="{$page|escape}" />
<input type="hidden" name="modcatid" value="{$modcatid}" />
{if $module_params.multiple eq 'y'}
<select name="modcatchange[]" multiple="multiple">
{else}
<select name="modcatchange" size="1" onchange="this.form.submit();">
{/if}
{if $module_params.add ne 'n'}
	{if $module_params.detail eq 'y'} <option value="0" style="font-style: italic;">{if $module_params.categorize}{tr}{$module_params.categorize}{/tr}{else}{tr}Categorize{/tr}{/if}</option>
	{elseif !isset($module_params.notop)} <option value="0" style="font-style: italic;">{tr}None{/tr}</option>{/if}
{/if}
{foreach key=k item=i from=$modcatlist}
	{if $module_params.detail ne 'y' or $i.incat ne 'y'}
	{if !($module_params.add eq 'n' and $i.incat ne 'y')}
	<option value="{$k}"{if $i.incat eq 'y'} selected="selected"{/if}>{if $module_params.path eq 'n'}{$i.name}{else}{$i.categpath}{/if}</option>
	{/if}
	{/if}
{/foreach}
{if $module_params.multiple eq 'y' and $modules_params.add ne 'n'}
<div align="center"><input type="submit" name="categorize" value="{if $module_params.categorize}{tr}{$module_params.categorize}{/tr}{else}{tr}Categorize{/tr}{/if}" /></div>
{/if}
</select>
</form>
</div>
{/if}

{/tikimodule}
{/if}
