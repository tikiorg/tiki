{* 
$Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-change_category.tpl,v 1.5 2006-02-17 15:10:47 sylvieg Exp $ 
parameters : id=1
id is the categId of the parent categ to list
note : lists the objects from a given category not a recursive tree
*}
{if $feature_categories eq 'y' and $page and $showmodule}
{tikimodule title=$modcattitle name="$modname" flip=$module_params.flip decorations=$module_params.decorations}
<form method="post" action="{$smarty.server.PHP_SELF}" target="_self">
<input type="hidden" name="page" value="{$smarty.request.page|escape}" />
<input type="hidden" name="modcatid" value="{$modcatid}" />
<select name="modcatchange" size="1" onchange="this.form.submit();">
<option value="0">{tr}top{/tr}</option>
{foreach key=k item=i from=$modcatlist}
<option value="{$k}"{if $i.incat eq 'y'} selected="selected"{/if}>{$i.categpath}</option>
{/foreach}
</select>
</form>
{/tikimodule}
{/if}
