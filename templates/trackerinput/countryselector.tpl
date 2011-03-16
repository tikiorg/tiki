<select name="{$field_value.ins_id}" {if $field_value.http_request}onchange="selectValues('trackerIdList={$field_value.http_request[0]}&amp;fieldlist={$field_value.http_request[3]}&amp;filterfield={$field_value.http_request[1]}&amp;status={$field_value.http_request[4]}&amp;mandatory={$field_value.http_request[6]}&amp;filtervalue='+escape(this.value),'{$field_value.http_request[5]}')"{/if}>
	{if $field_value.isMandatory ne 'y' || empty($field_value.value)}
		<option value=""{if $field_value.value eq '' or $field_value.value eq 'None'} selected="selected"{/if}>&nbsp;</option>
	{/if}
	{if empty($field_value.itemChoices)}
		<option value="Other"{if $field_value.value eq 'None'} selected="selected"{/if}{if $field_value.options_array[0] ne '1'} style="background-image:url('img/flags/Other.gif');background-repeat:no-repeat;padding-left:25px;padding-bottom:3px;"{/if}>{tr}Other{/tr}</option>
	{/if}

	{foreach key=flagicon item=flag from=$field_value.flags}
		{if $flagicon ne 'None' and $flagicon ne 'Other' and ( ! isset($field_value.itemChoices) || $field_value.itemChoices|@count eq 0 || in_array($flagicon, $field_value.itemChoices) )}
			 <option value="{$flagicon|escape}" {if $field_value.value eq $flagicon}selected="selected"{elseif $flagicon eq $field_value.defaultvalue}selected="selected"{/if}{if $field_value.options_array[0] ne '1'} style="background-image:url('img/flags/{$flagicon}.gif');background-repeat:no-repeat;padding-left:25px;padding-bottom:3px;"{/if}>{$flag|escape}</option>
		 {/if}
	{/foreach} 
</select>
