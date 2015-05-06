<div class="map-container" data-geo-center="{defaultmapcenter}" data-target-field="{$field.ins_id}" style="height: 400px;position: sticky;" data-map-controls="controls,search_location,current_location,navigation,layers"></div>
<input type="text" class="form-control" name="{$field.ins_id}" id="{$field.ins_id}" value="{$field.value|escape}" size="60" class="form-control">
<div>{tr}Format: x,y,zoom where x is the longitude, and y is the latitude. Zoom is between 0 (view Earth) and 19.{/tr}</div>
