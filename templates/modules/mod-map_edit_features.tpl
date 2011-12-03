{tikimodule error=$module_params.error title=$tpl_module_title name="map_edit_features" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle}
	<form class="map-edit-features" method="post" action="">
	</form>
	{jq}
	$('.map-edit-features').hide();
	$(function () {
		var map;

		$('.map-edit-features').removeClass('map-layer-selector').each(function () {
			map = $(this).closest('.tab, #appframe, body').find('.map-container:first')[0];

			if (! map) {
				return;
			}

			$(this).show();
		});

		$(map).one('initialized', function () {
			var vlayer = new OpenLayers.Layer.Vector( "Editable" );
			map.map.addLayer(vlayer);
			map.modeManager.addMode({
				name: 'Draw',
				controls: [ new OpenLayers.Control.EditingToolbar(vlayer) ]
			});
		});
	});
	{/jq}
{/tikimodule}
