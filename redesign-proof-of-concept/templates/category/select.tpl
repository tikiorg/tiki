<select name="categories[]">
	{foreach from=$categories item=categ key=categId}
		<option value="{$categId|escape}" {if $categ.selected} selected="selected" {/if}>{$categ.name|escape}</option>
	{/foreach}
</select>
