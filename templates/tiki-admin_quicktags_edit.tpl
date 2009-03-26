
<form action="tiki-admin_quicktags.php" method="post">
<input type="hidden" name="tagId" value="{$tagId|escape}" />
<input type="hidden" name="offset" value="{$offset|escape}" />
<input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
<table class="normal">
<tr class="formcolor"><td>{tr}Label{/tr}:</td>
<td><input type="text" maxlength="255" size="25" name="taglabel" value="{$info.taglabel|escape}" /></td></tr>
<tr class="formcolor"><td>{tr}Insert (use 'text' for figuring the selection){/tr}:</td>

<td><textarea cols ="50" rows="5" name="taginsert">{$info.taginsert|escape}</textarea>

{if $list_categories|@count gt 1}
</td></tr>
<tr class="formcolor">
	<td>{tr}Category{/tr}:</td>
	<td>
		<select name="tagcategory">
		{foreach item=text key=value from=$list_categories}
			<option value="{$value}"{if $category eq $value} selected="selected"{/if}>{$text}</option>
		{/foreach}
		</select>
{else}
	{foreach item=text key=value from=$list_categories}
		<input type="hidden" name="tagcategory" value="{$value}" />
	{/foreach}
{/if}

</td></tr>
<tr class="formcolor"><td>{tr}Path to the tag icon{/tr}:</td><td>
<select name="tagicon">
{section name=it loop=$list_icons}
<option style="background-image:url('{icon _id=$list_icons[it] _notag='y'}');background-repeat:no-repeat;padding-left:26px;height:14px;"{if $info.tagicon eq $list_icons[it]} selected="selected"{/if}>{$list_icons[it]}</option>
{/section}
</select>
</td></tr>
<tr class="formcolor"><td>&nbsp;</td><td><input type="submit" name="save" value="{tr}Save{/tr}" /></td></tr>
</table>
</form>
