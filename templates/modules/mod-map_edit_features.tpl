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
			var vlayer = new OpenLayers.Layer.Vector( "Editable", {
				onFeatureInsert: function (feature) {
				}
			}), toolbar, modify;
			map.map.addLayer(vlayer);
			map.vectors = vlayer;

			modify = new OpenLayers.Control.ModifyFeature(vlayer, {
				mode: OpenLayers.Control.ModifyFeature.DRAG | OpenLayers.Control.ModifyFeature.RESHAPE,
			});
			vlayer.events.on({
				featureselected: function (event) {
					var format = new OpenLayers.Format.GeoJSON;
					format.write(event.feature);
				}
			});
			toolbar = new OpenLayers.Control.EditingToolbar(vlayer);
			toolbar.addControls([modify]);

			map.modeManager.addMode({
				name: 'Draw',
				controls: [ toolbar ]
			});
		});
	});
	{/jq}
{/tikimodule}
