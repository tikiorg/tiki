<select id="universalReportsType">
	<option value="">{tr}Report Type{/tr}</option>
	
	{foreach from=$definitions item=definition}
		<option value="{$definition}">{$definition}</option>
	{/foreach}
</select>

<form id="universalReportsEditor">

</form>

{button _text="Preview" _id="universalReportsPreview"}
{button _text="Save" _id="universalReportsSave"}

<form id="universalReportsDebug">

</form>