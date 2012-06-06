{tikimodule error=$module_params.error title=$tpl_module_title name="map_layer_selector" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle}
	<form class="map-layer-selector" method="post" action="">
		<select name="baseLayers">
		</select>
		<div class="optionalLayers">
		</div>
	</form>
	{jq}
	$('.map-layer-selector').hide();
	$(function () {
		var realRefresh, map, refreshLayers = function () {
			realRefresh();
		};
		$('.map-container').one('initialized', function () {
			$('.map-layer-selector').removeClass('map-layer-selector').each(function () {
				map = $(this).closest('.tab, #appframe, body').find('.map-container:first')[0];
				var baseLayers= $(this.baseLayers);
				var optionalLayers= $('.optionalLayers', this);

				if (! map) {
					return;
				}

				$(this).show();

				baseLayers.change(function () {
					if (map.map) {
						var layer = map.map.layers[$(this).val()];
						map.map.setBaseLayer(layer);
						if (layer.isBlank) {
							layer.setVisibility(false);
						}
					}
				});

				realRefresh = function () {
					baseLayers.empty();
					optionalLayers.empty();
					$.each(map.map.layers, function (k, thisLayer) {
						if (! thisLayer.displayInLayerSwitcher) {
							return;
						}

						if (thisLayer.isBaseLayer) {
							baseLayers.append($('<option/>')
								.attr('value', k)
								.text(thisLayer.name)
								.attr('selected', thisLayer === map.map.baseLayer));
						} else {
							var label, checkbox;
							optionalLayers.append(label = $('<label/>').text(thisLayer.name).prepend(
								checkbox = $('<input type="checkbox"/>')
									.attr('checked', thisLayer.getVisibility())));
							checkbox.change(function (e) {
								thisLayer.setVisibility($(this).is(':checked'));
							});
						}
					});
				};
			});

			// Wait for OpenLayers to initialize
			refreshLayers();
			map.map.events.register('addlayer', {}, refreshLayers);
			map.map.events.register('removelayer', {}, refreshLayers);
			map.map.events.register('changelayer', {}, refreshLayers);
			map.map.events.register('changebaselayer', {}, refreshLayers);
			$.each(map.map.getControlsByClass('OpenLayers.Control.LayerSwitcher'), function (k, c) {
				map.map.removeControl(c);
			});
		});
	});
	{/jq}
{/tikimodule}
