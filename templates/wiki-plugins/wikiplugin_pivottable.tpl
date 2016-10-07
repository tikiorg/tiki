<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/c3/0.4.11/c3.min.css">
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/d3/3.5.5/d3.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/c3/0.4.11/c3.min.js"></script>
{jq}

    var pivotData = {{$pivottable|json_encode}};
    $('#output_' + pivotData.id).each(function () {

        var currentData=pivotData;
        var derivers = $.pivotUtilities.derivers;
        var renderers = $.extend($.pivotUtilities.renderers, 
        
        $.pivotUtilities.c3_renderers);
        $.getJSON($.service('pivot', 'fetchUnifiedData', {trackerId: currentData.trackerId}), function(mps) {
            $("#output_"+currentData.id).pivotUI(mps, {
                renderers: renderers,
                cols: [{{$pivottable.tcolumns}}], rows: [{{$pivottable.trows}}],
                rendererName: currentData.rendererName,
                width:currentData.width,
                height:pivotData.height,
                aggregatorName:currentData.aggregatorName,
            });
        });
     });
        
{/jq}

<div id="output_{$pivottable.id}"></div>