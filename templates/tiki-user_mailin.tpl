{* $Id$ *}
{title}{tr}My Mail-in{/tr}{/title}

{include file='tiki-mytiki_bar.tpl'}
<br>

<script type="text/javascript">

function structSelChanged(itemNr)
{
	var elem = document.getElementById('changed_'+itemNr);
	if (elem != null) {
		elem.value='y';
	}
}

function confirm_delete()
{
	if (confirm('{tr}Are you sure you want to delete the routing rule?{/tr}')) {
		return true;
	} else {
		return false;
	}
}

</script>

<br>
{if $tikifeedback}
{section name=n loop=$tikifeedback}<div class="simplebox {if $tikifeedback[n].num > 0} highlight{/if}">{$tikifeedback[n].mes}</div>{/section}
<br>
{/if}

{tabset name="user_mailin"}
{tab name="{tr}Structure Routing{/tr}"}
<p>
{tr}Structure routing will only affect newly created pages, using the mail-in wiki-put function.{/tr}
</p>
{if $prefs.feature_wiki_structure eq 'y'}
	<form action="#" method="POST">
	<table>
	<tr>
	<td>Subject pattern</td>
	<td>Body pattern</td>
	<td>Structure</td>
	<td>Parent page name</td>
	<td>Active</td>
	</tr>
	{foreach from=$userStructs item=ustruct name=mstruct}
		<tr>
		<td><input type="text" name="mailinSubjPatt{$smarty.foreach.mstruct.iteration}" size="25" value="{$ustruct.subj_pattern}" onchange="structSelChanged({$smarty.foreach.mstruct.iteration});"/></td>
		<td><input type="text" name="mailinBodyPatt{$smarty.foreach.mstruct.iteration}" size="25" value="{$ustruct.body_pattern}" onchange="structSelChanged({$smarty.foreach.mstruct.iteration});"/></td>
		<td>		
			<select name="mailinStruct{$smarty.foreach.mstruct.iteration}" onchange="structSelChanged({$smarty.foreach.mstruct.iteration});">
			{foreach from=$structs item=struct}
				<option value="{$struct.structure_id}" {if $struct.structure_id eq $ustruct.structure_id}selected='selected'"{/if}">{$struct.pageName}</option>
			{/foreach}
			</select>
		</td>
		<td><input type="text" name="mailinPar{$smarty.foreach.mstruct.iteration}"  value="{$ustruct.pageName}" onchange="structSelChanged({$smarty.foreach.mstruct.iteration});" /></td>
		<td>
		<input type="checkbox" name="mailinAct{$smarty.foreach.mstruct.iteration}" {if $ustruct.is_active eq 'y'}checked="checked"{/if} onchange="structSelChanged({$smarty.foreach.mstruct.iteration});" />
		&nbsp;
		<a href="?delete=y&mailin_struct_id={$ustruct.mailin_struct_id}" onclick="return confirm_delete();"><img src="img/icons/delete.gif" /></a>

		{* Hidden field to track changes *}
		<input type="hidden" id="changed_{$smarty.foreach.mstruct.iteration}" name="changed_{$smarty.foreach.mstruct.iteration}" />
		{* Hidden field to remember mailin_struct_id *}
		<input type="hidden" name="mailin_struct_id_{$smarty.foreach.mstruct.iteration}" value="{$ustruct.mailin_struct_id}" />
		</td>
		</tr>
	{/foreach}
	{if isset($addNewRoute)}
		<tr>
		<td><input type="text" name="mailinSubjPattNew"  size="25" /></td>
		<td><input type="text" name="mailinBodyPattNew"  size="25" /></td>
		<td>
		<select name="mailinNewStruct">
		<option value="0" selected></option>
		{foreach from=$structs item=struct2}
			<option value="{$struct2.structure_id}">{$struct2.pageName}</option>
		{/foreach}
		</select>
		</td>
		<td><input type="text" name="mailinParNew" /></td>
		<td><input type="checkbox" name="mailinActNew" /></td>
		</tr>
	{/if}
	</table>
	<input type="submit" value="Save" />
	</form>
{else}
	<p>
	{tr}Wiki structures feature is not enabled{/tr}
	</p>
	<a href="tiki-admin.php?page=wiki&highlighted='feature_wiki_structure'">Go to wiki structure setting</a>
{/if}
{/tab}

{/tabset}
{$testing}
