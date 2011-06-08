{if $field.isMultilingual ne 'y'}
	{*prepend*}{if $field.options_array[2]}<span class="formunit">{$field.options_array[2]}&nbsp;</span>{/if}
	<input type="text" id="{$field.ins_id|replace:'[':'_'|replace:']':''}" name="{$field.ins_id}" {if $field.options_array[1]}size="{$field.options_array[1]}"{/if} {if $field.options_array[4]}maxlength="{$field.options_array[4]}"{/if} value="{if $field.value}{$field.value|escape}{else}{$field.defaultvalue|escape}{/if}" />
	{*append*}{if $field.options_array[3]}<span class="formunit">&nbsp;{$field.options_array[3]}</span>{/if}
	{if $field.options_array[5] eq 'y' && $prefs.javascript_enabled eq 'y' and $prefs.feature_jquery_autocomplete eq 'y'}
		{if !empty($item)}
			{autocomplete element="#"|cat:$field.ins_id|replace:"[":"_"|replace:"]":"" type="trackervalue"
					options="trackerId:"|cat:$item.trackerId|cat:",fieldId:"|cat:$field.fieldId}
		{else}
			{autocomplete element="#"|cat:$field.ins_id|replace:"[":"_"|replace:"]":"" type="trackervalue"
					options="trackerId:"|cat:$trackerId|cat:",fieldId:"|cat:$field.fieldId}
		{/if}
	{/if}
{else}
	{foreach from=$field.lingualvalue item=ling name=multi}
		<label for="{$field.ins_id|replace:'[':'_'|replace:']':''}_{$ling.lang}">{$ling.lang|langname}</label>
		<div>
		{*prepend*}{if $field.options_array[2]}<span class="formunit">{$field.options_array[2]}&nbsp;</span>{/if}
		<input type="text" id="{$field.ins_id|replace:'[':'_'|replace:']':''}_{$ling.lang}" name="{$field.ins_id}[{$ling.lang}]" value="{$ling.value|escape}" {if $field.options_array[1]}size="{$field.options_array[1]}"{/if} {if $field.options_array[4]}maxlength="{$field.options_array[4]}"{/if} /> {*@@ missing value*}
		{*append*}{if $field.options_array[3]}<span class="formunit">&nbsp;{$field.options_array[3]}</span>{/if}
		{if $field.options_array[5] eq 'y' && $prefs.javascript_enabled eq 'y' and $prefs.feature_jquery_autocomplete eq 'y'}
			{if !empty($item)}
				{autocomplete element="#"|cat:$field.ins_id|replace:"[":"_"|replace:"]":""|cat:"_"|cat:$ling.lang type="trackervalue"
						options="trackerId:"|cat:$item.trackerId|cat:",fieldId:"|cat:$field.fieldId}
			{else}
				{autocomplete element="#"|cat:$field.ins_id|replace:"[":"_"|replace:"]":""|cat:"_"|cat:$ling.lang type="trackervalue"
						options="trackerId:"|cat:$trackerId|cat:",fieldId:"|cat:$field.fieldId}
			{/if}
		{/if}
		</div>
	{/foreach}
{/if}
