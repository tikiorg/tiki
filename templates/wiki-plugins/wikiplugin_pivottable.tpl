
{if $pivottable.showView}
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
        
        var defaultMps=mps;
        $("#output_"+currentData.id).pivotUI(mps, {
                renderers: renderers,
                cols: [{{$pivottable.tcolumns}}], rows: [{{$pivottable.trows}}],
                rendererName: currentData.rendererName,
                width:currentData.width,
                height:pivotData.height,
                aggregatorName:currentData.aggregatorName,
                vals: [{{$pivottable.vals}}]
         });
          
           $("#pivotEditBtn_"+currentData.id).on("click", function(){
              
              showControls("#output_"+currentData.id,currentData.id);
          
          });
          
          $("#restore_"+currentData.id).on("click", function(){
          $("#output_"+currentData.id).pivotUI(mps,JSON.parse(defaultSettings),true);
          	$("#output_"+currentData.id+"_opControls").fadeOut();
          
          });
          $("#save_"+currentData.id).on("click", function(){
              fieldsArr=currentData.fieldsArr.toString();
          
              saveConfig("#output_"+currentData.id,"{{$page}}",currentData.index,currentData.trackerId,fieldsArr.split(","));
          
          });
          
        });
        createEditBtn(currentData.id);
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
       {else}
               {remarksbox type='errors' title="{tr}Access Denied{/tr}"}
	           {tr}You do not have rights to view tracker data.{/tr} {if empty($pivottable.user)}{tr}Please Login.{/tr}{/if}
	           {/remarksbox}
       {/if}
    </div>