<select name="{$field.ins_id}" {if $field.http_request}onchange="selectValues('trackerIdList={$field.http_request[0]}&amp;fieldlist={$field.http_request[3]}&amp;filterfield={$field.http_request[1]}&amp;status={$field.http_request[4]}&amp;mandatory={$field.http_request[6]}&amp;filtervalue='+escape(this.value),'{$field.http_request[5]}')"{/if}>
	{if $field.isMandatory ne 'y' || empty($field.value)}
		<option value=""{if $field.value eq '' or $field.value eq 'None'} selected="selected"{/if}>&nbsp;</option>
	{/if}
	{if empty($field.itemChoices)}
		<option value="Other"{if $field.value eq 'None'} selected="selected"{/if}{if $field.options_array[0] ne '1'} style="background-image:url('img/flags/Other.gif');background-repeat:no-repeat;padding-left:25px;padding-bottom:3px;"{/if}>{tr}Other{/tr}</option>
	{/if}

	{foreach key=flagicon item=flag from=$field.flags}
		{if $flagicon ne 'None' and $flagicon ne 'Other' and ( ! isset($field.itemChoices) || $field.itemChoices|@count eq 0 || in_array($flagicon, $field.itemChoices) )}
			 <option value="{$flagicon|escape}" {if $field.value eq $flagicon}selected="selected"{elseif $flagicon eq $field.defaultvalue}selected="selected"{/if}{if $field.options_array[0] ne '1'} style="background-image:url('img/flags/{$flagicon}.gif');background-repeat:no-repeat;padding-left:25px;padding-bottom:3px;"{/if}>{$flag|escape}</option>
		 {/if}
	{/foreach} 
</select>
