{tikimodule error=$module_params.error title=$tpl_module_title name="map_mode_selector" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle}
	<form class="map-mode-selector" method="post" action="">
		<select name="modeControl">
		</select>
	</form>
	{jq}
	$('.map-mode-selector').hide();
	$(function () {
		$('.map-mode-selector').removeClass('map-mode-selector').each(function () {
			var map = $(this).closest('.tab, #appframe, body').find('.map-container:first')[0];
			var modeControl= $(this.modeControl);

			if (! map) {
				return;
			}

			$(this).show();

			modeControl.change(function () {
				if (map.modeManager) {
					map.modeManager.switchTo($(this).val());
				}
			});

			var refreshModes = function () {
				modeControl.empty();
				$.each(map.modeManager.modes, function (k, mode) {
					modeControl.append($('<option/>')
						.attr('value', mode.name)
						.text(mode.name)
						.attr('selected', mode === map.modeManager.activeMode));
				});
			};

			$(map).one('initialized', function () {
				// Wait for OpenLayers to initialize
				refreshModes();

				$(map).bind('modechanged', refreshModes);
			});
		});
	});
	{/jq}
{/tikimodule}
