{* 
$Header: /cvsroot/tikiwiki/tiki/templates/modules/mod-change_category.tpl,v 1.2 2005-01-22 22:56:27 mose Exp $ 
parameters : id=1
id is the categId of the parent categ to list
note : lists the objects from a given category not a recursive tree
*}
{if $feature_categories eq 'y' and $page and $showmodule}
{tikimodule title=$modcattitle name="$modname"}
<form method="post" action="{$smarty.server.PHP_SELF}" target="_self">
<input type="hidden" name="page" value="{$smarty.request.page|escape}" />
<input type="hidden" name="modcatid" value="{$modcatid}" />
<select name="modcatchange" size="1" onchange="this.form.submit();">
<option value="0">{tr}none{/tr}</option>
{foreach key=k item=i from=$modcatlist}
<option value="{$k}"{if $i.incat eq 'y'} selected="selected"{/if}>{$i.name}</option>
{/foreach}
</select>
</form>
{/tikimodule}
{/if}
