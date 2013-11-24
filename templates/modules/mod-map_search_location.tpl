<form id="{$search_location_id|escape}" method="get" action="" class="map-location-search">
	<input type="search" name="address" placeholder="{tr}Location{/tr}"/>
	<input type="submit" class="btn btn-default btn-sm" value="{tr}Search{/tr}"/>
</form>
{jq}
	var id = '#{{$search_location_id|escape}}';

	$(id).submit(function () {
		$('.map-container:visible').trigger('search', [ {address: $(this.address).val()} ]);
		$(this.address).val('');
		return false;
	});
{/jq}
