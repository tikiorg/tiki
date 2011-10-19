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
		$('.map-layer-selector').removeClass('map-layer-selector').each(function () {
			var map = $(this).closest('.tab, #appframe, body').find('.map-container:first')[0];
			var baseLayers= $(this.baseLayers);
			var optionalLayers= $('.optionalLayers', this);

			if (! map) {
				return;
			}

			$(this).show();

			baseLayers.change(function () {
				if (map.map) {
					map.map.setBaseLayer(map.map.layers[$(this).val()]);
				}
			});

			var refreshLayers = function () {
				baseLayers.empty();
				optionalLayers.empty();
				$.each(map.map.layers, function (k, layer) {
					if (! layer.displayInLayerSwitcher) {
						return;
					}

					if (layer.isBaseLayer) {
						baseLayers.append($('<option/>')
							.attr('value', k)
							.text(layer.name)
							.attr('selected', layer === map.map.baseLayer));
					} else {
						optionalLayers.append($('<label/>').text(layer.name).prepend(
							$('<input type="checkbox"/>')
								.attr('checked', layer.getVisibility())))
								.change(function () {
									layer.setVisibility($(this).is(':checked'));
								});
					}
				});
			};

			setTimeout(function () {
				// Wait for OpenLayers to initialize
				refreshLayers();
				map.map.events.register('addlayer', {}, refreshLayers);
				map.map.events.register('removelayer', {}, refreshLayers);
				map.map.events.register('changelayer', {}, refreshLayers);
				map.map.events.register('changebaselayer', {}, refreshLayers);
				$.each(map.map.getControlsByClass('OpenLayers.Control.LayerSwitcher'), function (k, c) {
					map.map.removeControl(c);
				});
			}, 500);
		});
	});
	{/jq}
{/tikimodule}
