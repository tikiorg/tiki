{jq}

    var pivotData = {{$pivottable|json_encode}};
   
$('#output_' + pivotData.id).each(function () {
        var currentData=pivotData;
        var derivers = $.pivotUtilities.derivers;
        var renderers = $.extend($.pivotUtilities.renderers, 
            $.pivotUtilities.c3_renderers);
         $.getJSON($.service('pivot', 'fetchUnifiedData', {
				trackerId: currentData.trackerId,
				
			}), function(mps) {
            $("#output_"+currentData.id).pivotUI(mps, {
                renderers: renderers,
                cols: [currentData.tcolumns], rows: [currentData.trows],
                rendererName: currentData.rendererName,
                width:currentData.width,
                height:pivotData.height,
                aggregatorName:currentData.aggregatorName,
                vals:currentData.vals,
                
                
            });
        });
     });
        
{/jq}

<div id="output_{$pivottable.id}"></div>