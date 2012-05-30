<a id="{$mapcontrol.id|escape}" href="#" title="{$mapcontrol.label|escape}">{icon _id=$mapcontrol.icon title=$mapcontrol.label}</a>
<div id="{$mapcontrol.id|escape}-dialog"></div>
{jq}
$('#appframe .map-container').bind('initialized', function () {
	var container = this
		, link = '#{{$mapcontrol.id|escape}}'
		, dialog = '#{{$mapcontrol.id|escape}}-dialog'
		, vlayer
		, feature
		;
	
	vlayer = container.vectors;

	vlayer.events.on({
		featureselected: function (ev) {
			feature = ev.feature;
		},
		featureunselected: function (ev) {
			feature = null;
			$(dialog).dialog('close');
		}
	});

	$(dialog)
		.ColorPicker({
			flat: true,
			onChange: function (hsb, hex) {
				feature.attributes.color = '#' + hex;
			}
		})
		.dialog({
			autoOpen: false,
			width: 400
		})
		;
	
	$(link).click(function () {
		if (feature) {
			$(dialog).ColorPickerSetColor(feature.attributes.color);
			$(dialog).dialog('open');
		}
	});

});
{/jq}
