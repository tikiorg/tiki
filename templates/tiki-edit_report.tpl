<select id="universalReportsType">
	<option value="">{tr}Report Type{/tr}</option>
	
	{foreach from=$definitions item=definition}
		<option value="{$definition}">{$definition}</option>
	{/foreach}
</select>

<form id="universalReportsEditor">

</form>

{button _text="{tr}Preview{/tr}" _id="universalReportsPreview"}
{button _text="{tr}Wiki Markup{/tr}" _id="universalReportsWikiSyntax"}
{button _text="{tr}Export As CSV{/tr}" _id="universalReportsExportCSV"}

<pre id="universalReportsWikiSyntaxOutput"></pre>

<form id="universalReportsDebug">

</form>