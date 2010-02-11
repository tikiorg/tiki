<select id="{'tr_sort_mode'|cat:$iTRACKERLIST}" name="{'tr_sort_mode'|cat:$iTRACKERLIST}">
	{foreach from=$sortchoice item=sc}
		<option value={$sc.value}{if $tr_sort_mode eq $sc.value} selected="selected"{/if}>{tr}{$sc.label|escape}{/tr}</option>
	{/foreach}
</select>
