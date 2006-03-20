{* $Header: /cvsroot/tikiwiki/tiki/templates/tiki-admin_quicktags.tpl,v 1.20 2006-03-20 06:15:12 lfagundes Exp $ *}

<h1><a class="pagetitle" href="tiki-admin_quicktags.php">{tr}Admin Quicktags{/tr}</a>

{if $feature_help eq 'y'}
<a href="{$helpurl}QuickTags" target="tikihelp" class="tikihelp" title="{tr}admin QuickTags{/tr}">
<img src="img/icons/help.gif" border="0" height="16" width="16" alt='{tr}help{/tr}' /></a>{/if}

{if $feature_view_tpl eq 'y'}
<a href="tiki-edit_templates.php?template=tiki-admin_quicktags.tpl" target="tikihelp" class="tikihelp" title="{tr}View template{/tr}: {tr}tiki admin quicktags template{/tr}">
<img src="img/icons/info.gif" border="0" width="16" height="16" alt='{tr}edit{/tr}' /></a>{/if}</h1>

<h2>{tr}Create/Edit QuickTags{/tr}</h2>
<form action="tiki-admin_quicktags.php" method="post">
<input type="hidden" name="tagId" value="{$tagId|escape}" />
<input type="hidden" name="offset" value="{$offset|escape}" />
<input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
<table class="normal">
<tr class="formcolor"><td>{tr}label{/tr}:</td>
<td><input type="text" maxlength="255" size="25" name="taglabel" value="{$info.taglabel|escape}" /></td></tr>
<tr class="formcolor"><td>{tr}Insert (use 'text' for figuring the selection){/tr}:</td>

<td><textarea maxlength="255" cols ="50" rows="5" name="taginsert">{$info.taginsert|escape}</textarea></td></tr>
<tr class="formcolor"><td>{tr}Category{/tr}:</td><td>
<select name="tagcategory">
{section name=ct loop=$list_categories}
<option {if $info.tagcategory eq $list_categories[ct]} selected="selected"{/if}>{$list_categories[ct]}</option>
{/section}
</select>
</td></tr>
<tr class="formcolor"><td>{tr}Path to the tag icon{/tr}:</td><td>
<select name="tagicon">
{section name=it loop=$list_icons}
<option style="background-image:url('{$list_icons[it]|escape}');background-repeat:no-repeat;padding-left:26px;height:14px;"{if $info.tagicon eq $list_icons[it]} selected="selected"{/if}>{$list_icons[it]}</option>
{/section}
</select>
</td></tr>
<tr class="formcolor"><td>&nbsp;</td><td><input type="submit" name="save" value="{tr}Save{/tr}" /></td></tr>
</table>
</form>

<h2>{tr}QuickTags{/tr}</h2>

<div id="quicktags-content">

{include file="tiki-admin_quicktags_content.tpl"}

</div>