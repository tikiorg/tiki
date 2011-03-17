{if $field.value}
	{if $prefs.feature_gmap eq 'y' and $prefs.gmap_key}
		{if $list_mode eq 'csv'}
			{$field.value}
		{elseif !empty($field.x) && !empty($field.y)}
			{if $list_mode eq 'y'}
				{wikiplugin _name=googlemap type=trackerfield width=200 height=200 controls=n locateitemtype=trackeritem locateitemid=`$item.itemId` trackerfieldid=`$field.fieldId` name=`$item.itemId`_`$field.fieldId`}{/wikiplugin}
				<div class="description">{tr}Latitude{/tr} (Y) = {$field.y}<br /> {tr}Longitude{/tr} (X) = {$field.x} {if $control ne 'n'}<br />Zoom = {$field.z}{/if}</div>
			{else}
				{wikiplugin _name=googlemap type=trackerfield width=500 height=400 controls=y locateitemtype=trackeritem locateitemid=`$item.itemId` trackerfieldid=`$field.fieldId` name=`$item.itemId`_`$field.fieldId`}{/wikiplugin}
				<div class="description">{tr}Latitude{/tr} (Y) = {$field.y}<br /> {tr}Longitude{/tr} (X) = {$field.x} {if $control ne 'n'}<br />Zoom = {$field.z}{/if}</div>
			{/if}
		{/if}
	{else}
	  <form method="get" action="">
		<div class="map-container" style="width: 250px; height: 250px;" data-target-field="location"></div>
		<input type="hidden" name="location" value="{$field.value|escape}" disabled="disabled"/>
	  </form>
	{/if}
{/if}
