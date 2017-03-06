{jq}
	var pivotData{{$pivottable.index}} = {{$pivottable.data|json_encode}};
	$('#output_{{$pivottable.id}}').each(function () {
		var derivers = $.pivotUtilities.derivers;
		var renderers = $.extend($.pivotUtilities.renderers, $.pivotUtilities.plotly_renderers);
		var opts = {
			renderers: renderers,
			rendererOptions: {
				pivotId: {{$pivottable.id|json_encode}},
				highlight: {{$pivottable.highlight|json_encode}}
			},
			derivedAttributes: { {{','|implode:$pivottable.derivedAttributes}} },
			cols: {{$pivottable.tcolumns|json_encode}}, rows: {{$pivottable.trows|json_encode}},
			rendererName: {{$pivottable.rendererName|json_encode}},
			width: {{$pivottable.width|json_encode}},
			height: {{$pivottable.height|json_encode}},
			aggregatorName: {{$pivottable.aggregatorName|json_encode}},
			vals: {{$pivottable.vals|json_encode}},
			inclusions: {{$pivottable.inclusions}},

			sorters: function(attr) {
				if($.inArray(attr, {{$pivottable.dateFields|json_encode}}) > -1) {
					return function(a, b) {
						return ( Date.parse(a) || 0 ) - ( Date.parse(b) || 0 );
					}
				}
			},

			{{if $pivottable.heatmapParams}}
			rendererOptions: {
				heatmap: {
					colorScaleGenerator: function(values) {
						return Plotly.d3.scale.linear()
							.domain({{$pivottable.heatmapParams.domain|json_encode}})
							.range({{$pivottable.heatmapParams.colors|json_encode}});
					}
				}
			},
			{{/if}}

			highlightMine: {{$pivottable.highlightMine|json_encode}},
			highlightGroup: {{$pivottable.highlightGroup|json_encode}}
		};
		if( {{$pivottable.menuLimit|json_encode}} ) {
			opts.menuLimit = {{$pivottable.menuLimit|json_encode}};
		}
		if( {{$pivottable.aggregateDetails|json_encode}} ) {
			opts.aggregateDetails = {{$pivottable.aggregateDetails|json_encode}};
			opts.rendererOptions.table = {
				clickCallback: function(e, value, filters, pivotData){
					var details = [];
					pivotData.forEachMatchingRecord(filters, function(record){
						details.push(record.pivotLink);
					});
					feedback(details.join("<br>\n"), 'info', true);
				}
			};
		}

		$("#output_{{$pivottable.id}}").pivotUI(pivotData{{$pivottable.index}}, opts);

		$("#pivotEditBtn_{{$pivottable.id}}").on("click", function(){
			showControls("#output_{{$pivottable.id}}",{{$pivottable.id|json_encode}});
		});

		$("#restore_{{$pivottable.id}}").on("click", function(){
			$("#output_{{$pivottable.id}}").pivotUI(pivotData{{$pivottable.index}},JSON.parse(defaultSettings),true);
			$("#output_{{$pivottable.id}}_opControls").fadeOut();
		});

		$("#save_{{$pivottable.id}}").on("click", function(){
			saveConfig("#output_{{$pivottable.id}}", "{{$page}}", {{$pivottable.index|json_encode}}, {{$pivottable.trackerId|json_encode}}, {{$pivottable.fieldsArr|json_encode}});
		});

		createEditBtn({{$pivottable.id|json_encode}});
	});
    //adding bind call for pdf creation
    $('.icon-pdf').parent().click(function(){storeSortTable('#container_{{$pivottable.id}}',$('#container_{{$pivottable.id}}').html())});
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
    <img id="png_container_{$pivottable.id}" style="display:none"></img>
</div>

<div id="pivotdetails_modal"></div>