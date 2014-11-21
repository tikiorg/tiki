{title admpage="reports" help="Reports"}{tr}Reports{/tr}{/title}
<style>
	#reports .reportOptionLabel {
		font-weight: bold;
		width: 90px ! important;
		display: inline-block;
	}

	.joinedReport {
		padding-left: 4em;
	}
	.joinHeader {
		padding: 3px;
	}
</style>
{if $reportFullscreen neq 'true'}
<h5 style="margin: 0px; padding: 3px;" class="ui-widget-header ui-corner-top">
	{tr}Report Builder{/tr}
</h5>
{/if}

<div id="reports" class="ui-widget-content">
	<div id="report">
		<div class="reportOptionLabel">{tr}Report Type{/tr}</div>
		<select id="reportType">
			<option value="">{tr}Select Report Type{/tr}</option>

			{foreach from=$definitions item=definition}
				<option value="{$definition}">{$definition}</option>
			{/foreach}
		</select>

		<form id="reportEditor" class="no-ajax" data-index="{$index}">

		</form>
	</div>
</div>

{if $reportFullscreen neq 'true'}
	<div id="reportButtons">
		{button _text="{tr}Preview{/tr}" _id="reportPreview"}
		{button _text="{tr}Wiki Data{/tr}" _id="reportWikiData"}
		{button _text="{tr}Export As CSV{/tr}" _id="reportExportCSV"}
	</div>
	<pre id="reportWikiDataOutput"></pre>

	<div id="reportSheetPreview"></div>
{else}

{/if}
