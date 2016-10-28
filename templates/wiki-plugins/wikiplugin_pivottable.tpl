<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/c3/0.4.11/c3.min.css">
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/d3/3.5.5/d3.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/c3/0.4.11/c3.min.js"></script>
{jq}
	var pivotData = {{$pivottable|json_encode}};
	$('#output_' + pivotData.id).each(function () {
		var derivers = $.pivotUtilities.derivers;
		var renderers = $.extend($.pivotUtilities.renderers, 
		
		$.pivotUtilities.c3_renderers);

		$("#output_"+pivotData.id).pivotUI(pivotData.data, {
			renderers: renderers,
			cols: pivotData.tcolumns, rows: pivotData.trows,
			rendererName: pivotData.rendererName,
			width: pivotData.width,
			height: pivotData.height,
			aggregatorName: pivotData.aggregatorName,
			vals: pivotData.vals
		});

		$("#pivotEditBtn_"+pivotData.id).on("click", function(){
			showControls("#output_"+pivotData.id,pivotData.id);
		});

		$("#restore_"+pivotData.id).on("click", function(){
			$("#output_"+pivotData.id).pivotUI(pivotData.data,JSON.parse(defaultSettings),true);
			$("#output_"+pivotData.id+"_opControls").fadeOut();
		});

		$("#save_"+pivotData.id).on("click", function(){
			fieldsArr=pivotData.fieldsArr.toString();
			saveConfig("#output_"+pivotData.id,"{{$page}}",pivotData.index,pivotData.trackerId,fieldsArr.split(","));
		});

		createEditBtn(pivotData.id);
	});
{/jq}
	
<style type="text/css">
 {literal} 
	#output_{$pivottable.id} .pvtVals,.pvtAxisContainer, .pvtUnused,.pvtRenderer, .pvtAxisContainer {
			display:none;

		 }
 {/literal}
</style>

<div id="container_{$pivottable.id}">
	<div id="output_{$pivottable.id}"></div>
	<div id="output_{$pivottable.id}_opControls" style="display:none">
	<input id="save_{$pivottable.id}" type="button" value="Save Changes" class="btn btn-primary ui-button ui-corner-all ui-widget" /><input class="btn btn-primary ui-button ui-corner-all ui-widget" id="restore_{$pivottable.id}" type="button" value="Cancel Edit" /></div>
	{if $pivottable.showControls}<div id="pivotControls_{$pivottable.id}"  style="display:none;position:relative;"><input type="button" id="pivotEditBtn_{$pivottable.id}" value="Edit Pivot Table"  class="btn btn-primary ui-button ui-corner-all ui-widget" /></div>{/if}
</div>
