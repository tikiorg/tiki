{if $field.options_array[1] neq 'y'}
	{tr}Put tags separated by spaces. For tags with more than one word, use no spaces and put words together or enclose them with double quotes.{/tr}
{/if}
<div>
	<input type="text" id="{$field.ins_id|replace:'[':'_'|replace:']':''}" name="{$field.ins_id}" {if $field.options_array[0]}size="{$field.options_array[0]}"{/if} value="{$field.value|escape}" class="form-control">
	{if $field.options_array[2] neq 'y'}
		<div class="{$field.ins_id|escape}">
			{foreach from=$field.tag_suggestion item=t}
				<a class="suggest" href="{$t|sefurl:'freetag'}" data-freetag="{$t|escape}">{$t|escape}</a>&nbsp; &nbsp;
			{/foreach}
		</div>
		{jq notonready=true}
			$('.{{$field.ins_id|escape}} .suggest').click(function () {
				var tag = $(this).data('freetag');
				if (tag.indexOf(' ') !== -1) {
					tag = '"' + tag + '"';
				}

				var f = $('#{{$field.ins_id|escape}}');
				f.val(f.val() + ' ' + tag);

				return false;
			});
		{/jq}
	{/if}
</div>
