{* 
$Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-change_category.tpl,v 1.9.2.1 2007-12-03 15:57:51 sylvieg Exp $ 
parameters : id=1
id is the categId of the parent categ to list
note : lists the objects from a given category not a recursive tree
*}
{if $prefs.feature_categories eq 'y' and $page and $showmodule}
{if !isset($tpl_module_title)}{assign var=tpl_module_title value="{tr}$modcattitle{/tr}"}{/if}
{tikimodule title=$tpl_module_title name="$modname" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox}
<form method="post" action="{$smarty.server.PHP_SELF}" target="_self">
<input type="hidden" name="page" value="{$smarty.request.page|escape}" />
<input type="hidden" name="modcatid" value="{$modcatid}" />
<select name="modcatchange" size="1" onchange="this.form.submit();">
{if !isset($module_params.notop)} <option value="0">{tr}None{/tr}</option>{/if}
{foreach key=k item=i from=$modcatlist}
<option value="{$k}"{if $i.incat eq 'y'} selected="selected"{/if}>{$i.categpath}</option>
{/foreach}
</select>
</form>
{/tikimodule}
{/if}
