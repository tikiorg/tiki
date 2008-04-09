{* $Id$ *}

{if is_array($fgal_options) and count($fgal_options) gt 0}
	{foreach key=key item=item from=$fgal_options}
<tr class="formcolor">
	<td>{$item.name}</td>
	<td><input type="checkbox" name="fgal_{$key}" {if $item.value eq 'y'}checked="checked"{/if}/></td>
</tr>
	{/foreach}
{/if}

{if is_array($fgal_listing_conf) and count($fgal_listing_conf) gt 0}
	{foreach key=key item=item from=$fgal_listing_conf}
<tr class="formcolor">
	<td>{$item.name}</td>
	<td>
		<select style="width: 100%" name="fgal_list_{$key}">
		{if $key eq 'name'}
			<option value="a"{if $item.value eq 'a'} selected="selected"{/if}>{tr}Name-filename{/tr}</option>
			<option value="n"{if $item.value eq 'n'} selected="selected"{/if}>{tr}Name{/tr}</option>
			<option value="f"{if $item.value eq 'f'} selected="selected"{/if}>{tr}Filename only{/tr}</option>
		{else}
			<option value='n'{if $item.value eq 'n'} selected="selected"{/if}>{tr}Hide{/tr}</option>
			<option value='y'{if $item.value eq 'y'} selected="selected"{/if}>{tr}Show as a column{/tr}</option>
			<option value='o'{if $item.value eq 'o'} selected="selected"{/if}>{tr}Show in popup box{/tr}</option>
			<option value='a'{if $item.value eq 'a'} selected="selected"{/if}>{tr}Both{/tr}</option>
		{/if}
		{if $key eq 'lockedby'}
			<option value='i'{if $item.value eq 'i'} selected="selected"{/if}>{tr}Show an icon in a column{/tr}</option>
		{/if}
		</select>
	</td>
</tr>
	{/foreach}
{/if}
