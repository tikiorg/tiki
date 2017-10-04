var defaultSettings;

function showControls(pivotTableID,id)
{
	//saving default config
	//checking if default settings are not saved
	defaultSettings=saveCurrentConfig(pivotTableID);	

	var hiddenControls=new Array(".pvtVals",".pvtAxisContainer",".pvtUnused",".pvtRenderer",".pvtAxisContainer");

	for(i=0;i<hiddenControls.length;i++) {
		$(pivotTableID+' '+hiddenControls[i]).fadeIn('slow');
	}
		
	//display restore and save button
	$(pivotTableID+"_opControls").fadeIn();
	//hide edit button 
	$("#pivotControls_"+id).fadeOut();
}




function createEditBtn(pivotTableID){
	
	var pivotTable="#output_"+pivotTableID;		
	var edit_btn_div="#pivotControls_"+pivotTableID;
	var edit_btn="#pivotEditBtn_"+pivotTableID;
		
	$("#container_"+pivotTableID).hover(function () {
		if(!$(pivotTable+"_opControls").is(":visible"))
		{
			var bleft = Math.round($(pivotTable+" .pvtUi").width()-$(edit_btn_div).width()+10);
			var btop = Math.round($(pivotTable).offset().top - $(pivotTable).offsetParent().offset().top + $(pivotTable+" .pvtUi").height()-5);
			
			//checking if not in edit mode then show edit button
			$(edit_btn_div).css({top: btop, left: bleft, position:'absolute'});
			$(edit_btn_div).show();
		}
	}, function(){ //mouseleave
		$(edit_btn_div).hide();
	});
		
}

//function to save current configuration for restore button

function saveCurrentConfig(pivotTableID){
	
	var config = $(pivotTableID).data("pivotUIOptions");
	var config_copy = JSON.parse(JSON.stringify(config));
	//delete some bulky default values
	delete config_copy["aggregators"];
	delete config_copy["renderers"];
	delete config_copy["localeStrings"];
	return JSON.stringify(config_copy, undefined, 2);
	
}
		
function saveConfig(pivotTableID,page,index,tracker,fieldsArr){
	var config = $(pivotTableID).data("pivotUIOptions");
	var config_copy = JSON.parse(JSON.stringify(config));

	//formatting data
	var rows=(config_copy['rows']).toString().split(',');
	var cols=(config_copy['cols']).toString().split(',');
	var vals=(config_copy['vals']).toString().split(',');

	//mapping data from title to param name
	rows = $.map(rows, function(name) {
		return fieldsArr[name];
	}).join(':');
	cols = $.map(cols, function(name) {
		return fieldsArr[name];
	}).join(':');
	vals = $.map(vals, function(name) {
		return fieldsArr[name];
	}).join(':');
				
	//formatting arguments
	var params = {
		data: "tracker:"+tracker,
		rows: rows,
		cols: cols,
		vals: vals,
		width: config_copy["width"],
		height: config_copy["height"],
		rendererName: config_copy["rendererName"],
		aggregatorName: config_copy["aggregatorName"],
		inclusions: JSON.stringify(config_copy["inclusions"]),
		menuLimit: config_copy["menuLimit"],
		aggregateDetails: config_copy["aggregateDetails"],
		highlightMine: config_copy["highlightMine"],
		highlightGroup: config_copy["highlightGroup"],
		xAxisLabel: config_copy["xAxisLabel"],
		yAxisLabel: config_copy["yAxisLabel"],
		chartTitle: config_copy["chartTitle"],
		chartHoverBar: config_copy["chartHoverBar"]
	};
	ajaxLoadingShow("output_pivottable"+index);

	//calling ajax edit plugin function
	$.ajax({
		type: 'POST',
		url: 'tiki-ajax_services.php',
		dataType: 'json',
		data: {
			controller: 'plugin',
			action: 'replace',
			ticket: $('#ticket').val(),
			daconfirm: "y",
			page: page,
			message:"Modified by Pivot Table Plugin",
			type: 'pivottable',
			content: '~same~',
			index: index,
			params: params
		},
		complete: function(){
			location.reload();
		}
	});
}

(function ($) {
	'use strict'

	var titleFormatter = function (text) {
		if (text && stringStartsWith(text, "[") && stringEndsWith(text, "]")) {
			text = text.substring(text.lastIndexOf("[") + 1)
			text = text.substring(0, text.length - 1);
		}
		return text;
	}
	var stringStartsWith = function (string, prefix) {
		return string.slice(0, prefix.length) == prefix;
	};
	var stringEndsWith = function (string, suffix) {
		return suffix == '' || string.slice(-suffix.length) == suffix;
	};

	var makePlotly = function (chartOpts) {
		return function (pivotData, opts) {
			var rowKeys = pivotData.getRowKeys();
			if (rowKeys.length === 0) {
				rowKeys.push([]);
			}
			var colKeys = pivotData.getColKeys();
			if (colKeys.length === 0) {
				colKeys.push([]);
			}

			var data = [];
			for (var _i = 0, _len = rowKeys.length; _i < _len; _i++) {
				var rowKey = rowKeys[_i];
				var trace = $.extend({
					x: [],
					y: [],
					name: rowKey.length > 0 ? rowKey.join("-") : ( opts.highlight && opts.highlight.length > 0 ? opts.localeStrings.all : '' )
				}, chartOpts.data);
				for (var _j = 0, _len1 = colKeys.length; _j < _len1; _j++) {
					var colKey = colKeys[_j];
					var agg = pivotData.getAggregator(rowKey, colKey);
					if (agg.value() != null) {
						trace.y.push(agg.value());
					} else {
						trace.y.push(null);
					}
					trace.x.push(colKey.join("-"));
				}
				if( trace.type == 'box' ) {
					delete trace.x;
				}
				data.push(trace);
			}

			if( opts.highlight && opts.highlight.length > 0 ) {
				var traces = {};
				for (var _i = 0, _len = opts.highlight.length; _i < _len; _i++) {
					var entry = opts.highlight[_i].item;
					var group = opts.highlight[_i].group;
					if( !group ) {
						group = opts.localeStrings.mine;
					}
					var color = opts.highlight[_i].color;
					if(!traces[group] ) {
						traces[group] = {
							type: 'scatter',
							mode: 'markers',
							x: [],
							y: [],
							text: [],
							name: group
						};
						if( opts.highlight[_i].color ) {
							traces[group].marker = {color: opts.highlight[_i].color};
						}
					}
					var colKey = [];
					for( var _j = 0, _len1 = pivotData.colAttrs.length; _j < _len1; _j++ ) {
						colKey.push(entry[pivotData.colAttrs[_j]]);
					}
					var rowKey = [];
					for( var _j = 0, _len1 = pivotData.rowAttrs.length; _j < _len1; _j++ ) {
						rowKey.push(entry[pivotData.rowAttrs[_j]]);
					}
					traces[group].y.push(pivotData.getAggregator(rowKey, colKey).value());
					if( chartOpts.data.type == 'box' ) {
						if( rowKeys.length <= 1 ) {
							traces[group].x.push(opts.localeStrings.all);
						} else {
							traces[group].x.push(rowKey.join('-'));
						}
					} else {
						traces[group].x.push(colKey.join("-"));
					}
					if (entry.pivotLink) {
						var pivotLinkText = $(entry.pivotLink).text();
						traces[group].text.push(pivotLinkText ? pivotLinkText : entry.pivotLink);
					}
				}
				for( var k in traces ) {
					data.push(traces[k]);
				}
			}

			var layout = $.extend({}, chartOpts.layout);

			layout.title = titleFormatter(pivotData.aggregatorName);

			var hAxisTitle = pivotData.rowAttrs.map(titleFormatter).join(",");
			if (hAxisTitle !== "") {
				layout.title += " " + opts.localeStrings.by + " " + hAxisTitle;
			}

			var groupByTitle = pivotData.colAttrs.map(titleFormatter).join(",");
			if (groupByTitle !== "") {
				if (hAxisTitle) {
					layout.title += " " + opts.localeStrings.and + " " + groupByTitle;
				} else {
					layout.title += " " + opts.localeStrings.by + " " + groupByTitle;
				}
			}

			layout.xaxis = {
				title: opts.xAxisLabel ? opts.xAxisLabel : groupByTitle
			}

			layout.yaxis = {
				title: opts.yAxisLabel ? opts.yAxisLabel : pivotData.aggregatorName + (pivotData.valAttrs.length ? "(" + (pivotData.valAttrs.join(", ")) + ")" : "")
			}

			if( opts.chartTitle ) {
				layout.title = opts.chartTitle;
			}

			if( opts.chartHoverBar === 'n' ) {
				layout.hovermode = 'closest';
			}

			$("<div id='pivotchart_"+opts.pivotId+"'>").appendTo('#output_'+opts.pivotId+' .pvtRendererArea');
			
			var d3 = Plotly.d3;
			var img_png = d3.select('#png_container_'+opts.pivotId);
			
			Plotly.newPlot('pivotchart_'+opts.pivotId, data, layout, {displayModeBar: opts.chartHoverBar !== 'n'}).then(
				function(gd)
				{
					 Plotly.toImage(gd)
					.then(
					function(url)
					{
						img_png.attr("src", url);
						return Plotly.toImage(gd,{format:'png'});
					}
				)
			});

			return $('#pivotchart_'+opts.pivotId);
		};
	};

	return $.pivotUtilities.plotly_renderers = {
		"Line Chart": makePlotly({ data: { type: 'scatter' } }),
		"Bar Chart": makePlotly({ data: { type: 'bar' } }),
		"Stacked Bar Chart": makePlotly({ data: { type: 'bar' }, layout: { barmode: 'stack' } }),
		"Horizontal Bar Chart": makePlotly({ data: { type: 'bar', orientation: 'h' } }),
		"Area Chart": makePlotly({ data: { type: 'scatter', fill: 'tonexty' } }),
		"Scatter Chart": makePlotly({ data: { type: 'scatter', mode: 'markers' } }),
		"Boxplot Chart": makePlotly({ data: { type: 'box', boxpoints: 'Outliers' } }),
	};
})(jQuery);

