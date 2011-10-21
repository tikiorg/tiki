<select id="reportType">
	<option value="">{tr}Report Type{/tr}</option>
	
	{foreach from=$definitions item=definition}
		<option value="{$definition}">{$definition}</option>
	{/foreach}
</select>

<form id="reportEditor">

</form>

{button _text="{tr}Preview{/tr}" _id="reportPreview"}
{button _text="{tr}Wiki Markup{/tr}" _id="reportWikiSyntax"}
{button _text="{tr}Export As CSV{/tr}" _id="reportExportCSV"}

<pre id="reportWikiSyntaxOutput"></pre>

<form id="reportDebug">

</form>