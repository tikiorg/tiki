{tikimodule error=$module_params.error title=$tpl_module_title name="map_layer_selector" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle}
	<form class="map-layer-selector" method="post" action="">
		{if $controls.baselayer}
			<select name="baseLayers">
			</select>
		{/if}

		{if $controls.optionallayers}
			<div class="optionalLayers">
			</div>
		{/if}
	</form>
	{jq}
	$('.map-layer-selector').hide();
	$(function () {
		$('.map-container').one('initialized', function () {
			$('.map-layer-selector').removeClass('map-layer-selector').each(function () {
				var refreshLayers, map = $(this).closest('.tab, #appframe, body').find('.map-container:first')[0]
					, baseLayers = $(this.baseLayers)
					, optionalLayers = $('.optionalLayers', tr(this)) /* e.g. tr('Editable') to be translatable via lang/../language.js */
					;

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

				refreshLayers = function () {
					baseLayers.empty();
					optionalLayers.empty();
					$.each(map.map.layers, function (k, thisLayer) {
						if (! thisLayer.displayInLayerSwitcher) {
							return;
						}

						if (thisLayer.isBaseLayer) {
							baseLayers.append($('<option/>')
								.attr('value', k)
								.text(tr(thisLayer.name))
								.prop('selected', thisLayer === map.map.baseLayer));
						} else {
							var label, checkbox;
							optionalLayers.append(label = $('<label/>').text(thisLayer.name).prepend(
								checkbox = $('<input type="checkbox"/>')
									.prop('checked', thisLayer.getVisibility())));
							checkbox.change(function (e) {
								thisLayer.setVisibility($(this).is(':checked'));
							});
						}
					});
				};

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
	});
	{/jq}
{/tikimodule}
