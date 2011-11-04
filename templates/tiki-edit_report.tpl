<style>
	#reports .reportOption {
		font-weight: bold;
		width: 90px ! important;
		display: inline-block;
	}
	
	.joinedReport {
		padding-left: 4em;
	}
</style>

<h5 style="margin: 0px; padding: 3px;" class="ui-widget-header ui-corner-top">
	{tr}Report Builder{/tr}
</h5>
<div id="reports" class="ui-widget-content">
	<div id="report">
		<div class="reportOption">{tr}Report Type{/tr}</div>
		<select id="reportType">
			<option value="">{tr}Select Report Type{/tr}</option>
			
			{foreach from=$definitions item=definition}
				<option value="{$definition}">{$definition}</option>
			{/foreach}
		</select>
		
		<form id="reportEditor">
		
		</form>
	</div>
</div>

<div id="reportButtons">
	{button _text="{tr}Preview{/tr}" _id="reportPreview"}
	{button _text="{tr}Wiki Data{/tr}" _id="reportWikiData"}
	{button _text="{tr}Export As CSV{/tr}" _id="reportExportCSV"}
</div>

<pre id="reportWikiDataOutput"></pre>

<div id="reportSheetPreview"></div>