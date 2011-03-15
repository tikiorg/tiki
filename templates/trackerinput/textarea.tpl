{strip}
{if $field_value.isMultilingual ne 'y'}
	{if $field_value.options_array[2] == 1}
		{if $field_value.options_array[0] eq 1}
   			{toolbars qtnum=$field_value.fieldId area_id="area_"|cat:$field_value.fieldId section="trackers"}
		{/if}
		<input type="text" id="area_{$field_value.fieldId}" name="{$field_value.ins_id}"{if $field_value.options_array[1] > 0} size="{$field_value.options_array[1]}"{/if}{if $field_value.options_array[3]>0} maxlength="{$field_value.options_array[3]}"{/if} value="{$field_value.value|escape}"{if $field_value.options_array[5]} onkeyup="wordCount({$field_value.options_array[5]}, this, 'cpt_{$field_value.fieldId}', "{tr}Word Limit Exceeded{/tr}")"{/if} {if $field_value.options_array[3]} onkeyup="charCount({$field_value.options_array[3]}, this, 'cpt_{$field_value.fieldId}', "{tr}Character Limit Exceeded{/tr}")"{/if} />
	{else}
		{capture name=textarea_id}area_{$field_value.fieldId}{/capture}
		{capture name=textarea_toolbars}{if $field_value.options_array[0] eq 1}y{else}n{/if}{/capture}
		{capture name=textarea_simple}n{/capture}
		{capture name=textarea_cols}{if $field_value.options_array[1] >= 1}{$field_value.options_array[1]}{else}50{/if}{/capture}
		{capture name=textarea_rows}{if $field_value.options_array[2] >= 1}{$field_value.options_array[2]}{else}4{/if}{/capture}
		{capture name=textarea_onkeyup}{if $field_value.options_array[5]}wordCount({$field_value.options_array[5]}, this, 'cpt_{$field_value.fieldId}', '{tr}Word Limit Exceeded{/tr}'){elseif $field_value.options_array[3]}charCount({$field_value.options_array[3]}, this, 'cpt_{$field_value.fieldId}', '{tr}Character Limit Exceeded{/tr}'){/if}{/capture}
		{textarea id=$smarty.capture.textarea_id name=$field_value.ins_id _toolbars=$smarty.capture.textarea_toolbars _simple=$smarty.capture.textarea_simple cols=$smarty.capture.textarea_cols rows=$smarty.capture.textarea_rows onkeyup=$smarty.capture.textarea_onkeyup _wysiwyg='n' section="trackers"}
			{$field_value.value}
		{/textarea}
	{/if}
	{if $field_value.options_array[3]}
		<div class="charCount">
			{if $prefs.javascript_enabled eq 'y'}
			 	 {tr}Character Count:{/tr} <input type="text" id="cpt_{$field_value.fieldId}" size="4" readOnly="true"{if !empty($field_value.value)} value="{$field_value.value|count_characters}"{/if} />
			{/if}
			{if $field_value.options_array[3] > 0} {tr}Max:{/tr} {$field_value.options_array[3]}{/if}
		</div>
	{/if}
	{if $field_value.options_array[5]}
		<div class="wordCount">
			{if $prefs.javascript_enabled eq 'y'}
				 {tr}Word Count:{/tr} <input type="text" id="cpt_{$field_value.fieldId}" size="4" readOnly="true"{if !empty($field_value.value)} value="{$field_value.value|count_words}"{/if} />
			{/if}
			{if $field_value.options_array[5] > 0} {tr}Max:{/tr} {$field_value.options_array[5]}{/if}
		</div>
	{/if}
{else}
	{foreach name=lg from=$field_value.lingualvalue item=ling}
		<label for="area_{$field_value.fieldId}_{$ling.lang}">{$ling.lang|langname}</label><br />
			{if $field_value.options_array[0] eq 1}
       			{toolbars qtnum=$field_value.id area_id=area_`$field_value.id`_`$ling.lang`}
       		{/if}
			<textarea id="area_{$field_value.fieldId}_{$ling.lang}" name="{$field_value.ins_id}[{$ling.lang}]" cols="{if $field_value.options_array[1] gt 1}{$field_value.options_array[1]}{else}50{/if}" rows="{if $field_value.options_array[2] gt 1}{$field_value.options_array[2]}{else}4{/if}"{if $field_value.options_array[5] > 0} onkeyup="wordCount({$field_value.options_array[5]}, this, 'cpt_{$field_value.fieldId}_{$ling.lang}', '{tr}Word Limit Exceeded{/tr}')"{/if}>
				{$ling.value|escape}
			</textarea>
			{if $field_value.options_array[5]}<div class="wordCount">{tr}Word Count:{/tr} <input type="text" id="cpt_{$field_value.fieldId}_{$ling.lang}" size="4" readOnly="true"{if !empty($ling.value)} value="{$ling.value|count_words}"{/if} />{if $field_value.options_array[5] > 0}{tr}Max:{/tr} {$field_value.options_array[5]}{/if}</div>i{elseif !$smarty.foreach.lg.last}<br />{/if}
	{/foreach}
{/if}
{/strip}