{if $field.options_array[1] neq 'y'}
	{tr}Put tags separated by spaces. For tags with more than one word, use no spaces and put words together or enclose them with double quotes.{/tr}
{/if}
<br />
<input type="text" id="{$field.ins_id|replace:'[':'_'|replace:']':''}" name="{$field.ins_id}" {if $field.options_array[0]}size="{$field.options_array[0]}"{/if} value="{$field.value|escape}" />
{if $field.options_array[2] neq 'y'}
	<br />
	{foreach from=$field.tag_suggestion item=t}
		{jq notonready=true}
			function addTag{{$field.ins_id|replace:"[":"_"|replace:"]":""}}(tag) {
				document.getElementById('{{$field.ins_id|replace:"[":"_"|replace:"]":""}').value = document.getElementById('{$field.ins_id|replace:"[":"_"|replace:"]":""}}').value + ' ' + tag;
			}
		{/jq}
		{capture name=tagurl}{if (strstr($t, ' '))}"{$t}"{else}{$t}{/if}{/capture}
		<a href="javascript:addTag{$field.ins_id|replace:"[":"_"|replace:"]":""}('{$smarty.capture.tagurl|escape:'javascript'|escape}');" onclick="javascript:needToConfirm=false">{$t|escape}</a>&nbsp; &nbsp; 
	{/foreach}
{/if}
