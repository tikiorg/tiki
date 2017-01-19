var defaultSettings;

function showControls(pivotTableID,id)
{
   //saving default config
    //checking if default settings are not saved
       defaultSettings=saveCurrentConfig(pivotTableID);	
	
	var hiddenControls=new Array(".pvtVals",".pvtAxisContainer",".pvtUnused",".pvtRenderer",".pvtAxisContainer");
		 
    for(i=0;i<hiddenControls.length;i++)
	  $(pivotTableID+' '+hiddenControls[i]).fadeIn('slow'); 
    
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
		 } }, function(){ //mouseleave
		       $(edit_btn_div).hide();
			
		});
		
}

//function to save current configuration for restore button

function saveCurrentConfig(pivotTableID){
	
                var config = $(pivotTableID).data("pivotUIOptions");
                var config_copy = JSON.parse(JSON.stringify(config));
				 delete config_copy["aggregators"];
                    delete config_copy["renderers"];
                    //delete some bulky default values
                    delete config_copy["rendererOptions"];
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
		aggregateDetails: config_copy["aggregateDetails"]
	};
	ajaxLoadingShow("output_pivottable"+index);

	//calling ajax edit plugin function
	$.ajax({
		type: 'POST',
		url: 'tiki-wikiplugin_edit.php',
		dataType: 'json',
		data: {
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
