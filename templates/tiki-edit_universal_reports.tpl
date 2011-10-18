<select id="universalReportsType">
	<option value="">{tr}Report Type{/tr}</option>
	
	{foreach from=$definitions item=definition}
		<option value="{$definition}">{$definition}</option>
	{/foreach}
</select>

<form id="universalReportsEditor">

</form>

{button _text="Update" _id="universalReportsUpdate"}

<form id="universalReportsDebug">

</form>