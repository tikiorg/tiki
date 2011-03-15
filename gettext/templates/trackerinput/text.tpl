{if $field_value.isMultilingual ne 'y'}
	{*prepend*}{if $field_value.options_array[2]}<span class="formunit">{$field_value.options_array[2]}&nbsp;</span>{/if}
	<input type="text" id="{$field_value.ins_id|replace:'[':'_'|replace:']':''}" name="{$field_value.ins_id}" {if $field_value.options_array[1]}size="{$field_value.options_array[1]}"{/if} {if $field_value.options_array[4]}maxlength="{$field_value.options_array[4]}"{/if} value="{if $field_value.value}{$field_value.value|escape}{else}{$field_value.defaultvalue|escape}{/if}" />
	{*append*}{if $field_value.options_array[3]}<span class="formunit">&nbsp;{$field_value.options_array[3]}</span>{/if}
	{if $field_value.options_array[5] eq 'y' && $prefs.javascript_enabled eq 'y' and $prefs.feature_jquery_autocomplete eq 'y'}
		{if !empty($item)}
			{autocomplete element="#"|cat:$field_value.ins_id|replace:"[":"_"|replace:"]":"" type="trackervalue"
					options="trackerId:"|cat:$item.trackerId|cat:",fieldId:"|cat:$field_value.fieldId}
		{else}
			{autocomplete element="#"|cat:$field_value.ins_id|replace:"[":"_"|replace:"]":"" type="trackervalue"
					options="trackerId:"|cat:$trackerId|cat:",fieldId:"|cat:$field_value.fieldId}
		{/if}
	{/if}
{else}
	{foreach from=$field_value.lingualvalue item=ling name=multi}
		<label for="{$field_value.ins_id|replace:'[':'_'|replace:']':''}_{$ling.lang}">{$ling.lang|langname}</label>
		<br />
		{*prepend*}{if $field_value.options_array[2]}<span class="formunit">{$field_value.options_array[2]}&nbsp;</span>{/if}
		<input type="text" id="{$field_value.ins_id|replace:'[':'_'|replace:']':''}_{$ling.lang}" name="{$field_value.ins_id}[{$ling.lang}]" value="{$ling.value|escape}" {if $field_value.options_array[1]}size="{$field_value.options_array[1]}"{/if} {if $field_value.options_array[4]}maxlength="{$field_value.options_array[4]}"{/if} /> {*@@ missing value*}
		{*append*}{if $field_value.options_array[3]}<span class="formunit">&nbsp;{$field_value.options_array[3]}</span>{/if}
		{if $field_value.options_array[5] eq 'y' && $prefs.javascript_enabled eq 'y' and $prefs.feature_jquery_autocomplete eq 'y'}
			{if !empty($item)}
				{autocomplete element="#"|cat:$field_value.ins_id|replace:"[":"_"|replace:"]":""|cat:"_"|cat:$ling.lang type="trackervalue"
						options="trackerId:"|cat:$item.trackerId|cat:",fieldId:"|cat:$field_value.fieldId}
			{else}
				{autocomplete element="#"|cat:$field_value.ins_id|replace:"[":"_"|replace:"]":""|cat:"_"|cat:$ling.lang type="trackervalue"
						options="trackerId:"|cat:$trackerId|cat:",fieldId:"|cat:$field_value.fieldId}
			{/if}
		{/if}
		{if !$smarty.foreach.multi.last}<br />{/if}
	{/foreach}
{/if}
